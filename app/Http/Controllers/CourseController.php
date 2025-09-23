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

        $courses = DB::table('courses as c')
            ->leftJoin('courseyears as cy', 'c.course_id', '=', 'cy.course_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
            ->where('cy.user_id', $user->user_id)
            ->where('cy.year', $selectedYear)
            ->select(
                'c.course_id',
                'c.course_name',
                's.startprompt',
                's.generated',
                's.downloaded',
                's.success'
            )
            ->get();

        return view('home', compact('courses', 'user', 'selectedYear'));
    }

    public function formdata()
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
            's.startprompt',
            's.generated',
            's.downloaded',
            's.success',
            'lp.course_text'
        )
        ->orderBy('cy.year', 'asc')
        ->get();

        return $courses;
    }

    public function updateStartPrompt(Request $request)
    {
        $courseId = $request->course_id;
        $user = Auth::user();

        if (!$courseId) {
            return response()->json(['error' => 'course_id is required'], 400);
        }

        $cy = Courseyears::where('course_id', $courseId)
            ->where('user_id', $user->user_id)
            ->orderByDesc('year')
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

    public function savePrompt(Request $request)
    {
        $data = $request->json()->all();

        $validator = Validator::make($data, [
            'course_id' => 'required',
            'prompt' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'ข้อมูลไม่ถูกต้อง'], 422);
        }

        $user = Auth::user();
        $cy = DB::table('courseyears')
            ->where('course_id', $data['course_id'])
            ->where('user_id', $user->user_id)
            ->orderByDesc('year')
            ->first();

        if (!$cy) {
            return response()->json(['message' => 'ไม่พบข้อมูลรายวิชา'], 404);
        }

        DB::table('prompts')->insert([
            'ref_id'     => $cy->id,
            'course_id'  => $data['course_id'],
            'course_text'=> $data['prompt'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('statuses')->updateOrInsert(
            ['ref_id' => $cy->id],
            [
                'generated'  => now(),
                'updated_at' => now(),
            ]
        );
    }

}
