<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plos;

class OllamaController extends Controller
{
    public function generateText(Request $request)
    {
        $prompt = $request->input('prompt');
        $course = $request->input('course');
        $num_clo = $request->input('numClo');
        $select_plo = $request->input('selectClo');

        $plo = Plos::all();

        $response = Http::post('http://localhost:11434/v1/completions', [
            'model' => 'elo_generator',
            'prompt' => 'ต่อจากนี้ให้ออกแบบ CLO ของหนึ่งรายวิชาให้สอดคล้องกับ PLO ของหลักสูตร โดยใช้ข้อมูลหลักสูตร:
                    ชื่อหลักสูตร: สาขาวิชาวิทยการคอมพิวเตอร์
                    รายละเอียด PLO ของหลักสูตร:
                    ' .$plo->map(function ($item) {
                            return $item->plo.': '.$item->description;
                        })->implode("\n").'
                    ใช้ข้อมูลรายวิชา:
                    ชื่อรายวิชา:'.$course.'
                    Course description:'.$prompt.'

                    เงื่อนไขการออกแบบ CLO:
                    - จำนวน CLO ที่ต้องการ:'.$num_clo.'
                    - เลือกให้ CLO เหล่านี้สอดคล้องกับ PLO เฉพาะที่ต้องการ:'. implode(', ', (array)$select_plo) .'
                    - รูปแบบการเขียน CLO: Action Verb - Object - Qualification Phase (ตามหลัก SMART)
                    - Learning’s Level ของ CLO สามารถเท่ากับหรือต่ำกว่า PLO ที่สอดคล้อง
                    - ต้องกำหนด Domain ของ CLO ให้ตรงกับ Domain ของ PLO
                    - ต้องเสนอ Assessment Method ที่เหมาะสมกับ Learning’s Level
                    - ต้องอธิบายเหตุผลประกอบการกำหนด Learning’s Level และ Assessment Method

                    **รูปแบบคำตอบที่ต้องการ:**
                    ตารางที่ประกอบด้วย:
                    | CLO | PLO ที่รองรับ | Domain | Learning’s Level | Assessment Method | เหตุผล |',
        ]);

        return response()->json($response->json());
    }
}
