<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    // แสดง preview (HTML)
    public function preview(Request $request)
    {
        $content = $this->documentContent();
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

        return view('component/preview', compact('Generates', 'course_id', 'year', 'term', 'TQF', 'content'));

    }

    // สร้าง .docx และดาวน์โหลด
    public function exportForm()
    {
        $content = $this->documentContent();

        $phpWord = new PhpWord();
        $section = $phpWord->addSection([
            'paperSize' => 'A4',
            'orientation' => 'portrait',
            'marginTop' => 1200,
            'marginBottom' => 1200,
            'marginLeft' => 1200,
            'marginRight' => 1200,
        ]);

        // หัวเรื่อง
        $section->addText($content['title'], ['bold' => true, 'size' => 16], ['align' => 'center']);
        $section->addText($content['subtitle'], ['bold' => true, 'size' => 16], ['align' => 'center']);
        $section->addTextBreak(1);

        // หัวข้อย่อย
        $section->addText('5.3 ความสอดคล้องของรายวิชากับ PLOs, CLOs และ LLLs', ['bold' => true, 'size' => 12]);
        $section->addText('หลักสูตรฯ วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(1);

        // ตาราง CLO ↔ PLO
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80
        ];
        $table = $section->addTable($tableStyle);

        // แถวที่ 1: รหัสวิชา + ชื่อวิชา (รวม colspan)
        // $table->addRow();
        // $table->addCell(9000, ['gridSpan' => 5, 'bgColor' => 'D9D9D9'])
        //     ->addText("รหัสวิชา10301351 ชื่อวิชา วิทยาการข้อมูล", ['bold' => true], ['align' => 'center']);

        // แถวที่ 2: หัว PLO
        $table->addRow();
        $table->addCell(3000)->addText("รหัสวิชา10301351 ชื่อวิชา วิทยาการข้อมูล", ['bold' => true], ['align' => 'center']);
        $table->addCell(1500)->addText("PLO1 (U)", ['bold' => true], ['align' => 'center']);
        $table->addCell(1500)->addText("PLO2 (E)", ['bold' => true], ['align' => 'center']);
        $table->addCell(1500)->addText("PLO3 (C)", ['bold' => true], ['align' => 'center']);
        $table->addCell(1500)->addText("PLO4 (AP)", ['bold' => true], ['align' => 'center']);

        // แถว CLO1
        $table->addRow();
        $table->addCell(3000)->addText("CLO1 ประเมินวิธีการวิเคราะห์ข้อมูล เพื่อแก้ไขปัญหาทางวิทยาการข้อมูลได้อย่างเหมาะสม");
        $table->addCell(1500)->addText(""); 
        $table->addCell(1500)->addText("");
        $table->addCell(1500)->addText(""); 
        $table->addCell(1500)->addText(""); 

        // แถว CLO2
        $table->addRow();
        $table->addCell(3000)->addText("CLO2 สร้างต้นแบบเพื่อการทำงานหรือการรู้จักจากชุดข้อมูลเพื่อประยุกต์ใช้ในการแก้ปัญหาจริง");
        $table->addCell(1500)->addText(""); 
        $table->addCell(1500)->addText(""); 
        $table->addCell(1500)->addText(""); 
        $table->addCell(1500)->addText("");

        // แถว CLO3
        $table->addRow();
        $table->addCell(3000)->addText("CLO3 สร้างการทำงานหรือการรู้จักจากชุดข้อมูลเพื่อนำมาแก้ไขและปรับเปลี่ยนในอนาคต");
        $table->addCell(1500)->addText(""); 
        $table->addCell(1500)->addText(""); 
        $table->addCell(1500)->addText(""); 
        $table->addCell(1500)->addText("");
         

        // ลงชื่อ
        // $section->addTextBreak(2);
        // $section->addText('ลงชื่อ ..................................', [], ['align' => 'right']);
        // $section->addText('(..................................)', [], ['align' => 'right']);

        // บันทึกชั่วคราวและดาวน์โหลด
        $fileName = ($content['fileName'] ?? 'application_form') . '.docx';
        $temp_file = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }


    // ข้อมูลเอกสารเดียวกันสำหรับ preview และ export
    private function documentContent()
    {
        return [
            'title' => 'มหาวิทยาลัยแม่โจ้',
            'subtitle' => 'มคอ.3 รายละเอียดรายวิชา',
            'personal' => [
                ['label' => 'รหัสวิชา ... ชื่อวิชา ... ', 'value' => '...',],
                ['label' => 'CLO1', 'value' => '..'],
                ['label' => 'CLO2', 'value' => '..'],
                ['label' => 'CLO3', 'value' => '..'],
            ],
            'education' => [
                ['degree' => 'ปริญญาตรี', 'institution' => '........................', 'year' => '....'],
            ],
            'fileName' => 'มคอ3',
        ];
    }
}
