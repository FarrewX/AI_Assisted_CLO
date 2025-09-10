<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $courses = DB::table('courses')
            ->leftJoin('statuses', 'courses.course_id', '=', 'statuses.course_id')
            ->where('courses.user_id', $user->user_id)
            ->select(
                'courses.course_id',
                'courses.course_name',
                'statuses.startprompt',
                'statuses.generated',
                'statuses.downloaded',
                'statuses.success'
            )
            ->get();

        return view('home', compact('courses', 'user'));
    }
    public function formdata()
    {
        $user = Auth::user();

        $courses = DB::table('users as u')
            ->join('courses as c', 'u.user_id', '=', 'c.user_id')
            ->where('u.user_id', $user->user_id)
            ->select(
                'c.course_id',
                'c.course_name',
                'c.course_detail_th',
                'c.user_id',
            )
            ->get();

        return view('form', compact('courses'));
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
