<?php

namespace App\Http\Controllers;

use App\Models\Plos;
use App\Models\Curriculum;
use App\Http\Requests\StorePlosRequest;
use App\Http\Requests\UpdatePlosRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlosController extends Controller
{
    public function index(Request $request)
    {
        // ดึงรายชื่อหลักสูตรมาใส่ Dropdown (เรียงปีล่าสุดขึ้นก่อน)
        $curriculum = \App\Models\Curriculum::orderBy('curriculum_year', 'desc')->get();

        // รับค่า 'year' จาก Dropdown
        $selectedYear = $request->input('year'); 
        $plos = [];

        if ($selectedYear) {
            // Query โดยใช้ curriculum_year_ref แทน curriculum_id
            $plos = \App\Models\Plos::where('curriculum_year_ref', $selectedYear)
                        ->orderBy('plo', 'asc')
                        ->get();
        }

        // ส่งตัวแปร $selectedYear ไปที่ View แทน $curriculumId
        return view('management.plo', compact('curriculum', 'plos', 'selectedYear'));
    }

    public function update(Request $request, $id)
    {
        $plo = Plos::find($id);
        if (!$plo) {
            return response()->json(['message' => 'ไม่พบ PLO นี้'], 404);
        }

        $plo->plo = $request->plo;
        $plo->description = $request->description;
        $plo->domain = $request->domain;
        $plo->learning_level = $request->learning_level;
        $plo->save();

        return response()->json(['message' => 'อัพเดทสำเร็จ']);
    }

    public function create(Request $request)
    {
        // Validate ข้อมูล
        $request->validate([
            'curriculum_year_ref' => 'required', // ต้องส่งปีมาด้วย
            'description'         => 'required|string',
            'plo'                 => [
                'required', 
                'integer',
                // เช็คว่า PLO นี้ซ้ำหรือไม่ "เฉพาะในหลักสูตรปีเดียวกัน"
                Rule::unique('plos')->where(function ($query) use ($request) {
                    return $query->where('curriculum_year_ref', $request->curriculum_year_ref);
                })
            ],
        ], [
            'plo.unique' => 'เลข PLO นี้มีอยู่แล้วในหลักสูตรปีนี้',
        ]);

        $plo = new Plos();
        // บันทึกปีหลักสูตร (สำคัญมาก!)
        $plo->curriculum_year_ref = $request->curriculum_year_ref; 
        
        $plo->plo = $request->plo;
        $plo->description = $request->description;
        $plo->domain = $request->domain;
        $plo->learning_level = $request->learning_level;
        $plo->save();

        return response()->json(['message' => 'เพิ่ม PLO สำเร็จ']);
    }

    public function destroy($id)
    {
        $plo = Plos::find($id);
        
        if (!$plo) {
            return response()->json(['message' => 'ไม่พบ PLO นี้'], 404);
        }
        
        $plo->delete();
        
        return response()->json(['message' => 'ลบ PLO สำเร็จ']);
    }
}
