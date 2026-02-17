<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CurriculumController extends Controller
{
    public function index()
    {
        // ดึงข้อมูลเรียงจากปีล่าสุด
        $curricula = Curriculum::withTrashed()->orderBy('curriculum_year', 'desc')->paginate(10);
        return view('management.curriculum', compact('curricula'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'curriculum_year' => 'required|string|max:255',
            'curriculum_name' => 'required|string|max:255',
            'faculty'         => 'required|string|max:255',
            'major'           => 'required|string|max:255',
            'campus'          => 'required|string|max:255',
        ], [
            'curriculum_year.required' => 'กรุณากรอกปีหลักสูตร',
            'curriculum_name.required' => 'กรุณากรอกชื่อหลักสูตร',
            'faculty.required'         => 'กรุณากรอกชื่อคณะ',
            'major.required'           => 'กรุณากรอกสาขาวิชา',
            'campus.required'          => 'กรุณากรอกวิทยาเขต',
        ]);

        // 1. ค้นหาข้อมูลที่มีอยู่แล้ว (รวมถึงรายการที่ถูก Soft Delete ไปแล้วด้วย)
        // เช็คความซ้ำซ้อนจาก ปี และ ชื่อหลักสูตร
        $curriculum = Curriculum::withTrashed()
            ->where('curriculum_year', $request->curriculum_year)
            ->where('curriculum_name', $request->curriculum_name)
            ->first();

        if ($curriculum) {
            // 2. ถ้าเจอข้อมูลเดิม และข้อมูลนั้นถูกลบอยู่ (Soft Deleted)
            if ($curriculum->trashed()) {
                $curriculum->restore(); // กู้คืนข้อมูล
                $curriculum->update($request->all()); // อัปเดตข้อมูลใหม่ทับลงไป
                return back()->with('success', 'กู้คืนหลักสูตรเดิมและอัปเดตข้อมูลใหม่เรียบร้อยแล้ว');
            }

            // 3. ถ้าเจอข้อมูลเดิม แต่ข้อมูลนั้น "ไม่ได้ถูกลบ" (Active อยู่แล้ว)
            return back()->with('error', 'มีหลักสูตรนี้อยู่ในระบบแล้วและกำลังใช้งานอยู่');
        }

        // 4. ถ้าไม่พบข้อมูลเลย ให้สร้างใหม่ตามปกติ
        Curriculum::create($request->all());

        return back()->with('success', 'เพิ่มหลักสูตรใหม่เรียบร้อยแล้ว');
    }

    public function update(Request $request, $id)
    {
        $curriculum = Curriculum::findOrFail($id);

        $request->validate([
            'curriculum_year' => 'required|numeric',
            'curriculum_name' => 'required|string',
            'faculty'         => 'required|string',
            'major'           => 'required|string',
            'campus'          => 'required|string',
        ]);

        try {
            // ถ้ามีการเปลี่ยนปีหลักสูตร (ค่าเก่า ไม่เท่ากับ ค่าใหม่)
            if ($request->curriculum_year != $curriculum->curriculum_year) {
                
                $existsInPlos = DB::table('plos')
                    ->where('curriculum_year_ref', $curriculum->curriculum_year)
                    ->exists();

                if ($existsInPlos) {
                    return back()->with('error', 'ไม่สามารถเปลี่ยนปีหลักสูตรได้ เนื่องจากมีข้อมูล PLO เชื่อมโยงกับปีนี้อยู่');
                }
            }
            // ถ้าไม่มีปัญหาเรื่องความซ้ำซ้อน ให้ทำการอัปเดตข้อมูล
            $curriculum->update([
                'curriculum_year' => $request->curriculum_year,
                'curriculum_name' => $request->curriculum_name,
                'faculty'         => $request->faculty,
                'major'           => $request->major,
                'campus'          => $request->campus,
            ]);

            return back()->with('success', 'แก้ไขหลักสูตรเรียบร้อยแล้ว');

        } catch (QueryException $e) {
            // Error code 23000 คือ Integrity constraint violation (Foreign Key fail)
            if ($e->getCode() == '23000') {
                return back()->with('error', 'ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากปีหลักสูตรนี้ถูกใช้งานอยู่ในตารางอื่น (PLO หรือ รายวิชา)');
            }

            return back()->with('error', 'เกิดข้อผิดพลาดฐานข้อมูล: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $curriculum = Curriculum::findOrFail($id);

        $curriculum->delete();

        return back()->with('success', 'ลบหลักสูตรเรียบร้อยแล้ว');
    }
}