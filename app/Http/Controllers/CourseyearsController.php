<?php

namespace App\Http\Controllers;

use App\Models\Courseyears;
use App\Http\Requests\StoreCourseyearsRequest;
use App\Http\Requests\UpdateCourseyearsRequest;
use App\Models\Course;
use App\Models\User;
use App\Models\Curriculum;
use App\Models\Curriculum_course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Psy\Command\WhereamiCommand;

class CourseyearsController extends Controller
{
   public function index(Request $request)
    {
        // 1. ดึงข้อมูลหลักสูตรทั้งหมดมาแสดงใน Dropdown แรก
        $curriculum = Curriculum::all();

        $courses = [];
        $professor = null;
        $users   = User::all();
        
        // รับค่าจาก Dropdown
        $curriculumId = $request->curriculum_id;
        $courseId = $request->course_id;

        // 2. ถ้ามีการเลือกหลักสูตรมาแล้ว ให้ไปดึงวิชา เฉพาะของหลักสูตรนั้น
        if ($curriculumId) {
            $courses = Course::whereHas('curriculum_courses', function ($query) use ($curriculumId) {
                $query->where('curriculum_id', $curriculumId);
            })->get();
        }

        // 3. ถ้ามีการเลือกวิชา (และต้องเลือกหลักสูตรแล้ว) ให้ดึงข้อมูลอาจารย์
        if ($curriculumId && $courseId) {
            $professor = Courseyears::with('user')
                ->whereHas('curriculum_course', function ($query) use ($courseId, $curriculumId) {
                    $query->where('course_id', $courseId)
                        ->where('curriculum_id', $curriculumId); // เช็คทั้งคู่เพื่อความชัวร์
                })
                ->where('year', '>=', date('Y'))
                ->orderBy('year', 'desc')
                ->get();
        }

        return view('management.course', compact('curriculum', 'courses', 'users', 'professor', 'courseId', 'curriculumId'));
    }

    public function store(Request $request, $courseId)
    {
        if (empty($request->term) || empty($request->TQF)) {
            return back()->with('error', 'กรุณากรอกภาคเรียนและมคอก่อนเพิ่มอาจารย์');
        }

        // 1. ค้นหา CC_id จาก course_id ที่ส่งมา
        // หมายเหตุ: ใช้ latest() เพื่อเลือกหลักสูตรล่าสุด ในกรณีที่วิชานี้อยู่ในหลายหลักสูตร
        $curriculumCourse = Curriculum_course::where('course_id', $courseId)->latest()->first();

        if (!$curriculumCourse) {
            return back()->with('error', 'ไม่พบรายวิชานี้ในหลักสูตรใดๆ กรุณาเพิ่มวิชาลงหลักสูตรก่อน');
        }

        // 2. ตรวจสอบข้อมูลซ้ำ โดยใช้ CC_id แทน course_id
        $duplicateUser = Courseyears::where('CC_id', $curriculumCourse->id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->exists();

        if ($duplicateUser) {
            return back()->with('error', 'อาจารย์ท่านอื่นมีข้อมูลใน ปี/เทอม/มคอ นี้แล้ว');
        }

        // 3. บันทึกข้อมูล (เปลี่ยนจาก course_id เป็น CC_id)
        Courseyears::create([
            'user_id' => $request->user_id,
            'CC_id'   => $curriculumCourse->id, // บันทึกเป็น ID ของตารางกลาง
            'year'    => $request->year,
            'term'    => $request->term,
            'TQF'     => $request->TQF,
        ]);

        return back()->with('success', 'เพิ่มอาจารย์สำเร็จ');
    }

    public function update(Request $request, $courseId, $id)
    {
        // ค้นหา Record เดิมเพื่อเอา CC_id มาใช้เช็ค
        $record = Courseyears::findOrFail($id);

        // ตรวจสอบข้อมูลซ้ำ (เช็คจาก CC_id เดิมที่มีอยู่แล้ว)
        $duplicateUser = Courseyears::where('CC_id', $record->CC_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->where('id', '!=', $id) // ไม่นับตัวเอง
            ->exists();

        if ($duplicateUser) {
            return back()->with('error', 'อาจารย์ท่านอื่นมีข้อมูลใน ปี/เทอม/มคอ นี้แล้ว');
        }

        // อัพเดทข้อมูล
        $record->user_id = $request->user_id;
        $record->year    = $request->year;
        $record->term    = $request->term;
        $record->TQF     = $request->TQF;
        // หมายเหตุ: CC_id (course_id) ไม่ควรเปลี่ยนในการ edit อาจารย์ผู้สอน จึงไม่ต้อง update
        
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
