<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Curriculum_course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CourseManagementController extends Controller
{
    public function index(Request $request)
    {
        // ดึงรายชื่อหลักสูตรทั้งหมดมาใส่ Dropdown
        $curricula = Curriculum::orderBy('curriculum_year', 'desc')->get();
        
        $selectedCurriculumId = $request->curriculum_id;
        $courses = [];

        // ถ้ามีการเลือกหลักสูตรมาแล้ว ให้ดึงวิชาของหลักสูตรนั้น
        if ($selectedCurriculumId) {
            $courses = Course::whereHas('curriculum_courses', function ($query) use ($selectedCurriculumId) {
                $query->where('curriculum_id', $selectedCurriculumId);
            })->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('management.addcourse', compact('curricula', 'courses', 'selectedCurriculumId'));
    }

    public function store(Request $request)
    {
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
            // หา "ในหลักสูตรที่เลือก (curriculum_id)" มีรหัสวิชานี้หรือยัง?
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
                // กรณี B: ยังไม่มีวิชานี้ "ในหลักสูตรนี้"
                // -> สร้างใหม่ เพื่อให้ได้ ID ใหม่ แยกอิสระจากหลักสูตรอื่น
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
        // ค้นหาข้อมูล
        $course = Course::withTrashed()->findOrFail($id);

        // Validate ข้อมูล
        $validated = $request->validate([
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
            // เช็คว่ารหัสวิชาใหม่ที่แก้ไข ไปซ้ำกับวิชาอื่น "ในหลักสูตรเดียวกัน" หรือไม่?
            $duplicate = Course::where('course_code', $request->course_code)
                ->where('id', '!=', $id) // ไม่นับตัวเอง
                ->whereHas('curriculum_courses', function($q) use ($request) {
                    $q->where('curriculum_id', $request->curriculum_id);
                })
                ->exists();

            if ($duplicate) {
                return back()->withErrors(['course_code' => 'รหัสวิชานี้มีอยู่แล้วในหลักสูตรนี้ (กรุณาใช้รหัสอื่น)']);
            }

            // ถ้าถูกลบให้กู้คืนก่อน
            if ($course->trashed()) {
                $course->restore();
            }

            // อัปเดตข้อมูล
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
        // ค้นหาและลบ (Soft Delete)
        $course = Course::findOrFail($id);
        $course->delete();
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

        $importedData = []; // เก็บเฉพาะวิชาที่ "สร้างใหม่" เท่านั้น

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 2) continue;

                // แปลง Encoding
                $row = array_map(function($text) {
                    return !mb_check_encoding($text, 'UTF-8') ? mb_convert_encoding($text, 'UTF-8', 'Windows-874') : trim($text);
                }, $row);

                $csvCode = $row[0];
                $csvNameTh = $row[1] ?? '';
                $csvNameEn = $row[2] ?? '';
                $csvDetailTh = $row[3] ?? '';
                $csvDetailEn = $row[4] ?? '';
                $csvCredit = $row[5] ?? '';

                // สร้าง Fingerprint ของ Detail จาก CSV (ตามสูตร 1 เว้น 5)
                $fpDetailTh = $this->sampleText($csvDetailTh);
                $fpDetailEn = $this->sampleText($csvDetailEn);

                // ดึงวิชาที่มีรหัสนี้ทั้งหมดในระบบ (รวมที่ถูกลบไปแล้ว) มาเช็ค
                $existingCourses = Course::withTrashed()->where('course_code', $csvCode)->get();

                $foundExactMatch = false;
                $targetCourseId = null;

                foreach ($existingCourses as $existing) {
                    // เช็คค่าตรงๆ สำหรับ Name และ Credit
                    $matchNameTh = ($existing->course_name_th === $csvNameTh);
                    $matchNameEn = ($existing->course_name_en === $csvNameEn);
                    $matchCredit = ((string)$existing->credit === (string)$csvCredit);

                    // เช็คแบบสุ่มตัวอักษร สำหรับ Detail
                    $matchDetailTh = ($this->sampleText($existing->course_detail_th) === $fpDetailTh);
                    $matchDetailEn = ($this->sampleText($existing->course_detail_en) === $fpDetailEn);

                    // ถ้า "เหมือนกันทุกอย่าง" ให้ใช้อันเดิม
                    if ($matchNameTh && $matchNameEn && $matchCredit && $matchDetailTh && $matchDetailEn) {
                        $foundExactMatch = true;
                        
                        // ถ้าอันที่เหมือนกันถูกลบอยู่ ให้กู้คืน (แต่ไม่แก้อะไรเพิ่ม เพราะเหมือนกันแล้ว)
                        if ($existing->trashed()) {
                            $existing->restore();
                        }
                        
                        $targetCourseId = $existing->id;
                        break; // เจอตัวเหมือนแล้ว หยุดหา
                    }
                }

                // ถ้าไม่เจอตัวเหมือนเป๊ะ -> สร้างใหม่ (Create New)
                if (!$foundExactMatch) {
                    $newCourse = Course::create([
                        'course_code'      => $csvCode,
                        'course_name_th'   => $csvNameTh,
                        'course_name_en'   => $csvNameEn,
                        'course_detail_th' => $csvDetailTh,
                        'course_detail_en' => $csvDetailEn,
                        'credit'           => $csvCredit,
                    ]);
                    
                    $targetCourseId = $newCourse->id;
                    
                    // เก็บลง Array เพื่อแจ้งเตือนเฉพาะอันใหม่
                    $importedData[] = $newCourse; 
                }

                // เชื่อมโยงเข้าหลักสูตร
                Curriculum_course::firstOrCreate([
                    'curriculum_id' => $request->curriculum_id,
                    'course_id'     => $targetCourseId
                ]);
            }
            
            DB::commit();
            fclose($handle);

            return redirect()->back()
                ->with('success', 'นำเข้าข้อมูลเรียบร้อยแล้ว')
                ->with('imported_courses', $importedData); // ส่งเฉพาะวิชาที่สร้างใหม่ไปแสดง Popup

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    private function sampleText($text)
    {
        if (empty($text)) return '';
        
        $result = '';
        $length = mb_strlen($text, 'UTF-8');
        
        // เริ่มที่ 0, บวกทีละ 6 (เอาตัวที่ 1, ข้าม 2-6, เอาตัวที่ 7...)
        for ($i = 0; $i < $length; $i += 6) { 
            $result .= mb_substr($text, $i, 1, 'UTF-8');
        }
        
        return $result;
    }
}