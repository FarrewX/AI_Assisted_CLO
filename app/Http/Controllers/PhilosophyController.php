<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Models\Philosophy;

class PhilosophyController extends Controller
{
    public function index(Request $request)
    {
        $curriculum = Curriculum::orderBy('curriculum_year', 'desc')->get();
        $selectedYear = $request->input('year');
        $philosophyData = null;

        if ($selectedYear) {
            $philosophyData = Philosophy::where('curriculum_year_ref', $selectedYear)->first();
        }

        return view('management.philosophy', compact('curriculum', 'selectedYear', 'philosophyData'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'mju_philosophy' => 'nullable|string',
            'education_philosophy' => 'nullable|string',
            'curriculum_philosophy' => 'nullable|string',
        ]);

        Philosophy::updateOrCreate(
            ['curriculum_year_ref' => $request->year],
            [
                'mju_philosophy' => $request->mju_philosophy,
                'education_philosophy' => $request->education_philosophy,
                'curriculum_philosophy' => $request->curriculum_philosophy,
            ]
        );

        return redirect()->route('philosophy.index', ['year' => $request->year])
            ->with('success', 'บันทึกข้อมูลปรัชญาสำเร็จเรียบร้อยแล้ว');
    }
}