<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Shared\Converter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function preview(Request $request)
    {
        $user = Auth::user();
        $course_id = $request->query('course_id');
        $year = (int) $request->query('year');
        $term = $request->query('term');
        $TQF = $request->query('TQF');

        if (!$course_id || !$year || $term === null || $TQF === null || !$user) {
            abort(400, 'Missing required parameters or not authenticated');
        }

        $context = DB::table('courseyears as cy')
            ->leftJoin('users as u', 'cy.user_id', '=', 'u.user_id')
            ->leftJoin('courses as c', 'cy.course_id', '=', 'c.course_id')
            ->leftJoin('prompts as p', 'cy.id', '=', 'cy.id')
            ->leftJoin('generates as g', 'p.ref_id', '=', 'g.ref_id')
            ->where('cy.user_id', $user->user_id)
            ->where('cy.course_id', $course_id)
            ->where('cy.year', $year)
            ->where('cy.term', $term)
            ->where('cy.TQF', $TQF)
            ->select(
                'cy.id',
                'u.name',
                'c.course_id',
                'c.course_name',
                'c.course_name_en',
                'c.course_detail_th',
                'c.course_detail_en',
                'cy.year',
                'cy.term',
                'g.ai_text',
            )
            ->first();
            
        if (!$context) {
            abort(404, 'TQF Document (CourseYear) not found for this user.');
        }

        $curricula = DB::table('curricula')->where('ref_id', $context->id)->first();
        
        $feedback = null;
        $plans = null;
        $generates = null;
        
        if ($curricula) {
            $curriculaRefId = $curricula->ref_id;

            $feedback = DB::table('feedback')->where('ref_id', $curriculaRefId)->first();
            $plans = DB::table('plans')->where('ref_id', $curriculaRefId)->first();

            $latestPrompt = DB::table('prompts')
                            ->where('ref_id', $context->id)
                            ->orderBy('updated_at', 'desc')
                            ->first();
            if($latestPrompt && property_exists($latestPrompt, 'id')){
                 $generates = DB::table('generates')
                            ->where('ref_id', $latestPrompt->id)
                            ->orderBy('updated_at', 'desc')
                            ->first();
            }
        }

        $curriculaDefaults = [
            'curriculum_name' => 'วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์',
            'faculty'         => 'วิทยาศาสตร์',
            'major'           => 'วิทยาการคอมพิวเตอร์',
            'campus'          => 'เชียงใหม่',
            'credits'         => '0 (0-0-0) (บรรยาย-ปฏิบัติ-ศึกษาด้วยตนเอง)',
        ];

        // Combine all data into one object
        $data = (array) $context + $curriculaDefaults;

        if ($curricula) {
            $curriculaArray = (array) $curricula;
            $curriculaArray['outcome_statement'] = isset($curricula->outcome_statement) ? json_decode($curricula->outcome_statement, true) : [];
            $curriculaArray['curriculum_map_data'] = isset($curricula->curriculum_map_data) ? json_decode($curricula->curriculum_map_data, true) : [];
            $curriculaArray['course_accord'] = isset($curricula->course_accord) ? json_decode($curricula->course_accord, true) : [];
            $data = array_merge($data, $curriculaArray);
        } else {
             $data['outcome_statement'] = [];
             $data['curriculum_map_data'] = [];
             $data['course_accord'] = [];
        }

        if ($feedback) {
             $feedbackArray = (array) $feedback;
             // Decode research JSON
             $feedbackArray['research'] = isset($feedback->research) ? json_decode($feedback->research, true) : [];
             $data = $data + $feedbackArray;
        } else {
             $data['research'] = [];
        }
        if ($plans) {
             $plansArray = (array) $plans;
             // Decode JSON columns from plans
             $plansArray['teaching_methods'] = isset($plans->teaching_methods) ? json_decode($plans->teaching_methods, true) : null;
             $plansArray['lesson_plan'] = isset($plans->lesson_plan) ? json_decode($plans->lesson_plan, true) : [];
             $plansArray['assessment_strategies'] = isset($plans->assessment_strategies) ? json_decode($plans->assessment_strategies, true) : [];
             $plansArray['rubrics'] = isset($plans->rubrics) ? json_decode($plans->rubrics, true) : [];
             $plansArray['grading_criteria'] = isset($plans->grading_criteria) ? json_decode($plans->grading_criteria, true) : [];
             $data = $data + $plansArray;
        } else {
             $data['teaching_methods'] = null;
             $data['lesson_plan'] = [];
             $data['assessment_strategies'] = [];
             $data['rubrics'] = [];
             $data['grading_criteria'] = [];
        }
         if ($generates) {
             if (property_exists($generates, 'ai_text')) {
                 $data['ai_text'] = $generates->ai_text;
             }
        }

        // แปลงกลับเป็น Object
        $data = (object) $data;

        if(isset($data->lesson_plan)) $data->plan_data = $data->lesson_plan;
        if(isset($data->assessment_strategies)) $data->assessment_data = $data->assessment_strategies;

        return view('component/preview', compact('data'));
    }

    public function savedataedit(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required',
            'year'      => 'required',
            'term'      => 'required',
            'TQF'       => 'required',
            'field'     => 'required|string',
            'value'     => 'nullable',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $courseYear = DB::table('courseyears')
                ->where('course_id', $validated['course_id'])
                ->where('user_id', $user->user_id)
                ->where('year', $validated['year'])
                ->where('term', $validated['term'])
                ->where('TQF', $validated['TQF'])
                ->select('id')
                ->first();
        } catch (\Exception $e) {
             return response()->json([
                'success' => false,
                'message' => 'Error querying courseyears table.',
                'error'   => $e->getMessage()
            ], 500);
        }

        if (!$courseYear) {
            return response()->json(['message' => 'TQF Document (CourseYear) not found.'], 404);
        }

        $courseYearId = $courseYear->id;

        $field = $validated['field'];
        $value = $validated['value'];
        $targetTable = '';

        try {
            $curriculaConditions = ['ref_id' => $courseYearId];
            $courseInfo = DB::table('courses')->where('course_id', $validated['course_id'])->first();
            $courseName = $courseInfo ? $courseInfo->course_name_en : 'Unknown Course';
            $curriculaDefaults = [
                'curriculum_name' => 'วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์',
                'faculty'         => 'วิทยาศาสตร์',
                'major'           => 'วิทยาการคอมพิวเตอร์',
                'campus'          => 'เชียงใหม่',
                'credits'         => '0 (0-0-0) (บรรยาย-ปฏิบัติ-ศึกษาด้วยตนเอง)',
            ];
            $curriculaId = $this->updateOrCreateWithDB('curricula', $curriculaConditions, [], $curriculaDefaults);


            // ตรรกะการกระจายข้อมูล (Data Router)
            if (in_array($field, [
                'feedback', 'improvement', 'agreement', 'agreement',
                'research_subjects', 'academic_service', 'art_culture',
            ]))
            {
                $targetTable = 'feedback';
                $conditions = ['ref_id' => $curriculaId];
                $dbField = ($field === 'agreement') ? 'agreement' : $field;
                $dataToUpdate = [$dbField => $value];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'."]);

            } else if (Str::startsWith($field, 'plan_') || in_array($field, ['grade_correction', 'art_culture'])) {
                $targetTable = 'plans';
                $conditions = ['ref_id' => $curriculaId];
                // Use the field name directly (e.g., 'grade_correction')
                $dataToUpdate = [$field => $value];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'."]);

            } else if (Str::startsWith($field, 'rubric_')) {
                $targetTable = 'rubrics';
                $conditions = ['ref_id' => $curriculaId];
                $dataToUpdate = [$field => $value];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'."]);

            } else if ($field === 'ai_text') {
                $targetTable = 'generates';
                 $latestPrompt = DB::table('prompts')->where('ref_id', $courseYearId)->orderBy('updated_at', 'desc')->first();
                if ($latestPrompt && property_exists($latestPrompt, 'id')) {
                    $promptId = $latestPrompt->id;
                    $latestGenerate = DB::table('generates')->where('ref_id', $promptId)->orderBy('updated_at', 'desc')->first();
                    if ($latestGenerate && property_exists($latestGenerate, 'id')) {
                        $generateId = $latestGenerate->id;
                        DB::table('generates')->where('id', $generateId)->update(['ai_text' => $value, 'updated_at' => now()]);
                        return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'."]);
                    } else { Log::warning("No 'generates' record found for prompt ID: " . $promptId); }
                } else { Log::warning("No 'prompts' record found for courseYear ID: " . $courseYearId); }
                 return response()->json(['success' => false, 'message' => "Could not find related record to update '{$field}'."], 404);

            } else if ($field === 'section6_data') {
                $targetTable = 'plans';
                $conditions = ['ref_id' => $curriculaId];

                $decodedValue = null;
                $jsonValue = '{}'; 

                // Decode the incoming value (which might already be an object/array or a JSON string)
                if (is_string($value)) {
                    try {
                        // Attempt to decode if it's a non-empty string
                        if(!empty(trim($value))) {
                             $decodedValue = json_decode($value, true);
                             // Check if decoding failed or resulted in non-array
                             if (json_last_error() !== JSON_ERROR_NONE || !is_array($decodedValue)) {
                                Log::warning("Failed to decode section6_data string or result is not an array: " . $value);
                                $decodedValue = [];
                             }
                        } else {
                             $decodedValue = [];
                        }
                    } catch (\Exception $e) {
                         Log::error("Exception during section6_data JSON decode: " . $e->getMessage());
                         $decodedValue = [];
                    }
                } elseif (is_array($value) || is_object($value)) {
                    // If Laravel already decoded it, convert object to associative array
                    $decodedValue = json_decode(json_encode($value), true);
                     if (!is_array($decodedValue)) {
                          Log::warning("Could not convert decoded section6_data to array.");
                          $decodedValue = [];
                     }
                } else {
                     Log::warning("Received section6_data is not string, array, or object.");
                     $decodedValue = [];
                }

                // Filter out empty CLO entries
                $filteredData = [];
                if(is_array($decodedValue)) {
                    foreach ($decodedValue as $cloKey => $cloData) {
                        // Check if cloData structure is valid and if both arrays are empty
                         if (is_array($cloData) && count($cloData) === 2 && isset($cloData[0]['วิธีการสอน']) && isset($cloData[1]['การประเมินผล'])) {
                            $teachingMethods = $cloData[0]['วิธีการสอน'];
                            $assessmentMethods = $cloData[1]['การประเมินผล'];

                            // Keep the entry only if at least one array is NOT empty
                            if (!empty($teachingMethods) || !empty($assessmentMethods)) {
                                $filteredData[$cloKey] = $cloData;
                            }
                        } else {
                             Log::warning("Invalid structure found for CLO key '{$cloKey}' in section6_data, skipping.");
                        }
                    }
                }

                // Sort the filtered data by CLO key numerically
                uksort($filteredData, function ($a, $b) {
                    preg_match('/(\d+)/', $a, $matchesA);
                    preg_match('/(\d+)/', $b, $matchesB);
                    $numA = isset($matchesA[1]) ? intval($matchesA[1]) : PHP_INT_MAX;
                    $numB = isset($matchesB[1]) ? intval($matchesB[1]) : PHP_INT_MAX;
                    return $numA <=> $numB;
                });

                // Encode the filtered and sorted data back to JSON string
                if (!empty($filteredData)) {
                    $jsonValue = json_encode($filteredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                } else {
                    $jsonValue = '{}';
                }

                // The column to store the JSON is 'teaching_methods'
                $dataToUpdate = ['teaching_methods' => $jsonValue];

                // Use updateOrCreate to handle both existing and new plan entries
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' (JSON) updated in '{$targetTable}'.teaching_methods."]);

            } else if ($field === 'section7_data') {
                $targetTable = 'plans';
                $conditions = ['ref_id' => $curriculaId];
                $decodedValue = $this->prepareJsonValue($value);
                $jsonToSave = json_encode($decodedValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                $dataToUpdate = ['lesson_plan' => $jsonToSave];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'.lesson_plan."]);

            } else if ($field === 'section8_1_data') {
                $targetTable = 'plans';
                $conditions = ['ref_id' => $curriculaId];
                $decodedValue = $this->prepareJsonValue($value);
                $jsonToSave = json_encode($decodedValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                $dataToUpdate = ['assessment_strategies' => $jsonToSave];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'.assessment_strategies."]);

            } else if ($field === 'section8_2_data') {
                $targetTable = 'plans';
                $conditions = ['ref_id' => $curriculaId];
                $decodedValue = $this->prepareJsonValue($value);
                $jsonToSave = json_encode($decodedValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                $dataToUpdate = ['rubrics' => $jsonToSave];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'.rubrics."]);

            } else if ($field === 'section9_1_data') {
                $targetTable = 'feedback';
                $conditions = ['ref_id' => $curriculaId];
                $decodedValue = $this->prepareJsonValue($value);
                $jsonToSave = json_encode($decodedValue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                $dataToUpdate = ['research' => $jsonToSave];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'.research."]);

            } // section 10
            else if (Str::startsWith($field, 'grade_')) {
                $targetTable = 'plans';
                $conditions = ['ref_id' => $curriculaId];
                $jsonColumn = 'grading_criteria';

                // Get current JSON data
                $currentPlans = DB::table($targetTable)->where($conditions)->first();
                $gradingData = ($currentPlans && $currentPlans->grading_criteria) ? json_decode($currentPlans->grading_criteria, true) : [];
                if (!is_array($gradingData)) $gradingData = [];

                // $field is 'grade_A_level', 'grade_A_criteria', etc.
                $gradingData[$field] = $value;

                // Prepare data to save
                $jsonToSave = json_encode($gradingData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                $dataToUpdate = [$jsonColumn => $jsonToSave];

                // Save
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in JSON column '{$jsonColumn}'."]);

            } // Section 5
            else if (Str::startsWith($field, 'plo') || Str::startsWith($field, 'curriculum_map_')) {

                $targetTable = 'curricula';
                $conditions = ['ref_id' => $curriculaId];
                $currentData = DB::table($targetTable)->where($conditions)->first();
                // Initialize arrays if data doesn't exist yet
                $outcomeStatementData = $currentData && $currentData->outcome_statement ? json_decode($currentData->outcome_statement, true) : [];
                $courseAccordData     = $currentData && $currentData->course_accord ? json_decode($currentData->course_accord, true) : [];
                $curriculumMapData    = $currentData && $currentData->curriculum_map_data ? json_decode($currentData->curriculum_map_data, true) : [];

                $processedValue = $value;
                if (Str::contains($field, ['_specific', '_generic', '_check'])) {
                    $processedValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                $jsonColumn = null;
                $jsonDataToSave = null;

                // Section 5.3
                if (Str::startsWith($field, 'plo_map_')) {
                    $jsonColumn = 'course_accord';
                     if (preg_match('/plo_map_r(\d+)_c(\d+)_(\w+)/', $field, $matches)) {
                        $rowIndex = (int)$matches[1]; $colIndex = (int)$matches[2]; $attribute = $matches[3]; $ploDbIndex = $colIndex + 1;
                        if (!isset($courseAccordData[$rowIndex]) || !is_array($courseAccordData[$rowIndex])) $courseAccordData[$rowIndex] = [];
                        if (!isset($courseAccordData[$rowIndex][$ploDbIndex]) || !is_array($courseAccordData[$rowIndex][$ploDbIndex])) $courseAccordData[$rowIndex][$ploDbIndex] = ['check' => false, 'level' => ''];
                        $courseAccordData[$rowIndex][$ploDbIndex][$attribute] = $processedValue;
                        $jsonDataToSave = $courseAccordData;
                    } else { Log::warning("Could not parse plo_map field name: {$field}"); }

                } 
                // Section 5.2 
                else if (Str::startsWith($field, 'curriculum_map_')) { 
                    $jsonColumn = 'curriculum_map_data';
                     if (preg_match('/curriculum_map_r(\d+)_c(\d+)/', $field, $matches)) {
                        $rowIndex = (int)$matches[1]; $colIndex = (int)$matches[2];
                        if (!isset($curriculumMapData[$rowIndex]) || !is_array($curriculumMapData[$rowIndex])) $curriculumMapData[$rowIndex] = [];
                        // Store as object
                        $curriculumMapData[$rowIndex][strval($colIndex)] = $processedValue;
                        $jsonDataToSave = $curriculumMapData;
                    } else { Log::warning("Could not parse curriculum_map field name: {$field}"); }

                }
                // Section 5.1
                else if (Str::startsWith($field, 'plo')) { 
                    $jsonColumn = 'outcome_statement';
                     if (preg_match('/plo(\d+)_(\w+)/', $field, $matches)) {
                        $ploIndex = (int)$matches[1]; $attribute = $matches[2];
                        while (count($outcomeStatementData) <= $ploIndex) { $outcomeStatementData[] = null; }
                        if (!isset($outcomeStatementData[$ploIndex]) || !is_array($outcomeStatementData[$ploIndex])) {
                             $outcomeStatementData[$ploIndex] = ['outcome' => null, 'specific' => false, 'generic' => false, 'level' => null, 'type' => null];
                        }
                        $outcomeStatementData[$ploIndex][$attribute] = $processedValue;

                        // Filter empty PLOs *after* updating
                        $filteredOutcomeStatementData = array_filter($outcomeStatementData, function($plo) {
                            if (empty($plo)) return false;
                            foreach ($plo as $val) { if (!in_array($val, [null, '', false], true)) return true; }
                            return false;
                        });
                        // Re-index array keys sequentially if needed after filtering, though json_encode handles sparse arrays
                        $jsonDataToSave = $filteredOutcomeStatementData;

                    } else { Log::warning("Could not parse plo field name: {$field}"); }
                }
                // Save updated JSON if a column was identified and data prepared
                if ($jsonColumn && $jsonDataToSave !== null) {
                    DB::table($targetTable)->where($conditions)->update([
                        $jsonColumn => json_encode($jsonDataToSave, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                        'updated_at' => now()
                    ]);
                    return response()->json(['success' => true, 'message' => "Field '{$field}' updated in JSON column '{$jsonColumn}'."]);
                } else {
                     // If parsing failed or no data to save, return appropriate message
                    Log::warning("JSON column update skipped for field: {$field}");
                    return response()->json(['success' => false, 'message' => "Could not process update for field '{$field}'."], 400);
                }

            } else {
                // ค่า Default: บันทึกลงตาราง 'curricula'
                $targetTable = 'curricula';
                $conditions = ['ref_id' => $curriculaId];
                $dataToUpdate = [$field => $value];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'."]);
            }

            // Fallback (should not be reached anymore)
             Log::error("Fell through all conditions in savedataedit for field: {$field}");
             return response()->json(['success' => false, 'message' => "Internal logic error for field '{$field}'."], 500);


        } catch (\Exception $e) {
            Log::error("Error in savedataedit: " . $e->getMessage() . "\n" . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update TQF.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    private function prepareJsonValue($value)
    {
         if (is_string($value)) {
            try {
                if(!empty(trim($value))) {
                    $decoded = json_decode($value, true); 
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    } else {
                        Log::warning("Failed to decode JSON value string: " . $value . " - Error: " . json_last_error_msg());
                        return $value; // Return original string if decode fails
                    }
                } else {
                    return []; // Treat empty string as empty data (array)
                }
            } catch (\Exception $e) {
                Log::error("Exception during JSON decode: " . $e->getMessage());
                return $value; // Return original string on exception
            }
        }
        if (is_array($value) || is_object($value)) {
            return json_decode(json_encode($value), true);
        }
        Log::warning("Received non-string/array/object value for JSON field.");
        return $value ?? [];
    }
    private function updateOrCreateWithDB($tableName, $conditions, $dataToUpdate, $defaults = [])
    {
        $existingRecord = DB::table($tableName)->where($conditions)->first();
        if ($existingRecord) {
            if (!empty($dataToUpdate)) {
                 $filteredDataToUpdate = array_diff_key($dataToUpdate, $conditions);
                 if (!empty($filteredDataToUpdate)) {
                    $filteredDataToUpdate['updated_at'] = now();
                    DB::table($tableName)->where($conditions)->update($filteredDataToUpdate);
                 }
            }
            return $existingRecord->id ?? $existingRecord->ref_id;
        } else {
             $defaultsToMerge = array_diff_key($defaults, $conditions, $dataToUpdate);
             $dataToInsert = array_merge($defaultsToMerge, $conditions, $dataToUpdate);
             $dataToInsert['created_at'] = now();
            $dataToInsert['updated_at'] = now();
             DB::table($tableName)->insert($dataToInsert);
            return $conditions['id'] ?? $conditions['ref_id'];
        }
    }

    private function updateLearningOutcomeDescription($code, $description)
    {
        try {
             DB::table('learning_outcomes')
                ->where('code', $code)
                ->update([
                    'description' => $description,
                ]);
             Log::info("Updated description for code: {$code}");
             return true;
        } catch (\Exception $e) {
             Log::error("Failed to update description for code {$code}: " . $e->getMessage());
             return false;
        }
    }

    // ดาวน์โหลด.docx 
    public function exportdocx()
    {
        // ข้อมูลจำลอง (DUMMY DATA)
        $data = [
            'logo_path' => public_path('image/mjulogo.jpg'),
            'title' => 'มหาวิทยาลัยแม่โจ้',
            'subtitle' => 'มคอ. 3 รายละเอียดรายวิชา',
            'fileName' => 'มคอ-3-รายละเอียดรายวิชา',

            // -- ปรัชญา --
            'philosophy' => 'มุ่งมั่นพัฒนาบัณฑิตสู่ความเป็นผู้อุดมด้วยปัญญา อดทน สู้งาน เป็นผู้มีคุณธรรมและจริยธรรม เพื่อความเจริญรุ่งเรืองวัฒนาของสังคมไทยที่มีการเกษตรเป็นรากฐาน',
            'philosophy_education' => 'จัดการศึกษาเพื่อเสริมสร้างปัญญา ในรูปแบบการเรียนรู ้จากการปฏิบัติที ่บูรณาการกับการทำงานตามอมตะโอวาท งานหนักไม่เคยฆ่าคน มุ่งให้ผู้เรียน มีทักษะการเรียนรู้ตลอดชีวิต',
            'philosophy_curriculum' => 'จัดการศึกษาเพื่อการพัฒนาเทคโนโลยี และส่งเสริมการสร้างนวัตกรรม เรียนรู้จากการปฏิบัติที่บูรณาการกับการทำงาน',
        ];


        try {
            // สร้างอ็อบเจกต์ PhpWord
            $phpWord = new PhpWord();
            $phpWord->setDefaultFontName('TH Sarabun New');
            $phpWord->setDefaultFontSize(16);

            // ตั้งค่าหน้ากระดาษ (A4, ขอบ 2.5cm)
            $cmMargin = 2.5;
            $section = $phpWord->addSection([
                'paperSize'   => 'A4',
                'orientation' => 'portrait',
                'marginTop'   => Converter::cmToTwip($cmMargin),
                'marginBottom'=> Converter::cmToTwip($cmMargin),
                'marginLeft'  => Converter::cmToTwip($cmMargin),
                'marginRight' => Converter::cmToTwip($cmMargin),
            ]);

            // เริ่มสร้างเอกสาร

            // === ส่วนหัว (Logo และ Title) ===
            if (file_exists($data['logo_path'])) {
                $section->addImage($data['logo_path'], [
                    'width'         => 80,
                    'height'        => 80,
                    'alignment'     => Jc::CENTER
                ]);
            }
            $section->addText($data['title'], ['bold' => true, 'size' => 20], ['alignment' => Jc::CENTER]);
            $section->addText($data['subtitle'], ['bold' => true, 'size' => 16], ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);

            
            // === ส่วนข้อมูล (ปรับปรุงใหม่ ใช้ตารางไร้ขอบ 1 ตาราง) ===
            $borderlessStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 0];
            $infoTable = $section->addTable($borderlessStyle);
            $infoTable->addRow();
            $infoTable->addCell(1000)->addText('คณะ', ['bold' => true]);
            $infoTable->addCell(3000)->addText($data['faculty']);
            $infoTable->addCell(1200)->addText('สาขาวิชา', ['bold' => true]);
            $infoTable->addCell(4800)->addText($data['major']);
            
            $infoTable->addRow();
            $infoTable->addCell()->addText('หลักสูตรปรับปรุง พ.ศ.', ['bold' => true]);
            $infoTable->addCell()->addText($data['curriculum_year']);
            $infoTable->addCell()->addText('');
            $infoTable->addCell()->addText('');

            $infoTable->addRow();
            $infoTable->addCell(1000)->addText('วิทยาเขต', ['bold' => true]);
            $infoTable->addCell(3000)->addText($data['campus']);
            $infoTable->addCell(2500)->addText('ภาคการศึกษา/ปีการศึกษา', ['bold' => true]);
            $infoTable->addCell(3500)->addText($data['term'] . ' / ' . $data['academic_year']);

            
            // === ส่วนปรัชญา ===
            $section->addTextBreak(1);
            $section->addText('ปรัชญามหาวิทยาลัยแม่โจ้', ['bold' => true, 'size' => 18]);
            $section->addText($data['philosophy'], [], ['spaceAfter' => 150]);
            $section->addText('ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้', ['bold' => true, 'size' => 18]);
            $section->addText($data['philosophy_education'], [], ['spaceAfter' => 150]);
            $section->addText('ปรัชญาหลักสูตร', ['bold' => true, 'size' => 18]);
            $section->addText($data['philosophy_curriculum'], [], ['spaceAfter' => 150]);

            // === สไตล์ตารางหลัก ===
            $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'width' => '100%'];
            $phpWord->addTableStyle('MainTable', $tableStyle);
            $thStyle = ['bgColor' => 'DBEAFE']; // bg-blue-100 (ใช้สีอ่อนลงเล็กน้อย)
            $thFont = ['bold' => true];
            $cellCenter = ['alignment' => Jc::CENTER];
            $vAlignCenter = ['valign' => 'center'];

            // === หมวดที่ 1 : ข้อมูลทั่วไป ===
            $section->addText('หมวดที่ 1 : ข้อมูลทั่วไป', ['bold' => true, 'size' => 16]);
            $table1 = $section->addTable('MainTable');
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('1. ชื่อวิชา', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_course_name']);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('รหัสวิชา', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_course_id']);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('จำนวนหน่วยกิต', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_credits']);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('หลักสูตร', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_curriculum_name']);

            // (แปลง Checkboxes เป็น Text)
            $typesText = '';
            $typesText .= ($data['course_types']['specific'] ? '[✓]' : '[ ]') . ' วิชาเฉพาะ ';
            $typesText .= ($data['course_types']['core'] ? '[✓]' : '[ ]') . ' กลุ่มวิชาแกน ';
            $typesText .= ($data['course_types']['major_required'] ? '[✓]' : '[ ]') . ' เอกบังคับ ';
            $typesText .= ($data['course_types']['major_elective'] ? '[✓]' : '[ ]') . ' เอกเลือก ';
            $typesText .= ($data['course_types']['free_elective'] ? '[✓]' : '[ ]') . ' วิชาเลือกเสรี';
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('5. ประเภทของรายวิชา', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($typesText);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('6. วิชาบังคับก่อน', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_prerequisites']);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('7. ผู้สอน', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_instructors']);

            // (เพิ่ม Row 8 ที่ขาดไป)
            $revisionText = 'ภาคการศึกษาที่ ';
            $revisionText .= ($data['s1_revision_term'] == '1' ? '[✓]' : '[ ]') . ' 1 ';
            $revisionText .= ($data['s1_revision_term'] == '2' ? '[✓]' : '[ ]') . ' 2 ';
            $revisionText .= ' ปีการศึกษา ' . $data['s1_revision_year'];
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('8. การแก้ไขล่าสุด', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($revisionText);
            
            // (Row 9 - Header ชั่วโมง)
            $table1->addRow();
            $table1->addCell(10000, ['gridSpan' => 4, 'bgColor' => 'F3F4F6'])->addText('9. จำนวนชั่วโมงที่ใช้ภาคการศึกษา', $thFont, $cellCenter);
            
            // (Row 10 - ชั่วโมง)
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('ภาคทฤษฎี ' . $data['s1_hours']['theory'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('ภาคปฏิบัติ ' . $data['s1_hours']['practice'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('การศึกษาด้วยตนเอง ' . $data['s1_hours']['self_study'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('ทัศนศึกษา/ฝึกงาน ' . $data['s1_hours']['field_trip'] . ' ชั่วโมง', null, $cellCenter);

            
            // === หมวดที่ 2 : คำอธิบายรายวิชา ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 2 : คำอธิบายรายวิชาและผลลัพธ์ระดับรายวิชา (CLOs)', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $table2 = $section->addTable('MainTable');
            $table2->addRow();
            $cell2_1 = $table2->addCell(10000);
            $cell2_1->addText('2.1 คำอธิบายรายวิชา', ['bold' => true]);
            $cell2_1->addText(htmlspecialchars($data['description']));
            $table2->addRow();
            $cell2_2 = $table2->addCell(10000);
            $cell2_2->addText('2.2 ผลลัพธ์การเรียนรู้ระดับรายวิชา (Course learning Outcome) CLOs', ['bold' => true]);
            $cell2_2->addText(htmlspecialchars($data['clos']));

            // === หมวดที่ 3 : การปรับปรุง ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 3 : การปรับปรุงรายวิชาตามข้อเสนอแนะจาก มคอ.5', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $table3 = $section->addTable('MainTable');
            $table3->addRow();
            $table3->addCell(5000, $thStyle)->addText('ข้อเสนอแนะ', $thFont);
            $table3->addCell(5000, $thStyle)->addText('การปรับปรุง', $thFont);
            $table3->addRow();
            $table3->addCell(5000)->addText(htmlspecialchars($data['feedback']));
            $table3->addCell(5000)->addText(htmlspecialchars($data['improvement']));

            // === หมวดที่ 4 : ข้อตกลงร่วมกัน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 4: ข้อตกลงร่วมกันระหว่างผู้สอนและผู้เรียน', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $section->addText(htmlspecialchars($data['agreement']));

            // === หมวดที่ 5 : ความสอดคล้อง ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 5: ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับหลักสูตร (PLOs) กับ ผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) และผลทักษะการเรียนรู้ตลอดชีวิต (LLLs)', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            
            // 5.1 PLOs
            $section->addText('5.1 ผลลัพธ์การเรียนรู้ของหลักสูตร', ['bold' => true, 'size' => 14]);
            $table5_1 = $section->addTable('MainTable');
            $table5_1->addRow();
            $table5_1->addCell(1000, $thStyle)->addText('PLOs', $thFont, $cellCenter);
            $table5_1->addCell(4500, $thStyle)->addText('Outcome Statement', $thFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Specific LO', $thFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Generic LO', $thFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Level', $thFont, $cellCenter);
            $table5_1->addCell(1500, $thStyle)->addText('Type', $thFont, $cellCenter);
            
            foreach ($data['s5_1_plos'] as $plo) {
                $table5_1->addRow();
                $table5_1->addCell(1000, $vAlignCenter)->addText($plo['id'], null, $cellCenter);
                $table5_1->addCell(4500)->addText(htmlspecialchars($plo['outcome']));
                $table5_1->addCell(1000, $vAlignCenter)->addText($plo['specific'] ? '✓' : '', null, $cellCenter);
                $table5_1->addCell(1000, $vAlignCenter)->addText($plo['generic'] ? '✓' : '', null, $cellCenter);
                $table5_1->addCell(1000, $vAlignCenter)->addText($plo['level'], null, $cellCenter);
                $table5_1->addCell(1500, $vAlignCenter)->addText($plo['type'], null, $cellCenter);
            }
            
            // 5.2 Mapping
            $section->addTextBreak(1);
            $section->addText('5.2 มาตรฐานผลการเรียนรู้รายวิชา (Curriculum Mapping)', ['bold' => true, 'size' => 14]);
            $table5_2 = $section->addTable('MainTable');
            $cellYellow = ['bgColor' => 'FFF9C4']; // bg-yellow-100
            $cellWhite = ['bgColor' => 'FFFFFF'];
            
            // Row 1 (Headers)
            $table5_2->addRow();
            $table5_2->addCell(2000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('รายวิชา', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 7, 'bgColor' => 'FFF9C4'])->addText('ด้านคุณธรรมและจริยธรรม', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 8, 'bgColor' => 'FFF9C4'])->addText('ด้านความรู้', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 4, 'bgColor' => 'FFFFFF'])->addText('ทักษะทางปัญญา', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 6, 'bgColor' => 'FFF9C4'])->addText('ทักษะระหว่างบุคคลและความรับผิดชอบ', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 4, 'bgColor' => 'FFFFFF'])->addText('ทักษะวิเคราะห์เชิงตัวเลข...', null, $cellCenter);
            
            // Row 2 (Sub-headers)
            $table5_2->addRow();
            $table5_2->addCell(null, ['vMerge' => 'continue']); // Cell จาก Row 1
            for ($i=1; $i<=7; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter);
            for ($i=1; $i<=8; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter);
            for ($i=1; $i<=4; $i++) $table5_2->addCell(300, $cellWhite)->addText($i, null, $cellCenter);
            for ($i=1; $i<=6; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter);
            for ($i=1; $i<=4; $i++) $table5_2->addCell(300, $cellWhite)->addText($i, null, $cellCenter);

            // Row 3 (Data)
            $mapSymbols = ['0' => '', '1' => '●', '2' => '○'];
            foreach ($data['s5_2_mapping_data'] as $mapRow) {
                $table5_2->addRow();
                $table5_2->addCell(2000)->addText($data['s1_course_id'] . "\n" . $data['s1_course_name']);
                foreach ($mapRow as $state) {
                    $table5_2->addCell(300)->addText($mapSymbols[$state] ?? '', ['bold' => true], $cellCenter);
                }
            }

            // 5.3 CLO-PLO Mapping
            $section->addTextBreak(1);
            $section->addText('5.3 ความสอดคล้องของรายวิชากับ PLOs, CLOs และ LLLs', ['bold' => true, 'size' => 14]);
            $table5_3 = $section->addTable('MainTable');
            $table5_3->addRow();
            $table5_3->addCell(1000, $thStyle)->addText('รหัส CLO', $thFont, $cellCenter);
            $table5_3->addCell(5000, $thStyle)->addText('รหัสวิชา ' . $data['s1_course_id'] . ' ' . $data['s1_course_name'], $thFont, $cellCenter);
            foreach ($data['s5_1_plos'] as $plo) { // สร้างหัวตาราง PLO แบบไดนามิก
                $table5_3->addCell(1000, $thStyle)->addText('PLO' . $plo['id'] . ' (' . $plo['level'] . ')', $thFont, $cellCenter);
            }
            
            foreach ($data['s5_3_clos_plo_mapping'] as $map) {
                $table5_3->addRow();
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['clo_id'], null, $cellCenter);
                $table5_3->addCell(5000)->addText(htmlspecialchars($map['clo_desc']));
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo1'], null, $cellCenter);
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo2'], null, $cellCenter);
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo3'], null, $cellCenter);
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo4'], null, $cellCenter);
            }

            // === หมวดที่ 6 : CLOs, วิธีการสอน, การประเมิน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 6 : ความสอดคล้องระหว่างผลการเรียนรู้ระดับรายวิชา (CLOs) วิธีการสอน และการประเมินผล', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $table6 = $section->addTable('MainTable');
            $table6->addRow();
            $table6->addCell(2000, $thStyle)->addText('CLO#', $thFont);
            $table6->addCell(4000, $thStyle)->addText('วิธีการสอน (Active Learning)', $thFont);
            $table6->addCell(4000, $thStyle)->addText('การประเมินผล', $thFont);
            
            foreach ($data['s6_clos_teaching'] as $row) {
                $table6->addRow();
                $table6->addCell(2000)->addText(htmlspecialchars($row['clo']));
                $table6->addCell(4000)->addText(htmlspecialchars($row['teaching']));
                $table6->addCell(4000)->addText(htmlspecialchars($row['assessment']));
            }
            
            // === หมวดที่ 7 : แผนการสอน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 7: แผนการสอน', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $section->addText('7.1 แผนการสอน', ['bold' => true, 'size' => 14]);
            $table7 = $section->addTable('MainTable');
            $table7->addRow();
            $table7->addCell(800, $thStyle)->addText('สัปดาห์ที่', $thFont, $cellCenter);
            $table7->addCell(1500, $thStyle)->addText('หัวข้อ', $thFont, $cellCenter);
            $table7->addCell(2000, $thStyle)->addText('วัตถุประสงค์', $thFont, $cellCenter);
            $table7->addCell(2500, $thStyle)->addText('กิจกรรม', $thFont, $cellCenter);
            $table7->addCell(1500, $thStyle)->addText('สื่อ/เครื่องมือ', $thFont, $cellCenter);
            $table7->addCell(1000, $thStyle)->addText('การประเมินผล', $thFont, $cellCenter);
            $table7->addCell(700, $thStyle)->addText('CLO', $thFont, $cellCenter);
            
            foreach ($data['s7_lesson_plan'] as $plan) {
                $table7->addRow();
                $table7->addCell(800, $vAlignCenter)->addText($plan['week'], null, $cellCenter);
                $table7->addCell(1500)->addText(htmlspecialchars($plan['topic']));
                $table7->addCell(2000)->addText(htmlspecialchars($plan['objective']));
                $table7->addCell(2500)->addText(htmlspecialchars($plan['activity']));
                $table7->addCell(1500)->addText(htmlspecialchars($plan['media']));
                $table7->addCell(1000)->addText(htmlspecialchars($plan['eval']));
                $table7->addCell(700, $vAlignCenter)->addText($plan['clo'], null, $cellCenter);
            }
            
            // === หมวดที่ 8 : การประเมิน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 8 : การประเมินการบรรลุผลลัพธ์การเรียนรู้รายวิชา (CLOs)', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            // 8.1 กลยุทธ์
            $section->addText('8.1 กลยุทธ์การประเมิน', ['bold' => true, 'size' => 14]);
            $table8_1 = $section->addTable('MainTable');
            $table8_1->addRow();
            $table8_1->addCell(2500, $thStyle)->addText('วิธีการประเมิน', $thFont, $cellCenter);
            $table8_1->addCell(3500, $thStyle)->addText('เครื่องมือ / รายละเอียด', $thFont, $cellCenter);
            $table8_1->addCell(1500, $thStyle)->addText('สัดส่วน (%)', $thFont, $cellCenter);
            $table8_1->addCell(2500, $thStyle)->addText('ความสอดคล้องกับ CLOs', $thFont, $cellCenter);
            
            foreach ($data['s8_1_assessment_strategies'] as $strategy) {
                $table8_1->addRow();
                $table8_1->addCell(2500)->addText(htmlspecialchars($strategy['method']));
                $table8_1->addCell(3500)->addText(htmlspecialchars($strategy['tool']));
                $table8_1->addCell(1500, $vAlignCenter)->addText($strategy['ratio'], null, $cellCenter);
                $table8_1->addCell(2500, $vAlignCenter)->addText($strategy['clos'], null, $cellCenter);
            }
            
            // 8.2 Rubrics
            $section->addTextBreak(1);
            $section->addText('8.2 วิธีการประเมิน แบบรูบริค (Rubric) หรือ อื่นๆ (ถ้ามี)', ['bold' => true, 'size' => 14]);
            
            foreach ($data['s8_2_rubrics'] as $rubric) {
                $section->addText(htmlspecialchars($rubric['title']), ['bold' => true, 'size' => 14]);
                $tableRubric = $section->addTable('MainTable');
                $tableRubric->addRow();
                $tableRubric->addCell(1500, $thStyle)->addText('ระดับ', $thFont, $cellCenter);
                $tableRubric->addCell(8500, $thStyle)->addText(htmlspecialchars($rubric['header']), $thFont, $cellCenter);
                
                foreach ($rubric['levels'] as $level) {
                    $tableRubric->addRow();
                    $tableRubric->addCell(1500, $vAlignCenter)->addText($level['level'], null, $cellCenter);
                    $tableRubric->addCell(8500)->addText(htmlspecialchars($level['desc']));
                }
                $section->addTextBreak(0.5); // เว้นวรรคระหว่าง Rubric
            }

            // === หมวดที่ 9 : สื่อการเรียนรู้ ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 9: สื่อการเรียนรู้และงานวิจัย', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            
            $section->addText('9.1 สื่อการเรียนรู้และสิ่งสนับสนุนการเรียนรู้', ['bold' => true, 'size' => 14]);
            foreach ($data['s9_references'] as $item) {
                $section->addListItem(htmlspecialchars($item), 0);
            }
            
            $section->addText('9.2 งานวิจัยที่นำมาสอนในรายวิชา', ['bold' => true, 'size' => 14]);
            $section->addText(htmlspecialchars($data['s9_research']));
            
            $section->addText('9.3 การบริการวิชาการ', ['bold' => true, 'size' => 14]);
            $section->addText(htmlspecialchars($data['s9_academic_service']));

            $section->addText('9.4 งานทำนุบำรุงศิลปวัฒนธรรม', ['bold' => true, 'size' => 14]);
            $section->addText(htmlspecialchars($data['s9_culture']));
            
            // === หมวดที่ 10 : เกณฑ์การประเมิน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 10 : เกณฑ์การประเมิน', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $table10 = $section->addTable('MainTable');
            $table10->addRow();
            $table10->addCell(5000, $thStyle)->addText('ระดับผลการศึกษา', $thFont, $cellCenter);
            $table10->addCell(5000, $thStyle)->addText('เกณฑ์การประเมินผล', $thFont, $cellCenter);
            
            foreach ($data['s10_grading_criteria'] as $grade) {
                $table10->addRow();
                $table10->addCell(5000, $vAlignCenter)->addText($grade['grade'], null, $cellCenter);
                $table10->addCell(5000, $vAlignCenter)->addText($grade['criteria'], null, $cellCenter);
            }

            // === หมวดที่ 11 : ขั้นตอนการแก้ไขคะแนน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 11 : ขั้นตอนการแก้ไขคะแนน', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $section->addText(htmlspecialchars($data['s11_grade_correction']));


            // 5. สิ้นสุดการสร้างเอกสาร
            
            // 6. บันทึกไฟล์และส่งให้ดาวน์โหลด
            $fileName = $data['fileName'] . '.docx';
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            // (จัดการ Error)
            return response('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
        }
    }
}
