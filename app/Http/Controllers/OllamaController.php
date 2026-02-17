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
        $course = $request->input('coursename');
        $num_clo = $request->input('numClo');
        $select_plo = $request->input('ploLabels', []);

        $plo = Plos::all();

        $promptString = 'ต่อจากนี้ให้ออกแบบ CLO ของหนึ่งรายวิชาให้สอดคล้องกับ PLO ของหลักสูตร โดยใช้ข้อมูลหลักสูตร:
ชื่อหลักสูตร: สาขาวิชาวิทยการคอมพิวเตอร์
รายละเอียด PLO ของหลักสูตร:
[PLO ที่ให้ไปจะอยู่ในรูปแบบ: เนื้อหา + Domain + Learning Level]
' .$plo->map(function ($item) {
        return $item->plo.': '.$item->description.' + Domain: '.$item->domain.' + Learning Level: '.$item->learning_level;
    })->implode("\n").'
โดยใช้ข้อมูลรายวิชาดังนี้
ชื่อรายวิชา:'.$course.'
Course description:'.$prompt.'

เงื่อนไขการออกแบบ CLO:
- จำนวน CLO ที่ต้องการ:'.$num_clo.'
- เลือกให้ CLO เหล่านี้สอดคล้องกับ PLO เฉพาะที่ต้องการ: โดยจะมี'. implode(', ', (array)$select_plo) .'
- รูปแบบการเขียน CLO: Action Verb - Object - Qualification Phase (ตามหลัก SMART)
- Learning’s Level ของ CLO สามารถเท่ากับหรือต่ำกว่า PLO ที่สอดคล้อง
- ต้องกำหนด Domain ของ CLO ให้ตรงกับ Domain ของ PLO
- ต้องเสนอ Assessment Method ที่เหมาะสมกับ Learning’s Level
- ต้องอธิบายเหตุผลประกอบการกำหนด Learning’s Level และ Assessment Method

**รูปแบบคำตอบที่ต้องการ:**
json ที่ประกอบด้วย:
"CLO 1" : {
    CLO : (เป็นคำอธิบาย CLO)
    PLO ที่รองรับ : (CLO นี้สอดคล้องกับ PLO ไหน ให้เอามา 1 PLO เอาเฉพาะ PLO ที่เป็นตัวเลขเท่านั้น เช่น PLO1 และเลือกมาจาก"โดยจะมี"ที่ระบุไว้ข้างต้นเท่านั้น)
    Domain : (Domain ของ CLO ให้เอามาจาก [PLO ที่ให้ไปจะอยู่ในรูปแบบ: เนื้อหา + Domain + Learning Level] ที่เป็น Domain อันที่มีข้อมูล Skills Ethics Characters Knowledge)
    Learning’s Level : (Learning Level ของ CLO ให้เอามาจาก [PLO ที่ให้ไปจะอยู่ในรูปแบบ: เนื้อหา + Domain + Learning Level] ที่เป็น Learning Level อันที่มีข้อมูล Precising Characterizing Valuating Evaluating Articulation)
    Assessment Method : (วิธีการประเมินผล)
    เหตุผล : (เหตุผลประกอบการกำหนด Learning’s Level และ Assessment Method)
}, ... (จำนวน CLO ตามที่ระบุ)
';

        $response = Http::post('http://localhost:11434/v1/completions', [
            'model' => 'elo_generator',
            'prompt' => $promptString,
        ]);

        return response()->json([
            'response' => $response->json(),
            'course_text' => $prompt,
            'course_name_th' => $course,
            'num_clo' => $num_clo,
            'select_plo' => $select_plo,
            'prompt_string' => $promptString,
        ]);
    }
}
