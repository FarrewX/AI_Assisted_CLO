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
            else $lvl = strtoupper(substr(trim($word), 0, 1));

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
            'curriculum_year' => $curriculum->curriculum_year ?? '',
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

    public function exportdocx(Request $request)
    {
        try {
            $context = $this->getBaseContext($request);
            $dataObj = $this->getAggregatedData($context);
        } catch (\Exception $e) {
            abort(404, 'ไม่สามารถโหลดข้อมูลรายวิชาได้: ' . $e->getMessage());
        }

        $data = (array) $dataObj;
        $get = function($key, $default = '') use ($data) {
            return isset($data[$key]) && $data[$key] !== '' && $data[$key] !== null ? $data[$key] : $default;
        };
        $getArray = function($key, $default = []) use ($data) {
            return (!empty($data[$key]) && is_array($data[$key])) ? $data[$key] : $default;
        };

        // ดึงคำอธิบาย CLO/LLL พ่วงเตรียมไว้
        $cloLllDescriptions = [];
        $aiTextJson = $get('ai_text');
        $aiTextData = $aiTextJson ? json_decode($aiTextJson, true) : [];
        if (is_array($aiTextData) && json_last_error() === JSON_ERROR_NONE) {
            foreach($aiTextData as $cloKey => $cloDetails) {
                if (is_array($cloDetails) && isset($cloDetails['CLO'])) {
                    $cloLllDescriptions[] = [
                        'code' => trim(str_replace(' ', '', $cloKey)), 
                        'description' => $cloDetails['CLO']
                    ];
                }
            }
        }
        
        $lllDataArray = json_decode($get('lll_data', '[]'), true) ?: [];
        $cloLllDescriptions = array_merge($cloLllDescriptions, $lllDataArray);

        $docxData = [
            'logo_path' => public_path('image/mjulogo.jpg'),
            'title' => 'มหาวิทยาลัยแม่โจ้',
            'subtitle' => 'มคอ. 3 รายละเอียดรายวิชา',
            'fileName' => 'มคอ-3-' . $get('course_code', 'unknown'),

            'philosophy' => $get('philosophy'),
            'philosophy_education' => $get('philosophy_education'),
            'philosophy_curriculum' => $get('philosophy_curriculum'),

            'faculty' => $get('faculty'),
            'major' => $get('major'),
            'curriculum_year' => $get('curriculum_year'),
            'campus' => $get('campus'),
            'term' => $get('term'),
            'academic_year' => $get('year'),

            's1_course_name' => $get('course_name_th', $get('course_name_en')),
            's1_course_id' => $get('course_code'),
            's1_credits' => $get('credit'),
            's1_curriculum_name' => $get('curriculum_name'),
            'course_types' => [
                'specific' => $get('is_specific', 0),
                'core' => $get('is_core', 0),
                'major_required' => $get('is_major_required', 0),
                'major_elective' => $get('is_major_elective', 0),
                'free_elective' => $get('is_free_elective', 0),
            ],
            's1_prerequisites' => $get('prerequisites', '-'),
            's1_instructors' => $get('instructor_name'),
            's1_revision_term' => $get('term'),
            's1_revision_year' => $get('year'),
            's1_hours' => [
                'theory' => $get('hours_theory', 0),
                'practice' => $get('hours_practice', 0),
                'self_study' => $get('hours_self_study', 0),
                'field_trip' => $get('hours_field_trip', 0),
            ],

            'description' => $get('course_detail_th', $get('course_detail_en')),
            'ai_text' => $get('ai_text'),
            
            'feedback' => $get('feedback'),
            'improvement' => $get('improvement'),
            'agreement' => $get('agreement'),

            's5_1_plos' => [],
            's5_2_mapping_data' => [],
            's5_3_clos_plo_mapping' => [],
            's6_clos_teaching' => [],
            's7_lesson_plan' => [],
            's8_1_assessment_strategies' => [],
            's8_2_rubrics' => [],

            's9_references' => $getArray('references_data'),
            's9_research' => $get('research_subjects'),
            's9_academic_service' => $get('academic_service'),
            's9_culture' => $get('art_culture'),

            's10_grading_criteria' => [],
            's11_grade_correction' => $get('grade_correction'),
        ];
        
        // =========================================================
        // ดึงข้อมูล JSON ลงตาราง
        // =========================================================

        // --- 5.1 PLOs ---
        $outcomeStatementData = $getArray('outcome_statement');
        if(!empty($outcomeStatementData)) {
            ksort($outcomeStatementData);
            foreach($outcomeStatementData as $index => $plo) {
                if(empty($plo)) continue;
                $docxData['s5_1_plos'][] = [
                    'id' => $plo['plo'] ?? $index,
                    'outcome' => $plo['outcome'] ?? '...',
                    'specific' => $plo['specific'] ?? false,
                    'generic' => !($plo['specific'] ?? true),
                    'level' => $plo['level'] ?? '...',
                    'type' => $plo['type'] ?? '...',
                ];
            }
        }
        
        // --- 5.2 Mapping Data ---
        $curriculumMapData = $getArray('curriculum_map_data');
        $totalCols = 29;
        if(!empty($curriculumMapData) && !empty($curriculumMapData[0])) {
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

        // --- 5.3 CLO-PLO Mapping ---
        $courseAccordData = $getArray('course_accord');
        
        // ฟังก์ชันช่วยแปลง Domain เป็นคำย่อ
        $getDomainAbbr = function($domainText) {
            if (empty($domainText)) return '';
            $lower = strtolower((string)$domainText);
            $types = [];
            if (strpos($lower, 'knowledge') !== false) $types[] = 'K';
            if (strpos($lower, 'skill') !== false) $types[] = 'S';
            if (strpos($lower, 'application') !== false || strpos($lower, 'responsibility') !== false) $types[] = 'AR';
            return implode(', ', $types);
        };

        // ฟังก์ชันช่วยดึงเฉพาะตัวเลข PLO
        $getPloNumbers = function($ploText) {
            if (empty($ploText)) return [];
            preg_match_all('/\d+/', (string)$ploText, $matches);
            return !empty($matches[0]) ? array_map('intval', $matches[0]) : [];
        };

        $aiTextData = json_decode($get('ai_text'), true) ?: [];

        if(!empty($cloLllDescriptions)) {
            foreach($cloLllDescriptions as $cloInfo) {
                $cloInfo = (array) $cloInfo;
                $code = $cloInfo['code'] ?? null; 
                $isLLL = str_starts_with($code, 'LLL'); // เช็คว่าเป็นบรรทัดของ LLL หรือไม่
                
                $aiMappedPlos = [];
                $aiDomainAbbr = '';
                
                if (!$isLLL) {
                    $originalKey = str_replace('CLO', 'CLO ', $code); 
                    $aiDetails = $aiTextData[$originalKey] ?? ($aiTextData[$code] ?? []);
                    
                    $ploRaw = '';
                    $domainRaw = '';
                    
                    if (is_array($aiDetails)) {
                        foreach ($aiDetails as $k => $v) {
                            $lowerK = strtolower($k);
                            if (strpos($lowerK, 'plo') !== false) $ploRaw = $v;
                            if (strpos($lowerK, 'domain') !== false) $domainRaw = $v;
                        }
                    }
                    
                    $aiMappedPlos = $getPloNumbers($ploRaw);
                    $aiDomainAbbr = $getDomainAbbr($domainRaw);
                }

                $mapping = ($code && isset($courseAccordData[$code])) ? $courseAccordData[$code] : [];
                $row = ['clo_id' => $code ?? '?', 'clo_desc' => $cloInfo['description'] ?? '...'];
                
                if (!empty($docxData['s5_1_plos'])) {
                    foreach ($docxData['s5_1_plos'] as $plo) {
                        $ploDbIndex = $plo['id']; 
                        $key = 'plo' . $ploDbIndex; 
                        
                        $savedCellData = ['check' => false, 'level' => ''];
                        $hasSavedData = false;

                        if (isset($mapping[(string)$ploDbIndex])) {
                            $savedCellData['check'] = !empty($mapping[(string)$ploDbIndex]['check']);
                            $savedCellData['level'] = $mapping[(string)$ploDbIndex]['level'] ?? '';
                            $hasSavedData = true;
                        }

                        if (!$hasSavedData && !$isLLL && in_array($ploDbIndex, $aiMappedPlos)) {
                            $savedCellData['check'] = true;
                            $savedCellData['level'] = $aiDomainAbbr;
                        }
                        
                        // แยกการแสดงผลระหว่าง CLO กับ LLL 
                        if ($savedCellData['check']) {
                            if ($isLLL) {
                                // ถ้าเป็น LLL ให้แสดงเครื่องหมายติ๊กถูก (✓)
                                $row[$key] = '✓' . (!empty($savedCellData['level']) ? " (" . $savedCellData['level'] . ")" : '');
                            } else {
                                // ถ้าเป็น CLO ให้แสดงแค่คำย่อ (K, S, AR)
                                $row[$key] = $savedCellData['level'];
                            }
                        } else {
                            $row[$key] = '';
                        }
                    }
                }
                $docxData['s5_3_clos_plo_mapping'][] = $row;
            }
        }

        // --- 6. Teaching Methods (แสดงทุกหัวข้อ และติ๊กถูกเฉพาะอันที่มีใน DB) ---
        $teachingMethodsData = $getArray('teaching_methods');
        
        // จำลองข้อมูล Options ให้เหมือนกับหน้าเว็บ
        $teachingOptions = [
            "On-site โดยมีการเรียนการสอนแบบ" => [
                "บรรยาย (Lecture)", "ฝึกปฏิบัติ (Laboratory Model)", "เรียนรู้จากการลงมือทำ (Learning by Doing)",
                "การเรียนรู้โดยใช้กิจกรรมเป็นฐาน (Activity-based Learning)", "การเรียนรู้โดยใช้วิจัยเป็นฐาน (Research-based Learning)",
                "ถามตอบสะท้อนคิด (Refractive Learning)", "นำเสนออภิปรายกลุ่ม (Discussion Group)",
                "เรียนรู้จากการสืบเสาะหาความรู้ (Inquiry-based Learning)", "การเรียนรู้แบบร่วมมือร่วมใจ (Cooperative Learning)",
                "การเรียนรู้แบบร่วมมือ (Collaborative Learning)", "การเรียนรู้โดยใช้โครงการเป็นฐาน (Project-based Learning: PBL)",
                "การเรียนรู้โดยใช้ปัญหาเป็นฐาน (Problem-based Learning)", "วิเคราะห์โจทย์ปัญหา (Problem Solving)",
                "เรียนรู้การตัดสินใจ (Decision Making)", "ศึกษาค้นคว้าจากกรณีศึกษา (Case Study)",
                "เรียนรู้ผ่านการเกม/การเล่น (Game/Play Learning)", "ศึกษาดูงาน (Field Trips)", "อื่น ๆ....................................."
            ],
            "Online โดยมีการเรียนการสอนแบบ" => [
                "บรรยาย (Lecture)"
            ]
        ];

        $assessmentOptions = [
            "คะแนนจากแบบทดสอบสัมฤทธิ์ผล (ข้อสอบ)" => [
                "ทดสอบย่อย (Quiz)", "ทดสอบกลางภาค (Midterm)", "ทดสอบปลายภาค (Final)"
            ],
            "คะแนนจากผลงานที่ได้รับมอบหมาย (Performance) โดยใช้เกณฑ์ Rubric Score การทำงานกลุ่มและเดี่ยว" => [
                "การทำงานเป็นทีม (Team Work)", "โปรแกรม ซอฟต์แวร์", "ผลงาน ชิ้นงาน", "รายงาน (Report)",
                "การนำเสนอ (Presentation)", "แฟ้มสะสมงาน (Portfolio)", "รายงานการศึกษาด้วยตนเอง (Self-Study Report)"
            ],
            "คะแนนจากผลการพฤติกรรม" => [
                "ความรับผิดชอบ การมีส่วนร่วม", "ประเมินผลการงานที่ส่ง"
            ]
        ];

        $docxData['s6_clos_teaching'] = [];

        if(!empty($teachingMethodsData)) {
            uksort($teachingMethodsData, function ($a, $b) {
                preg_match('/(\d+)/', $a, $matchesA); preg_match('/(\d+)/', $b, $matchesB);
                return (intval($matchesA[1] ?? 999)) <=> (intval($matchesB[1] ?? 999));
            });

            foreach($teachingMethodsData as $cloKey => $dataT) {
                if(!is_array($dataT) || count($dataT) < 2) continue;
                
                $teachingRaw = $dataT[0]['วิธีการสอน'] ?? [];
                $assessmentRaw = $dataT[1]['การประเมินผล'] ?? [];

                // ตรวจสอบว่าหัวข้อนี้ถูกบันทึกไว้ใน DB หรือไม่ (รองรับทั้งฟอร์แมตเก่าและใหม่)
                $isChecked = function($categoryLabel, $itemText, $rawData) {
                    if (!is_array($rawData)) return false;
                    
                    $target = trim($itemText);

                    // ตรวจสอบข้อมูลแบบใหม่ (แยกตาม Category จากหน้าเว็บล่าสุด)
                    if (isset($rawData[$categoryLabel]) && is_array($rawData[$categoryLabel])) {
                        foreach ($rawData[$categoryLabel] as $val) {
                            if (is_string($val) && trim($val) === $target) return true;
                        }
                    }

                    // ตรวจสอบข้อมูลแบบเก่า (ค้นหาแบบเหวี่ยงแห ป้องกัน Database ผสมกัน)
                    foreach ($rawData as $val) {
                        if (is_string($val)) {
                            if (trim($val) === $target) return true;
                        } elseif (is_array($val)) {
                            // ถ้าเจอ Array ซ้อนกัน ให้เข้าไปค้นข้างในอีกชั้น (ดัก Error ของ trim)
                            foreach ($val as $subVal) {
                                if (is_string($subVal) && trim($subVal) === $target) return true;
                            }
                        }
                    }
                    
                    return false;
                };

                // สร้างข้อความ "วิธีการสอน"
                $teachingFlat = [];
                foreach ($teachingOptions as $catLabel => $items) {
                    $teachingFlat[] = $catLabel; // ใส่ชื่อหมวดหมู่
                    foreach ($items as $item) {
                        $mark = $isChecked($catLabel, $item, $teachingRaw) ? "☑" : "☐";
                        $teachingFlat[] = "   " . $mark . " " . $item; // ย่อหน้าและใส่ Checkbox
                    }
                }

                // สร้างข้อความ "การประเมินผล"
                $assessmentFlat = [];
                foreach ($assessmentOptions as $catLabel => $items) {
                    $assessmentFlat[] = $catLabel; // ใส่ชื่อหมวดหมู่
                    foreach ($items as $item) {
                        $mark = $isChecked($catLabel, $item, $assessmentRaw) ? "☑" : "☐";
                        $assessmentFlat[] = "   " . $mark . " " . $item; // ย่อหน้าและใส่ Checkbox
                    }
                }

                // นำไปเก็บลง Array รอวาดลงตาราง
                $docxData['s6_clos_teaching'][] = [
                    'clo' => $cloKey,
                    'teaching' => implode("\n", $teachingFlat),
                    'assessment' => implode("\n", $assessmentFlat)
                ];
            }
        }
        
        // --- 7. Lesson Plan ---
        $planData = $getArray('plan_data'); 
        if(!empty($planData)) {
             // เรียงตาม week
             usort($planData, function($a, $b) { return ((int)($a['week'] ?? 0)) <=> ((int)($b['week'] ?? 0)); });
             
             foreach($planData as $weekData) {
                 if(empty($weekData)) continue;
                 $docxData['s7_lesson_plan'][] = [
                     'week'      => $weekData['week'] ?? '',
                     'topic'     => $weekData['topic'] ?? '',
                     'objective' => $weekData['objective'] ?? '',
                     'activity'  => $weekData['activity'] ?? '',
                     'media'     => $weekData['tool'] ?? '',
                     'eval'      => $weekData['assessment'] ?? '',
                     'clo'       => $weekData['clo'] ?? ''
                 ];
            }
        }

        // --- 8.1 Assessment Strategies ---
        $assessmentData = $getArray('assessment_data'); 
         if(!empty($assessmentData)) {
             foreach($assessmentData as $stratData) {
                  if(empty($stratData)) continue;
                  $docxData['s8_1_assessment_strategies'][] = [
                      'method' => $stratData['method'] ?? '',
                      'tool'   => $stratData['tool'] ?? '',
                      'ratio'  => $stratData['percent'] ?? '',
                      'clos'   => $stratData['clo_desc'] ?? ($stratData['clo'] ?? '') // ใช้ clo_desc ก่อน ถ้าไม่มีใช้ clo รหัสสั้น
                  ];
             }
         }
        
        // --- 8.2 Rubrics (ปรับให้อ่าน {5: "...", 4: "..."}) ---
        $rubricsData = $getArray('rubrics_data');
         if(!empty($rubricsData)) {
             foreach($rubricsData as $rubric) {
                 if(empty($rubric)) continue;
                 $levelsArray = [];
                 $rowsData = $rubric['rows'] ?? [];

                 // วนลูปตายตัวจากคะแนน 5 ลงไป 0
                 $scoreLevels = [5, 4, 3, 2, 1, 0];
                 foreach($scoreLevels as $lvl) {
                     $desc = '';
                     if(is_array($rowsData)) {
                         $desc = $rowsData[(string)$lvl] ?? ($rowsData[$lvl] ?? '');
                     }
                     $levelsArray[] = [
                         'level' => (string)$lvl, 
                         'desc'  => $desc
                     ];
                 }

                 $docxData['s8_2_rubrics'][] = [
                     'title'  => $rubric['title'] ?? '',
                     'header' => $rubric['header'] ?? '',
                     'levels' => $levelsArray
                 ];
             }
         }

        // --- 10. Grading Criteria ---
        $gradingData = $getArray('grading_criteria');
        $grades = ['A', 'Bp', 'B', 'Cp', 'C', 'Dp', 'D', 'F'];
        foreach($grades as $g) {
            $gradeKey = "grade_{$g}_level";
            $criteriaKey = "grade_{$g}_criteria";
            
            $docxData['s10_grading_criteria'][] = [
                'grade' => $gradingData[$gradeKey] ?? str_replace('p', '+', $g),
                'criteria' => $gradingData[$criteriaKey] ?? ''
            ];
        }


        // ==========================================================
        // DOCX GENERATION (สร้างไฟล์ Word)
        // ==========================================================
        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();

            $phpWord->getSettings()->setHideSpellingErrors(true);
            $phpWord->getSettings()->setHideGrammaticalErrors(true);

            $phpWord->setDefaultFontName('TH Sarabun New');
            $phpWord->setDefaultFontSize(14);

            $phpWord->setDefaultParagraphStyle([
                'spaceAfter' => 0,   // ระยะห่างหลังย่อหน้าเป็น 0
                'spaceBefore' => 0,  // ระยะห่างก่อนย่อหน้าเป็น 0
            ]);

            $cmMargin = 2.5;
            $section = $phpWord->addSection([
                'paperSize'   => 'A4',
                'orientation' => 'portrait',
                'marginTop'   => \PhpOffice\PhpWord\Shared\Converter::cmToTwip($cmMargin),
                'marginBottom'=> \PhpOffice\PhpWord\Shared\Converter::cmToTwip($cmMargin),
                'marginLeft'  => \PhpOffice\PhpWord\Shared\Converter::cmToTwip($cmMargin),
                'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip($cmMargin),
            ]);

            $boldFont = ['bold' => true];
            $underlineFont = ['underline' => 'single'];
            $cellCenter = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
            $vAlignCenter = ['valign' => 'center'];
            $thStyle = ['bgColor' => 'DBEAFE'];
            $head = ['size' => 14];
            $paragraph_spacing = ['spaceAfter' => 200];
            
            if (file_exists($docxData['logo_path'])) {
                $section->addImage($docxData['logo_path'], ['width' => 70, 'height' => 70, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            }
            $section->addText($docxData['title'], array_merge($boldFont,), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
            $section->addText($docxData['subtitle'], array_merge($boldFont,), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 200]);

            $borderlessStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 0, 'spaceAfter'  => 0, 'spaceBefore' => 0];
            $infoTable = $section->addTable($borderlessStyle);
            $infoTable->addRow();
            $infoTable->addCell(1500)->addText('คณะ', $boldFont);
            $infoTable->addCell(2000)->addText(htmlspecialchars($docxData['faculty']), $underlineFont);
            $infoTable->addCell(1300)->addText('สาขาวิชา', $boldFont);
            $infoTable->addCell(3000, ['gridSpan' => 2])->addText(htmlspecialchars($docxData['major']), $underlineFont);
            $infoTable->addCell(1700)->addText('หลักสูตรปรับปรุง', $boldFont);
            $infoTable->addCell(1500)->addText(('พ.ศ. '.$docxData['curriculum_year']), $underlineFont);
            $infoTable->addRow();
            $infoTable->addCell(1500)->addText('วิทยาเขต', $boldFont);
            $infoTable->addCell(2000)->addText(htmlspecialchars($docxData['campus']), $underlineFont);
            $infoTable->addCell(2700, ['gridSpan' => 2])->addText('ภาคการศึกษา/ปีการศึกษา', $boldFont);
            $infoTable->addCell(1000)->addText(($docxData['term'] . ' / ' . $docxData['academic_year']), $underlineFont);
            
            $section->addText('ปรัชญามหาวิทยาลัยแม่โจ้', array_merge($boldFont));
            $section->addText(htmlspecialchars($docxData['philosophy']), [$paragraph_spacing]);
            $section->addText('ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้', array_merge($boldFont));
            $section->addText(htmlspecialchars($docxData['philosophy_education']), [$paragraph_spacing]);
            $section->addText('ปรัชญาหลักสูตร', array_merge($boldFont));
            $section->addText(htmlspecialchars($docxData['philosophy_curriculum']), [$paragraph_spacing]);

            $phpWord->addTableStyle('MainTable', ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'width' => '100%']);

            $noBorderCell = ['valign' => 'top', 'borderSize' => 0, 'borderColor' => 'FFFFFF'];

            // === หมวดที่ 1 ===
            $table1Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table1Head->addRow();
            $table1Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 1 : ข้อมูลทั่วไป', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $table1 = $section->addTable('MainTable');
            
            $table1->addRow(); $table1->addCell(2500, $thStyle)->addText('1. ชื่อวิชา', $boldFont); 
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_course_name']));
            
            $table1->addRow(); $table1->addCell(2500, $thStyle)->addText('2. รหัสวิชา', $boldFont); 
            $table1->addCell(7500, ['gridSpan' => 3])->addText($docxData['s1_course_id']);
            
            $table1->addRow(); $table1->addCell(2500, $thStyle)->addText('3. จำนวนหน่วยกิต', $boldFont); 
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_credits']));
            
            $table1->addRow(); $table1->addCell(2500, $thStyle)->addText('4. หลักสูตร', $boldFont); 
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_curriculum_name']));
            
            $table1->addRow(); 
            $table1->addCell(2500, $thStyle)->addText('5. ประเภทของรายวิชา', $boldFont);
            $cell5 = $table1->addCell(7500, ['gridSpan' => 3]);
            $textRun5 = $cell5->addTextRun();
            
            $cbSpec    = $docxData['course_types']['specific'] ? '☑' : '☐';
            $cbCore    = $docxData['course_types']['core'] ? '☑' : '☐';
            $cbMajReq  = $docxData['course_types']['major_required'] ? '☑' : '☐';
            $cbMajElec = $docxData['course_types']['major_elective'] ? '☑' : '☐';
            $cbFreeElec = $docxData['course_types']['free_elective'] ? '☑' : '☐';

            // บรรทัดแรกของข้อ 5
            $textRun5->addText("{$cbSpec} วิชาเฉพาะ      กลุ่มวิชา   {$cbCore} แกน   {$cbMajReq} เอกบังคับ   {$cbMajElec} เอกเลือก");
            $textRun5->addTextBreak(1);
            // บรรทัดสองของข้อ 5
            $textRun5->addText("{$cbFreeElec} วิชาเลือกเสรี");

            $table1->addRow(); $table1->addCell(2500, $thStyle)->addText('6. วิชาบังคับก่อน', $boldFont); 
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_prerequisites']));
            
            $table1->addRow(); $table1->addCell(2500, $thStyle)->addText('7. ผู้สอน', $boldFont); 
            $table1->addCell(7500, ['gridSpan' => 3])->addText(htmlspecialchars($docxData['s1_instructors']));
            
            $table1->addRow(); 
            $table1->addCell(2500, $thStyle)->addText('8. การแก้ไขล่าสุด', $boldFont);
            $cell8 = $table1->addCell(7500, ['gridSpan' => 3]);
            $textRun8 = $cell8->addTextRun();
            
            $cbTerm1 = ($docxData['s1_revision_term'] == '1') ? '☑' : '☐';
            $cbTerm2 = ($docxData['s1_revision_term'] == '2') ? '☑' : '☐';
            
            $textRun8->addText("ภาคเรียนที่  {$cbTerm1} 1   {$cbTerm2} 2  ปีการศึกษา " . $docxData['s1_revision_year']);

            $table1->addRow();
            $table1->addCell(10000, ['gridSpan' => 4])->addText('9. จำนวนชั่วโมงที่ใช้ต่อภาคการศึกษา', $boldFont);
            
            $table1->addRow();
            $h_theory = empty($docxData['s1_hours']['theory']) ? '-' : $docxData['s1_hours']['theory'];
            $h_prac   = empty($docxData['s1_hours']['practice']) ? '-' : $docxData['s1_hours']['practice'];
            $h_self   = empty($docxData['s1_hours']['self_study']) ? '-' : $docxData['s1_hours']['self_study'];
            $h_field  = empty($docxData['s1_hours']['field_trip']) ? '-' : $docxData['s1_hours']['field_trip'];

            $cell9_1 = $table1->addCell(2500, ['valign' => 'center']);
            $cell9_1->addText("ภาคทฤษฎี  {$h_theory} ชั่วโมง", null, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

            $cell9_2 = $table1->addCell(2500, ['valign' => 'center']);
            $cell9_2->addText("ภาคปฏิบัติ   {$h_prac} ชั่วโมง", null, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

            $cell9_3 = $table1->addCell(2500, ['valign' => 'center']);
            $cell9_3->addText("การศึกษา  {$h_self} ชั่วโมง", null, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

            $cell9_4 = $table1->addCell(2500, ['valign' => 'center']);
            $cell9_4->addText("ทัศนศึกษา/ฝึกงาน  {$h_field} ชั่วโมง", null, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        
            // === หมวด 2 ===
            $section->addTextBreak(1); 
            $table2Head = $section->addTable(['borderSize' => 0, 'width' => '100%']);
            $table2Head->addRow();
            $table2Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 2 : คำอธิบายรายวิชาและผลลัพธ์ระดับรายวิชา (CLOs)', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);

            $section->addTextBreak(1, ['size' => 6]);
            $section->addText('2.1 คำอธิบายรายวิชา', $boldFont);
            
            $paragraphStyle = [
                'indentation' => ['firstLine' => 720], 
                'spaceAfter' => 100, 
                'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT
            ]; 

            $cleanDescription = function($text) {
                if (empty($text)) return [];
                $text = strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
                
                $paragraphs = preg_split('/[\r\n]{2,}/', $text);
                
                $result = [];
                foreach ($paragraphs as $p) {
                    $p = preg_replace('/[\r\n]+/', ' ', $p);
                    $p = preg_replace('/[ \t]+/u', ' ', $p);
                    
                    $p = trim($p);
                    if (!empty($p)) {
                        $result[] = $p;
                    }
                }
                return $result;
            };

            $descThParagraphs = $cleanDescription($get('course_detail_th')); 
            $descEnParagraphs = $cleanDescription($get('course_detail_en')); 

            // วาดข้อความลง Word
            $hasDesc = false;
            if (!empty($descThParagraphs)) {
                foreach ($descThParagraphs as $p) {
                    $section->addText(htmlspecialchars($p), null, $paragraphStyle);
                    $section->addTextBreak(1);
                    $hasDesc = true;
                }
            }
            if (!empty($descEnParagraphs)) {
                foreach ($descEnParagraphs as $p) {
                    $section->addText(htmlspecialchars($p), null, $paragraphStyle);
                    $section->addTextBreak(1);
                    $hasDesc = true;
                }
            }
            
            // กันเหนียวกรณีใช้ description หลัก
            if (!$hasDesc && !empty($docxData['description'])) {
                foreach ($cleanDescription($docxData['description']) as $p) {
                    $section->addText(htmlspecialchars($p), null, $paragraphStyle);
                }
            }
            $section->addTextBreak(1, ['size' => 6]);
            $section->addText('2.2 ผลลัพธ์การเรียนรู้ระดับรายวิชา (Course learning Outcome) CLOs', $boldFont);
            
            $table2_2 = $section->addTable([
                'borderSize'  => 0, 
                'borderColor' => 'FFFFFF',
                'cellMargin'  => 0, 
                'width'       => 100 * 50
            ]);

            $closData = json_decode($docxData['ai_text'], true); 
            if (is_array($closData) && json_last_error() === JSON_ERROR_NONE) {
                uksort($closData, function ($a, $b) { 
                    preg_match('/(\d+)/', $a, $matchesA); 
                    preg_match('/(\d+)/', $b, $matchesB); 
                    return (intval($matchesA[1]??999)) <=> (intval($matchesB[1]??999)); 
                });
                
                foreach ($closData as $cloKey => $cloDetails) {
                    $cleanCloKey = str_replace(' ', '', htmlspecialchars($cloKey));
                    $cloDesc = '';
                    
                    if (is_array($cloDetails) && isset($cloDetails['CLO'])) {
                        $cloDesc = htmlspecialchars($cloDetails['CLO']);
                    } elseif (is_string($cloDetails)) {
                        $cloDesc = htmlspecialchars($cloDetails);
                    }

                    $table2_2->addRow();
                    $table2_2->addCell(1000, $noBorderCell)->addText($cleanCloKey, null);
                    $table2_2->addCell(9000, $noBorderCell)->addText($cloDesc, null);
                }
            } else {
                $table2_2->addRow();
                $table2_2->addCell(10000, $noBorderCell)->addText('ไม่มีข้อมูล CLO', ['color' => 'red']);
            }

            // === หมวด 3 ===
            $section->addTextBreak(1);
            $table3Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table3Head->addRow();
            $table3Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 3 : การปรับปรุงรายวิชาตามข้อเสนอแนะจาก มคอ.5', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $table3 = $section->addTable('MainTable');
            $table3->addRow(); $table3->addCell(5000, $thStyle)->addText('ข้อเสนอแนะ', $boldFont, $cellCenter); $table3->addCell(5000, $thStyle)->addText('การปรับปรุง', $boldFont, $cellCenter);
            $table3->addRow(); $table3->addCell(5000)->addText(htmlspecialchars($docxData['feedback'])); $table3->addCell(5000)->addText(htmlspecialchars($docxData['improvement']));

            // === หมวด 4 ===
            $section->addTextBreak(1);
            $table4Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table4Head->addRow();
            $table4Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 4: ข้อตกลงร่วมกันระหว่างผู้สอนและผู้เรียน', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            
            // ดึงข้อมูลจาก Database
            $agreementRaw = $docxData['agreement'] ?? '';
            $agreementRaw = strip_tags(html_entity_decode($agreementRaw, ENT_QUOTES, 'UTF-8'));
            
            // ป้องกันปัญหาประโยคขาดกลาง: เปลี่ยน Enter ทุกชนิดให้เป็นช่องว่าง (รวมข้อความทั้งหมดเป็น 1 ก้อนยาวๆ)
            $agreementRaw = preg_replace('/[\r\n]+/', ' ', $agreementRaw);
            
            // จัดระเบียบช่องว่าง: ถอดช่องว่างที่เคาะติดกันหลายอันให้เหลือ 1 เคาะ (เช่น "ได้รับการ   ประเมิน" -> "ได้รับการ ประเมิน")
            $agreementRaw = preg_replace('/[ \t]+/u', ' ', $agreementRaw);

            // หั่นประโยคใหม่: สั่งให้เว้นบรรทัด (\n) "เฉพาะตอนที่เจอเลข 4.1, 4.2, 4.3..." เท่านั้น!
            $agreementRaw = preg_replace('/ (4\.\d+)/', "\n$1", trim($agreementRaw));

            // แยกข้อความแต่ละข้อออกจากกัน กลายเป็น Array [ "4.1 ...", "4.2 ..." ]
            $agreementParagraphs = array_filter(array_map('trim', explode("\n", $agreementRaw)));

            // วาดข้อความลง Word ด้วยสไตล์ "Hanging Indent" (ย่อหน้าแบบแขวน)
            foreach ($agreementParagraphs as $p) {
                $section->addText(htmlspecialchars($p), null, [
                    'indentation' => [
                        'left' => 500,      // ดันข้อความ "ทั้งหมด" ขยับเข้ามาด้านใน
                        'hanging' => 500    // ผลัก "เฉพาะบรรทัดแรก" (ตรงที่เป็นตัวเลข 4.1) กลับไปชิดขอบซ้าย
                    ], 
                    'spaceAfter' => 100,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT // จัดการกระจายข้อความให้ชิดซ้าย-ขวา แบบเอกสารราชการ
                ]);
            }

            // === หมวด 5 ===
            $section->addTextBreak(1);
            $table5Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table5Head->addRow();
            $table5Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 5: ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับหลักสูตร (PLOs) กับ ผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) และผลทักษะการเรียนรู้ตลอดชีวิต (LLLs)', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);

            $section->addText('5.1 ผลลัพธ์การเรียนรู้ของหลักสูตร', array_merge($boldFont));
            $table5_1 = $section->addTable('MainTable');
            $table5_1->addRow(); $table5_1->addCell(1000, $thStyle)->addText('PLOs', $boldFont, $cellCenter); $table5_1->addCell(4500, $thStyle)->addText('Outcome Statement', $boldFont, $cellCenter); $table5_1->addCell(1000, $thStyle)->addText('Specific LO', $boldFont, $cellCenter); $table5_1->addCell(1000, $thStyle)->addText('Generic LO', $boldFont, $cellCenter); $table5_1->addCell(1000, $thStyle)->addText('Level', $boldFont, $cellCenter); $table5_1->addCell(1500, $thStyle)->addText('Type', $boldFont, $cellCenter);
            foreach ($docxData['s5_1_plos'] as $plo) {
                $table5_1->addRow(); $table5_1->addCell(1000, $vAlignCenter)->addText($plo['id'], null, $cellCenter); $table5_1->addCell(4500)->addText(htmlspecialchars($plo['outcome'])); $table5_1->addCell(1000, $vAlignCenter)->addText($plo['specific'] ? '✓' : '', null, $cellCenter); $table5_1->addCell(1000, $vAlignCenter)->addText($plo['generic'] ? '✓' : '', null, $cellCenter); $table5_1->addCell(1000, $vAlignCenter)->addText($plo['level'], null, $cellCenter); $table5_1->addCell(1500, $vAlignCenter)->addText($plo['type'], null, $cellCenter);
            }
            
            $section->addTextBreak(1); $section->addText('5.2 มาตรฐานผลการเรียนรู้รายวิชา (Curriculum Mapping)', array_merge($boldFont));
            $table5_2 = $section->addTable('MainTable');
            $cellYellow = ['bgColor' => 'FFF9C4'];  $cellWhite = ['bgColor' => 'FFFFFF'];
            $table5_2->addRow(); $table5_2->addCell(2000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('รายวิชา', null, $cellCenter); $table5_2->addCell(null, ['gridSpan' => 7, 'bgColor' => 'FFF9C4'])->addText('ด้านคุณธรรมและจริยธรรม', null, $cellCenter); $table5_2->addCell(null, ['gridSpan' => 8, 'bgColor' => 'FFF9C4'])->addText('ด้านความรู้', null, $cellCenter); $table5_2->addCell(null, ['gridSpan' => 4, 'bgColor' => 'FFFFFF'])->addText('ทักษะทางปัญญา', null, $cellCenter); $table5_2->addCell(null, ['gridSpan' => 6, 'bgColor' => 'FFF9C4'])->addText('ทักษะระหว่างบุคคลและความรับผิดชอบ', null, $cellCenter); $table5_2->addCell(null, ['gridSpan' => 4, 'bgColor' => 'FFFFFF'])->addText('ทักษะวิเคราะห์เชิงตัวเลข...', null, $cellCenter);
            $table5_2->addRow(); $table5_2->addCell(null, ['vMerge' => 'continue']); 
            for ($i=1; $i<=7; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter); for ($i=1; $i<=8; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter); for ($i=1; $i<=4; $i++) $table5_2->addCell(300, $cellWhite)->addText($i, null, $cellCenter); for ($i=1; $i<=6; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter); for ($i=1; $i<=4; $i++) $table5_2->addCell(300, $cellWhite)->addText($i, null, $cellCenter);
            $mapSymbols = ['0' => '', '1' => '●', '2' => '○'];
            foreach ($docxData['s5_2_mapping_data'] as $mapRow) {
                $table5_2->addRow(); $table5_2->addCell(2000)->addText($docxData['s1_course_id'] . "\n" . $docxData['s1_course_name']);
                foreach ($mapRow as $state) $table5_2->addCell(300)->addText($mapSymbols[$state] ?? '', $boldFont, $cellCenter);
            }

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
                
                // คลีนข้อความ ลบ Enter ที่แอบซ่อนอยู่ออกให้หมด เพื่อให้ประโยคต่อกัน
                $cleanDesc = $map['clo_desc'];
                $cleanDesc = preg_replace('/[\r\n]+/', ' ', $cleanDesc); // แปลง Enter ทุกชนิดให้เป็นช่องว่าง 1 เคาะ
                $cleanDesc = preg_replace('/[ \t]+/u', ' ', $cleanDesc); // ยุบช่องว่างที่เกินให้เหลือแค่อันเดียว
                $cleanDesc = trim($cleanDesc);

                // นำข้อความที่คลีนแล้วมายัดลงตาราง
                $table5_3->addCell(5000)->addText(htmlspecialchars($cleanDesc));
                
                if (!empty($docxData['s5_1_plos'])) {
                    foreach ($docxData['s5_1_plos'] as $plo) {
                        $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo' . ($plo['id'] ?? '')] ?? '', null, $cellCenter); 
                    }
                }
            }

            // === หมวด 6 ===
            $section->addTextBreak(1);
            $table6Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table6Head->addRow();
            $table6Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 6 : ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) วิธีการสอน และการประเมินผล', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $table6 = $section->addTable('MainTable');
            $table6->addRow(); $table6->addCell(2000, $thStyle)->addText('CLO#', $boldFont); $table6->addCell(4000, $thStyle)->addText('วิธีการสอน (Active Learning)', $boldFont); $table6->addCell(4000, $thStyle)->addText('การประเมินผล', $boldFont);
            foreach ($docxData['s6_clos_teaching'] as $row) {
                $table6->addRow(); $table6->addCell(2000)->addText(htmlspecialchars($row['clo']), ['size' => 12]);
                $cellTeach = $table6->addCell(4000); foreach(explode("\n", $row['teaching']) as $item) $cellTeach->addText(htmlspecialchars($item), ['size' => 12]);
                $cellAssess = $table6->addCell(4000); foreach(explode("\n", $row['assessment']) as $item) $cellAssess->addText(htmlspecialchars($item), ['size' => 12]);
                $section->addTextBreak(1);
            }
            
            // === หมวด 7 ===
            $section->addTextBreak(1);
            $table7Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table7Head->addRow();
            $table7Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 7: แผนการสอน', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $section->addText('7.1 แผนการสอน', array_merge($boldFont));
            $table7 = $section->addTable('MainTable');
            $table7->addRow(); $table7->addCell(800, $thStyle)->addText('สัปดาห์ที่', $boldFont, $cellCenter); $table7->addCell(1500, $thStyle)->addText('หัวข้อ', $boldFont, $cellCenter); $table7->addCell(2000, $thStyle)->addText('วัตถุประสงค์', $boldFont, $cellCenter); $table7->addCell(2500, $thStyle)->addText('กิจกรรม', $boldFont, $cellCenter); $table7->addCell(1500, $thStyle)->addText('สื่อ/เครื่องมือ', $boldFont, $cellCenter); $table7->addCell(1000, $thStyle)->addText('การประเมินผล', $boldFont, $cellCenter); $table7->addCell(700, $thStyle)->addText('CLO', $boldFont, $cellCenter);
            foreach ($docxData['s7_lesson_plan'] as $plan) {
                $table7->addRow(); $table7->addCell(800, $vAlignCenter)->addText($plan['week'], null, $cellCenter); $table7->addCell(1500)->addText(htmlspecialchars($plan['topic'])); $table7->addCell(2000)->addText(htmlspecialchars($plan['objective'])); $table7->addCell(2500)->addText(htmlspecialchars($plan['activity'])); $table7->addCell(1500)->addText(htmlspecialchars($plan['media'])); $table7->addCell(1000)->addText(htmlspecialchars($plan['eval'])); $table7->addCell(700, $vAlignCenter)->addText($plan['clo'], null, $cellCenter);
            }
            
            // === หมวด 8 ===
            $section->addTextBreak(1);
            $table8Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table8Head->addRow();
            $table8Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 8 : การประเมินการบรรลุผลลัพธ์การเรียนรู้รายวิชา (CLOs)', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $section->addText('8.1 กลยุทธ์การประเมิน', array_merge($boldFont));
            $table8_1 = $section->addTable('MainTable');
            $table8_1->addRow(); $table8_1->addCell(2500, $thStyle)->addText('วิธีการประเมิน', $boldFont, $cellCenter); $table8_1->addCell(3500, $thStyle)->addText('เครื่องมือ / รายละเอียด', $boldFont, $cellCenter); $table8_1->addCell(1500, $thStyle)->addText('สัดส่วน (%)', $boldFont, $cellCenter); $table8_1->addCell(2500, $thStyle)->addText('ความสอดคล้องกับ CLOs', $boldFont, $cellCenter);
            foreach ($docxData['s8_1_assessment_strategies'] as $strategy) {
                $table8_1->addRow(); $table8_1->addCell(2500)->addText(htmlspecialchars($strategy['method'])); $table8_1->addCell(3500)->addText(htmlspecialchars($strategy['tool'])); $table8_1->addCell(1500, $vAlignCenter)->addText($strategy['ratio'], null, $cellCenter); $table8_1->addCell(2500, $vAlignCenter)->addText($strategy['clos'], null, $cellCenter);
            }
            
            $section->addTextBreak(1); $section->addText('8.2 วิธีการประเมิน แบบรูบริค (Rubric) หรือ อื่นๆ (ถ้ามี)', array_merge($boldFont));
            foreach ($docxData['s8_2_rubrics'] as $rubric) {
                $section->addText(htmlspecialchars($rubric['title']), array_merge($boldFont));
                $tableRubric = $section->addTable('MainTable');
                $tableRubric->addRow(); $tableRubric->addCell(1500, $thStyle)->addText('ระดับ', $boldFont, $cellCenter); $tableRubric->addCell(8500, $thStyle)->addText(htmlspecialchars($rubric['header']), $boldFont, $cellCenter);
                foreach ($rubric['levels'] as $level) {
                    $tableRubric->addRow(); $tableRubric->addCell(1500, $vAlignCenter)->addText($level['level'], null, $cellCenter); $tableRubric->addCell(8500)->addText(htmlspecialchars($level['desc']));
                }
                $section->addTextBreak(0.5);
            }

            // === หมวด 9 ===
            $section->addTextBreak(1);
            $table9Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table9Head->addRow();
            $table9Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 9: สื่อการเรียนรู้และงานวิจัย', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $section->addText('9.1 สื่อการเรียนรู้และสิ่งสนับสนุนการเรียนรู้', array_merge($boldFont));
            if (!empty($docxData['s9_references']) && is_array($docxData['s9_references'])) {
                foreach ($docxData['s9_references'] as $item) $section->addListItem(htmlspecialchars($item), 0);
            } else {
                 $section->addListItem(htmlspecialchars($get('research', '...')), 0);
            }
            $section->addText('9.2 งานวิจัยที่นำมาสอนในรายวิชา', array_merge($boldFont)); $section->addText(htmlspecialchars($docxData['s9_research']));
            $section->addText('9.3 การบริการวิชาการ', array_merge($boldFont)); $section->addText(htmlspecialchars($docxData['s9_academic_service']));
            $section->addText('9.4 งานทำนุบำรุงศิลปวัฒนธรรม', array_merge($boldFont)); $section->addText(htmlspecialchars($docxData['s9_culture']));
            
            // === หมวด 10 ===
            $section->addTextBreak(1);
            $table10Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table10Head->addRow();
            $table10Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 10 : เกณฑ์การประเมิน', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $table10 = $section->addTable('MainTable');
            $table10->addRow(); $table10->addCell(3000, $thStyle)->addText('ระดับผลการศึกษา', $boldFont, $cellCenter); $table10->addCell(5000, $thStyle)->addText('เกณฑ์การประเมินผล', $boldFont, $cellCenter);
            if (!empty($docxData['s10_grading_criteria'])) {
                foreach ($docxData['s10_grading_criteria'] as $grade) {
                    $table10->addRow(); $table10->addCell(5000, $vAlignCenter)->addText($grade['grade'], null, $cellCenter); $table10->addCell(5000, $vAlignCenter)->addText($grade['criteria'], null, $cellCenter);
                }
            } else {
                 foreach (['A', 'B+', 'B', 'C+', 'C', 'D+', 'D', 'F'] as $g) {
                      $table10->addRow(); $table10->addCell(5000, $vAlignCenter)->addText($g, null, $cellCenter); $table10->addCell(5000, $vAlignCenter)->addText('...', null, $cellCenter);
                 }
            }

            // === หมวด 11 ===
            $section->addTextBreak(1);
            $table11Head = $section->addTable(['borderSize' => 0, 'width' => '100%', 'borderColor' => 'FFFFFF',]);
            $table11Head->addRow();
            $table11Head->addCell(10000, ['bgColor' => 'DBEAFE', 'valign' => 'center'])->addText('หมวดที่ 11 : ขั้นตอนการแก้ไขคะแนน', array_merge($boldFont, $head), ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 0, 'spaceBefore' => 0]);
            $section->addText(htmlspecialchars($docxData['s11_grade_correction']));

            $fileName = $docxData['fileName'] . '.docx';
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $tempFile = tempnam(sys_get_temp_dir(), 'phpword');
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error exporting DOCX: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response('เกิดข้อผิดพลาดในการสร้างเอกสาร: ' . $e->getMessage(), 500);
        }
    }

    public function previewDocx(Request $request)
    {
        $context = $this->getBaseContext($request);
        $data = $this->getAggregatedData($context);

        return view('preview-docx', compact('data'));
    }
}
