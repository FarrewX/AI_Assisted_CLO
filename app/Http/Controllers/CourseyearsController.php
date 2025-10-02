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
use Psy\Command\WhereamiCommand;

class CourseyearsController extends Controller
{
   public function index(Request $request)
    {
        $courses = Course::all();
        $users   = User::all();
        $professor = null;
        $courseId = $request->course_id;

        if ($courseId) {
            $professor = Courseyears::with('user')
                ->where('course_id', $courseId)
                ->Where('year', '>=', date('Y'))
                ->orderBy('year', 'desc')
                ->get();
        }

        return view('setting.course', compact('courses', 'users', 'professor', 'courseId'));
    }

    public function store(Request $request, $courseId)
    {
        // เช็คcourse + year + term + clo
        $exists = Courseyears::where('course_id', $courseId)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('clo', $request->clo)
            ->where('user_id', $request->user_id)
            ->doesntExist();

        $duplicateUser = Courseyears::where('course_id', $courseId)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('clo', $request->clo)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($duplicateUser) {
            return back()->with('error', 'อาจารย์คนนี้มีข้อมูลในปี/เทอมนี้แล้ว');
        }

        Courseyears::create([
            'user_id'   => $request->user_id,
            'course_id' => $courseId,
            'year'      => $request->year,
            'term'      => $request->term,
            'clo'       => $request->clo,
        ]);

        return back()->with('success', 'เพิ่มอาจารย์สำเร็จ');
    }

    public function update(Request $request, $courseId, $id)
    {
        // ตรวจสอบเฉพาะ user ซ้ำใน same course + year + term + clo
        $duplicateUser = Courseyears::where('course_id', $courseId)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('clo', $request->clo)
            ->where('user_id', $request->user_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($duplicateUser) {
            return back()->with('error', 'อาจารย์คนนี้มีข้อมูลใน ปี/เทอม/มคอ นี้แล้ว');
        }

        $record = Courseyears::findOrFail($id);

        $record->user_id = $request->user_id;
        $record->year    = $request->year;
        $record->term    = $request->term;
        $record->clo     = $request->clo;
        $record->save();

        return back()->with('success', 'อัพเดทสำเร็จ');
    }

    public function destroy($courseId, $id)
    {
        $record = Courseyears::findOrFail($id);
        $record->delete();

        return back()->with('success', 'ลบอาจารย์สำเร็จ');
    }
}
