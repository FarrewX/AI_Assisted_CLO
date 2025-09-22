<?php

namespace App\Http\Controllers;

use App\Models\Courseyears;
use App\Http\Requests\StoreCourseyearsRequest;
use App\Http\Requests\UpdateCourseyearsRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseyearsController extends Controller
{
   public function index(Request $request)
{
    $courses = Course::all();
    $users   = User::all();
    $teachers = null;
    $courseId = $request->course_id;

    if ($courseId) {
        $teachers = Courseyears::with('user')
            ->where('course_id', $courseId)
            ->get();
    }

    return view('setting.course', compact('courses', 'users', 'teachers', 'courseId'));
}

public function store(Request $request, $courseId)
{
    $exists = \App\Models\Courseyears::where('course_id', $courseId)
        ->where('year', $request->year)
        ->exists();

    if ($exists) {
        return back()->with('error', 'ปีนี้มีอาจารย์สอนอยู่แล้ว');
    }

    Courseyears::create([
        'user_id'   => $request->user_id,
        'course_id' => $courseId,
        'year'      => $request->year,
    ]);
    return back()->with('success', 'เพิ่มอาจารย์สำเร็จ');
}

public function update(Request $request, $courseId, $id)
{
    $record = Courseyears::findOrFail($id);
    $record->year = $request->year;
    if ($request->has('user_id')) {
        $record->user_id = $request->user_id;
    }
    $record->save();
    return back()->with('success', 'อัพเดทสำเร็จ');
}
}
