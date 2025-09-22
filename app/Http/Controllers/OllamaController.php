<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OllamaController extends Controller
{
    public function generateText(Request $request)
    {
        $prompt = $request->input('prompt');
        $course = $request->input('course');

        $response = Http::post('http://localhost:11434/v1/completions', [
            'model' => 'elo_generator',
            'prompt' => 'ต้องการทำการสอนในวิชาเรียน'.$course.' โดยมีเนื้้อหาการเรียนดังนี้ '.$prompt.
                ' จงสร้างclo 3 ข้อ โดยแต่ละข้อมีรายละเอียดดังนี้ 1.clo ที่ได้มาตรงกับ PLOs ไหน 2.รายละเอียดของ clo 
                3.สอดคล้องกับหลักจริยธรรมไดบ้าง 4.การประเมินผลเป็นอย่างไร 5ตัวชี้วัดที่ใช้ในการประเมินผลการเรียนรู้',
        ]);
        
        $result = $response->json();

        return response()->json($response->json());
    }
}
