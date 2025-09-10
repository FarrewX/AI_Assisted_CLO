<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        $courses = DB::table('courseyears as cy')
        ->join('users as u', 'u.user_id', '=', 'cy.user_id')
        ->join('courses as c', 'cy.course_id', '=', 'c.course_id')
        ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
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
            's.success'
        )
        ->orderBy('cy.year', 'asc')
        ->get();

        return view('form', compact('courses', 'currentYear', 'nextYear'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
    }
}
