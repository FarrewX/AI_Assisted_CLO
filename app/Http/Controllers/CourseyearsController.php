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
        // 1. ดึงข้อมูลหลักสูตรทั้งหมด
        $curriculum = Curriculum::all(); 
        $users     = User::all();
        
        $courses = [];
        $professor = null;
        
        $curriculumId = $request->curriculum_id;
        $courseId = $request->course_id;

        // 2. ถ้าเลือกหลักสูตร -> ดึงรายวิชาที่ผูกกับหลักสูตรนั้น (ผ่านตารางกลาง curriculum_courses)
        if ($curriculumId) {
            $courses = Course::whereHas('curriculum_courses', function ($query) use ($curriculumId) {
                $query->where('curriculum_id', $curriculumId);
            })->get();
        }

        // 3. ถ้าเลือกทั้งหลักสูตรและวิชา -> ดึงข้อมูลการเปิดสอน (Courseyears)
        if ($curriculumId && $courseId) {
            $professor = Courseyears::with('user')
                // Query วิ่งผ่าน CC_id ไปหาตารางกลาง เพื่อเช็ค course_id และ curriculum_id
                ->whereHas('curriculum_course', function ($query) use ($courseId, $curriculumId) {
                    $query->where('course_id', $courseId)
                          ->where('curriculum_id', $curriculumId);
                })
                ->orderBy('year', 'desc')
                ->get();
        }

        // ส่งตัวแปรกลับไปที่ View
        return view('management.course', compact('curriculum', 'courses', 'users', 'professor', 'courseId', 'curriculumId'));
    }

    public function store(Request $request, $courseId)
    {
        // 1. ตรวจสอบข้อมูล (Validate)
        // ต้องแก้ชื่อตารางให้ถูก: exists:ชื่อตาราง,ชื่อคอลัมน์
        $request->validate([
            'curriculum_id' => 'required|exists:curriculum,id', // เช็คว่ามีหลักสูตรนี้จริง
            'user_id'       => 'required|exists:users,user_id', // เช็คว่ามี user_id นี้ในตาราง users
            'term'          => 'required',
            'TQF'           => 'required',
            'year'          => 'required|numeric',
        ], [
            'term.required' => 'กรุณากรอกภาคเรียน',
            'TQF.required'  => 'กรุณากรอกมคอ.',
        ]);

        // 2. แปลงปี ค.ศ. -> พ.ศ. (Logic ใหม่)
        $yearBE = $request->year;
        if ($yearBE < 2400) {
            $yearBE += 543;
        }

        // 3. ค้นหา CC_id จาก course_id และ curriculum_id (Logic ที่ถูกต้อง)
        // เปลี่ยนจาก latest() เป็นการระบุ curriculum_id เพื่อให้ได้คู่ที่ถูกต้องแน่นอน
        $curriculumCourse = Curriculum_course::where('course_id', $courseId)
            ->where('curriculum_id', $request->curriculum_id)
            ->first();

        if (!$curriculumCourse) {
            return back()->with('error', 'ไม่พบรายวิชานี้ในหลักสูตรที่เลือก (กรุณาตรวจสอบว่าเลือกหลักสูตรถูกต้องหรือไม่)');
        }

        // 4. ตรวจสอบข้อมูลซ้ำ โดยใช้ CC_id และ ปีที่แปลงเป็น พ.ศ. แล้ว
        $duplicateUser = Courseyears::where('CC_id', $curriculumCourse->id)
            ->where('year', $yearBE)  // เช็คด้วยปี พ.ศ.
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            // ->where('user_id', $request->user_id) // (Optional) ถ้าอยากเช็คว่าอาจารย์คนเดิมห้ามสอนซ้ำ ให้เปิดบรรทัดนี้
            ->exists();

        if ($duplicateUser) {
            return back()->with('error', 'อาจารย์ท่านอื่นมีข้อมูลใน ปี/เทอม/มคอ นี้แล้ว');
        }

        // 5. บันทึกข้อมูล
        Courseyears::create([
            'user_id' => $request->user_id,
            'CC_id'   => $curriculumCourse->id,
            'year'    => $yearBE, // บันทึกเป็น พ.ศ.
            'term'    => $request->term,
            'TQF'     => $request->TQF,
        ]);

        return back()->with('success', 'เพิ่มอาจารย์และข้อมูลปีการศึกษาสำเร็จ');
    }

    public function update(Request $request, $courseId, $id)
    {
        // 1. Validate ข้อมูล (ต้องเช็ค curriculum_id และ user_id ให้ถูกต้อง)
        $request->validate([
            'curriculum_id' => 'required|exists:curriculum,id',
            'user_id'       => 'required|exists:users,user_id',
            'term'          => 'required',
            'TQF'           => 'required',
            'year'          => 'required|numeric',
        ]);

        $record = Courseyears::findOrFail($id);

        // 2. ตรวจสอบความถูกต้อง (Integrity Check)
        // เช็คว่า Record นี้เป็นของ หลักสูตร และ วิชา ที่เรากำลังดูอยู่จริงไหม
        $relation = Curriculum_course::find($record->CC_id);
        
        if (!$relation || 
            $relation->course_id != $courseId || 
            $relation->curriculum_id != $request->curriculum_id) {
            return back()->with('error', 'ผิดพลาด: ข้อมูลที่แก้ไขไม่ตรงกับหลักสูตรหรือวิชาปัจจุบัน');
        }

        // 3. แปลงปี ค.ศ. -> พ.ศ.
        $yearBE = $request->year;
        if ($yearBE < 2400) {
            $yearBE += 543;
        }

        // 4. ตรวจสอบข้อมูลซ้ำ (Duplicate Check) โดยไม่นับตัวเอง
        $duplicate = Courseyears::where('CC_id', $record->CC_id)
            ->where('year', $yearBE)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->where('id', '!=', $id) // สำคัญ: ไม่นับตัวเอง
            ->exists();

        if ($duplicate) {
            return back()->with('error', 'ไม่สามารถแก้ไขได้ เนื่องจากมีข้อมูล ปี/เทอม/มคอ. นี้อยู่แล้ว');
        }

        // 5. บันทึกการแก้ไข
        $record->user_id = $request->user_id;
        $record->year    = $yearBE;
        $record->term    = $request->term;
        $record->TQF     = $request->TQF;
        $record->save();

        return back()->with('success', 'อัพเดทข้อมูลสำเร็จ');
    }

    public function destroy($courseId, $id)
    {
        $record = Courseyears::findOrFail($id);

        // ตรวจสอบก่อนลบว่าข้อมูลตรงกับวิชาที่ส่งมาไหม (กันการลบผิดตัว)
        $relation = Curriculum_course::find($record->CC_id);
        if ($relation && $relation->course_id != $courseId) {
             return back()->with('error', 'เกิดข้อผิดพลาด: ไม่สามารถลบข้อมูลข้ามรายวิชาได้');
        }

        $record->delete();
        return back()->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
