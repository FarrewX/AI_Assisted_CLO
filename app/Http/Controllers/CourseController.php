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
        $selectedYear = $request->input('year', date('Y')); 

        $courses = DB::table('courseyears as cy')
            ->join('curriculum_courses as cc', 'cy.CC_id', '=', 'cc.id')
            ->join('courses as c', 'cc.course_id', '=', 'c.id') 
            ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
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
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;

        // Subquery: เอา course_text ล่าสุดของแต่ละ ref_id (courseyears.id)
        $latestPrompts = DB::table('prompts as p1')
            ->select('p1.ref_id', 'p1.course_text')
            ->whereRaw('p1.updated_at = (select max(p2.updated_at) from prompts as p2 where p2.ref_id = p1.ref_id)')
            ->groupBy('p1.ref_id', 'p1.course_text');

        $courses = DB::table('courseyears as cy')
            // แก้ไข: Join ผ่านตารางกลาง curriculum_courses
            ->join('curriculum_courses as cc', 'cy.CC_id', '=', 'cc.id')
            ->join('courses as c', 'cc.course_code', '=', 'c.course_code')
            ->join('users as u', 'u.user_id', '=', 'cy.user_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
            ->leftJoinSub($latestPrompts, 'lp', function($join) {
                $join->on('cy.id', '=', 'lp.ref_id');
            })
            ->where('u.user_id', $user->user_id)
            ->whereIn('cy.year', [$currentYear, $nextYear])
            ->where(function ($q) {
                $q->whereNull('s.startprompt')
                ->orWhereNull('s.generated')
                ->orWhereNull('s.success');
            })
            ->select(
                'c.course_code',
                'c.course_name_th',
                'c.course_detail_th',
                'cy.year',
                'cy.term',
                'cy.TQF',
                's.startprompt',
                's.generated',
                's.success',
                'lp.course_text',
                'lp.ref_id'
            )
            ->orderBy('cy.year', 'desc')
            ->orderBy('cy.term', 'asc')
            ->orderBy('cy.TQF', 'asc')
            ->get();

        return $courses;
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
                    ['ref_id' => $cy->id],
                    ['startprompt' => now(), 'updated_at' => now()]
                );
        }

        return response()->json(['message' => 'startprompt updated']);
    }

    public function getPrompt(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'course_code' => 'required|string',
            'year'      => 'required|integer',
            'term'      => 'required|string',
            'TQF'       => 'required|string',
        ]);

        // 1. หา Courseyears โดยวิ่งผ่านตารางกลาง
        // ใช้ with เพื่อดึงข้อมูล Course Detail มาสำรองไว้เลย
        $cy = Courseyears::with('curriculum_course.course')
            ->whereHas('curriculum_course', function($q) use ($request) {
                $q->where('course_code', $request->course_code);
            })
            ->where('user_id', $user->user_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->first();

        if (!$cy) {
            return response()->json(['message' => 'ไม่พบข้อมูลรายวิชา'], 404);
        }

        // 2. เช็คว่ามี prompt ที่เคย Save ไว้ไหม (ในตาราง prompts)
        $prompt = DB::table('prompts')
            ->where('ref_id', $cy->id) // ใช้ ref_id (id ของ courseyears) เป็นหลัก
            ->first();

        // อัปเดตสถานะว่าเริ่มทำแล้ว
        DB::table('statuses')->updateOrInsert(
            ['ref_id' => $cy->id],
            [
                'startprompt' => now(),
                'updated_at'  => now(),
            ]
        );
            
        // กรณี 1: มี Prompt ที่เคย Save ไว้ -> ส่งกลับไป
        if ($prompt) {
            return response()->json(['prompt' => $prompt->course_text]);
        }
                
        // กรณี 2: ยังไม่มี -> ดึงรายละเอียดวิชาจาก Master Data (table courses)
        // เราดึงมาแล้วผ่าน Relation ข้างบน ($cy->curriculum_course->course)
        $courseDetail = '';
        if ($cy->curriculum_course && $cy->curriculum_course->course) {
             // เช็คว่ามี detail_th หรือ detail_en
             $c = $cy->curriculum_course->course;
             $courseDetail = $c->course_detail_th ?? $c->course_detail_en ?? '';
        }

        return response()->json(['prompt' => $courseDetail]);
    }

    public function savePrompt(Request $request)
    {
        $data = $request->validate([
            'course_code' => 'required|string',
            'year'      => 'required|integer',
            'term'      => 'required|string',
            'TQF'       => 'required|string',
            'prompt'    => 'required|string',
        ]);

        $user = Auth::user();

        // 1. หา Courseyears เพื่อเอา ID (ref_id)
        $cy = Courseyears::whereHas('curriculum_course', function($q) use ($data) {
                $q->where('course_code', $data['course_code']);
            })
            ->where('user_id', $user->user_id)
            ->where('year', $data['year'])
            ->where('term', $data['term'])
            ->where('TQF', $data['TQF'])
            ->first();

        if (!$cy) {
            return response()->json(['message' => 'ไม่พบข้อมูลรายวิชา'], 404);
        }

        // 2. บันทึก Prompt
        DB::table('prompts')
            ->updateOrInsert(
                ['ref_id' => $cy->id], // เงื่อนไขการค้นหา
                [
                    'course_code'   => $data['course_code'], // เก็บไว้เผื่อดูง่ายๆ
                    'course_text' => $data['prompt'],
                    'updated_at'  => now()
                ]
            );

        // 3. อัปเดตสถานะ generated
        DB::table('statuses')->updateOrInsert(
            ['ref_id' => $cy->id],
            [
                'generated'  => now(),
                'updated_at' => now(),
            ]
        );
            
        return response()->json(['message' => 'Prompt saved successfully']);
    }
}
