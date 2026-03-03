<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plos;
use Illuminate\Support\Facades\DB;
use Illuminate\Log\log;

class OllamaController extends Controller
{
    public function generateText(Request $request)
    {
        $prompt = $request->input('prompt');
        $course = $request->input('coursename');
        $num_clo = $request->input('numClo');
        $select_plo = $request->input('ploLabels', []);

        $plo = Plos::all();

        $promptString = 'คุณเป็นผู้เชี่ยวชาญด้านการออกแบบหลักสูตรอุดมศึกษา (Higher Education Curriculum Design)  
ที่เข้าใจการทำงานแบบ Backward Curriculum Design และการเขียน CLO ให้สอดคล้องกับ PLO ของหลักสูตร
พร้อมกำหนด Learning’s Level, Domain, และ Assessment Method อย่างเหมาะสมตาม Bloom’s taxonomy / Learning Outcome taxonomy

ต่อจากนี้ให้ออกแบบ CLO ของหนึ่งรายวิชาให้สอดคล้องกับ PLO ของหลักสูตร โดยใช้ข้อมูลหลักสูตร:
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
            'max_tokens'  => 2048, // เทียบเท่า n_predict (จำนวนคำสูงสุดที่อนุญาตให้พิมพ์ตอบ)
            'temperature' => 0.7,  // ปรับความสุ่ม/ความคิดสร้างสรรค์
            'top_p'       => 0.9,  // ปรับความหลากหลายของคำ
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

    public function generateLessonPlanAI(Request $request)
    {
        try {
            set_time_limit(600);

            $CC_id = $request->input('CC_id');
            $weekCount = $request->input('weekCount');
            
            $rawClo = $request->input('cloData');
            $formattedClo = "";
            if (is_array($rawClo)) {
                foreach ($rawClo as $key => $details) {
                    $cloText = !empty($details['CLO']) ? $details['CLO'] : '-';
                    $plo     = !empty($details['PLO ต่อ ร้องรับ']) ? $details['PLO ต่อ ร้องรับ'] : (!empty($details['PLO']) ? $details['PLO'] : '-');
                    $domain  = !empty($details['Domain']) ? $details['Domain'] : '-';
                    $level   = !empty($details["Learning's Level"]) ? $details["Learning's Level"] : (!empty($details['Learning Level']) ? $details['Learning Level'] : '-');
                    
                    if (preg_match('/^(PLO\s*\d+)/i', $plo, $matches)) {
                        $plo = $matches[1];
                    }

                    $formattedClo .= "- {$key}: {$cloText} (อ้างอิง: {$plo}, โดเมน: {$domain}, ระดับ: {$level})\n            ";
                }
            } else {
                $formattedClo = "ไม่มีข้อมูล CLO";
            }

            $rawS6 = $request->input('section6Data');
            $formattedS6 = "";
            if (is_array($rawS6)) {
                foreach ($rawS6 as $key => $details) {
                    $teach = isset($details['วิธีการสอน']) ? implode(', ', $details['วิธีการสอน']) : '-';
                    $assess = isset($details['การประเมินผล']) ? implode(', ', $details['การประเมินผล']) : '-';
                    
                    $formattedS6 .= "- สำหรับ {$key} -> วิธีการสอน: {$teach} | การประเมิน: {$assess}\n            ";
                }
            } else {
                $formattedS6 = "ไม่มีข้อมูลวิธีการสอน";
            }

            $prompt = "
            ในฐานะผู้เชี่ยวชาญด้านการออกแบบหลักสูตรและการสอนแบบ Active Learning
            โปรดออกแบบแผนการสอนรายสัปดาห์จำนวน {$weekCount} สัปดาห์
            โดยอ้างอิงจาก ผลลัพธ์การเรียนรู้ (CLOs) และวิธีการสอน-การประเมิน ต่อไปนี้:
            
            [ข้อมูล CLO ของรายวิชา]:
            {$formattedClo}
            
            [วิธีการสอนและการประเมินที่เลือกไว้]:
            {$formattedS6}

            คำสั่งบังคับ (STRICT INSTRUCTIONS):
            1. คุณต้องสร้างแผนการสอนให้ครบตั้งแต่ สัปดาห์ที่ 1 ถึง สัปดาห์ที่ {$weekCount} ห้ามทำแค่สัปดาห์เดียว ห้ามหยุดกลางคัน
            2. หัวข้อ (topic) ให้เรียงลำดับเนื้อหาจากพื้นฐานไปสู่ขั้นสูง
            3. กิจกรรมการเรียนรู้ (activity) ต้องสอดคล้องกับวิธีการสอนที่กำหนด และระบุให้เห็นภาพการปฏิบัติจริง
            4. วัตถุประสงค์ (objective) ให้สอดคล้องกับ Domain และ Learning Level ของ CLO ในสัปดาห์นั้น
            5. tool เป็นได้ทั้งอุปกกรณ์ ภาษาที่ใช้เรียน เช่น สไลด์, Jupyter, Python, Anaconda, CSVตัวอย่าง เป็นต้น
            6. assessment คือการประเมินการเรียนในสัปดาห์นั้นๆ เช่น มีส่วนร่วม, Lab Exercise, นำเสนอ เป็นต้น
            7. คืนค่าผลลัพธ์เป็น JSON Array เท่านั้น ห้ามมีข้อความอธิบายอื่นใด
            
            ตัวอย่างรูปแบบโครงสร้าง JSON ที่ต้องการ:
            [
            {
                \"week\": 1,
                \"topic\": \"หัวข้อสัปดาห์ที่ 1\",
                \"objective\": \"วัตถุประสงค์\",
                \"activity\": \"กิจกรรมแบบ Active Learning\",
                \"tool\": \"สื่อและอุปกรณ์\",
                \"assessment\": \"วิธีประเมินผล\",
                \"clo\": \"CLO1\"
            }
            ]
            ";

            $response = Http::timeout(600)->post('http://localhost:11434/api/generate', [
                'model' => 'elo_generator',
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'num_ctx' => 8192, 
                    'num_predict' => 8192, 
                    'temperature' => 0.2
                ]
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ollama API Error: ' . $response->status() . ' - ' . $response->body(),
                    'raw_output' => 'API Connection Failed'
                ]);
            }

            $responseData = $response->json();
            if (!isset($responseData['response'])) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Ollama ไม่ได้ส่งข้อความ response กลับมา',
                    'raw_output' => json_encode($responseData)
                ]);
            }

            $aiText = $responseData['response'];

            // คลีนข้อความ
            $cleanedText = preg_replace('/```json/i', '', $aiText);
            $cleanedText = preg_replace('/```/', '', $cleanedText);
            $cleanedText = trim($cleanedText);

            // หา Array [ ... ]
            $start = strpos($cleanedText, '[');
            $end = strrpos($cleanedText, ']');
            if ($start !== false && $end !== false) {
                $cleanedText = substr($cleanedText, $start, $end - $start + 1);
            }

            $cleanedText = preg_replace('/,\s*([\]}])/m', '$1', $cleanedText); 
            $cleanedText = str_replace(["\r", "\n", "\t"], [' ', ' ', ' '], $cleanedText);

            $decodedData = json_decode($cleanedText, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
                // ดักจับกรณีมันดื้อส่งมาเป็น Object เดี่ยวๆ
                if (isset($decodedData['week'])) {
                    $decodedData = [$decodedData]; 
                }

                return response()->json([
                    'success' => true, 
                    'data' => $decodedData,
                    'prompt_debug' => $prompt, 
                    'raw_output' => $aiText // แอบดูว่ามันส่งอะไรมา
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => "AI พิมพ์ JSON ผิดไวยากรณ์: " . json_last_error_msg(),
                    'prompt_debug' => $prompt,
                    'raw_output' => $aiText 
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อกับ AI: ' . $e->getMessage(),
                'raw_output' => 'Exception Caught'
            ]);
        }
    }
}
