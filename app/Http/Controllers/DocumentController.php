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
        // 1. ดึงข้อมูลพื้นฐานของรายวิชา (แยกเป็นฟังก์ชันเพื่อใช้ซ้ำ)
        $context = $this->getBaseContext($request);
        
        // 2. ดึงข้อมูลและรวมข้อมูลทั้งหมดจากทุกตาราง
        $data = $this->getAggregatedData($context);

        // 3. ส่งข้อมูลไปที่ View
        return view('editdoc', compact('data'));
    }

    public function savedataedit(Request $request)
    {
        $validated = $request->validate([
            'CC_id' => 'required',
            'year'  => 'required',
            'term'  => 'required',
            'TQF'   => 'required',
            'field' => 'required|string',
            'value' => 'nullable',
        ]);

        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        try {
            // หา CourseYear ID
            $courseYear = DB::table('courseyears')
                ->where('CC_id', $validated['CC_id'])
                ->where('user_id', $user->user_id)
                ->where('year', $validated['year'])
                ->where('term', $validated['term'])
                ->where('TQF', $validated['TQF'])
                ->first();

            if (!$courseYear) {
                return response()->json(['message' => 'TQF Document (CourseYear) not found.'], 404);
            }

            // เรียกใช้ Data Router เพื่อเลือกตารางและจัดการข้อมูล
            $result = $this->routeAndSaveData($courseYear->id, $validated['field'], $validated['value']);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error("Error in savedataedit: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update TQF.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    private function getBaseContext(Request $request)
    {
        $user = Auth::user();
        $CC_id = $request->query('CC_id') ?? $request->input('CC_id');
        $year = (int) ($request->query('year') ?? $request->input('year'));
        $term = $request->query('term') ?? $request->input('term');
        $TQF = $request->query('TQF') ?? $request->input('TQF');

        if (!$CC_id || !$year || $term === null || $TQF === null || !$user) {
            abort(400, 'Missing required parameters or not authenticated');
        }

        // ค้นหาตามโครงสร้างใหม่
        $context = DB::table('courseyears as cy')
            ->leftJoin('users as u', 'cy.user_id', '=', 'u.user_id')
            ->leftJoin('curriculum_courses as cc', 'cy.CC_id', '=', 'cc.id')
            ->leftJoin('courses as c', 'cc.course_code', '=', 'c.id') 
            ->select(
                'cy.id as courseYearId',
                'u.name as instructorName',
                'c.course_code',
                'c.course_name_th',
                'c.course_name_en',
                'c.course_detail_th',
                'c.course_detail_en',
                'cy.year',
                'cy.term',
                'cy.CC_id'
            )
            ->where('cy.user_id', $user->user_id)
            ->where('cy.CC_id', $CC_id)
            ->where('cy.year', $year)
            ->where('cy.term', $term)
            ->where('cy.TQF', $TQF)
            ->first();

        if (!$context) abort(404, 'TQF Document (CourseYear) not found.');

        return $context;
    }

    private function getAggregatedData($context)
    {
        $cyId = $context->courseYearId;
        $CC_id = $context->CC_id;

        // ดึงข้อมูลจากตารางใหม่
        $curriculum = DB::table('curriculum_courses')
            ->join('curriculum', 'curriculum_courses.curriculum_id', '=', 'curriculum.id')
            ->join('courses', 'curriculum_courses.course_code', '=', 'courses.id')
            ->leftJoin('philosophy', 'philosophy.curriculum_year_ref', '=', 'curriculum.curriculum_year')
            ->where('curriculum_courses.id', $CC_id)
            ->first();
        $evaluations = DB::table('course_evaluations')->where('courseyear_id_ref', $cyId)->first();
        $learningMaps = DB::table('course_learning_maps')->where('courseyear_id_ref', $cyId)->first();
        $resources = DB::table('course_resources')->where('courseyear_id_ref', $cyId)->first();
        $plans = DB::table('plans')->where('courseyear_id_ref', $cyId)->first();
        $generates = DB::table('generates')->where('courseyear_id_ref', $cyId)->orderBy('created_at', 'desc')->first();

        // ค่า Default
        $defaults = [
            'curriculum_name' => $curriculum->curriculum_name ?? '',
            'faculty'         => $curriculum->faculty ?? '',
            'major'           => $curriculum->major ?? '',
            'campus'          => $curriculum->campus ?? '',
            'credits'         => $curriculum->credit ?? '',
            'curriculum_year' => $context->year,

            // ข้อมูลปรัชญาต่างๆ
            'philosophy' => $curriculum->mju_philosophy ?? '',
            'philosophy_education' => $curriculum->education_philosophy ?? '',
            'philosophy_curriculum' => $curriculum->curriculum_philosophy ?? '',
            
            // ข้อมูลตารางต่างๆ
            'feedback' => $evaluations->feedback ?? '',
            'improvement' => $evaluations->improvement ?? '',
            'agreement' => $evaluations->agreement ?? '',
            'curriculum_map_data' => $this->safeJsonDecode($evaluations->curriculum_map_data ?? '[]'),
            
            'course_accord' => $this->safeJsonDecode($learningMaps->course_accord ?? '[]'),
            
            'references_data' => $this->safeJsonDecode($resources->research ?? '[]'),
            'research_subjects' => $resources->research_subjects ?? '',
            'academic_service' => $resources->academic_service ?? '',
            'art_culture' => $resources->art_culture ?? '',
            
            'plan_data' => $this->safeJsonDecode($plans->lesson_plan ?? '[]'),
            'teaching_methods' => $this->safeJsonDecode($plans->teaching_methods ?? '{}'),
            'assessment_data' => $this->safeJsonDecode($plans->assessment_strategies ?? '[]'),
            'rubrics_data' => $this->safeJsonDecode($plans->rubrics ?? '[]'),
            'grading_criteria' => $this->safeJsonDecode($plans->grading_criteria ?? '{}'),
            'grade_correction' => $plans->grade_correction ?? '',
            
            'ai_text' => $generates->ai_text ?? '',
        ];

        // รวมค่า Context และ Defaults
        $data = (array) $context + $defaults;

        // สร้าง Outcome Statements
        $data['outcome_statement'] = $this->buildOutcomeStatements($data['course_accord']);

        return (object) $data;
    }

    private function routeAndSaveData($courseYearId, $field, $value)
    {
        $conditions = ['courseyear_id_ref' => $courseYearId];
        $targetTable = '';
        $dataToUpdate = [];

        // 1. หมวด Course Evaluations
        if (in_array($field, ['feedback', 'improvement', 'agreement'])) {
            $targetTable = 'course_evaluations';
            $dataToUpdate = [$field => $value];
        } 
        else if (Str::startsWith($field, 'curriculum_map_')) {
            $targetTable = 'course_evaluations';
            // Logic อัปเดต JSON ของ curriculum_map_data
            $currentData = DB::table($targetTable)->where($conditions)->first();
            $jsonData = $this->safeJsonDecode($currentData->curriculum_map_data ?? '[]');
            
            if (preg_match('/curriculum_map_r(\d+)_c(\d+)/', $field, $matches)) {
                $colIndex = (int)$matches[2];
                if (empty($jsonData)) $jsonData = [(object)[]];
                $jsonData[0]->{strval($colIndex)} = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            $dataToUpdate = ['curriculum_map_data' => json_encode($jsonData, JSON_UNESCAPED_UNICODE)];
        }
        
        // 2. หมวด Course Learning Maps (PLO/CLO Mappings)
        else if (Str::startsWith($field, 'plo_map_')) {
            $targetTable = 'course_learning_maps';
            $currentData = DB::table($targetTable)->where($conditions)->first();
            $jsonData = $this->safeJsonDecode($currentData->course_accord ?? '{}');

            if (preg_match('/plo_map_([a-zA-Z0-9]+)_c(\d+)_(\w+)/', $field, $matches)) {
                $cloCode = $matches[1]; $colIndex = (int)$matches[2]; $attribute = $matches[3];
                $ploDbIndex = $colIndex + 1;
                
                $processedValue = in_array($attribute, ['check']) ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : $value;

                if (!isset($jsonData[$cloCode])) $jsonData[$cloCode] = [];
                if (!isset($jsonData[$cloCode][$ploDbIndex])) $jsonData[$cloCode][$ploDbIndex] = ['check' => false, 'level' => ''];
                $jsonData[$cloCode][$ploDbIndex][$attribute] = $processedValue;
            }
            $dataToUpdate = ['course_accord' => json_encode($jsonData, JSON_UNESCAPED_UNICODE)];
        }

        // 3. หมวด Course Resources
        else if (in_array($field, ['research_subjects', 'academic_service', 'art_culture'])) {
            $targetTable = 'course_resources';
            $dataToUpdate = [$field => $value];
        }
        else if ($field === 'section9_1_data') {
            $targetTable = 'course_resources';
            $dataToUpdate = ['research' => json_encode($this->prepareJsonValue($value), JSON_UNESCAPED_UNICODE)];
        }

        // 4. หมวด Plans
        else if (in_array($field, ['grade_correction'])) {
            $targetTable = 'plans';
            $dataToUpdate = [$field => $value];
        }
        else if ($field === 'section6_data') {
            $targetTable = 'plans';
            $decodedValue = $this->prepareJsonValue($value);
            // ... (Logic กรองข้อมูลว่างๆ แบบเดิมของคุณ) ...
            $dataToUpdate = ['teaching_methods' => json_encode($decodedValue, JSON_UNESCAPED_UNICODE)];
        }
        else if ($field === 'section7_data') {
            $targetTable = 'plans';
            $dataToUpdate = ['lesson_plan' => json_encode($this->prepareJsonValue($value), JSON_UNESCAPED_UNICODE)];
        }
        else if ($field === 'section8_1_data') {
            $targetTable = 'plans';
            $dataToUpdate = ['assessment_strategies' => json_encode($this->prepareJsonValue($value), JSON_UNESCAPED_UNICODE)];
        }
        else if ($field === 'section8_2_data') {
            $targetTable = 'plans';
            $dataToUpdate = ['rubrics' => json_encode($this->prepareJsonValue($value), JSON_UNESCAPED_UNICODE)];
        }
        else if (Str::startsWith($field, 'grade_')) {
            $targetTable = 'plans';
            $currentData = DB::table($targetTable)->where($conditions)->first();
            $jsonData = $this->safeJsonDecode($currentData->grading_criteria ?? '{}');
            $jsonData[$field] = $value;
            $dataToUpdate = ['grading_criteria' => json_encode($jsonData, JSON_UNESCAPED_UNICODE)];
        }

        // 5. หมวด Generates (AI Text)
        else if ($field === 'ai_text') {
            $targetTable = 'generates';
            $dataToUpdate = ['ai_text' => $value];
        }

        // บันทึกลงฐานข้อมูล
        if (!empty($targetTable)) {
            $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
            return ['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'."];
        }

        return ['success' => false, 'message' => "Unknown field '{$field}'."];
    }

    private function buildOutcomeStatements($savedOutcomeData)
    {
        $dbPlos = DB::table('plos')->get()->keyBy('plo');
        $finalOutcomeStatement = [];
        $loopLimit = max(5, $dbPlos->keys()->max() ?? 0); // บังคับขั้นต่ำ 5 ข้อ

        for ($i = 1; $i <= $loopLimit; $i++) {
            $masterPlo = $dbPlos->get($i);
            $savedData = $savedOutcomeData[$i] ?? []; 
            
            // Logic: 1-4 เป็น Specific, 5 เป็น Generic
            $isSpecific = $i < 5;
            $isGeneric = $i == 5;

            $finalOutcomeStatement[$i] = [
                'outcome' => $savedData['outcome'] ?? ($masterPlo->description ?? ''),
                'specific' => $savedData['specific'] ?? $isSpecific,
                'generic' => $savedData['generic'] ?? $isGeneric,
                'level' => $savedData['level'] ?? 'U',
                'type' => $savedData['type'] ?? 'K'
            ];
        }
        return $finalOutcomeStatement;
    }

    private function safeJsonDecode($jsonString, $assoc = true)
    {
        if (empty($jsonString)) return $assoc ? [] : new \stdClass();
        $decoded = json_decode($jsonString, $assoc);
        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : ($assoc ? [] : new \stdClass());
    }

    private function prepareJsonValue($value)
    {
         if (is_string($value)) {
            try {
                if(!empty(trim($value))) {
                     $decoded = json_decode($value, true); 
                     if (json_last_error() === JSON_ERROR_NONE) return $decoded;
                }
            } catch (\Exception $e) { }
        }
        if (is_array($value) || is_object($value)) {
            return json_decode(json_encode($value), true);
        }
        return [];
    }

    private function updateOrCreateWithDB($tableName, $conditions, $dataToUpdate, $defaults = [])
    {
        $exists = DB::table($tableName)->where($conditions)->exists();
        if ($exists) {
            if (!empty($dataToUpdate)) {
                $dataToUpdate['updated_at'] = now();
                DB::table($tableName)->where($conditions)->update($dataToUpdate);
            }
        } else {
            $dataToInsert = array_merge($defaults, $conditions, $dataToUpdate);
            $dataToInsert['created_at'] = now();
            $dataToInsert['updated_at'] = now();
            DB::table($tableName)->insert($dataToInsert);
        }
    }

}
