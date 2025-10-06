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

        $selectedYear = $request->input('year', now()->year);

        $courses = collect(DB::table('courses as c')
            ->leftJoin('courseyears as cy', 'c.course_id', '=', 'cy.course_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
            ->where('cy.user_id', $user->user_id)
            ->where('cy.year', $selectedYear)
            ->select(
                'c.course_id',
                'c.course_name',
                'cy.year',
                'cy.term',
                'cy.clo',
                's.startprompt',
                's.generated',
                's.downloaded',
                's.success'
            )
            ->get());

        return view('home', compact('courses', 'user', 'selectedYear'));
    }

    public function formdata(Request $request)
    {
        $user = Auth::user();
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;

        // Subquery: เอา course_text ล่าสุดของแต่ละ cy.id
        $latestPrompts = DB::table('prompts as p1')
            ->select('p1.ref_id', 'p1.course_text')
            ->whereRaw('p1.updated_at = (select max(p2.updated_at) from prompts as p2 where p2.ref_id = p1.ref_id)')
            ->groupBy('p1.ref_id', 'p1.course_text');

        $courses = DB::table('courseyears as cy')
        ->join('users as u', 'u.user_id', '=', 'cy.user_id')
        ->join('courses as c', 'cy.course_id', '=', 'c.course_id')
        ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
        ->leftJoinSub($latestPrompts, 'lp', function($join) {
            $join->on('cy.id', '=', 'lp.ref_id');
        })
        ->where('u.user_id', $user->user_id)
        ->whereIn('cy.year', [$currentYear, $nextYear])
        ->where(function ($q) {
            $q->whereNull('s.startprompt')
            ->orWhereNull('s.generated')
            ->orWhereNull('s.downloaded')
            ->orWhereNull('s.success');
        })
        ->select(
            'c.course_id',
            'c.course_name',
            'c.course_detail_th',
            'cy.year',
            'cy.term',
            'cy.clo',
            's.startprompt',
            's.generated',
            's.downloaded',
            's.success',
            'lp.course_text',
            'lp.ref_id',
            
        )
        ->orderBy('cy.year', 'desc')
        ->orderBy('cy.term', 'asc')
        ->orderBy('cy.clo', 'asc')

        ->get();

        return $courses;
    }

    public function updateStartPrompt(Request $request)
    {
        $courseId = $request->course_id;
        $user = Auth::user();

        if (!$courseId || !$request->year || !$request->term || !$request->clo) {
            return response()->json(['error' => 'course_id, year, term, and clo are required'], 400);
        }

        $cy = Courseyears::where('course_id', $courseId)
            ->where('user_id', $user->user_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('clo', $request->clo)
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
            'course_id' => 'required|string',
            'year'      => 'required|integer',
            'term'      => 'required|string',
            'clo'       => 'required|string',
        ]);

        $cy = DB::table('courseyears')
            ->where('course_id', $request->course_id)
            ->where('user_id', $user->user_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('clo', $request->clo)
            ->first();

        if (!$cy) {
            return response()->json(['message' => 'ไม่พบข้อมูลรายวิชา'], 404);
        }

        // เช็คว่ามี prompt อยู่แล้วหรือยัง
        $prompt = DB::table('prompts')
            ->where('course_id', $request->course_id)
            ->where('ref_id', function($q) use ($user, $request) {
                $q->select('id')
                ->from('courseyears')
                ->where('course_id', $request->course_id)
                ->where('user_id', $user->user_id)
                ->where('year', $request->year)
                ->limit(1);
            })
            ->first();

        DB::table('statuses')->updateOrInsert(
            ['ref_id' => $cy->id],
            [
                'startprompt' => now(),
                'updated_at'  => now(),
            ]
        );
            
        if ($prompt) {
            return response()->json(['prompt' => $prompt->course_text]);
        }
                
        // ถ้าไม่มี → ดึงจาก courses
        $course = DB::table('courses')->where('course_id', $request->course_id)->first();

        return response()->json(['prompt' => $course->course_text ?? $course->course_detail_th ?? '']);
    }

    public function savePrompt(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|string',
            'year'      => 'required|integer',
            'term'      => 'required|string',
            'clo'       => 'required|string',
            'prompt'    => 'required|string',
        ]);

        $user = Auth::user();

        $cy = DB::table('courseyears')
            ->where('course_id', $data['course_id'])
            ->where('user_id', $user->user_id)
            ->where('year', $data['year'])
            ->where('term', $data['term'])
            ->where('clo', $data['clo'])
            ->first();

        if (!$cy) {
            return response()->json(['message' => 'ไม่พบข้อมูลรายวิชา'], 404);
        }

        DB::table('prompts')
            ->updateOrInsert(
                ['ref_id' => $cy->id, 'course_id' => $data['course_id']],
                ['course_text' => $data['prompt'], 'updated_at' => now()]
            );

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
