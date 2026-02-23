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
        // ดึงข้อมูลพื้นฐานของรายวิชา (แยกเป็นฟังก์ชันเพื่อใช้ซ้ำ)
        $context = $this->getBaseContext($request);
        
        // ดึงข้อมูลและรวมข้อมูลทั้งหมดจากทุกตาราง
        $data = $this->getAggregatedData($context);

        // ส่งข้อมูลไปที่ View
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
            ->leftJoin('course_types', 'course_types.CC_id_ref', '=', 'curriculum_courses.id')
            ->where('curriculum_courses.id', $CC_id)
            ->first();
        $evaluations = DB::table('course_evaluations')->where('courseyear_id_ref', $cyId)->first();
        $learningMaps = DB::table('course_learning_maps')->where('courseyear_id_ref', $cyId)->first();
        $resources = DB::table('course_resources')->where('courseyear_id_ref', $cyId)->first();
        $plans = DB::table('plans')->where('courseyear_id_ref', $cyId)->first();
        $generates = DB::table('generates')->where('courseyear_id_ref', $cyId)->orderBy('created_at', 'desc')->first();

        $curriculumYear = DB::table('curriculum_courses')
            ->join('curriculum', 'curriculum_courses.curriculum_id', '=', 'curriculum.id')
            ->where('curriculum_courses.id', $CC_id)
            ->value('curriculum.curriculum_year');
        
        // ดึงข้อมูล check_LLL มาด้วย โดยใช้ curriculum_year_ref เป็นตัวกรอง
        $dbLlls = DB::table('curriculum_llls')
            ->where('curriculum_year_ref', $curriculumYear)
            ->orderBy('num_LLL')
            ->get(['num_LLL', 'name_LLL', 'check_LLL']);

        $lllDataArray = [];
        
        // ดึง course_accord ของเดิมมาก่อน (เพื่อไม่ให้ข้อมูล CLO ที่เซฟไว้หาย)
        $courseAccordArray = $this->safeJsonDecode($learningMaps->course_accord ?? '{}', true);
        $hasLllUpdates = false;

        foreach ($dbLlls as $lll) {
            $lllKey = 'LLL' . $lll->num_LLL;
            
            // เตรียม Array สำหรับส่งให้ JS โชว์ชื่อ LLL
            $lllDataArray[] = [
                'code' => $lllKey,
                'description' => $lll->name_LLL
            ];

            // แปลง check_LLL เป็น JSON Format ตามที่ต้องการ
            if (!isset($courseAccordArray[$lllKey])) {
                $courseAccordArray[$lllKey] = [];
            }

            if (!empty($lll->check_LLL)) {
                // หั่นด้วยลูกน้ำ เช่น "2,3,5,6" -> ["2", "3", "5", "6"]
                $checkedPlos = array_map('trim', explode(',', $lll->check_LLL));

                foreach ($checkedPlos as $ploNum) {
                    if (empty($ploNum)) continue;

                    // ถ้าใน course_accord ยังไม่มีค่านี้ ให้เพิ่มเข้าไปเป็น true
                    if (!isset($courseAccordArray[$lllKey][$ploNum])) {
                        $courseAccordArray[$lllKey][$ploNum] = [
                            'check' => true,
                            'level' => ''
                        ];
                        $hasLllUpdates = true; // ตั้งสถานะว่ามีการอัปเดตใหม่
                    }
                }
            }
        }

        // ถ้าพบว่ามีการดึง LLL ใหม่เข้ามา ให้ Auto-save กลับลงไปใน Database เลย
        if ($hasLllUpdates) {
            $updatedCourseAccordJson = json_encode($courseAccordArray, JSON_UNESCAPED_UNICODE);
            if ($learningMaps) {
                DB::table('course_learning_maps')
                    ->where('courseyear_id_ref', $cyId)
                    ->update(['course_accord' => $updatedCourseAccordJson, 'updated_at' => now()]);
            } else {
                DB::table('course_learning_maps')->insert([
                    'courseyear_id_ref' => $cyId,
                    'course_accord' => $updatedCourseAccordJson,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            // อัปเดตตัวแปรเพื่อให้ส่งไปหน้าเว็บได้ถูกต้อง
            $learningMaps = (object)['course_accord' => $updatedCourseAccordJson];
        }

        $dbPlos = DB::table('plos')->get(['learning_level']);
        $extractedLevels = [];
        
        foreach ($dbPlos as $plo) {
            $word = $plo->learning_level ?? '';
            if (empty($word)) continue;
            
            $lowerWord = strtolower(trim($word));
            $lvl = '';

            if (str_contains($lowerWord, 'remember')) $lvl = 'R';
            elseif (str_contains($lowerWord, 'understand')) $lvl = 'U';
            elseif (str_contains($lowerWord, 'apply')) $lvl = 'AP';
            elseif (str_contains($lowerWord, 'analyze')) $lvl = 'AN';
            elseif (str_contains($lowerWord, 'evaluate')) $lvl = 'E';
            elseif (str_contains($lowerWord, 'create')) $lvl = 'C';
            else $lvl = strtoupper(substr(trim($word), 0, 1)); // ถ้าไม่ตรงเงื่อนไขเลย ให้ดึงตัวอักษรแรกมาตัวใหญ่

            // เก็บเฉพาะค่าที่ไม่ซ้ำลงในกล่อง
            if ($lvl && !in_array($lvl, $extractedLevels)) {
                $extractedLevels[] = $lvl;
            }
        }

        $defaultGrading = [
            'grade_A_level' => 'A',   'grade_A_criteria' => '',
            'grade_Bp_level' => 'B+', 'grade_Bp_criteria' => '',
            'grade_B_level' => 'B',   'grade_B_criteria' => '',
            'grade_Cp_level' => 'C+', 'grade_Cp_criteria' => '',
            'grade_C_level' => 'C',   'grade_C_criteria' => '',
            'grade_Dp_level' => 'D+', 'grade_Dp_criteria' => '',
            'grade_D_level' => 'D',   'grade_D_criteria' => '',
            'grade_F_level' => 'F',   'grade_F_criteria' => '',
        ];

        // ดึงข้อมูลที่เคยบันทึกไว้ใน Database (ถ้ามี) มาแปลงเป็น Array
        $savedGrading = $this->safeJsonDecode($plans->grading_criteria ?? '{}', true);
        if (!is_array($savedGrading)) {
            $savedGrading = [];
        }

        //================================================ ดึงข้อมูลเก่า =======================================================================================
        // ดึงข้อมูล S4 agreement
        $agreementText = $evaluations->agreement ?? '';

        $user = Auth::user();
        $hasPreviousAgreement = DB::table('courseyears')
            ->join('course_evaluations', 'courseyears.id', '=', 'course_evaluations.courseyear_id_ref')
            ->where('courseyears.CC_id', $context->CC_id)
            // ->where('courseyears.user_id', $user->user_id)
            ->where(function ($query) use ($context) {
                // เงื่อนไข: ปีก่อนหน้าทั้งหมด OR (ปีเดียวกัน แต่เทอมน้อยกว่า)
                $query->where('courseyears.year', '<', $context->year)
                      ->orWhere(function ($q) use ($context) {
                          $q->where('courseyears.year', $context->year)
                            ->where('courseyears.term', '<', $context->term);
                      });
            })
            ->whereNotNull('course_evaluations.agreement') // ต้องมีข้อมูล
            ->where('course_evaluations.agreement', '!=', '')
            ->exists();

        // เช็คว่ามีข้อมูล agreement
        $user = Auth::user();
        $hasPreviousAgreement = DB::table('courseyears')
            ->join('course_evaluations', 'courseyears.id', '=', 'course_evaluations.courseyear_id_ref')
            ->where('courseyears.CC_id', $context->CC_id)
            // ->where('courseyears.user_id', $user->user_id)
            ->where(function ($query) use ($context) {
                $query->where('courseyears.year', '<', $context->year)
                      ->orWhere(function ($q) use ($context) {
                          $q->where('courseyears.year', $context->year)
                            ->where('courseyears.term', '<', $context->term);
                      });
            })
            ->whereNotNull('course_evaluations.agreement')
            ->where('course_evaluations.agreement', '!=', '')
            ->exists();

        // เช็คว่ามีข้อมูลตาราง 5.2
        $hasPrevCurriculumMap = DB::table('courseyears')
            ->join('course_evaluations', 'courseyears.id', '=', 'course_evaluations.courseyear_id_ref')
            ->where('courseyears.CC_id', $context->CC_id)
            // ->where('courseyears.user_id', Auth::user()->user_id)
            ->where(function ($query) use ($context) {
                $query->where('courseyears.year', '<', $context->year)
                      ->orWhere(function ($q) use ($context) {
                          $q->where('courseyears.year', $context->year)
                            ->where('courseyears.term', '<', $context->term);
                      });
            })
            ->whereNotNull('course_evaluations.curriculum_map_data')
            ->where('course_evaluations.curriculum_map_data', '!=', '[]')
            ->where('course_evaluations.curriculum_map_data', '!=', 'null')
            ->exists();

        // เช็คข้อมูลหมวด 7 (แผนการสอน)
        $hasPrevLessonPlan = DB::table('courseyears')
            ->join('plans', 'courseyears.id', '=', 'plans.courseyear_id_ref')
            ->where('courseyears.CC_id', $context->CC_id)
            // ->where('courseyears.user_id', Auth::user()->user_id)
            ->where(function ($query) use ($context) {
                $query->where('courseyears.year', '<', $context->year)
                      ->orWhere(function ($q) use ($context) {
                          $q->where('courseyears.year', $context->year)
                            ->where('courseyears.term', '<', $context->term);
                      });
            })
            ->whereNotNull('plans.lesson_plan')
            ->where('plans.lesson_plan', '!=', '[]')
            ->where('plans.lesson_plan', '!=', 'null')
            ->where('plans.lesson_plan', '!=', '')
            ->exists();

        // เช็คข้อมูลหมวด 8.1
        $hasPrevAssessment = DB::table('courseyears')
            ->join('plans', 'courseyears.id', '=', 'plans.courseyear_id_ref')
            ->where('courseyears.CC_id', $context->CC_id)
            // ->where('courseyears.user_id', Auth::user()->user_id)
            ->where(function ($query) use ($context) {
                $query->where('courseyears.year', '<', $context->year)
                      ->orWhere(function ($q) use ($context) {
                          $q->where('courseyears.year', $context->year)
                            ->where('courseyears.term', '<', $context->term);
                      });
            })
            ->whereNotNull('plans.assessment_strategies')
            ->where('plans.assessment_strategies', '!=', '[]')
            ->where('plans.assessment_strategies', '!=', 'null')
            ->where('plans.assessment_strategies', '!=', '')
            ->exists();

        //  เช็คข้อมูลหมวด 8.2 (Rubric)
        $hasPrevRubrics = DB::table('courseyears')
            ->join('plans', 'courseyears.id', '=', 'plans.courseyear_id_ref')
            ->where('courseyears.CC_id', $context->CC_id)
            // ->where('courseyears.user_id', Auth::user()->user_id)
            ->where(function ($query) use ($context) {
                $query->where('courseyears.year', '<', $context->year)
                      ->orWhere(function ($q) use ($context) {
                          $q->where('courseyears.year', $context->year)
                            ->where('courseyears.term', '<', $context->term);
                      });
            })
            ->whereNotNull('plans.rubrics')
            ->where('plans.rubrics', '!=', '[]')
            ->where('plans.rubrics', '!=', 'null')
            ->where('plans.rubrics', '!=', '')
            ->exists();

        // เช็คข้อมูลหมวด 10 (เกณฑ์การประเมินผลการเรียน)
        $hasPrevGrading = DB::table('courseyears')
            ->join('plans', 'courseyears.id', '=', 'plans.courseyear_id_ref')
            ->where('courseyears.CC_id', $context->CC_id)
            // ->where('courseyears.user_id', Auth::user()->user_id)
            ->where(function ($query) use ($context) {
                $query->where('courseyears.year', '<', $context->year)
                      ->orWhere(function ($q) use ($context) {
                          $q->where('courseyears.year', $context->year)
                            ->where('courseyears.term', '<', $context->term);
                      });
            })
            ->whereNotNull('plans.grading_criteria')
            ->where('plans.grading_criteria', '!=', '{}')
            ->where('plans.grading_criteria', '!=', '[]')
            ->where('plans.grading_criteria', '!=', 'null')
            ->where('plans.grading_criteria', '!=', '')
            ->exists();
        //=====================================================================================================================================================

        // ค่า Default
        $defaults = [
            'curriculum_name' => $curriculum->curriculum_name ?? '',
            'faculty'         => $curriculum->faculty ?? '',
            'major'           => $curriculum->major ?? '',
            'campus'          => $curriculum->campus ?? '',
            'credit'         => $curriculum->credit ?? '',
            'curriculum_year' => $context->year,
            'instructor_name'   => $context->instructor_name ?? $context->instructorName ?? '',

            // ข้อมูลปรัชญาต่างๆ
            'philosophy' => $curriculum->mju_philosophy ?? '',
            'philosophy_education' => $curriculum->education_philosophy ?? '',
            'philosophy_curriculum' => $curriculum->curriculum_philosophy ?? '',
            

            'prerequisites'     => $curriculum->prerequisites ?? '',
            'hours_theory'      => $curriculum->hours_theory ?? '',
            'hours_practice'    => $curriculum->hours_practice ?? '',
            'hours_self_study'  => $curriculum->hours_self_study ?? '',
            'hours_field_trip'  => $curriculum->hours_field_trip ?? '',

            'is_specific'       => $curriculum->specific ?? 0,
            'is_core'           => $curriculum->core ?? 0,
            'is_major_required' => $curriculum->major_required ?? 0,
            'is_major_elective' => $curriculum->major_elective ?? 0,
            'is_free_elective'  => $curriculum->free_elective ?? 0,

            // ข้อมูลตารางต่างๆ
            'feedback' => $evaluations->feedback ?? '',
            'improvement' => $evaluations->improvement ?? '',
            'agreement' => $agreementText,
            'curriculum_map_data' => $this->safeJsonDecode($evaluations->curriculum_map_data ?? '[]'),
            
            'course_accord' => $this->safeJsonDecode($learningMaps->course_accord ?? '[]'),
            
            'references_data' => $this->safeJsonDecode($resources->research ?? '[]'),
            'research_subjects' => $resources->research_subjects ?? '',
            'academic_service' => $resources->academic_service ?? '',
            'art_culture' => $resources->art_culture ?? '',

            'lll_data' => json_encode($lllDataArray, JSON_UNESCAPED_UNICODE),
            'level_options' => $extractedLevels,
            
            'plan_data' => $this->safeJsonDecode($plans->lesson_plan ?? '[]'),
            'teaching_methods' => $this->safeJsonDecode($plans->teaching_methods ?? '{}'),
            'assessment_data' => $this->safeJsonDecode($plans->assessment_strategies ?? '[]'),
            'rubrics_data' => $this->safeJsonDecode($plans->rubrics ?? '[]'),
            'grading_criteria' => array_merge($defaultGrading, $savedGrading),
            'grade_correction' => $plans->grade_correction ?? '',
            
            'ai_text' => $generates->ai_text ?? '',

            'has_previous_agreement' => $hasPreviousAgreement,
            'has_previous_curriculum_map' => $hasPrevCurriculumMap,
            'has_previous_lesson_plan' => $hasPrevLessonPlan,
            'has_previous_assessment_data' => $hasPrevAssessment,
            'has_previous_rubrics_data' => $hasPrevRubrics,
            'has_previous_grading_criteria' => $hasPrevGrading,
        ];

        // รวมค่า Context และ Defaults
        $data = (array) $context + $defaults;

        // สร้าง Outcome Statements
        $data['outcome_statement'] = $this->buildOutcomeStatements();

        return (object) $data;
    }

    private function routeAndSaveData($courseYearId, $field, $value)
    {
        $conditions = ['courseyear_id_ref' => $courseYearId];
        $targetTable = '';
        $dataToUpdate = [];

        // หมวด 1: ข้อมูลรายวิชา (บันทึกลงตาราง courses)
        $courseTypeFields = [
            'prerequisites', 
            'hours_theory', 'hours_practice', 'hours_self_study', 'hours_field_trip',
            'is_specific', 'is_core', 'is_major_required', 'is_major_elective', 'is_free_elective'
        ];

        if (in_array($field, $courseTypeFields)) {
            // ดึง CC_id เพื่อเอาไปเชื่อมกับ CC_id_ref
            $cy = DB::table('courseyears')->where('id', $courseYearId)->first();
            $cc_id = $cy->CC_id;
            
            $dbField = $field; // ตัวแปรสำหรับชื่อคอลัมน์ที่จะเซฟลง DB

            // ถ้าเป็น Checkbox ที่มีคำว่า is_ ให้แปลงเป็น 1/0 และตัดคำว่า is_ ออกให้ตรงกับ Database
            if (Str::startsWith($field, 'is_')) {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                $dbField = str_replace('is_', '', $field); // จาก 'is_specific' จะเหลือแค่ 'specific'
            }
            
            // ✅ บันทึกลงตาราง course_types โดยใช้ CC_id_ref ตามรูปที่คุณส่งมา
            $this->updateOrCreateWithDB('course_types', ['CC_id_ref' => $cc_id], [$dbField => $value]);
            
            return ['success' => true, 'message' => "Field '{$field}' updated in 'course_types'."];
        }

        // หมวด 1: ผู้สอน (บันทึกลงตาราง courseyears)
        if ($field === 'instructor_name') {
            DB::table('courseyears')->where('id', $courseYearId)->update([
                'instructor_name' => $value,
                'updated_at' => now()
            ]);
            return ['success' => true, 'message' => "Field '{$field}' updated in 'courseyears'."];
        }

        if ($field === 'credit') {
            $cy = DB::table('courseyears')->where('id', $courseYearId)->first();
            $cc = DB::table('curriculum_courses')->where('id', $cy->CC_id)->first();
            DB::table('courses')->where('id', $cc->course_code)->update([
                'credit' => $value,
                'updated_at' => now()
            ]);
            return ['success' => true, 'message' => "Field '{$field}' updated in 'courses'."];
        }

        // หมวด Course Evaluations
        if (in_array($field, ['feedback', 'improvement', 'agreement'])) {
            $targetTable = 'course_evaluations';
            $dataToUpdate = [$field => $value];
        } 
        else if (Str::startsWith($field, 'curriculum_map_')) {
            $targetTable = 'course_evaluations';
            
            // เซฟข้อมูลแบบรวดเดียวทั้งตาราง (ปุ่มดึงข้อมูลเก่า)
            if ($field === 'curriculum_map_data_all') {
                $dataToUpdate = [
                    'curriculum_map_data' => is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE)
                ];
            } 
            // เซฟข้อมูลแบบทีละจุด (คลิกเอง)
            else {
                $currentData = DB::table($targetTable)->where($conditions)->first();
                
                // แปลงข้อมูลเดิมมาเป็น Array
                $jsonData = $this->safeJsonDecode($currentData ? $currentData->curriculum_map_data : '[]');
                
                if (preg_match('/curriculum_map_r(\d+)_c(\d+)/', $field, $matches)) {
                    $colIndex = (int)$matches[2];
                    
                    if (empty($jsonData) || !isset($jsonData[0])) {
                        $jsonData[0] = [];
                    } else if (!is_array($jsonData[0])) {
                        $jsonData[0] = (array) $jsonData[0]; 
                    }

                    $jsonData[0][strval($colIndex)] = (int) $value; 
                }
                
                $dataToUpdate = ['curriculum_map_data' => json_encode($jsonData, JSON_UNESCAPED_UNICODE)];
            }
        }
        
        // หมวด Course Learning Maps (PLO/CLO Mappings)
        else if (Str::startsWith($field, 'plo_map_')) {
            $targetTable = 'course_learning_maps';
            $currentData = DB::table($targetTable)->where($conditions)->first();
            $jsonData = $this->safeJsonDecode($currentData ? $currentData->course_accord : '{}');

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

        // หมวด Course Resources
        else if (in_array($field, ['research_subjects', 'academic_service', 'art_culture'])) {
            $targetTable = 'course_resources';
            $dataToUpdate = [$field => $value];
        }
        else if ($field === 'section9_1_data') {
            $targetTable = 'course_resources';
            $dataToUpdate = ['research' => json_encode($this->prepareJsonValue($value), JSON_UNESCAPED_UNICODE)];
        }

        // หมวด Plans
        else if (in_array($field, ['grade_correction'])) {
            $targetTable = 'plans';
            $dataToUpdate = [$field => $value];
        }
        else if ($field === 'section6_data') {
            $targetTable = 'plans';
            $decodedValue = $this->prepareJsonValue($value);
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
            $jsonData = $this->safeJsonDecode($currentData ? $currentData->grading_criteria : '{}');
            $jsonData[$field] = $value;
            $dataToUpdate = ['grading_criteria' => json_encode($jsonData, JSON_UNESCAPED_UNICODE)];
        }

        // หมวด Generates (AI Text)
        else if ($field === 'ai_text') {
            $targetTable = 'generates';
            $dataToUpdate = ['ai_text' => $value];
        }

        // สำหรับบันทึกคำอธิบาย CLO ที่แก้ไขรายบรรทัด หมวด2.2
        else if (Str::startsWith($field, 'cloLll_desc_')) {
            $targetTable = 'generates';
            
            // ดึง Code ออกมา (เช่น CLO1) และจัด Format ให้มีช่องว่าง (เช่น CLO 1) เพื่อให้ตรงกับ JSON เดิม
            $rawCode = str_replace('cloLll_desc_', '', $field); 
            $cloKey = preg_replace('/([a-zA-Z]+)(\d+)/', '$1 $2', $rawCode); // "CLO1" -> "CLO 1"

            // ดึงข้อมูลเดิมจาก DB มาก่อน
            $currentData = DB::table($targetTable)->where($conditions)->first();
            $aiTextArray = $this->safeJsonDecode($currentData->ai_text ?? '{}');

            //อัปเดตเฉพาะเนื้อหาของ CLO นั้นๆ
            if (!isset($aiTextArray[$cloKey])) {
                $aiTextArray[$cloKey] = ['CLO' => $value];
            } else {
                $aiTextArray[$cloKey]['CLO'] = $value;
            }

            // เตรียมบันทึกกลับเป็น JSON String
            $dataToUpdate = ['ai_text' => json_encode($aiTextArray, JSON_UNESCAPED_UNICODE)];
        }

        else if ($field === 'grading_criteria_all') {
            $targetTable = 'plans';
            $dataToUpdate = ['grading_criteria' => is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE)];
        }

        // บันทึกลงฐานข้อมูล
        if (!empty($targetTable)) {
            $this->updateOrCreateWithDB($targetTable, $conditions, $dataToUpdate);
            return ['success' => true, 'message' => "Field '{$field}' updated in '{$targetTable}'."];
        }

        return ['success' => false, 'message' => "Unknown field '{$field}'."];
    }

    private function buildOutcomeStatements()
    {
        // ดึงข้อมูลทั้งหมดจากตาราง plos เรียงตามตัวเลข
        $dbPlos = DB::table('plos')->orderBy('plo')->get();
        $finalOutcomeStatement = [];

        // เช็คและแปลงคำสำหรับ Level (รองรับกรณีมีหลายคำ)
        $formatLevel = function($word) {
            if (empty($word)) return '';
            $lowerWord = strtolower(trim($word)); 
            
            $levels = [];
            $levels_full = [];

            if (str_contains($lowerWord, 'remember'))   { $levels[] = 'R'; $levels_full[] = 'Remember'; }
            if (str_contains($lowerWord, 'understand')) { $levels[] = 'U'; $levels_full[] = 'Understand'; }
            if (str_contains($lowerWord, 'apply'))      { $levels[] = 'AP'; $levels_full[] = 'Apply'; }
            if (str_contains($lowerWord, 'analyze'))    { $levels[] = 'AN'; $levels_full[] = 'Analyze'; }
            if (str_contains($lowerWord, 'evaluate'))   { $levels[] = 'E'; $levels_full[] = 'Evaluate'; }
            if (str_contains($lowerWord, 'create'))     { $levels[] = 'C'; $levels_full[] = 'Create'; }
            
            // ถ้าเจอตัั้งแต่ 1 คำขึ้นไป ให้นำมาต่อกันด้วยลูกน้ำ (, )
            if (!empty($levels)) return implode(', ', $levels);
            
            return ucfirst(trim($word)); 
        };

        // เช็คและแปลงคำสำหรับ Type (รองรับกรณีมีหลายคำ)
        $formatType = function($word) {
            if (empty($word)) return '';
            $lowerWord = strtolower(trim($word));
            
            $types = [];
            $types_full = [];

            if (str_contains($lowerWord, 'knowledge')) {
                $types[] = 'K'; $types_full[] = 'Knowledge';
            }
            if (str_contains($lowerWord, 'skill')) {
                $types[] = 'S'; $types_full[] = 'Skill';
            }
            if (str_contains($lowerWord, 'application') || str_contains($lowerWord, 'responsibility')) {
                $types[] = 'AR'; $types_full[] = 'Application and Responsibility';
            }
            
            // ถ้าเจอตัั้งแต่ 1 คำขึ้นไป ให้นำมาต่อกันด้วยลูกน้ำ (, )
            if (!empty($types)) return implode(', ', $types);
            
            return ucfirst(trim($word)); 
        };

        // จัดเตรียมข้อมูลสำหรับแสดงผลลงตาราง
        foreach ($dbPlos as $masterPlo) {
            $isSpecific = property_exists($masterPlo, 'specific_lo') ? $masterPlo->specific_lo : ($masterPlo->plo < 5 ? 1 : 0);

            $finalOutcomeStatement[$masterPlo->plo] = [
                'plo'      => $masterPlo->plo,
                'outcome'  => $masterPlo->description ?? '',
                'specific' => $isSpecific,
                'level'    => $formatLevel($masterPlo->learning_level ?? ''),
                'type'     => $formatType($masterPlo->domain ?? '')
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

    public function getPreviousAgreement(Request $request)
    {
        $user = Auth::user();
        $CC_id = $request->input('CC_id');
        $year = (int) $request->input('year');
        $term = (int) $request->input('term');

        // ค้นหาข้อมูลจากปีและเทอมก่อนหน้า
        $previousAgreement = DB::table('courseyears')
            ->join('course_evaluations', 'courseyears.id', '=', 'course_evaluations.courseyear_id_ref')
            ->where('courseyears.CC_id', $CC_id)
            // ->where('courseyears.user_id', $user->user_id) // ดึงเฉพาะของอาจารย์ท่านนี้
            ->where(function ($query) use ($year, $term) {
                $query->where('courseyears.year', '<', $year)
                      ->orWhere(function ($q) use ($year, $term) {
                          $q->where('courseyears.year', $year)
                            ->where('courseyears.term', '<', $term);
                      });
            })
            ->whereNotNull('course_evaluations.agreement')
            ->where('course_evaluations.agreement', '!=', '')
            ->orderBy('courseyears.year', 'desc')
            ->orderBy('courseyears.term', 'desc')
            ->value('course_evaluations.agreement');

        if ($previousAgreement) {
            return response()->json(['success' => true, 'data' => $previousAgreement]);
        }

        return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลจากเทอมก่อนหน้า']);
    }

    public function getPreviousCurriculumMap(Request $request)
    {
        $user = Auth::user();
        $CC_id = $request->input('CC_id');
        $year = (int) $request->input('year');
        $term = (int) $request->input('term');

        $previousMap = DB::table('courseyears')
            ->join('course_evaluations', 'courseyears.id', '=', 'course_evaluations.courseyear_id_ref')
            ->where('courseyears.CC_id', $CC_id)
            // ->where('courseyears.user_id', $user->user_id)
            ->where(function ($query) use ($year, $term) {
                $query->where('courseyears.year', '<', $year)
                      ->orWhere(function ($q) use ($year, $term) {
                          $q->where('courseyears.year', $year)
                            ->where('courseyears.term', '<', $term);
                      });
            })
            ->whereNotNull('course_evaluations.curriculum_map_data')
            ->where('course_evaluations.curriculum_map_data', '!=', '[]')
            ->where('course_evaluations.curriculum_map_data', '!=', 'null')
            ->orderBy('courseyears.year', 'desc')
            ->orderBy('courseyears.term', 'desc')
            ->value('course_evaluations.curriculum_map_data');

        if ($previousMap) {
            return response()->json(['success' => true, 'data' => json_decode($previousMap, true)]);
        }
        return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลตารางเก่า']);
    }

    public function getPreviousLessonPlan(Request $request)
    {
        $user = Auth::user();
        $CC_id = $request->input('CC_id');
        $year = (int) $request->input('year');
        $term = (int) $request->input('term');

        $previousPlan = DB::table('courseyears')
            ->join('plans', 'courseyears.id', '=', 'plans.courseyear_id_ref')
            ->where('courseyears.CC_id', $CC_id)
            // ->where('courseyears.user_id', $user->user_id)
            ->where(function ($query) use ($year, $term) {
                $query->where('courseyears.year', '<', $year)
                      ->orWhere(function ($q) use ($year, $term) {
                          $q->where('courseyears.year', $year)
                            ->where('courseyears.term', '<', $term);
                      });
            })
            ->whereNotNull('plans.lesson_plan')
            ->where('plans.lesson_plan', '!=', '[]')
            ->where('plans.lesson_plan', '!=', 'null')
            ->where('plans.lesson_plan', '!=', '')
            ->orderBy('courseyears.year', 'desc')
            ->orderBy('courseyears.term', 'desc')
            ->value('plans.lesson_plan');

        if ($previousPlan) {
            return response()->json(['success' => true, 'data' => json_decode($previousPlan, true)]);
        }
        return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลแผนการสอนเก่า']);
    }

    public function getPreviousAssessmentData(Request $request)
    {
        $user = Auth::user();
        $CC_id = $request->input('CC_id');
        $year = (int) $request->input('year');
        $term = (int) $request->input('term');

        $previousAssessment = DB::table('courseyears')
            ->join('plans', 'courseyears.id', '=', 'plans.courseyear_id_ref')
            ->where('courseyears.CC_id', $CC_id)
            // ->where('courseyears.user_id', $user->user_id)
            ->where(function ($query) use ($year, $term) {
                $query->where('courseyears.year', '<', $year)
                      ->orWhere(function ($q) use ($year, $term) {
                          $q->where('courseyears.year', $year)
                            ->where('courseyears.term', '<', $term);
                      });
            })
            ->whereNotNull('plans.assessment_strategies')
            ->where('plans.assessment_strategies', '!=', '[]')
            ->where('plans.assessment_strategies', '!=', 'null')
            ->where('plans.assessment_strategies', '!=', '')
            ->orderBy('courseyears.year', 'desc')
            ->orderBy('courseyears.term', 'desc')
            ->value('plans.assessment_strategies');

        if ($previousAssessment) {
            return response()->json(['success' => true, 'data' => json_decode($previousAssessment, true)]);
        }
        return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลตารางประเมินเก่า']);
    }

    public function getPreviousRubricsData(Request $request)
    {
        $user = Auth::user();
        $CC_id = $request->input('CC_id');
        $year = (int) $request->input('year');
        $term = (int) $request->input('term');

        $previousRubrics = DB::table('courseyears')
            ->join('plans', 'courseyears.id', '=', 'plans.courseyear_id_ref')
            ->where('courseyears.CC_id', $CC_id)
            // ->where('courseyears.user_id', $user->user_id)
            ->where(function ($query) use ($year, $term) {
                $query->where('courseyears.year', '<', $year)
                      ->orWhere(function ($q) use ($year, $term) {
                          $q->where('courseyears.year', $year)
                            ->where('courseyears.term', '<', $term);
                      });
            })
            ->whereNotNull('plans.rubrics')
            ->where('plans.rubrics', '!=', '[]')
            ->where('plans.rubrics', '!=', 'null')
            ->where('plans.rubrics', '!=', '')
            ->orderBy('courseyears.year', 'desc')
            ->orderBy('courseyears.term', 'desc')
            ->value('plans.rubrics');

        if ($previousRubrics) {
            return response()->json(['success' => true, 'data' => json_decode($previousRubrics, true)]);
        }
        return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลรูบริคเก่า']);
    }

    public function getPreviousGradingCriteria(Request $request)
    {
        $user = Auth::user();
        $CC_id = $request->input('CC_id');
        $year = (int) $request->input('year');
        $term = (int) $request->input('term');

        $previousGrading = DB::table('courseyears')
            ->join('plans', 'courseyears.id', '=', 'plans.courseyear_id_ref')
            ->where('courseyears.CC_id', $CC_id)
            // ->where('courseyears.user_id', $user->user_id)
            ->where(function ($query) use ($year, $term) {
                $query->where('courseyears.year', '<', $year)
                      ->orWhere(function ($q) use ($year, $term) {
                          $q->where('courseyears.year', $year)
                            ->where('courseyears.term', '<', $term);
                      });
            })
            ->whereNotNull('plans.grading_criteria')
            ->where('plans.grading_criteria', '!=', '{}')
            ->where('plans.grading_criteria', '!=', '[]')
            ->where('plans.grading_criteria', '!=', 'null')
            ->where('plans.grading_criteria', '!=', '')
            ->orderBy('courseyears.year', 'desc')
            ->orderBy('courseyears.term', 'desc')
            ->value('plans.grading_criteria');

        if ($previousGrading) {
            return response()->json(['success' => true, 'data' => json_decode($previousGrading, true)]);
        }
        return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลเกณฑ์การประเมินเก่า']);
    }
}
