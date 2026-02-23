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
            set_time_limit(300);

            $CC_id = $request->input('CC_id');
            $weekCount = $request->input('weekCount');
            
            $rawClo = $request->input('cloData');
            $formattedClo = "";
            if (is_array($rawClo)) {
                foreach ($rawClo as $key => $details) {
                    $cloText = $details['CLO'] ?? '-';
                    $plo = $details['PLO ต่อ ร้องรับ'] ?? $details['PLO'] ?? '-';
                    $domain = $details['Domain'] ?? '-';
                    $level = $details["Learning's Level"] ?? $details["Learning"] ?? '-';
                    
                    $formattedClo .= "- {$key}: {$cloText} (อ้างอิง: {$plo}, โดเมน: {$domain}, ระดับ: {$level})\n            ";
                }
            } else {
                $formattedClo = "ไม่มีข้อมูล CLO";
            }

            // จัดรูปแบบข้อมูลวิธีการสอน (หมวด 6) ให้อ่านง่าย
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

            // ดึงคำอธิบายรายวิชา 
            $courseDescription = DB::table('curriculum_courses')
                ->join('courses', 'curriculum_courses.course_code', '=', 'courses.course_code')
                ->where('curriculum_courses.id', $CC_id)
                ->value('courses.course_detail_th');

            if (!$courseDescription) {
                $courseDescription = "ไม่พบคำอธิบายรายวิชา (โปรดออกแบบเนื้อหาจาก CLO เป็นหลัก)";
            }

            $prompt = "
            ในฐานะผู้เชี่ยวชาญด้านการออกแบบหลักสูตรและการสอนแบบ Active Learning
            โปรดออกแบบแผนการสอนรายสัปดาห์จำนวน {$weekCount} สัปดาห์ 
            โดยอ้างอิงจาก ผลลัพธ์การเรียนรู้ (CLOs) และวิธีการสอน-การประเมิน ต่อไปนี้:
            
            [ข้อมูล CLO ของรายวิชา]:
            {$formattedClo}
            
            [วิธีการสอนและการประเมินที่เลือกไว้ (อ้างอิง Active Learning)]:
            {$formattedS6}

            คำสั่ง:
            1. กระจาย CLO ให้ครบถ้วนและสอดคล้องกับหัวข้อในแต่ละสัปดาห์
            2. หัวข้อ (topic) ให้เรียงลำดับเนื้อหาจากพื้นฐานไปสู่ขั้นสูง
            3. กิจกรรมการเรียนรู้ (activity) ต้องสอดคล้องกับวิธีการสอนที่กำหนด และระบุให้เห็นภาพการปฏิบัติจริง
            4. วัตถุประสงค์ (objective) ให้สอดคล้องกับ Domain และ Learning Level ของ CLO ในสัปดาห์นั้น
            5. คืนค่ากลับมาเป็นรูปแบบ JSON Array เท่านั้น ห้ามมีข้อความอื่นปน
            
            รูปแบบ JSON ที่ต้องการอย่างเคร่งครัด:
            [
              {
                \"week\": 1,
                \"topic\": \"หัวข้อการเรียน\",
                \"objective\": \"วัตถุประสงค์\",
                \"activity\": \"กิจกรรมการเรียนรู้แบบ Active Learning\",
                \"tool\": \"สื่อและอุปกรณ์\",
                \"assessment\": \"วิธีประเมินผล\",
                \"clo\": \"CLO1\"
              },
              ... (ทำจนครบ {$weekCount} สัปดาห์)
            ]
        ";

            $response = Http::timeout(300)->post('http://localhost:11434/api/generate', [
                'model' => 'elo_generator',
                'prompt' => $prompt,        // ส่งตัวแปร $prompt ไปตรงๆ แบบนี้เลย
                'format' => 'json',         // ท่าไม้ตาย: บังคับให้ออกมาเป็น JSON เท่านั้น
                'stream' => false,          // ปิด stream เพื่อให้มันคิดให้เสร็จก่อนค่อยส่งกลับมา (ถ้าเปิดเป็น true มันจะส่งมาทีละตัวอักษร PHP จะพังเอาครับ)
                'options' => [
                    'num_ctx' => 1024, //8192,      // ขยายความจำให้รับข้อความยาวๆ ได้
                    'num_predict' => 1024, //8192,  // อนุญาตให้พิมพ์ตอบยาวสูงสุด 8192 คำ (แก้ปัญหาพิมพ์ไม่จบประโยค)
                    'temperature' => 0.5    // บังคับให้คิดเป็นเหตุเป็นผล ไม่ต้องแต่งเรื่องมั่ว
                ]
            ]);

            if (!$response->successful()) {
                throw new \Exception('Ollama API Error: ' . $response->body());
            }

            $responseData = $response->json();
            if (!isset($responseData['response'])) {
                throw new \Exception('โครงสร้างการตอบกลับจาก AI ผิดพลาด');
            }

            $aiText = $responseData['response'];

            // คลีนตัวหนังสือขยะรอบๆ
            $aiText = preg_replace('/```json/i', '', $aiText);
            $aiText = preg_replace('/```/', '', $aiText);
            $aiText = trim($aiText);

            // หาจุดเริ่มต้นและจุดสิ้นสุดของ Array [...]
            $start = strpos($aiText, '[');
            $end = strrpos($aiText, ']');
            if ($start !== false && $end !== false) {
                $aiText = substr($aiText, $start, $end - $start + 1);
            }

            // ลบลูกน้ำตัวสุดท้ายที่เกินมาก่อนปิด Array/Object (Trailing Commas)
            $aiText = preg_replace('/,\s*([\]}])/m', '$1', $aiText); 
            $aiText = str_replace(["\r", "\n", "\t"], [' ', ' ', ' '], $aiText);

            $decodedData = json_decode($aiText, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData)) {
                return response()->json([
                    'success' => true, 
                    'data' => $decodedData,
                    'prompt_debug' => $prompt // แอบส่ง prompt กลับไปดูใน console
                ]);
            } else {
                $errorMsg = json_last_error_msg();
                $preview = mb_substr($aiText, 0, 500); 
                return response()->json([
                    'success' => false, 
                    'message' => "AI พิมพ์ JSON ผิดไวยากรณ์ ($errorMsg)\n\nตัวอย่าง:\n$preview"
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
