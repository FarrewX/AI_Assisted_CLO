<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Courseyears;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $defaultYear = date('Y') + 543;
        $selectedYear = $request->input('year', $defaultYear); 

        $courses = DB::table('courseyears as cy')
            ->join('curriculum_courses as cc', 'cy.CC_id', '=', 'cc.id')
            ->join('courses as c', 'cc.course_code', '=', 'c.id') 
            ->leftJoin('statuses as s', 'cy.id', '=', 's.courseyear_id_ref')
            ->where('cy.user_id', $user->user_id) 
            ->where('cy.year', $selectedYear)
            ->select(
                'c.course_code',
                'c.course_name_th',
                'c.course_name_en',
                'cy.year',
                'cy.term',
                'cy.TQF',
                'cy.CC_id',
                's.startprompt',
                's.generated',
                's.success'
            )
            ->orderBy('cy.term', 'asc')
            ->orderBy('c.course_code', 'asc')
            ->get();

        return view('home', compact('courses', 'selectedYear'));
    }

    public function formdata(Request $request)
    {
        $user = Auth::user();
        $currentYearBE = now()->year + 543;
        $targetYears = [$currentYearBE, $currentYearBE + 1, $currentYearBE - 1];

        $plos = collect([]); 

        if ($request->has('CC_id')) {
            // หาปีหลักสูตร (Curriculum Year) จาก CC_id ที่ส่งมา
            $curriculumYear = DB::table('curriculum_courses as cc')
                ->join('curriculum as cur', 'cc.curriculum_id', '=', 'cur.id')
                ->where('cc.id', $request->CC_id)
                ->value('cur.curriculum_year');

            // ถ้าพบปีหลักสูตร ให้ดึง PLO ที่เป็นของปีนั้นๆ
            if ($curriculumYear) {
                $plos = DB::table('plos')
                    ->where('curriculum_year_ref', $curriculumYear)
                    ->orderBy('plo', 'asc')
                    ->get();
            }
        } else {
            // กรณีเข้ามาครั้งแรก (ยังไม่มี CC_id) ปล่อยว่างไว้
            $plos = collect([]);
        }

        // ดึงรายวิชา (Courses) พร้อมรายละเอียดและหลักสูตร
        // เอา Prompt ล่าสุดที่เคยเจนไว้ (ถ้ามี)
        $latestPrompts = DB::table('prompts as p1')
            ->select('p1.courseyear_id_ref', 'p1.course_text')
            ->whereRaw('p1.updated_at = (select max(p2.updated_at) from prompts as p2 where p2.courseyear_id_ref = p1.courseyear_id_ref)')
            ->groupBy('p1.courseyear_id_ref', 'p1.course_text');

        $courseOptions = DB::table('courseyears as cy')
            ->join('curriculum_courses as cc', 'cy.CC_id', '=', 'cc.id')
            ->join('courses as c', 'cc.course_code', '=', 'c.id') 
            ->join('curriculum as cur', 'cc.curriculum_id', '=', 'cur.id')
            ->join('users as u', 'u.user_id', '=', 'cy.user_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.courseyear_id_ref')
            ->leftJoinSub($latestPrompts, 'lp', function($join) {
                $join->on('cy.id', '=', 'lp.courseyear_id_ref');
            })
            ->where('cy.user_id', $user->user_id)
            ->whereIn('cy.year', $targetYears)
            ->select(
                'c.id as course_pk',
                'cc.id as cc_id',
                'c.course_code',
                'c.course_name_th',
                'c.course_name_en',
                'c.course_detail_th',
                'c.course_detail_en',
                'cy.year',
                'cy.term',
                'cy.TQF',
                'cur.curriculum_year',
                'lp.course_text',
                'lp.courseyear_id_ref'
            )
            ->orderBy('cy.year', 'desc')
            ->orderBy('cy.term', 'asc')
            ->get(); 

        return view('form', [
            'course_options' => $courseOptions,
            'plos' => $plos
        ]);
    }

    public function updateStartPrompt(Request $request)
    {
        $courseId = $request->course_code;
        $user = Auth::user();

        if (!$courseId || !$request->year || !$request->term || !$request->TQF) {
            return response()->json(['error' => 'course_code, year, term, and TQF are required'], 400);
        }

        // แก้ไข: ค้นหาโดยวิ่งผ่านตารางกลาง (curriculum_course)
        $cy = Courseyears::whereHas('curriculum_course', function($q) use ($courseId) {
                $q->where('course_code', $courseId);
            })
            ->where('user_id', $user->user_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->first();

        if ($cy) {
            DB::table('statuses')
                ->updateOrInsert(
                    ['courseyear_id_ref' => $cy->id],
                    ['startprompt' => now(), 'updated_at' => now()]
                );
        }

        return response()->json(['message' => 'startprompt updated']);
    }

    public function getPrompt(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'CC_id' => 'required|integer', 
            'year'  => 'required|integer',
            'term'  => 'required|string',
            'TQF'   => 'required|string',
        ]);

        $cy = Courseyears::with('curriculum_course.course')
            ->where('CC_id', $request->CC_id)
            ->where('user_id', $user->user_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->first();

        if (!$cy) {
            return response()->json(['message' => 'ไม่พบข้อมูลรายวิชา', 'prompt' => ''], 404);
        }

        // อัปเดตสถานะ startprompt
        DB::table('statuses')->updateOrInsert(
            ['courseyear_id_ref' => $cy->id],
            ['startprompt' => now(), 'updated_at' => now()]
        );

        // Prompt ที่เคย Save (ถ้ามี)
        $savedPrompt = DB::table('prompts')->where('courseyear_id_ref', $cy->id)->value('course_text');
        
        if ($savedPrompt) {
            return response()->json(['prompt' => $savedPrompt]);
        }

        // ถ้าไม่มี -> ดึง Course Detail จาก Master Data
        $courseDetail = '';
        if ($cy->curriculum_course && $cy->curriculum_course->course) {
             $c = $cy->curriculum_course->course;
             $courseDetail = $c->course_detail_th ?? $c->course_detail_en ?? '';
        }

        return response()->json(['prompt' => $courseDetail]);
    }

    public function savePrompt(Request $request)
    {
        // Validate ข้อมูล
        $request->validate([
            'CC_id'  => 'required',
            'year'   => 'required',
            'term'   => 'required',
            'TQF'    => 'required',
            'prompt' => 'required|string',
        ]);

        $user = Auth::user();

        // หา ID ของ Courseyears
        $cy = Courseyears::where('CC_id', $request->CC_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->first();

        if (!$cy) {
            return response()->json([
                'message' => 'ไม่พบข้อมูลรายวิชาในฐานข้อมูล (Course Data Mismatch)',
                'debug_received' => $request->only(['CC_id', 'year', 'term', 'TQF']),
            ], 404);
        }

        if ($cy->user_id != $user->user_id) { 
            return response()->json([
                'message' => 'รายวิชานี้ไม่ใช่ของคุณ (User Ownership Mismatch)',
                'db_owner' => $cy->user_id,
                'current_user' => $user->user_id,
            ], 403);
        }

        try {
            DB::beginTransaction();

            // บันทึก Prompt
            DB::table('prompts')->updateOrInsert(
                ['courseyear_id_ref' => $cy->id],
                [
                    'course_text' => $request->prompt,
                    'updated_at'  => now(),
                ]
            );

            DB::commit();

            return response()->json(['message' => 'บันทึกข้อมูลเรียบร้อยแล้ว']);

        } catch (\Exception $e) {
            DB::rollBack(); // ยกเลิกถ้ามี error
            return response()->json(['message' => 'Database Error: ' . $e->getMessage()], 500);
        }
    }
}
