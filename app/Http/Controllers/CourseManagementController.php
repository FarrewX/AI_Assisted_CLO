<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Curriculum; // อย่าลืม Model นี้
use App\Models\Curriculum_course; // อย่าลืม Model นี้
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CourseManagementController extends Controller
{
    public function index(Request $request)
    {
        // 1. ดึงรายชื่อหลักสูตรทั้งหมดมาใส่ Dropdown
        $curricula = Curriculum::orderBy('curriculum_year', 'desc')->get();
        
        $selectedCurriculumId = $request->curriculum_id;
        $courses = [];

        // 2. ถ้ามีการเลือกหลักสูตรมาแล้ว ให้ดึงวิชาของหลักสูตรนั้น
        if ($selectedCurriculumId) {
            $courses = Course::whereHas('curriculum_courses', function ($query) use ($selectedCurriculumId) {
                $query->where('curriculum_id', $selectedCurriculumId);
            })->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('management.addcourse', compact('curricula', 'courses', 'selectedCurriculumId'));
    }

    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'curriculum_id'    => 'required|exists:curriculum,id',
            'course_code'      => 'required|string', 
            'course_name_th'   => 'required|string',
            'course_name_en'   => 'required|string',
            'course_detail_th' => 'required|string',
            'course_detail_en' => 'required|string',
            'credit'           => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 2. ค้นหาว่า "ในหลักสูตรที่เลือก (curriculum_id)" มีรหัสวิชานี้หรือยัง?
            // เราใช้ whereHas เพื่อกรองเฉพาะวิชาที่ผูกกับ curriculum_id ที่ส่งมาเท่านั้น
            $course = Course::where('course_code', $request->course_code)
                ->whereHas('curriculum_courses', function($q) use ($request) {
                    $q->where('curriculum_id', $request->curriculum_id);
                })
                ->withTrashed() // รวมที่ถูกลบไปแล้วด้วย
                ->first();

            if ($course) {
                // กรณี A: เจอวิชานี้ "ในหลักสูตรนี้" แล้ว
                if ($course->trashed()) {
                    // A1: ถ้าถูกลบอยู่ -> กู้คืนกลับมา (Restore) และอัปเดตข้อมูล
                    $course->restore();
                    $course->update([
                        'course_name_th'   => $request->course_name_th,
                        'course_name_en'   => $request->course_name_en,
                        'course_detail_th' => $request->course_detail_th,
                        'course_detail_en' => $request->course_detail_en,
                        'credit'           => $request->credit,
                    ]);
                    $message = 'กู้คืนรายวิชาในหลักสูตรนี้เรียบร้อยแล้ว';
                } else {
                    // A2: ถ้ามีอยู่แล้วและยังใช้งานอยู่ -> แจ้ง Error
                    return back()->withErrors(['course_code' => 'รหัสวิชานี้มีอยู่แล้วในหลักสูตรนี้']);
                }
            } else {
                // กรณี B: ยังไม่มีวิชานี้ "ในหลักสูตรนี้" (แม้ว่าจะมีรหัสนี้ในหลักสูตรอื่น ก็ถือว่าไม่เกี่ยวกัน)
                // -> สร้างใหม่ (Create New) เพื่อให้ได้ ID ใหม่ แยกอิสระจากหลักสูตรอื่น
                $course = Course::create([
                    'course_code'      => $request->course_code,
                    'course_name_th'   => $request->course_name_th,
                    'course_name_en'   => $request->course_name_en,
                    'course_detail_th' => $request->course_detail_th,
                    'course_detail_en' => $request->course_detail_en,
                    'credit'           => $request->credit,
                ]);

                // จับคู่เข้ากับหลักสูตร
                Curriculum_course::create([
                    'curriculum_id' => $request->curriculum_id,
                    'course_id'     => $course->id
                ]);

                $message = 'เพิ่มรายวิชาใหม่ในหลักสูตรนี้เรียบร้อยแล้ว (ข้อมูลแยกอิสระ)';
            }

            DB::commit();
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // 1. ค้นหาข้อมูล (รวมที่ถูกลบด้วย เผื่อกรณีแก้ข้อมูลที่ถูกลบ)
        $course = Course::withTrashed()->findOrFail($id);

        // 2. Validate ข้อมูล (เอา unique ออก)
        $validated = $request->validate([
            'curriculum_id'    => 'required|exists:curriculum,id', // ต้องส่ง curriculum_id มาด้วยเพื่อเช็คซ้ำ
            'course_code'      => 'required|string', 
            'course_name_th'   => 'required|string',
            'course_name_en'   => 'required|string',
            'course_detail_th' => 'required|string',
            'course_detail_en' => 'required|string',
            'credit'           => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 3. เช็คว่ารหัสวิชาใหม่ที่แก้ไข ไปซ้ำกับวิชาอื่น "ในหลักสูตรเดียวกัน" หรือไม่?
            // (ต้องไม่นับตัวเอง: where('id', '!=', $id))
            $duplicate = Course::where('course_code', $request->course_code)
                ->where('id', '!=', $id) // ไม่นับตัวเอง
                ->whereHas('curriculum_courses', function($q) use ($request) {
                    $q->where('curriculum_id', $request->curriculum_id);
                })
                ->exists();

            if ($duplicate) {
                return back()->withErrors(['course_code' => 'รหัสวิชานี้มีอยู่แล้วในหลักสูตรนี้ (กรุณาใช้รหัสอื่น)']);
            }

            // 4. ถ้าถูกลบอยู่ ให้กู้คืนก่อน
            if ($course->trashed()) {
                $course->restore();
            }

            // 5. อัปเดตข้อมูล
            $course->update([
                'course_code'      => $request->course_code,
                'course_name_th'   => $request->course_name_th,
                'course_name_en'   => $request->course_name_en,
                'course_detail_th' => $request->course_detail_th,
                'course_detail_en' => $request->course_detail_en,
                'credit'           => $request->credit,
            ]);

            DB::commit();
            return back()->with('success', 'อัปเดตรายวิชาเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาดในการอัปเดต: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        // 1. ค้นหาและลบ (Soft Delete)
        $course = Course::findOrFail($id);
        $course->delete();

        // 2. Redirect กลับไปหน้าเดิม พร้อม curriculum_id
        // ตรวจสอบชื่อ Route ให้แน่ใจว่าเป็น 'addcourse.list' หรือชื่อที่คุณตั้งไว้ใน web.php
        return redirect()->route('addcourse.list', ['curriculum_id' => $request->curriculum_id])
                         ->with('success', 'ลบรายวิชาเรียบร้อยแล้ว');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
            'curriculum_id' => 'required|exists:curriculum,id'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->path(), 'r');
        fgetcsv($handle); // ข้าม Header

        $importedData = []; // เก็บข้อมูลที่ถูก process (สร้างใหม่ หรือ กู้คืน)

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 2) continue;

                // 1. แปลง Encoding ภาษาไทย
                $row = array_map(function($text) {
                    return !mb_check_encoding($text, 'UTF-8') ? mb_convert_encoding($text, 'UTF-8', 'Windows-874') : $text;
                }, $row);

                $courseCode = $row[0];
                
                // 2. ค้นหา Course (รวมที่ถูก Soft Delete ไปแล้วด้วย)
                $course = Course::withTrashed()->where('course_code', $courseCode)->first();

                $isProcessed = false; // ตัวแปรเช็คว่ามีการเปลี่ยนแปลงไหม

                if ($course) {
                    // กรณี A: มีข้อมูลอยู่แล้ว
                    if ($course->trashed()) {
                        // A1. ถ้าถูกลบอยู่ (Soft Delete) -> กู้คืน และ อัปเดตข้อมูลใหม่
                        $course->restore();
                        $course->update([
                            'course_name_th'   => $row[1] ?? $course->course_name_th,
                            'course_name_en'   => $row[2] ?? $course->course_name_en,
                            'course_detail_th' => $row[3] ?? $course->course_detail_th,
                            'course_detail_en' => $row[4] ?? $course->course_detail_en,
                            'credit'           => $row[5] ?? $course->credit,
                        ]);
                        $isProcessed = true;
                    } 
                    // A2. ถ้ามีอยู่แล้วและยัง Active -> ไม่ทำอะไร (ข้ามการอัปเดต)
                } else {
                    // กรณี B: ไม่เคยมีมาก่อน -> สร้างใหม่
                    $course = Course::create([
                        'course_code'      => $courseCode,
                        'course_name_th'   => $row[1] ?? '',
                        'course_name_en'   => $row[2] ?? '',
                        'course_detail_th' => $row[3] ?? '',
                        'course_detail_en' => $row[4] ?? '',
                        'credit'           => $row[5] ?? null,
                    ]);
                    $isProcessed = true;
                }

                // 3. เชื่อมโยงกับหลักสูตรเสมอ (แม้ว่าจะเป็นวิชาที่มีอยู่แล้ว ก็ต้องจับคู่เข้าหลักสูตรนี้)
                Curriculum_course::firstOrCreate([
                    'curriculum_id' => $request->curriculum_id,
                    'course_id'     => $course->id
                ]);

                // 4. ถ้ามีการ สร้างใหม่ หรือ กู้คืน ให้เก็บลง Array เพื่อไปแสดงผล
                if ($isProcessed) {
                    $importedData[] = $course;
                }
            }
            
            DB::commit();
            fclose($handle);

            // ส่งข้อมูลกลับไปแสดงผล
            return back()
                ->with('success', 'นำเข้าข้อมูล CSV เรียบร้อยแล้ว')
                ->with('imported_courses', $importedData);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}