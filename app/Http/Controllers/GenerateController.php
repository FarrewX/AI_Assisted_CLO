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
        // validate incoming data
        $request->validate([
            'CC_id' => 'required', 
            'year' => 'required',
            'term' => 'required',
            'TQF' => 'required',
            'ai_response' => 'required|string',
        ]);

        $user = Auth::user();

        // หา ID ของ Courseyears
        $cy = DB::table('courseyears')
            ->where('CC_id', $request->CC_id) 
            ->where('user_id', $user->user_id)
            ->where('year', $request->year)
            ->where('term', $request->term)
            ->where('TQF', $request->TQF)
            ->first();

        // เช็คว่าเจอหรือไม่
        if (!$cy) {
            return response()->json([
                'message' => 'ไม่พบข้อมูลรายวิชา (Courseyear not found)',
                'debug' => $request->all()
            ], 404);
        }

        try {
            DB::beginTransaction();

            // บันทึก/อัปเดต table Generates
            DB::table('generates')->updateOrInsert(
                ['courseyear_id_ref' => $cy->id],
                [
                    'ai_text'    => $request->ai_response,
                    'updated_at' => now(),
                ]
            );

            // อัปเดต Status
            DB::table('statuses')->updateOrInsert(
                ['courseyear_id_ref' => $cy->id],
                [
                    'generated'  => now(),
                ]
            );

            DB::commit();

            // ส่ง Redirect
            return response()->json([
                'message'  => 'บันทึกเรียบร้อยแล้ว',
                'redirect' => route('editdoc', [
                    'CC_id' => $request->CC_id,
                    'year'      => $request->year,
                    'term'      => $request->term,
                    'TQF'       => $request->TQF
                ]),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // ส่ง Error Message กลับไปให้ JS เห็น
            return response()->json(['message' => 'Database Error: ' . $e->getMessage()], 500);
        }
    }
}
