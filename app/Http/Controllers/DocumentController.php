<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Shared\Converter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function editdoc(Request $request)
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
            ->leftJoin('prompts as p', 'cy.id', '=', 'p.ref_id')
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

       $dbPlos = [];
        try {
            // Get PLOs and use 'plo' column as array key
            $dbPlos = DB::table('plos')->get()->keyBy('plo'); 
        } catch (\Exception $e) {
            Log::error("Error fetching 'plos' table: " . $e->getMessage());
        }

        $curriculaDefaults = [
            'curriculum_name' => 'วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์',
            'faculty'         => 'วิทยาศาสตร์',
            'major'           => 'วิทยาการคอมพิวเตอร์',
            'campus'          => 'เชียงใหม่',
            'credits'         => '0(0-0-0)',
            'curriculum_year' => $context->year ? $context->year + 543 : '',
            'outcome_statement' => [],
            'curriculum_map_data' => [],
            'course_accord' => [],
            'feedback' => '', 'improvement' => '', 's4_agreement' => '',
            'references_data' => [], 'research_subjects' => '', 'academic_service' => '', 'art_culture' => '',
            'teaching_methods' => null, 'lesson_plan' => [], 'assessment_strategies' => [],
            'rubrics' => [],
            'grading_criteria' => [],
            'grade_correction' => '',
            'ai_text' => ''
        ];

        $data = (array) $context + $curriculaDefaults;
        $feedback = null;
        $plans = null;
        $generates = null;

        $curricula = DB::table('curricula')->where('ref_id', $context->id)->first();

        $savedOutcomeData = ($curricula && $curricula->outcome_statement) 
                            ? json_decode($curricula->outcome_statement, true) 
                            : [];
        if (!is_array($savedOutcomeData)) $savedOutcomeData = [];

        $finalOutcomeStatement = [];
        
        // Determine the loop limit: max of 4 (default), max key in DB, max key in saved JSON
        $maxPloKeyDb = $dbPlos->keys()->isEmpty() ? 0 : $dbPlos->keys()->max();
        $maxPloKeyDb = $dbPlos->keys()->isEmpty() ? 0 : $dbPlos->keys()->max();
        $maxPloKeyJson = empty($savedOutcomeData) ? 0 : (max(array_keys($savedOutcomeData)) ?: 0);
        $loopLimit = max(4, $maxPloKeyDb, $maxPloKeyJson);

        for ($i = 1; $i <= $loopLimit; $i++) {
            $ploId = $i;
            $masterPlo = $dbPlos->get($ploId);
            $savedData = $savedOutcomeData[$ploId] ?? []; 

            if ($ploId == 1 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? true,
                    'generic' => $savedData['generic'] ?? false,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
            if ($ploId == 2 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? true,
                    'generic' => $savedData['generic'] ?? false,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
            if ($ploId == 3 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? true,
                    'generic' => $savedData['generic'] ?? false,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
            if ($ploId == 4 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? true,
                    'generic' => $savedData['generic'] ?? false,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
            if ($ploId == 5 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? false,
                    'generic' => $savedData['generic'] ?? true,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
        }

        if ($curricula) {
            $curriculaRefId = $curricula->ref_id;
            $feedback = DB::table('feedback')->where('ref_id', $curriculaRefId)->first();
            $plans = DB::table('plans')->where('ref_id', $curriculaRefId)->first();

            $latestPrompt = DB::table('prompts')->where('ref_id', $context->id)->orderBy('updated_at', 'desc')->first();
            if($latestPrompt && property_exists($latestPrompt, 'id')){
                 $generates = DB::table('generates')->where('ref_id', $latestPrompt->id)->orderBy('updated_at', 'desc')->first();
            }
            
            $curriculaArray = (array) $curricula;
            
            $curriculaArray['outcome_statement'] = $finalOutcomeStatement;
            $curriculaArray['curriculum_map_data'] = isset($curricula->curriculum_map_data) ? json_decode($curricula->curriculum_map_data, true) : $data['curriculum_map_data'];
            $curriculaArray['course_accord'] = isset($curricula->course_accord) ? json_decode($curricula->course_accord, true) : $data['course_accord'];

            $data = $curriculaArray + $data;
        } else {
             $data['outcome_statement'] = $finalOutcomeStatement;
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

        return view('editdoc', compact('data'));
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

                $defaultGradingData = [
                    "grade_A_level" => "A",
                    "grade_A_criteria" => "80.00% ขึ้นไป",
                    "grade_Bp_level" => "B+",
                    "grade_Bp_criteria" => "75.00% - 79.99%",
                    "grade_B_level" => "B",
                    "grade_B_criteria" => "70.00% - 74.99%",
                    "grade_Cp_level" => "C+",
                    "grade_Cp_criteria" => "65.00% - 69.99%",
                    "grade_C_level" => "C",
                    "grade_C_criteria" => "60.00% - 64.99%",
                    "grade_Dp_level" => "D+",
                    "grade_Dp_criteria" => "55.00% - 59.99%",
                    "grade_D_level" => "D",
                    "grade_D_criteria" => "50.00% - 54.99%",
                    "grade_F_level" => "F",
                    "grade_F_criteria" => "ต่ำกว่า 50.00%"
                ];

                // 1. Get current JSON data
                $currentPlans = DB::table($targetTable)->where($conditions)->first();
                $gradingData = ($currentPlans && $currentPlans->grading_criteria) ? json_decode($currentPlans->grading_criteria, true) : [];
                if (!is_array($gradingData)) $gradingData = [];
                $gradingData = $gradingData + $defaultGradingData; 
                
                // 2. Update the specific key
                $gradingData[$field] = $value;

                // 3. Prepare data to save
                $jsonToSave = json_encode($gradingData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                $dataToUpdate = [$jsonColumn => $jsonToSave];

                // 4. Save
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
                return response()->json(['success' => true, 'message' => "Field '{$field}' updated in JSON column '{$jsonColumn}'."]);

            } // Section 5
            else if (Str::startsWith($field, 'plo') || Str::startsWith($field, 'curriculum_map_') || Str::startsWith($field, 'cloLll_desc_')) {
                $targetTable = 'curricula';
                $conditions = ['ref_id' => $curriculaId];
                $currentData = DB::table('curricula')->where($conditions)->first();
                
                $dbPlos = [];
                try {
                    $dbPlos = DB::table('plos')->get()->keyBy('plo');
                } catch (\Exception $e) {
                    Log::error("Error fetching 'plos' table in savedataedit: " . $e->getMessage());
                }
                
                $outcomeStatementData = ($currentData && $currentData->outcome_statement) ? json_decode($currentData->outcome_statement, true) : [];
                $courseAccordData     = ($currentData && $currentData->course_accord) ? json_decode($currentData->course_accord, true) : [];
                $curriculumMapData    = ($currentData && $currentData->curriculum_map_data) ? json_decode($currentData->curriculum_map_data, true) : [];
                if (!is_array($outcomeStatementData)) $outcomeStatementData = [];
                if (!is_array($courseAccordData)) $courseAccordData = [];
                if (!is_array($curriculumMapData)) $curriculumMapData = [];

                $processedValue = $value;
                if (Str::contains($field, ['_specific', '_generic', '_check'])) {
                    $processedValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                $jsonColumn = null;
                $jsonDataToSave = null;

                if (Str::startsWith($field, 'plo_map_')) { // Section 5.3
                    $jsonColumn = 'course_accord';
                    if (preg_match('/plo_map_([a-zA-Z0-9]+)_c(\d+)_(\w+)/', $field, $matches)) {
                        $cloCode = $matches[1];
                        $colIndex = (int)$matches[2];
                        $attribute = $matches[3];
                        $ploDbIndex = $colIndex + 1;

                        if (!isset($courseAccordData[$cloCode]) || !is_array($courseAccordData[$cloCode])) $courseAccordData[$cloCode] = [];
                        if (!isset($courseAccordData[$cloCode][$ploDbIndex]) || !is_array($courseAccordData[$cloCode][$ploDbIndex])) $courseAccordData[$cloCode][$ploDbIndex] = ['check' => false, 'level' => ''];
                        $courseAccordData[$cloCode][$ploDbIndex][$attribute] = $processedValue;
                        $jsonDataToSave = $courseAccordData;
                    } else { Log::warning("Could not parse plo_map field name: {$field}"); }

                } else if (Str::startsWith($field, 'cloLll_desc_')) { // Section 5.3 Description Update
                    $targetTable = 'learning_outcomes';
                    $code = Str::after($field, 'cloLll_desc_');
                    $this->updateLearningOutcomeDescription($code, $value);
                    return response()->json(['success' => true, 'message' => "Description for '{$code}' updated in '{$targetTable}'."]);
                    
                } else if (Str::startsWith($field, 'curriculum_map_')) { // Section 5.2
                    $jsonColumn = 'curriculum_map_data';
                    if (preg_match('/curriculum_map_r(\d+)_c(\d+)/', $field, $matches)) {
                        $rowIndex = (int)$matches[1]; $colIndex = (int)$matches[2];
                        if (!isset($curriculumMapData[0]) || !is_object($curriculumMapData[0])) $curriculumMapData = [(object)[]];
                        $curriculumMapData[0]->{strval($colIndex)} = $processedValue;
                        $jsonDataToSave = $curriculumMapData;
                    } else { Log::warning("Could not parse curriculum_map field name: {$field}"); }
                    
                } else if (Str::startsWith($field, 'plo')) { // Section 5.1
                    $jsonColumn = 'outcome_statement';
                    
                    // This ensures we save the 'outcome' from DB 'plos' + the changed data
                    $finalOutcomeStatement = [];
                    $maxPloKeyDb = $dbPlos->keys()->isEmpty() ? 0 : $dbPlos->keys()->max();
                    $maxPloKeyJson = empty($outcomeStatementData) ? 0 : (max(array_keys($outcomeStatementData)) ?: 0);
                    $loopLimit = max(4, $maxPloKeyDb, $maxPloKeyJson);

                    for ($i = 1; $i <= $loopLimit; $i++) {
                        $ploId = $i;
                        $masterPlo = $dbPlos->get($ploId);
                        $savedData = $outcomeStatementData[$ploId] ?? []; 
                        
                        $finalOutcomeStatement[$ploId] = [
                            'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''), 
                            'specific' => $savedData['specific'] ?? false,
                            'generic' => $savedData['generic'] ?? false,
                            'level' => $savedData['level'] ?? '',
                            'type' => $savedData['type'] ?? ''
                        ];
                    }
                    // [!!! END RE-BUILD !!!]
                    
                    if (preg_match('/plo(\d+)_(\w+)/', $field, $matches)) {
                        $ploIndex = (int)$matches[1]; $attribute = $matches[2];
                        
                        // Update the specific field that changed
                        if (isset($finalOutcomeStatement[$ploIndex])) {
                            $finalOutcomeStatement[$ploIndex][$attribute] = $processedValue;
                        } else {
                             // If it's a new row (e.g. PLO5)
                             $finalOutcomeStatement[$ploIndex] = [
                                 'outcome' => ($attribute === 'outcome') ? $processedValue : '',
                                 'specific' => ($attribute === 'specific') ? $processedValue : false,
                                 'generic' => ($attribute === 'generic') ? $processedValue : false,
                                 'level' => ($attribute === 'level') ? $processedValue : '',
                                 'type' => ($attribute === 'type') ? $processedValue : ''
                             ];
                        }

                        // Filter empty PLOs *after* updating
                        $filteredOutcomeStatementData = array_filter($finalOutcomeStatement, function($plo) {
                            if (empty($plo)) return false;
                            // Check if all values are default/empty
                            if (empty($plo['outcome']) && !$plo['specific'] && !$plo['generic'] && empty($plo['level']) && empty($plo['type'])) {
                                return false;
                            }
                            return true;
                        });
                        $jsonDataToSave = $filteredOutcomeStatementData;
                    } else { Log::warning("Could not parse plo field name: {$field}"); }
                 }

                if ($jsonColumn && $jsonDataToSave !== null) {
                    DB::table('curricula')->where($conditions)->update([
                        $jsonColumn => json_encode($jsonDataToSave, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                        'updated_at' => now()
                    ]);
                    return response()->json(['success' => true, 'message' => "Field '{$field}' updated in JSON column '{$jsonColumn}'."]);
                } else if (!$jsonColumn && !Str::startsWith($field, 'cloLll_desc_')) {
                    Log::warning("JSON column update skipped or failed for field: {$field}");
                    return response()->json(['success' => false, 'message' => "Could not process JSON update for field '{$field}'."], 400);
                }

            } else {
                // ค่า Default: บันทึกลงตาราง 'curricula'
                $targetTable = 'curricula';
                $conditions = ['ref_id' => $curriculaId];
                $dataToUpdate = [$field => $value];
                $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate, $curriculaDefaults);
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

    // ดาวน์โหลด.docx 
    public function exportdocx(Request $request)
    {
        // DATA FETCHING
        $user = Auth::user();
        $course_id = $request->query('course_id');
        $year = (int) $request->query('year');
        $term = $request->query('term');
        $TQF = $request->query('TQF');

        if (!$course_id || !$year || $term === null || $TQF === null || !$user) {
            abort(400, 'Missing required parameters or not authenticated');
        }

        // 1. Fetch main context (Fixed: Removed faulty joins)
        $context = DB::table('courseyears as cy')
            ->leftJoin('users as u', 'cy.user_id', '=', 'u.user_id')
            ->leftJoin('courses as c', 'cy.course_id', '=', 'c.course_id')
            ->leftJoin('prompts as p', 'cy.id', '=', 'p.ref_id')
            ->leftJoin('generates as g', 'p.ref_id', '=', 'g.ref_id')
            ->where('cy.user_id', $user->user_id)
            ->where('cy.course_id', $course_id)
            ->where('cy.year', $year)
            ->where('cy.term', $term)
            ->where('cy.TQF', $TQF)
            ->select(
                'cy.id as courseYearId',
                'u.name as instructorName',
                'c.course_id',
                'c.course_name',
                'c.course_name_en',
                'c.course_detail_th',
                'c.course_detail_en',
                'cy.year',
                'cy.term',
                'cy.TQF',
                'g.ai_text',
            )
            ->first();
            
        if (!$context) {
            abort(404, 'TQF Document (CourseYear) not found for this user.');
        }

        $dbPlos = [];
        try {
            $dbPlos = DB::table('plos')->get()->keyBy('plo'); 
        } catch (\Exception $e) {
            Log::error("Error fetching 'plos' table: " . $e->getMessage());
        }

        // Define the defaults array
        $curriculaDefaults = [
            'curriculum_name' => 'วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์',
            'faculty'         => 'วิทยาศาสตร์',
            'major'           => 'วิทยาการคอมพิวเตอร์',
            'campus'          => 'เชียงใหม่',
            'credits'         => '0(0-0-0)',
            'curriculum_year' => $context->year ? $context->year + 543 : '',
            'outcome_statement' => [],
            'curriculum_map_data' => [],
            'course_accord' => [],
            'feedback' => '', 'improvement' => '', 's4_agreement' => '',
            'references_data' => [], 'research_subjects' => '', 'academic_service' => '', 'art_culture' => '',
            'teaching_methods' => null, 'lesson_plan' => [], 'assessment_strategies' => [],
            'rubrics' => [],
            'grading_criteria' => [],
            'grade_correction' => '',
        ];
        
        $data = $curriculaDefaults + (array) $context; // Start with defaults

        $curricula = DB::table('curricula')->where('ref_id', $context->courseYearId)->first();
        $feedback = null; $plans = null; $generates = null;
        $savedOutcomeData = ($curricula && $curricula->outcome_statement) 
                            ? json_decode($curricula->outcome_statement, true) 
                            : [];
        if (!is_array($savedOutcomeData)) $savedOutcomeData = [];

        $finalOutcomeStatement = [];
        $maxPloKeyDb = $dbPlos->keys()->isEmpty() ? 0 : $dbPlos->keys()->max();
        $maxPloKeyJson = empty($savedOutcomeData) ? 0 : (max(array_keys($savedOutcomeData)) ?: 0);
        $loopLimit = max(4, $maxPloKeyDb, $maxPloKeyJson); 

        for ($i = 1; $i <= $loopLimit; $i++) {
            $ploId = $i;
            $masterPlo = $dbPlos->get($ploId);
            $savedData = $savedOutcomeData[$ploId] ?? []; 

            if ($ploId == 1 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? true,
                    'generic' => $savedData['generic'] ?? false,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
            if ($ploId == 2 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? true,
                    'generic' => $savedData['generic'] ?? false,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
            if ($ploId == 3 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? true,
                    'generic' => $savedData['generic'] ?? false,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
            if ($ploId == 4 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? true,
                    'generic' => $savedData['generic'] ?? false,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
            if ($ploId == 5 && empty($finalOutcomeStatement[$ploId]['outcome'])) {
                $finalOutcomeStatement[$ploId] = [
                    'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                    'specific' => $savedData['specific'] ?? false,
                    'generic' => $savedData['generic'] ?? true,
                    'level' => $savedData['level'] ?? 'U',
                    'type' => $savedData['type'] ?? 'K'
                ];
            }
        }

        if ($curricula) {
            $curriculaRefId = $curricula->ref_id;
            $feedback = DB::table('feedback')->where('ref_id', $curriculaRefId)->first();
            $plans = DB::table('plans')->where('ref_id', $curriculaRefId)->first();
            
            $latestPrompt = DB::table('prompts')->where('ref_id', $context->courseYearId)->orderBy('updated_at', 'desc')->first();
            if($latestPrompt && property_exists($latestPrompt, 'id')){
                $generates = DB::table('generates')->where('ref_id', $latestPrompt->id)->orderBy('updated_at', 'desc')->first();
            }
            
            $curriculaArray = (array) $curricula;
            $curriculaArray['outcome_statement'] = $finalOutcomeStatement;
            $curriculaArray['curriculum_map_data'] = isset($curricula->curriculum_map_data) ? json_decode($curricula->curriculum_map_data, true) : $data['curriculum_map_data'];
            $curriculaArray['course_accord'] = isset($curricula->course_accord) ? json_decode($curricula->course_accord, true) : $data['course_accord'];
            $data = $curriculaArray + $data;
        } else {
             $data['outcome_statement'] = $finalOutcomeStatement;
        }

        if ($feedback) {
            $feedbackArray = (array) $feedback;
            $feedbackArray['references_data'] = isset($feedback->research) ? json_decode($feedback->research, true) : $data['references_data'];
            if(isset($feedback->agreement)) $feedbackArray['s4_agreement'] = $feedback->agreement; 
            $data = $feedbackArray + $data;
        }

        if ($plans) {
            $plansArray = (array) $plans;
            $plansArray['teaching_methods'] = isset($plans->teaching_methods) ? json_decode($plans->teaching_methods, true) : $data['teaching_methods'];
            $plansArray['lesson_plan'] = isset($plans->lesson_plan) ? json_decode($plans->lesson_plan, true) : $data['lesson_plan'];
            $plansArray['assessment_strategies'] = isset($plans->assessment_strategies) ? json_decode($plans->assessment_strategies, true) : $data['assessment_strategies'];
            $plansArray['rubrics_data'] = isset($plans->rubrics) ? json_decode($plans->rubrics, true) : $data['rubrics'];
            $plansArray['grading_criteria'] = isset($plans->grading_criteria) ? json_decode($plans->grading_criteria, true) : $data['grading_criteria'];
            if(isset($plans->grade_correction)) $plansArray['grade_correction'] = $plans->grade_correction;
            $data = $plansArray + $data;
        }
        
        if ($generates) {
            if (property_exists($generates, 'ai_text')) {
                $data['ai_text'] = $generates->ai_text;
            }
        }

        // 3. Use Hardcoded CLO/LLL Descriptions
        $cloLllDescriptions = [];
        $aiTextJson = $data['ai_text'] ?? null;
        $aiTextData = $aiTextJson ? json_decode($aiTextJson, true) : [];

        if (is_array($aiTextData) && json_last_error() === JSON_ERROR_NONE) {
            foreach($aiTextData as $cloKey => $cloDetails) {
                // $cloKey is "CLO 1", $cloDetails is {"CLO": "...", ...}
                if (is_array($cloDetails) && isset($cloDetails['CLO'])) {
                    $cloLllDescriptions[] = [
                        'code' => trim(str_replace(' ', '', $cloKey)), // "CLO 1" -> "CLO1"
                        'description' => $cloDetails['CLO']
                    ];
                }
            }
        }

        $lllData = [
            ['code' => "LLL1", 'description' => "Creativity ความคิดสร้างสรรค์"],
            ['code' => "LLL2", 'description' => "Problem Solving การแก้ปัญหา"],
            ['code' => "LLL3", 'description' => "Critical Thinking การคิดเชิงวิพากษ์"],
            ['code' => "LLL4", 'description' => "Leadership การเป็นผู้นำ"],
            ['code' => "LLL5", 'description' => "Communication การสื่อสาร"],
            ['code' => "LLL6", 'description' => "Collaboration การประสานงาน"],
            ['code' => "LLL7", 'description' => "Information Management การจัดการข้อมูล "],
            ['code' => "LLL8", 'description' => "Adaptability การปรับตัว"],
            ['code' => "LLL9", 'description' => "Curiosity ความอยากรู้อยากเห็น"],
            ['code' => "LLL10", 'description' => "Reflection การสะท้อนทักษะความรู้"]
        ];
         
        $cloLllDescriptions = array_merge($cloLllDescriptions, $lllData);

        // 4. Convert final merged array (which is $data) for mapping
        $dataArray = $data;

        // DATA MAPPING
        $get = function($key, $default = '...') use ($dataArray) {
            return !empty($dataArray[$key]) ? $dataArray[$key] : $default;
        };
        $getJson = function($key, $default = []) use ($dataArray) {
            return !empty($dataArray[$key]) ? $dataArray[$key] : $default;
        };

        $docxData = [
            'logo_path' => public_path('image/mjulogo.jpg'),
            'title' => 'มหาวิทยาลัยแม่โจ้',
            'subtitle' => 'มคอ. 3 รายละเอียดรายวิชา',
            'fileName' => 'มคอ-3-' . $get('course_id', 'unknown'),

            'philosophy' => 'มุ่งมั่นพัฒนาบัณฑิตสู่ความเป็นผู้อุดมด้วยปัญญา อดทน สู้งาน เป็นผู้มีคุณธรรมและจริยธรรม เพื่อความเจริญรุ่งเรืองวัฒนาของสังคมไทยที่มีการเกษตรเป็นรากฐาน',
            'philosophy_education' => 'จัดการศึกษาเพื่อเสริมสร้างปัญญา ในรูปแบบการเรียนรู ้จากการปฏิบัติที ่บูรณาการกับการทำงานตามอมตะโอวาท งานหนักไม่เคยฆ่าคน มุ่งให้ผู้เรียน มีทักษะการเรียนรู้ตลอดชีวิต',
            'philosophy_curriculum' => 'จัดการศึกษาเพื่อการพัฒนาเทคโนโลยี และส่งเสริมการสร้างนวัตกรรม เรียนรู้จากการปฏิบัติที่บูรณาการกับการทำงาน',

            // -- Header Info --
            'faculty' => $get('faculty'),
            'major' => $get('major'),
            'curriculum_year' => $get('curriculum_year'),
            'campus' => $get('campus'),
            'term' => $get('term'),
            'academic_year' => $get('year') ? $get('year') + 543 : '',

            // -- หมวด 1 --
            's1_course_name' => $get('course_name', $get('course_name_en')),
            's1_course_id' => $get('course_id'),
            's1_credits' => $get('credits'),
            's1_curriculum_name' => $get('curriculum_name'),
            'course_types' => [
                'specific' => $get('is_specific', false),
                'core' => $get('is_core', false),
                'major_required' => $get('is_major_required', false),
                'major_elective' => $get('is_major_elective', false),
                'free_elective' => $get('is_free_elective', false),
            ],
            's1_prerequisites' => $get('prerequisites'),
            's1_instructors' => $get('instructorName'),
            's1_revision_term' => $get('term'),
            's1_revision_year' => $get('year') ? $get('year') + 543 : '',
            's1_hours' => [
                'theory' => $get('hours_theory', 0),
                'practice' => $get('hours_practice', 0),
                'self_study' => $get('hours_self_study', 0),
                'field_trip' => $get('hours_field_trip', 0),
            ],

            // -- หมวด 2 --
            'description' => $get('description', $get('course_detail_th')),
            'ai_text' => $get('ai_text'),

            // -- หมวด 3 --
            'feedback' => $get('feedback'),
            'improvement' => $get('improvement'),

            // -- หมวด 4 --
            'agreement' => $get('s4_agreement', $get('agreement')),

            // -- หมวด 5 --
            's5_1_plos' => [],
            's5_2_mapping_data' => [],
            's5_3_clos_plo_mapping' => [],

            // -- หมวด 6 --
            's6_clos_teaching' => [],

            // -- หมวด 7 --
            's7_lesson_plan' => [],

            // -- หมวด 8 --
            's8_1_assessment_strategies' => [],
            's8_2_rubrics' => [],

            // -- หมวด 9 --
            's9_references' => $getJson('references_data', []),
            's9_research' => $get('research_subjects'),
            's9_academic_service' => $get('academic_service'),
            's9_culture' => $get('art_culture'),

            // -- หมวด 10 --
            's10_grading_criteria' => [],

            // -- หมวด 11 --
            's11_grade_correction' => $get('grade_correction'),
        ];
        
        // JSON/ARRAY DATA MAPPING
        // 5.1 PLOs
        $outcomeStatementData = $getJson('outcome_statement', []);
        if(is_array($outcomeStatementData)) {
            ksort($outcomeStatementData);
            foreach($outcomeStatementData as $index => $plo) {
                if(empty($plo) || !is_array($plo)) continue;
                $docxData['s5_1_plos'][] = [
                    'id' => $index,
                    'outcome' => $plo['outcome'] ?? '...',
                    'specific' => $plo['specific'] ?? false,
                    'generic' => $plo['generic'] ?? false,
                    'level' => $plo['level'] ? explode(' - ', $plo['level'])[0] : '...',
                    'type' => $plo['type'] ? explode(' - ', $plo['type'])[0] : '...',
                ];
            }
        }
        
        // 5.2 Mapping Data
        $curriculumMapData = $getJson('curriculum_map_data', []);
        $totalCols = 29;
        if(is_array($curriculumMapData) && !empty($curriculumMapData[0]) && (is_object($curriculumMapData[0]) || is_array($curriculumMapData[0]))) {
             $mapObject = (array) $curriculumMapData[0];
             $mapArray = array_fill(0, $totalCols, '0');
             for($k=0; $k < $totalCols; $k++) {
                 if(isset($mapObject[strval($k)])) {
                     $mapArray[$k] = (string)$mapObject[strval($k)];
                 }
             }
             $docxData['s5_2_mapping_data'][] = $mapArray;
        } else {
             $docxData['s5_2_mapping_data'][] = array_fill(0, $totalCols, '0');
        }


        // 5.3 CLO-PLO Mapping
        $courseAccordData = $getJson('course_accord', []);
        if(is_array($cloLllDescriptions) && is_array($courseAccordData)) {
            foreach($cloLllDescriptions as $rowIndex => $cloInfo) {
                $cloInfo = (array) $cloInfo;
                $code = $cloInfo['code'] ?? null;

                $mapping = ($code && isset($courseAccordData[$code])) ? $courseAccordData[$code] : [];
                
                $row = [
                    'clo_id'   => $code ?? '?',
                    'clo_desc' => $cloInfo['description'] ?? '...',
                ];

                if (is_array($mapping)) {
                    foreach ($mapping as $ploIndex => $ploData) {
                        if (is_array($ploData)) {
                            $row['plo' . $ploIndex] = $ploData['check']
                                ? ($ploData['level'] ?: '✓')
                                : '';
                        } else {
                            $row['plo' . $ploIndex] = '';
                        }
                    }
                }
                
                $docxData['s5_3_clos_plo_mapping'][] = $row;
            }
        }

        // 6. Teaching Methods
        $teachingMethodsData = $getJson('teaching_methods', []);
        if(is_array($teachingMethodsData)) {
            uksort($teachingMethodsData, function ($a, $b) {
                preg_match('/(\d+)/', $a, $matchesA); preg_match('/(\d+)/', $b, $matchesB);
                $numA = isset($matchesA[1]) ? intval($matchesA[1]) : PHP_INT_MAX;
                $numB = isset($matchesB[1]) ? intval($matchesB[1]) : PHP_INT_MAX;
                return $numA <=> $numB;
            });
            foreach($teachingMethodsData as $cloKey => $data) {
                 if(!is_array($data) || count($data) < 2) continue;
                 $teaching = $data[0]['วิธีการสอน'] ?? [];
                 $assessment = $data[1]['การประเมินผล'] ?? [];
                 $docxData['s6_clos_teaching'][] = [
                     'clo' => $cloKey,
                     'teaching' => implode("\n", $teaching),
                     'assessment' => implode("\n", $assessment)
                 ];
            }
        }
        
        // 7. Lesson Plan
        $planData = $getJson('lesson_plan', []);
        if(is_array($planData)) {
             usort($planData, function($a, $b) { return ($a['week'] ?? 0) <=> ($b['week'] ?? 0); });
            foreach($planData as $weekData) {
                 if(empty($weekData) || !is_array($weekData)) continue;
                 $docxData['s7_lesson_plan'][] = [
                     'week' => $weekData['week'] ?? '?',
                     'topic' => $weekData['topic'] ?? '...',
                     'objective' => $weekData['objective'] ?? '...',
                     'activity' => $weekData['activity'] ?? '...',
                     'media' => $weekData['tool'] ?? '...',
                     'eval' => $weekData['assessment'] ?? '...',
                     'clo' => $weekData['clo'] ?? '...'
                 ];
            }
        }

        // 8.1 Assessment Strategies
        $assessmentData = $getJson('assessment_strategies', []);
         if(is_array($assessmentData)) {
             foreach($assessmentData as $stratData) {
                  if(empty($stratData) || !is_array($stratData)) continue;
                  $docxData['s8_1_assessment_strategies'][] = [
                      'method' => $stratData['method'] ?? '...',
                      'tool' => $stratData['tool'] ?? '...',
                      'ratio' => $stratData['percent'] ?? '...',
                      'clos' => $stratData['clo_desc'] ?? ($stratData['clo'] ?? '...')
                  ];
             }
         }
        
        // 8.2 Rubrics
        $rubricsData = $getJson('rubrics_data', []); // Use the 'rubrics_data' alias
         if(is_array($rubricsData)) {
             foreach($rubricsData as $rubric) {
                 if(empty($rubric) || !is_array($rubric)) continue;
                 $levelsArray = [];
                 if(isset($rubric['rows']) && (is_array($rubric['rows']) || is_object($rubric['rows']))) {
                     $rubricRows = (array) $rubric['rows'];
                     if(count($rubricRows) > 0) {
                         $isNumericArray = true; $isAssociativeObject = false;
                         foreach(array_keys($rubricRows) as $key) {
                             if(!is_numeric($key)) $isNumericArray = false;
                             if(in_array($key, ["5","4","3","2","1","0"])) $isAssociativeObject = true;
                         }
                         if ($isAssociativeObject) {
                             $levels = ["5", "4", "3", "2", "1", "0"];
                             foreach($levels as $level) {
                                 $levelsArray[] = ['level' => $level, 'desc' => $rubricRows[$level] ?? '...'];
                             }
                         } else if ($isNumericArray) {
                             $levels = [0, 1, 2, 3, 4, 5];
                             foreach($levels as $level) {
                                 $levelsArray[] = ['level' => $level, 'desc' => $rubricRows[$level] ?? '...'];
                             }
                             $levelsArray = array_reverse($levelsArray);
                         }
                     }
                 }
                 $docxData['s8_2_rubrics'][] = [
                     'title' => $rubric['title'] ?? '...',
                     'header' => $rubric['header'] ?? '...',
                     'levels' => $levelsArray
                 ];
             }
         }

        // 10. Grading Criteria
        $gradingData = $getJson('grading_criteria', []);
        $grades = ['A', 'Bp', 'B', 'Cp', 'C', 'Dp', 'D', 'F'];
        
        foreach($grades as $g) {
            $gradeKey = "grade_{$g}_level";
            $criteriaKey = "grade_{$g}_criteria";
            
            $docxData['s10_grading_criteria'][] = [
                'grade' => $gradingData[$gradeKey] ?? str_replace('p', '+', $g),
                'criteria' => $gradingData[$criteriaKey] ?? '...'
            ];
        }

        // DOCX GENERATION
        try {
            // สร้างอ็อบเจกต์ PhpWord
            $phpWord = new PhpWord();
            $phpWord->setDefaultFontName('TH Sarabun New');
            $phpWord->setDefaultFontSize(14);

            // ... (ตั้งค่า Section) ...
            $cmMargin = 2.5;
            $section = $phpWord->addSection([
                'paperSize'   => 'A4',
                'orientation' => 'portrait',
                'marginTop'   => Converter::cmToTwip($cmMargin),
                'marginBottom'=> Converter::cmToTwip($cmMargin),
                'marginLeft'  => Converter::cmToTwip($cmMargin),
                'marginRight' => Converter::cmToTwip($cmMargin),
                'spaceAfter'  => 0,
                'spaceBefore' => 0,
            ]);

            $boldFont = ['bold' => true];
            $underlineFont = ['underline' => 'single'];
            $leftAlign = ['align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0];
            $head = ['size' => 16];

            // สไตล์ Cell (มีเส้นขอบทุกด้าน) - อิงจากรูปภาพ
            $cellStyle = [
                'borderColor' => '000000',
            ];

            $mergedCellStyle = array_merge($cellStyle, ['gridSpan' => 2]);

            $paragraph_spacing = ['spaceAfter' => 200];
            
            // ... (ส่วนหัว, ข้อมูล, ปรัชญา) ...
            if (file_exists($docxData['logo_path'])) {
                $section->addImage($docxData['logo_path'], ['width' => 70, 'height' => 70, 'alignment' => Jc::CENTER]);
            }
            $section->addText($docxData['title'], array_merge($boldFont, ['size' => 16]), ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);
            $section->addText($docxData['subtitle'], array_merge($boldFont, ['size' => 16]), ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);
            $borderlessStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 0, 'spaceAfter'  => 0, 'spaceBefore' => 0];
            $infoTable = $section->addTable($borderlessStyle);
            $infoTable->addRow();
            $infoTable->addCell(1500)->addText('คณะ', $boldFont);
            $infoTable->addCell(2000)->addText(htmlspecialchars($docxData['faculty']), $underlineFont);
            $infoTable->addCell(1300)->addText('สาขาวิชา', $boldFont);
            $infoTable->addCell(3000, $mergedCellStyle)->addText(htmlspecialchars($docxData['major']), $underlineFont);
            $infoTable->addCell(1700)->addText('หลักสูตรปรับปรุง', $boldFont);
            $infoTable->addCell(1500)->addText(('พ.ศ. '.$docxData['curriculum_year']), $underlineFont);
            $infoTable->addRow();
            $infoTable->addCell(1500)->addText('วิทยาเขต', $boldFont);
            $infoTable->addCell(2000)->addText(htmlspecialchars($docxData['campus']), $underlineFont);
            $infoTable->addCell(2700, $mergedCellStyle)->addText('ภาคการศึกษา/ปีการศึกษา', $boldFont);
            $infoTable->addCell(1000)->addText(($docxData['term'] . ' / ' . $docxData['academic_year']), $underlineFont);
            $infoTable->addCell(1700)->addText('', null);
            $infoTable->addCell(1500)->addText('', null);
            $section->addTextBreak(1);
            $section->addText('ปรัชญามหาวิทยาลัยแม่โจ้', array_merge($boldFont, $head));
            $section->addText(htmlspecialchars($docxData['philosophy']), [$paragraph_spacing]);
            $section->addText('ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้', array_merge($boldFont, $head));
            $section->addText(htmlspecialchars($docxData['philosophy_education']), [$paragraph_spacing]);
            $section->addText('ปรัชญาหลักสูตร', array_merge($boldFont, $head));
            $section->addText(htmlspecialchars($docxData['philosophy_curriculum']), [$paragraph_spacing]);

            // ... (สไตล์ตาราง) ...
            $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'width' => '100%'];
            $phpWord->addTableStyle('MainTable', $tableStyle);
            $thStyle = ['bgColor' => 'DBEAFE'];
            $cellCenter = ['alignment' => Jc::CENTER];
            $vAlignCenter = ['valign' => 'center'];

            // === หมวดที่ 1 : ข้อมูลทั่วไป ===
            $section->addText('หมวดที่ 1 : ข้อมูลทั่วไป', array_merge($boldFont, $head));
            $table1 = $section->addTable('MainTable');
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('1. ชื่อวิชา', $boldFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_course_name']));
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('2. รหัสวิชา', $boldFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($docxData['s1_course_id']);
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('3. จำนวนหน่วยกิต', $boldFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_credits']));
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('4. หลักสูตร', $boldFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_curriculum_name']));
            $typesText = '';
            $typesText .= ($docxData['course_types']['specific'] ? '[✓]' : '[ ]') . ' วิชาเฉพาะ ';
            $typesText .= ($docxData['course_types']['core'] ? '[✓]' : '[ ]') . ' กลุ่มวิชาแกน ';
            $typesText .= ($docxData['course_types']['major_required'] ? '[✓]' : '[ ]') . ' เอกบังคับ ';
            $typesText .= ($docxData['course_types']['major_elective'] ? '[✓]' : '[ ]') . ' เอกเลือก ';
            $typesText .= ($docxData['course_types']['free_elective'] ? '[✓]' : '[ ]') . ' วิชาเลือกเสรี';
            
            $table1->addRow(); 
            $table1->addCell(2500, $thStyle)->addText('5. ประเภทของรายวิชา', $boldFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($typesText);
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('6. วิชาบังคับก่อน', $boldFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_prerequisites']));
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('7. ผู้สอน', $boldFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_instructors']));
            $revisionText = 'ภาคการศึกษาที่ ';
            $revisionText .= ($docxData['s1_revision_term'] == '1' ? '[✓]' : '[ ]') . ' 1 ';
            $revisionText .= ($docxData['s1_revision_term'] == '2' ? '[✓]' : '[ ]') . ' 2 ';
            $revisionText .= ' ปีการศึกษา ' . $docxData['s1_revision_year'];
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('8. การแก้ไขล่าสุด', $boldFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($revisionText);
            $table1->addRow();
            $table1->addCell(10000, ['gridSpan' => 4, 'bgColor' => 'F3F4F6'])->addText('9. จำนวนชั่วโมงที่ใช้ภาคการศึกษา', $boldFont, $cellCenter);
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('ภาคทฤษฎี ' . $docxData['s1_hours']['theory'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('ภาคปฏิบัติ ' . $docxData['s1_hours']['practice'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('การศึกษาด้วยตนเอง ' . $docxData['s1_hours']['self_study'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('ทัศนศึกษา/ฝึกงาน ' . $docxData['s1_hours']['field_trip'] . ' ชั่วโมง', null, $cellCenter);

            
            // === หมวดที่ 2 : คำอธิบายรายวิชา ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 2 : คำอธิบายรายวิชาและผลลัพธ์ระดับรายวิชา (CLOs)', array_merge($boldFont, $head, $thStyle));
            $table2 = $section->addTable('MainTable');
            $table2->addRow();
            $cell2_1 = $table2->addCell(10000);
            $cell2_1->addText('2.1 คำอธิบายรายวิชา', $boldFont);
            $cell2_1->addText(htmlspecialchars($docxData['description']));
            
            $table2->addRow();
            $cell2_2 = $table2->addCell(10000);
            $cell2_2->addText('2.2 ผลลัพธ์การเรียนรู้ระดับรายวิชา (Course learning Outcome) CLOs', $boldFont);
            
            // Decode and loop through CLOs
            $closData = json_decode($docxData['ai_text'], true); // Decode the ai_text JSON
            
            if (is_array($closData) && json_last_error() === JSON_ERROR_NONE) {
                // [!!! FIX !!!] Sort keys numerically (CLO 1, CLO 2...)
                uksort($closData, function ($a, $b) {
                    preg_match('/(\d+)/', $a, $matchesA); preg_match('/(\d+)/', $b, $matchesB);
                    $numA = isset($matchesA[1]) ? intval($matchesA[1]) : PHP_INT_MAX;
                    $numB = isset($matchesB[1]) ? intval($matchesB[1]) : PHP_INT_MAX;
                    return $numA <=> $numB;
                });
                
                foreach ($closData as $cloKey => $cloDetails) {
                    $cell2_2->addText(htmlspecialchars($cloKey) . ':', ['bold' => true]); 
                    if (is_array($cloDetails)) {
                        foreach ($cloDetails as $key => $value) {
                             $cell2_2->addListItem(htmlspecialchars($key . ': ' . $value), 0, null, ['indent' => 360]);
                        }
                    }
                    $cell2_2->addTextBreak(0.5); // Add space between CLOs
                }
            } else {
                 $cell2_2->addText(htmlspecialchars($docxData['clos']));
            }

            // === หมวดที่ 3 : การปรับปรุง ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 3 : การปรับปรุงรายวิชาตามข้อเสนอแนะจาก มคอ.5', array_merge($boldFont, $head, $thStyle));
            $table3 = $section->addTable('MainTable');
            $table3->addRow();
            $table3->addCell(5000, $thStyle)->addText('ข้อเสนอแนะ', $boldFont);
            $table3->addCell(5000, $thStyle)->addText('การปรับปรุง', $boldFont);
            $table3->addRow();
            $table3->addCell(5000)->addText(htmlspecialchars($docxData['feedback']));
            $table3->addCell(5000)->addText(htmlspecialchars($docxData['improvement']));

            // === หมวดที่ 4 : ข้อตกลงร่วมกัน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 4: ข้อตกลงร่วมกันระหว่างผู้สอนและผู้เรียน', array_merge($boldFont, $head, $thStyle));
            $section->addText(htmlspecialchars($docxData['agreement']));

            // === หมวดที่ 5 : ความสอดคล้อง ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 5: ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับหลักสูตร (PLOs) กับ ผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) และผลทักษะการเรียนรู้ตลอดชีวิต (LLLs)', array_merge($boldFont, $head, $thStyle));
            
            // 5.1 PLOs
            $section->addText('5.1 ผลลัพธ์การเรียนรู้ของหลักสูตร', array_merge($boldFont));
            $table5_1 = $section->addTable('MainTable');
            $table5_1->addRow();
            $table5_1->addCell(1000, $thStyle)->addText('PLOs', $boldFont, $cellCenter);
            $table5_1->addCell(4500, $thStyle)->addText('Outcome Statement', $boldFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Specific LO', $boldFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Generic LO', $boldFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Level', $boldFont, $cellCenter);
            $table5_1->addCell(1500, $thStyle)->addText('Type', $boldFont, $cellCenter);
            
            foreach ($docxData['s5_1_plos'] as $plo) {
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
            $section->addText('5.2 มาตรฐานผลการเรียนรู้รายวิชา (Curriculum Mapping)', array_merge($boldFont));
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
            foreach ($docxData['s5_2_mapping_data'] as $mapRow) {
                $table5_2->addRow();
                $table5_2->addCell(2000)->addText($docxData['s1_course_id'] . "\n" . $docxData['s1_course_name']);
                foreach ($mapRow as $state) {
                    $table5_2->addCell(300)->addText($mapSymbols[$state] ?? '', $boldFont, $cellCenter);
                }
            }

            // 5.3 CLO-PLO Mapping
            $section->addTextBreak(1);
            $section->addText('5.3 ความสอดคล้องของรายวิชากับ PLOs, CLOs และ LLLs', array_merge($boldFont));
            $table5_3 = $section->addTable('MainTable');
            $table5_3->addRow();
            $table5_3->addCell(1000, $thStyle)->addText('รหัส', $boldFont, $cellCenter);
            $table5_3->addCell(5000, $thStyle)->addText('คำอธิบาย CLOs/LLLs', $boldFont, $cellCenter);
            foreach ($docxData['s5_1_plos'] as $plo) { 
                $table5_3->addCell(1000, $thStyle)->addText('PLO' . $plo['id'] . ' (' . $plo['level'] . ')', $boldFont, $cellCenter);
            }
            
            foreach ($docxData['s5_3_clos_plo_mapping'] as $map) {
                $table5_3->addRow();
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['clo_id'], null, $cellCenter);
                $table5_3->addCell(5000)->addText(htmlspecialchars($map['clo_desc']));
                // แสดงค่า PLO mapping ตามจำนวนจริง
                if (!empty($docxData['s5_1_plos'])) {
                    foreach ($docxData['s5_1_plos'] as $plo) {
                        $key = 'plo' . ($plo['id'] ?? '');
                        $table5_3->addCell(1000, $vAlignCenter)
                                ->addText($map[$key] ?? '', null, $cellCenter);
                    }
                }
            }

            // === หมวดที่ 6 : CLOs, วิธีการสอน, การประเมิน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 6 : ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) วิธีการสอน และการประเมินผล', array_merge($boldFont, $head, $thStyle));
            $table6 = $section->addTable('MainTable');
            $table6->addRow();
            $table6->addCell(2000, $thStyle)->addText('CLO#', $boldFont);
            $table6->addCell(4000, $thStyle)->addText('วิธีการสอน (Active Learning)', $boldFont);
            $table6->addCell(4000, $thStyle)->addText('การประเมินผล', $boldFont);
            
            foreach ($docxData['s6_clos_teaching'] as $row) {
                $table6->addRow();
                $table6->addCell(2000)->addText(htmlspecialchars($row['clo']));
                $cellTeach = $table6->addCell(4000);
                $teachItems = explode("\n", $row['teaching']);
                foreach($teachItems as $item) $cellTeach->addText(htmlspecialchars($item));
                
                $cellAssess = $table6->addCell(4000);
                 $assessItems = explode("\n", $row['assessment']);
                foreach($assessItems as $item) $cellAssess->addText(htmlspecialchars($item));
            }
            
            // === หมวดที่ 7 : แผนการสอน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 7: แผนการสอน', array_merge($boldFont, $head, $thStyle));
            $section->addText('7.1 แผนการสอน', array_merge($boldFont));
            $table7 = $section->addTable('MainTable');
            $table7->addRow();
            $table7->addCell(800, $thStyle)->addText('สัปดาห์ที่', $boldFont, $cellCenter);
            $table7->addCell(1500, $thStyle)->addText('หัวข้อ', $boldFont, $cellCenter);
            $table7->addCell(2000, $thStyle)->addText('วัตถุประสงค์', $boldFont, $cellCenter);
            $table7->addCell(2500, $thStyle)->addText('กิจกรรม', $boldFont, $cellCenter);
            $table7->addCell(1500, $thStyle)->addText('สื่อ/เครื่องมือ', $boldFont, $cellCenter);
            $table7->addCell(1000, $thStyle)->addText('การประเมินผล', $boldFont, $cellCenter);
            $table7->addCell(700, $thStyle)->addText('CLO', $boldFont, $cellCenter);
            
            foreach ($docxData['s7_lesson_plan'] as $plan) {
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
            $section->addText('หมวดที่ 8 : การประเมินการบรรลุผลลัพธ์การเรียนรู้รายวิชา (CLOs)', array_merge($boldFont, $head, $thStyle));
            // 8.1 กลยุทธ์
            $section->addText('8.1 กลยุทธ์การประเมิน', array_merge($boldFont));
            $table8_1 = $section->addTable('MainTable');
            $table8_1->addRow();
            $table8_1->addCell(2500, $thStyle)->addText('วิธีการประเมิน', $boldFont, $cellCenter);
            $table8_1->addCell(3500, $thStyle)->addText('เครื่องมือ / รายละเอียด', $boldFont, $cellCenter);
            $table8_1->addCell(1500, $thStyle)->addText('สัดส่วน (%)', $boldFont, $cellCenter);
            $table8_1->addCell(2500, $thStyle)->addText('ความสอดคล้องกับ CLOs', $boldFont, $cellCenter);
            
            foreach ($docxData['s8_1_assessment_strategies'] as $strategy) {
                $table8_1->addRow();
                $table8_1->addCell(2500)->addText(htmlspecialchars($strategy['method']));
                $table8_1->addCell(3500)->addText(htmlspecialchars($strategy['tool']));
                $table8_1->addCell(1500, $vAlignCenter)->addText($strategy['ratio'], null, $cellCenter);
                $table8_1->addCell(2500, $vAlignCenter)->addText($strategy['clos'], null, $cellCenter);
            }
            
            // 8.2 Rubrics
            $section->addTextBreak(1);
            $section->addText('8.2 วิธีการประเมิน แบบรูบริค (Rubric) หรือ อื่นๆ (ถ้ามี)', array_merge($boldFont));
            
            foreach ($docxData['s8_2_rubrics'] as $rubric) {
                $section->addText(htmlspecialchars($rubric['title']), array_merge($boldFont));
                $tableRubric = $section->addTable('MainTable');
                $tableRubric->addRow();
                $tableRubric->addCell(1500, $thStyle)->addText('ระดับ', $boldFont, $cellCenter);
                $tableRubric->addCell(8500, $thStyle)->addText(htmlspecialchars($rubric['header']), $boldFont, $cellCenter);
                
                foreach ($rubric['levels'] as $level) {
                    $tableRubric->addRow();
                    $tableRubric->addCell(1500, $vAlignCenter)->addText($level['level'], null, $cellCenter);
                    $tableRubric->addCell(8500)->addText(htmlspecialchars($level['desc']));
                }
                $section->addTextBreak(0.5);
            }

            // === หมวดที่ 9 : สื่อการเรียนรู้ ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 9: สื่อการเรียนรู้และงานวิจัย', array_merge($boldFont, $head, $thStyle));
            
            $section->addText('9.1 สื่อการเรียนรู้และสิ่งสนับสนุนการเรียนรู้', array_merge($boldFont));
            if (!empty($docxData['s9_references']) && is_array($docxData['s9_references'])) {
                foreach ($docxData['s9_references'] as $item) {
                    $section->addListItem(htmlspecialchars($item), 0);
                }
            } else {
                 $section->addListItem(htmlspecialchars($get('research', '...')), 0);
            }
            
            $section->addText('9.2 งานวิจัยที่นำมาสอนในรายวิชา', array_merge($boldFont));
            $section->addText(htmlspecialchars($docxData['s9_research']));
            
            $section->addText('9.3 การบริการวิชาการ', array_merge($boldFont));
            $section->addText(htmlspecialchars($docxData['s9_academic_service']));

            $section->addText('9.4 งานทำนุบำรุงศิลปวัฒนธรรม', array_merge($boldFont));
            $section->addText(htmlspecialchars($docxData['s9_culture']));
            
            // === หมวดที่ 10 : เกณฑ์การประเมิน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 10 : เกณฑ์การประเมิน', array_merge($boldFont, $head, $thStyle));
            $table10 = $section->addTable('MainTable');
            $table10->addRow();
            $table10->addCell(3000, $thStyle)->addText('ระดับผลการศึกษา', $boldFont, $cellCenter);
            $table10->addCell(5000, $thStyle)->addText('เกณฑ์การประเมินผล', $boldFont, $cellCenter);
            
            if (!empty($docxData['s10_grading_criteria'])) {
                foreach ($docxData['s10_grading_criteria'] as $grade) {
                    $table10->addRow();
                    $table10->addCell(5000, $vAlignCenter)->addText($grade['grade'], null, $cellCenter);
                    $table10->addCell(5000, $vAlignCenter)->addText($grade['criteria'], null, $cellCenter);
                }
            } else {
                 // Add default empty rows if no data
                 $defaultGrades = ['A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F'];
                 foreach ($defaultGrades as $g) {
                      $table10->addRow();
                      $table10->addCell(5000, $vAlignCenter)->addText($g, null, $cellCenter);
                      $table10->addCell(5000, $vAlignCenter)->addText('...', null, $cellCenter);
                 }
            }


            // === หมวดที่ 11 : ขั้นตอนการแก้ไขคะแนน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 11 : ขั้นตอนการแก้ไขคะแนน', array_merge($boldFont, $head, $thStyle));
            $section->addText(htmlspecialchars($docxData['s11_grade_correction']));


            // 5. สิ้นสุดการสร้างเอกสาร
            
            // 6. บันทึกไฟล์และส่งให้ดาวน์โหลด
            $fileName = $docxData['fileName'] . '.docx';
            
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error("Error exporting DOCX: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response('เกิดข้อผิดพลาดในการสร้างเอกสาร: ' . $e->getMessage(), 500);
        }
    }
}
