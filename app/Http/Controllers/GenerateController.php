<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Plos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GenerateController extends Controller
{
    public function saveGeneratedText(Request $request)
    {
        $request->validate([
            'course_id' => 'required|string',
            'year' => 'required|integer',
            'term' => 'required|string',
            'TQF' => 'required|string',
            'ai_response' => 'required|string',
        ]);

        $user = Auth::user();

        $cy = DB::table('courseyears')
            ->where('course_id', $request->course_id)
            ->where('user_id', $user->user_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->first();

        if (!$cy) return response()->json(['message' => 'ไม่พบ courseyear'], 404);

        $promptRow = DB::table('prompts')
            ->where('ref_id', $cy->id)
            ->first();

        if (!$promptRow) return response()->json(['message' => 'ไม่พบ prompt'], 404);

        // Insert ลง database
        DB::table('generates')->insert([
            'ref_id' => $promptRow->ref_id,
            'ai_text' => $request->input('ai_response'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'บันทึก generated เรียบร้อยแล้ว']);

        return redirect()->route('preview', [
            'course_id' => $request->course_id,
            'year' => $request->year,
            'term' => $request->term,
            'TQF' => $request->TQF
        ]);
    }
}
