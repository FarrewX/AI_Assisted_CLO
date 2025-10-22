<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Shared\Converter;

class DocumentController extends Controller
{
    // แสดง preview (HTML)
    public function preview(Request $request)
    {
        $user = Auth::user();

        $course_id = $request->query('course_id');
        $year = (int) $request->query('year');
        $term = $request->query('term');
        $TQF = $request->query('TQF');

        if (!$course_id || !$year || !$term || !$TQF) {
            abort(400, 'Missing required parameters');
        }

        $cy = DB::table('courseyears')
            ->where('course_id', $course_id)
            ->where('user_id', $user->user_id)
            ->where('year', $year)
            ->where('term', $term)
            ->where('TQF', $TQF)
            ->first();

        if (!$cy) return response()->json(['message' => 'ไม่พบ courseyear'], 404);

        $promptRow = DB::table('prompts')
            ->where('ref_id', $cy->id)
            ->first();

        if (!$promptRow) return response()->json(['message' => 'ไม่พบ prompt'], 404);

        $Generates = DB::table('generates')
            ->where('ref_id', $promptRow->ref_id)
            ->orderByDesc('created_at')
            ->get();

        return view('component/preview', compact('Generates', 'course_id', 'year', 'term', 'TQF', ));

    }

    // สร้าง .docx และดาวน์โหลด
    public function exportdocx()
    {
        // --- 1. (สำคัญ) นี่คือข้อมูลจำลอง (DUMMY DATA) ---
        // คุณต้องแทนที่ส่วนนี้ด้วยการดึงข้อมูลจริงจาก Database
        $data = [
            'logo_path' => public_path('image/mjulogo.jpg'), // (ต้องแก้ Path ให้ถูกต้อง)
            'title' => 'มหาวิทยาลัยแม่โจ้',
            'subtitle' => 'มคอ. 3 รายละเอียดรายวิชา',
            'fileName' => 'มคอ-3-รายละเอียดรายวิชา',

            // -- ข้อมูลส่วนหัว --
            'faculty' => 'วิทยาศาสตร์',
            'major' => 'วิทยาการคอมพิวเตอร์',
            'curriculum_year' => '2565',
            'campus' => 'เชียงใหม่',
            'term' => '1',
            'academic_year' => '2568', // ปี พ.ศ. ที่คำนวณแล้ว

            // -- ปรัชญา --
            'philosophy' => 'มุ่งมั่นพัฒนาบัณฑิตสู่ความเป็นผู้อุดมด้วยปัญญา อดทน สู้งาน เป็นผู้มีคุณธรรมและจริยธรรม เพื่อความเจริญรุ่งเรืองวัฒนาของสังคมไทยที่มีการเกษตรเป็นรากฐาน',
            'philosophy_education' => 'จัดการศึกษาเพื่อเสริมสร้างปัญญา ในรูปแบบการเรียนรู ้จากการปฏิบัติที ่บูรณาการกับการทำงานตามอมตะโอวาท งานหนักไม่เคยฆ่าคน มุ่งให้ผู้เรียน มีทักษะการเรียนรู้ตลอดชีวิต',
            'philosophy_curriculum' => 'จัดการศึกษาเพื่อการพัฒนาเทคโนโลยี และส่งเสริมการสร้างนวัตกรรม เรียนรู้จากการปฏิบัติที่บูรณาการกับการทำงาน',

            // -- หมวดที่ 1 --
            's1_course_name' => 'วิทยาการข้อมูล Datascience',
            's1_course_id' => '10301351',
            's1_credits' => '3 (2-3-5)', // (ค่าจาก input)
            's1_curriculum_name' => 'วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์', // (ค่าจาก input)
            's1_course_types' => [ // (ค่าจาก checkbox)
                'specific' => true, 'core' => false, 'major_required' => false,
                'major_elective' => false, 'free_elective' => false
            ],
            's1_prerequisites' => 'ไม่มี', // (ค่าจาก input)
            's1_instructors' => 'Admin2',
            's1_revision_term' => '1', // (ค่าจาก checkbox)
            's1_revision_year' => '2568', // (ค่าจาก span)
            's1_hours' => [
                'theory' => '30', 'practice' => '45',
                'self_study' => '75', 'field_trip' => '50'
            ],

            // -- หมวดที่ 2 --
            's2_description' => 'เนื้อหาคำอธิบายรายวิชา ที่ดึงมาจากฐานข้อมูล...',
            's2_clos' => 'เนื้อหา CLOs ที่ดึงมาจากฐานข้อมูล...',

            // -- หมวดที่ 3 --
            's3_feedback' => 'ข้อเสนอแนะจาก มคอ.5 (มาจาก DB)',
            's3_improvement' => 'การปรับปรุง (มาจาก DB)',
            
            // -- หมวดที่ 4 --
            's4_agreement' => 'รายละเอียดข้อตกลงร่วมกัน... (มาจาก DB)',
            
            // -- หมวดที่ 5 --
            's5_1_plos' => [
                ['id' => 1, 'outcome' => 'PLO 1 Outcome Statement...', 'specific' => true, 'generic' => false, 'level' => 'U', 'type' => 'K'],
                ['id' => 2, 'outcome' => 'PLO 2 Outcome Statement...', 'specific' => false, 'generic' => true, 'level' => 'E', 'type' => 'S'],
                ['id' => 3, 'outcome' => 'PLO 3 Outcome Statement...', 'specific' => true, 'generic' => false, 'level' => 'C', 'type' => 'AR'],
                ['id' => 4, 'outcome' => 'PLO 4 Outcome Statement...', 'specific' => true, 'generic' => false, 'level' => 'AP', 'type' => 'Rs'],
            ],
            's5_2_mapping_data' => [
                // (ข้อมูล ● ○ จากตาราง 5.2)
                // 0 = &nbsp;, 1 = ●, 2 = ○
                // ข้อมูลนี้ควรมี 29 ช่อง ตามจำนวนคอลัมน์
                [1, 0, 0, 0, 1, 0, 0, 2, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 2, 0, 0, 1]
            ],
            's5_3_clos_plo_mapping' => [
                ['clo_id' => 'CLO1', 'clo_desc' => 'ประเมินวิธีการวิเคราะห์ข้อมูล...', 'plo1' => '✓', 'plo2' => '', 'plo3' => '✓', 'plo4' => ''],
                ['clo_id' => 'CLO2', 'clo_desc' => 'สร้างต้นแบบเพื่อการทำงาน...', 'plo1' => '', 'plo2' => '✓', 'plo3' => '', 'plo4' => '✓'],
                ['clo_id' => 'CLO3', 'clo_desc' => 'สร้างการทำงานหรือการรู้จัก...', 'plo1' => '', 'plo2' => '', 'plo3' => '✓', 'plo4' => ''],
            ],

            // -- หมวดที่ 6 --
            's6_clos_teaching' => [
                ['clo' => 'CLO1: [คำอธิบาย]', 'teaching' => 'บรรยาย, อภิปราย', 'assessment' => 'สอบย่อย, สอบกลางภาค'],
                ['clo' => 'CLO2: [คำอธิบาย]', 'teaching' => 'ทำโครงงานกลุ่ม', 'assessment' => 'คะแนนโครงงาน, การนำเสนอ'],
                ['clo' => 'CLO3: [คำอธิบาย]', 'teaching' => 'กรณีศึกษา', 'assessment' => 'รายงานกรณีศึกษา'],
            ],

            // -- หมวดที่ 7 --
            's7_lesson_plan' => [
                ['week' => 1, 'topic' => 'แนะนำ...', 'objective' => '...', 'activity' => '...', 'media' => '...', 'eval' => '...', 'clo' => '1'],
                ['week' => 2, 'topic' => 'การวิเคราะห์...', 'objective' => '...', 'activity' => '...', 'media' => '...', 'eval' => '...', 'clo' => '1, 2'],
                // (เพิ่มข้อมูลจนครบตามจำนวนสัปดาห์)
            ],

            // -- หมวดที่ 8 --
            's8_1_assessment_strategies' => [
                ['method' => 'สอบกลางภาค', 'tool' => 'ข้อสอบอัตนัย', 'ratio' => '30', 'clos' => '1, 2'],
                ['method' => 'โครงงาน', 'tool' => 'Rubric (ตาม 8.2)', 'ratio' => '40', 'clos' => '2, 3'],
                ['method' => 'สอบปลายภาค', 'tool' => 'ข้อสอบ', 'ratio' => '30', 'clos' => '1, 3'],
            ],
            's8_2_rubrics' => [
                [
                    'title' => 'ก. การประเมินการปฏิบัติงาน (Performance)',
                    'header' => 'คำอธิบายเกณฑ์การให้คะแนนการปฏิบัติงาน',
                    'levels' => [
                        ['level' => 5, 'desc' => 'แสดงถึงความเข้าใจปัญหา ...'],
                        ['level' => 4, 'desc' => '[ใส่คำอธิบายสำหรับระดับ 4]'],
                        ['level' => 3, 'desc' => '[ใส่คำอธิบายสำหรับระดับ 3]'],
                        ['level' => 2, 'desc' => '[ใส่คำอธิบายสำหรับระดับ 2]'],
                        ['level' => 1, 'desc' => '[ใส่คำอธิบายสำหรับระดับ 1]'],
                        ['level' => 0, 'desc' => 'ไม่ส่งผลงาน'],
                    ]
                ],
                [
                    'title' => 'ข. การประเมินการนำเสนอ (Presentation)',
                    'header' => 'คำอธิบายเกณฑ์การให้คะแนนการนำเสนอ',
                    'levels' => [
                        ['level' => 5, 'desc' => 'นำเสนอได้ชัดเจน...'],
                        //... (เพิ่ม level/desc)
                        ['level' => 0, 'desc' => 'ไม่นำเสนอ'],
                    ]
                ],
            ],
            
            // -- หมวดที่ 9 --
            's9_references' => [
                'สื่อการเรียนรู้ 1...',
                'สื่อการเรียนรู้ 2...',
            ],
            's9_research' => 'รายละเอียดงานวิจัยที่นำมาสอน...',
            's9_academic_service' => 'รายละเอียดการบริการวิชาการ...',
            's9_culture' => 'รายละเอียดงานทำนุบำรุงศิลปวัฒนธรรม...',
            
            // -- หมวดที่ 10 --
            's10_grading_criteria' => [
                ['grade' => 'A', 'criteria' => '80.00% ขึ้นไป'],
                ['grade' => 'B+', 'criteria' => '75.00% - 79.99%'],
                ['grade' => 'B', 'criteria' => '70.00% - 74.99%'],
                ['grade' => 'C+', 'criteria' => '65.00% - 69.99%'],
                ['grade' => 'C', 'criteria' => '60.00% - 64.99%'],
                ['grade' => 'D+', 'criteria' => '55.00% - 59.99%'],
                ['grade' => 'D', 'criteria' => '50.00% - 54.99%'],
                ['grade' => 'F', 'criteria' => 'ต่ำกว่า 50.00%'],
            ],

            // -- หมวดที่ 11 --
            's11_grade_correction' => 'รายละเอียดขั้นตอนการแก้ไขคะแนน...',
        ];
        // --- สิ้นสุดส่วนข้อมูลจำลอง ---


        try {
            // 2. สร้างอ็อบเจกต์ PhpWord
            $phpWord = new PhpWord();
            $phpWord->setDefaultFontName('TH Sarabun New'); // (ต้องติดตั้งฟอนต์นี้บนเซิร์ฟเวอร์)
            $phpWord->setDefaultFontSize(16);

            // 3. ตั้งค่าหน้ากระดาษ (A4, ขอบ 2.5cm)
            $cmMargin = 2.5;
            $section = $phpWord->addSection([
                'paperSize'   => 'A4',
                'orientation' => 'portrait',
                'marginTop'   => Converter::cmToTwip($cmMargin),
                'marginBottom'=> Converter::cmToTwip($cmMargin),
                'marginLeft'  => Converter::cmToTwip($cmMargin),
                'marginRight' => Converter::cmToTwip($cmMargin),
            ]);

            // 4. --- เริ่มสร้างเอกสาร ---

            // === ส่วนหัว (Logo และ Title) ===
            if (file_exists($data['logo_path'])) {
                $section->addImage($data['logo_path'], [
                    'width'         => 80,
                    'height'        => 80,
                    'alignment'     => Jc::CENTER
                ]);
            }
            $section->addText($data['title'], ['bold' => true, 'size' => 20], ['alignment' => Jc::CENTER]);
            $section->addText($data['subtitle'], ['bold' => true, 'size' => 16], ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);

            
            // === ส่วนข้อมูล (ปรับปรุงใหม่ ใช้ตารางไร้ขอบ 1 ตาราง) ===
            $borderlessStyle = ['borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 0];
            $infoTable = $section->addTable($borderlessStyle);
            $infoTable->addRow();
            $infoTable->addCell(1000)->addText('คณะ', ['bold' => true]);
            $infoTable->addCell(3000)->addText($data['faculty']);
            $infoTable->addCell(1200)->addText('สาขาวิชา', ['bold' => true]);
            $infoTable->addCell(4800)->addText($data['major']);
            
            $infoTable->addRow();
            $infoTable->addCell()->addText('หลักสูตรปรับปรุง พ.ศ.', ['bold' => true]);
            $infoTable->addCell()->addText($data['curriculum_year']);
            $infoTable->addCell()->addText('');
            $infoTable->addCell()->addText('');

            $infoTable->addRow();
            $infoTable->addCell(1000)->addText('วิทยาเขต', ['bold' => true]);
            $infoTable->addCell(3000)->addText($data['campus']);
            $infoTable->addCell(2500)->addText('ภาคการศึกษา/ปีการศึกษา', ['bold' => true]);
            $infoTable->addCell(3500)->addText($data['term'] . ' / ' . $data['academic_year']);

            
            // === ส่วนปรัชญา ===
            $section->addTextBreak(1);
            $section->addText('ปรัชญามหาวิทยาลัยแม่โจ้', ['bold' => true, 'size' => 18]);
            $section->addText($data['philosophy'], [], ['spaceAfter' => 150]);
            $section->addText('ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้', ['bold' => true, 'size' => 18]);
            $section->addText($data['philosophy_education'], [], ['spaceAfter' => 150]);
            $section->addText('ปรัชญาหลักสูตร', ['bold' => true, 'size' => 18]);
            $section->addText($data['philosophy_curriculum'], [], ['spaceAfter' => 150]);

            // === สไตล์ตารางหลัก ===
            $tableStyle = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80, 'width' => '100%'];
            $phpWord->addTableStyle('MainTable', $tableStyle);
            $thStyle = ['bgColor' => 'DBEAFE']; // bg-blue-100 (ใช้สีอ่อนลงเล็กน้อย)
            $thFont = ['bold' => true];
            $cellCenter = ['alignment' => Jc::CENTER];
            $vAlignCenter = ['valign' => 'center'];

            // === หมวดที่ 1 : ข้อมูลทั่วไป ===
            $section->addText('หมวดที่ 1 : ข้อมูลทั่วไป', ['bold' => true, 'size' => 16]);
            $table1 = $section->addTable('MainTable');
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('1. ชื่อวิชา', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_course_name']);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('2. รหัสวิชา', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_course_id']);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('3. จำนวนหน่วยกิต', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_credits']);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('4. หลักสูตร', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_curriculum_name']);

            // (แปลง Checkboxes เป็น Text)
            $typesText = '';
            $typesText .= ($data['s1_course_types']['specific'] ? '[✓]' : '[ ]') . ' วิชาเฉพาะ ';
            $typesText .= ($data['s1_course_types']['core'] ? '[✓]' : '[ ]') . ' กลุ่มวิชาแกน ';
            $typesText .= ($data['s1_course_types']['major_required'] ? '[✓]' : '[ ]') . ' เอกบังคับ ';
            $typesText .= ($data['s1_course_types']['major_elective'] ? '[✓]' : '[ ]') . ' เอกเลือก ';
            $typesText .= ($data['s1_course_types']['free_elective'] ? '[✓]' : '[ ]') . ' วิชาเลือกเสรี';
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('5. ประเภทของรายวิชา', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($typesText);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('6. วิชาบังคับก่อน', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_prerequisites']);
            
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('7. ผู้สอน', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($data['s1_instructors']);

            // (เพิ่ม Row 8 ที่ขาดไป)
            $revisionText = 'ภาคการศึกษาที่ ';
            $revisionText .= ($data['s1_revision_term'] == '1' ? '[✓]' : '[ ]') . ' 1 ';
            $revisionText .= ($data['s1_revision_term'] == '2' ? '[✓]' : '[ ]') . ' 2 ';
            $revisionText .= ' ปีการศึกษา ' . $data['s1_revision_year'];
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('8. การแก้ไขล่าสุด', $thFont, $cellCenter);
            $table1->addCell(7500, ['gridSpan' => 3])->addText($revisionText);
            
            // (Row 9 - Header ชั่วโมง)
            $table1->addRow();
            $table1->addCell(10000, ['gridSpan' => 4, 'bgColor' => 'F3F4F6'])->addText('9. จำนวนชั่วโมงที่ใช้ภาคการศึกษา', $thFont, $cellCenter);
            
            // (Row 10 - ชั่วโมง)
            $table1->addRow();
            $table1->addCell(2500, $thStyle)->addText('ภาคทฤษฎี ' . $data['s1_hours']['theory'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('ภาคปฏิบัติ ' . $data['s1_hours']['practice'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('การศึกษาด้วยตนเอง ' . $data['s1_hours']['self_study'] . ' ชั่วโมง', null, $cellCenter);
            $table1->addCell(2500, $thStyle)->addText('ทัศนศึกษา/ฝึกงาน ' . $data['s1_hours']['field_trip'] . ' ชั่วโมง', null, $cellCenter);

            
            // === หมวดที่ 2 : คำอธิบายรายวิชา ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 2 : คำอธิบายรายวิชาและผลลัพธ์ระดับรายวิชา (CLOs)', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $table2 = $section->addTable('MainTable');
            $table2->addRow();
            $cell2_1 = $table2->addCell(10000);
            $cell2_1->addText('2.1 คำอธิบายรายวิชา', ['bold' => true]);
            $cell2_1->addText(htmlspecialchars($data['s2_description']));
            $table2->addRow();
            $cell2_2 = $table2->addCell(10000);
            $cell2_2->addText('2.2 ผลลัพธ์การเรียนรู้ระดับรายวิชา (Course learning Outcome) CLOs', ['bold' => true]);
            $cell2_2->addText(htmlspecialchars($data['s2_clos']));

            // === หมวดที่ 3 : การปรับปรุง ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 3 : การปรับปรุงรายวิชาตามข้อเสนอแนะจาก มคอ.5', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $table3 = $section->addTable('MainTable');
            $table3->addRow();
            $table3->addCell(5000, $thStyle)->addText('ข้อเสนอแนะ', $thFont);
            $table3->addCell(5000, $thStyle)->addText('การปรับปรุง', $thFont);
            $table3->addRow();
            $table3->addCell(5000)->addText(htmlspecialchars($data['s3_feedback']));
            $table3->addCell(5000)->addText(htmlspecialchars($data['s3_improvement']));

            // === หมวดที่ 4 : ข้อตกลงร่วมกัน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 4: ข้อตกลงร่วมกันระหว่างผู้สอนและผู้เรียน', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $section->addText(htmlspecialchars($data['s4_agreement']));

            // === หมวดที่ 5 : ความสอดคล้อง ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 5: ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับหลักสูตร (PLOs) กับ ผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) และผลทักษะการเรียนรู้ตลอดชีวิต (LLLs)', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            
            // --- 5.1 PLOs ---
            $section->addText('5.1 ผลลัพธ์การเรียนรู้ของหลักสูตร', ['bold' => true, 'size' => 14]);
            $table5_1 = $section->addTable('MainTable');
            $table5_1->addRow();
            $table5_1->addCell(1000, $thStyle)->addText('PLOs', $thFont, $cellCenter);
            $table5_1->addCell(4500, $thStyle)->addText('Outcome Statement', $thFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Specific LO', $thFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Generic LO', $thFont, $cellCenter);
            $table5_1->addCell(1000, $thStyle)->addText('Level', $thFont, $cellCenter);
            $table5_1->addCell(1500, $thStyle)->addText('Type', $thFont, $cellCenter);
            
            foreach ($data['s5_1_plos'] as $plo) {
                $table5_1->addRow();
                $table5_1->addCell(1000, $vAlignCenter)->addText($plo['id'], null, $cellCenter);
                $table5_1->addCell(4500)->addText(htmlspecialchars($plo['outcome']));
                $table5_1->addCell(1000, $vAlignCenter)->addText($plo['specific'] ? '✓' : '', null, $cellCenter);
                $table5_1->addCell(1000, $vAlignCenter)->addText($plo['generic'] ? '✓' : '', null, $cellCenter);
                $table5_1->addCell(1000, $vAlignCenter)->addText($plo['level'], null, $cellCenter);
                $table5_1->addCell(1500, $vAlignCenter)->addText($plo['type'], null, $cellCenter);
            }
            
            // --- 5.2 Mapping ---
            $section->addTextBreak(1);
            $section->addText('5.2 มาตรฐานผลการเรียนรู้รายวิชา (Curriculum Mapping)', ['bold' => true, 'size' => 14]);
            $table5_2 = $section->addTable('MainTable');
            $cellYellow = ['bgColor' => 'FFF9C4']; // bg-yellow-100
            $cellWhite = ['bgColor' => 'FFFFFF'];
            
            // Row 1 (Headers)
            $table5_2->addRow();
            $table5_2->addCell(2000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('รายวิชา', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 7, 'bgColor' => 'FFF9C4'])->addText('ด้านคุณธรรมและจริยธรรม', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 8, 'bgColor' => 'FFF9C4'])->addText('ด้านความรู้', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 4, 'bgColor' => 'FFFFFF'])->addText('ทักษะทางปัญญา', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 6, 'bgColor' => 'FFF9C4'])->addText('ทักษะระหว่างบุคคลและความรับผิดชอบ', null, $cellCenter);
            $table5_2->addCell(null, ['gridSpan' => 4, 'bgColor' => 'FFFFFF'])->addText('ทักษะวิเคราะห์เชิงตัวเลข...', null, $cellCenter);
            
            // Row 2 (Sub-headers)
            $table5_2->addRow();
            $table5_2->addCell(null, ['vMerge' => 'continue']); // Cell จาก Row 1
            for ($i=1; $i<=7; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter);
            for ($i=1; $i<=8; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter);
            for ($i=1; $i<=4; $i++) $table5_2->addCell(300, $cellWhite)->addText($i, null, $cellCenter);
            for ($i=1; $i<=6; $i++) $table5_2->addCell(300, $cellYellow)->addText($i, null, $cellCenter);
            for ($i=1; $i<=4; $i++) $table5_2->addCell(300, $cellWhite)->addText($i, null, $cellCenter);

            // Row 3 (Data)
            $mapSymbols = ['0' => '', '1' => '●', '2' => '○'];
            foreach ($data['s5_2_mapping_data'] as $mapRow) {
                $table5_2->addRow();
                $table5_2->addCell(2000)->addText($data['s1_course_id'] . "\n" . $data['s1_course_name']);
                foreach ($mapRow as $state) {
                    $table5_2->addCell(300)->addText($mapSymbols[$state] ?? '', ['bold' => true], $cellCenter);
                }
            }

            // --- 5.3 CLO-PLO Mapping ---
            $section->addTextBreak(1);
            $section->addText('5.3 ความสอดคล้องของรายวิชากับ PLOs, CLOs และ LLLs', ['bold' => true, 'size' => 14]);
            $table5_3 = $section->addTable('MainTable');
            $table5_3->addRow();
            $table5_3->addCell(1000, $thStyle)->addText('รหัส CLO', $thFont, $cellCenter);
            $table5_3->addCell(5000, $thStyle)->addText('รหัสวิชา ' . $data['s1_course_id'] . ' ' . $data['s1_course_name'], $thFont, $cellCenter);
            foreach ($data['s5_1_plos'] as $plo) { // สร้างหัวตาราง PLO แบบไดนามิก
                $table5_3->addCell(1000, $thStyle)->addText('PLO' . $plo['id'] . ' (' . $plo['level'] . ')', $thFont, $cellCenter);
            }
            
            foreach ($data['s5_3_clos_plo_mapping'] as $map) {
                $table5_3->addRow();
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['clo_id'], null, $cellCenter);
                $table5_3->addCell(5000)->addText(htmlspecialchars($map['clo_desc']));
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo1'], null, $cellCenter);
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo2'], null, $cellCenter);
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo3'], null, $cellCenter);
                $table5_3->addCell(1000, $vAlignCenter)->addText($map['plo4'], null, $cellCenter);
            }

            // === หมวดที่ 6 : CLOs, วิธีการสอน, การประเมิน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 6 : ความสอดคล้องระหว่างผลการเรียนรู้ระดับรายวิชา (CLOs) วิธีการสอน และการประเมินผล', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $table6 = $section->addTable('MainTable');
            $table6->addRow();
            $table6->addCell(2000, $thStyle)->addText('CLO#', $thFont);
            $table6->addCell(4000, $thStyle)->addText('วิธีการสอน (Active Learning)', $thFont);
            $table6->addCell(4000, $thStyle)->addText('การประเมินผล', $thFont);
            
            foreach ($data['s6_clos_teaching'] as $row) {
                $table6->addRow();
                $table6->addCell(2000)->addText(htmlspecialchars($row['clo']));
                $table6->addCell(4000)->addText(htmlspecialchars($row['teaching']));
                $table6->addCell(4000)->addText(htmlspecialchars($row['assessment']));
            }
            
            // === หมวดที่ 7 : แผนการสอน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 7: แผนการสอน', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $section->addText('7.1 แผนการสอน', ['bold' => true, 'size' => 14]);
            $table7 = $section->addTable('MainTable');
            $table7->addRow();
            $table7->addCell(800, $thStyle)->addText('สัปดาห์ที่', $thFont, $cellCenter);
            $table7->addCell(1500, $thStyle)->addText('หัวข้อ', $thFont, $cellCenter);
            $table7->addCell(2000, $thStyle)->addText('วัตถุประสงค์', $thFont, $cellCenter);
            $table7->addCell(2500, $thStyle)->addText('กิจกรรม', $thFont, $cellCenter);
            $table7->addCell(1500, $thStyle)->addText('สื่อ/เครื่องมือ', $thFont, $cellCenter);
            $table7->addCell(1000, $thStyle)->addText('การประเมินผล', $thFont, $cellCenter);
            $table7->addCell(700, $thStyle)->addText('CLO', $thFont, $cellCenter);
            
            foreach ($data['s7_lesson_plan'] as $plan) {
                $table7->addRow();
                $table7->addCell(800, $vAlignCenter)->addText($plan['week'], null, $cellCenter);
                $table7->addCell(1500)->addText(htmlspecialchars($plan['topic']));
                $table7->addCell(2000)->addText(htmlspecialchars($plan['objective']));
                $table7->addCell(2500)->addText(htmlspecialchars($plan['activity']));
                $table7->addCell(1500)->addText(htmlspecialchars($plan['media']));
                $table7->addCell(1000)->addText(htmlspecialchars($plan['eval']));
                $table7->addCell(700, $vAlignCenter)->addText($plan['clo'], null, $cellCenter);
            }
            
            // === หมวดที่ 8 : การประเมิน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 8 : การประเมินการบรรลุผลลัพธ์การเรียนรู้รายวิชา (CLOs)', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            // --- 8.1 กลยุทธ์ ---
            $section->addText('8.1 กลยุทธ์การประเมิน', ['bold' => true, 'size' => 14]);
            $table8_1 = $section->addTable('MainTable');
            $table8_1->addRow();
            $table8_1->addCell(2500, $thStyle)->addText('วิธีการประเมิน', $thFont, $cellCenter);
            $table8_1->addCell(3500, $thStyle)->addText('เครื่องมือ / รายละเอียด', $thFont, $cellCenter);
            $table8_1->addCell(1500, $thStyle)->addText('สัดส่วน (%)', $thFont, $cellCenter);
            $table8_1->addCell(2500, $thStyle)->addText('ความสอดคล้องกับ CLOs', $thFont, $cellCenter);
            
            foreach ($data['s8_1_assessment_strategies'] as $strategy) {
                $table8_1->addRow();
                $table8_1->addCell(2500)->addText(htmlspecialchars($strategy['method']));
                $table8_1->addCell(3500)->addText(htmlspecialchars($strategy['tool']));
                $table8_1->addCell(1500, $vAlignCenter)->addText($strategy['ratio'], null, $cellCenter);
                $table8_1->addCell(2500, $vAlignCenter)->addText($strategy['clos'], null, $cellCenter);
            }
            
            // --- 8.2 Rubrics ---
            $section->addTextBreak(1);
            $section->addText('8.2 วิธีการประเมิน แบบรูบริค (Rubric) หรือ อื่นๆ (ถ้ามี)', ['bold' => true, 'size' => 14]);
            
            foreach ($data['s8_2_rubrics'] as $rubric) {
                $section->addText(htmlspecialchars($rubric['title']), ['bold' => true, 'size' => 14]);
                $tableRubric = $section->addTable('MainTable');
                $tableRubric->addRow();
                $tableRubric->addCell(1500, $thStyle)->addText('ระดับ', $thFont, $cellCenter);
                $tableRubric->addCell(8500, $thStyle)->addText(htmlspecialchars($rubric['header']), $thFont, $cellCenter);
                
                foreach ($rubric['levels'] as $level) {
                    $tableRubric->addRow();
                    $tableRubric->addCell(1500, $vAlignCenter)->addText($level['level'], null, $cellCenter);
                    $tableRubric->addCell(8500)->addText(htmlspecialchars($level['desc']));
                }
                $section->addTextBreak(0.5); // เว้นวรรคระหว่าง Rubric
            }

            // === หมวดที่ 9 : สื่อการเรียนรู้ ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 9: สื่อการเรียนรู้และงานวิจัย', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            
            $section->addText('9.1 สื่อการเรียนรู้และสิ่งสนับสนุนการเรียนรู้', ['bold' => true, 'size' => 14]);
            foreach ($data['s9_references'] as $item) {
                $section->addListItem(htmlspecialchars($item), 0);
            }
            
            $section->addText('9.2 งานวิจัยที่นำมาสอนในรายวิชา', ['bold' => true, 'size' => 14]);
            $section->addText(htmlspecialchars($data['s9_research']));
            
            $section->addText('9.3 การบริการวิชาการ', ['bold' => true, 'size' => 14]);
            $section->addText(htmlspecialchars($data['s9_academic_service']));

            $section->addText('9.4 งานทำนุบำรุงศิลปวัฒนธรรม', ['bold' => true, 'size' => 14]);
            $section->addText(htmlspecialchars($data['s9_culture']));
            
            // === หมวดที่ 10 : เกณฑ์การประเมิน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 10 : เกณฑ์การประเมิน', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $table10 = $section->addTable('MainTable');
            $table10->addRow();
            $table10->addCell(5000, $thStyle)->addText('ระดับผลการศึกษา', $thFont, $cellCenter);
            $table10->addCell(5000, $thStyle)->addText('เกณฑ์การประเมินผล', $thFont, $cellCenter);
            
            foreach ($data['s10_grading_criteria'] as $grade) {
                $table10->addRow();
                $table10->addCell(5000, $vAlignCenter)->addText($grade['grade'], null, $cellCenter);
                $table10->addCell(5000, $vAlignCenter)->addText($grade['criteria'], null, $cellCenter);
            }

            // === หมวดที่ 11 : ขั้นตอนการแก้ไขคะแนน ===
            $section->addTextBreak(1);
            $section->addText('หมวดที่ 11 : ขั้นตอนการแก้ไขคะแนน', ['bold' => true, 'size' => 16, 'bgColor' => 'DBEAFE']);
            $section->addText(htmlspecialchars($data['s11_grade_correction']));


            // 5. --- สิ้นสุดการสร้างเอกสาร ---
            
            // 6. บันทึกไฟล์และส่งให้ดาวน์โหลด
            $fileName = $data['fileName'] . '.docx';
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            // (จัดการ Error)
            return response('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
        }
    }
}
