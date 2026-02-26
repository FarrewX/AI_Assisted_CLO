<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Models\Curriculum_llls;
use App\Models\Plos;

class Curriculum_lllsController  extends Controller
{
    public function index(Request $request)
    {
        $curriculum = Curriculum::orderBy('curriculum_year', 'desc')->get();
        $selectedYear = $request->input('year');
        $llls = [];
        $plos = []; 

        if ($selectedYear) {
            $llls = Curriculum_llls::where('curriculum_year_ref', $selectedYear)->orderBy('num_LLL', 'asc')->get();
            $plos = Plos::where('curriculum_year_ref', $selectedYear)->orderBy('plo', 'asc')->get();
        }

        return view('management.llls', compact('curriculum', 'selectedYear', 'llls', 'plos'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'curriculum_year_ref' => 'required',
                'num_LLL' => 'required|integer',
                'name_LLL' => 'required|string',
                'check_LLL' => 'nullable|array'
            ]);

            $curriculum = Curriculum::where('curriculum_year', $request->curriculum_year_ref)->first();

            $lll = new Curriculum_llls();
            $lll->curriculum_year_ref = $request->curriculum_year_ref;
            $lll->curriculum_id_ref = $curriculum->id;
            $lll->num_LLL = $request->num_LLL;
            $lll->name_LLL = $request->name_LLL;
            $lll->check_LLL = $request->has('check_LLL') ? implode(',', $request->check_LLL) : null;
            
            $lll->save();

            return response()->json(['success' => true, 'message' => 'เพิ่มข้อมูล LLL สำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'num_LLL' => 'required|integer',
                'name_LLL' => 'required|string',
                'check_LLL' => 'nullable|array'
            ]);

            $lll = Curriculum_llls::findOrFail($id);
            $lll->num_LLL = $request->num_LLL;
            $lll->name_LLL = $request->name_LLL;
            
            $lll->check_LLL = $request->has('check_LLL') ? implode(',', $request->check_LLL) : null;
            
            $lll->save();

            return response()->json(['success' => true, 'message' => 'อัปเดตข้อมูลสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $lll = Curriculum_llls::findOrFail($id);
            $lll->delete();

            return response()->json(['success' => true, 'message' => 'ลบข้อมูลสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'ลบข้อมูลไม่สำเร็จ'], 500);
        }
    }
}