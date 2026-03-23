<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Preview เอกสาร มคอ.{{ $data->TQF ?? 3 }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #525659; 
            font-family: 'Sarabun', sans-serif; 
            margin: 0;
            padding: 20px 0;
        }

        .a4-paper {
            background: white;
            width: 21cm; 
            min-height: 29.7cm; 
            margin: 0 auto;
            padding: 2.54cm; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            font-size: 16pt; 
            line-height: 1.5;
            color: #000;
        }

        .heading-blue {
            background-color: #DBEAFE;
            text-align: center;
            font-weight: bold;
            padding: 5px;
            margin-top: 25px;
            margin-bottom: 15px;
            border: 1px solid #000;
        }

        /* สำหรับข้อมูลที่เป็น Text ยาวๆ ให้รักษารูปแบบการขึ้นบรรทัดใหม่ */
        .text-content {
            white-space: pre-wrap;
            word-break: break-word;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            background-color: #f9fafb;
            min-height: 50px;
        }

        @media print {
            body { background: white; padding: 0; }
            .a4-paper { width: 100%; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/preview-docx.js'])
    @endif
</head>
<body class="font-sans antialiased">

    <div class="a4-paper">
        <div class="text-center font-bold text-2xl mb-6">
            รายละเอียดของรายวิชา (มคอ.{{ $data->TQF ?? 3 }})
        </div>

        <div class="flex justify-center mb-4">
            <img src="/image/mjulogo.jpg" alt="โลโก้ MJU" class="h-24 w-24 rounded-full object-cover border border-gray-300">
        </div>
    
        <h1 class="text-center text-xl font-bold">มหาวิทยาลัยแม่โจ้</h1>
        <h2 class="text-center font-semibold mb-6">มคอ. 3 รายละเอียดรายวิชา</h2>

        <div class="text-base leading-relaxed mb-6 border border-gray-300 p-4 rounded-lg">
            <div class="grid grid-cols-2 gap-4">
                <div><span class="font-bold">คณะ:</span> {{ $data->faculty ?? '-' }}</div>
                <div><span class="font-bold">สาขาวิชา:</span> {{ $data->major ?? '-' }}</div>
                <div><span class="font-bold">วิทยาเขต:</span> {{ $data->campus ?? '-' }}</div>
                <div><span class="font-bold">หลักสูตรปรับปรุง พ.ศ.:</span> {{ $data->curriculum_year ?? '-' }}</div>
                <div class="col-span-2"><span class="font-bold">ภาคการศึกษา/ปีการศึกษา:</span> {{ $data->term ?? '-' }} / {{ $data->year ?? '-' }}</div>
            </div>
        </div>

        <div class="mb-6 space-y-4 text-base">
            <div>
                <h3 class="font-bold">ปรัชญามหาวิทยาลัยแม่โจ้:</h3>
                <div class="pl-4">{{ $data->philosophy ?? 'ไม่พบข้อมูล' }}</div>
            </div>
            <div>
                <h3 class="font-bold">ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้:</h3>
                <div class="pl-4">{{ $data->philosophy_education ?? 'ไม่พบข้อมูล' }}</div>
            </div>
            <div>
                <h3 class="font-bold">ปรัชญาหลักสูตร:</h3>
                <div class="pl-4">{{ $data->philosophy_curriculum ?? 'ไม่พบข้อมูล' }}</div>
            </div>
        </div>

        {{-- ============================== หมวด 1 ============================== --}}
        <div class="heading-blue">หมวดที่ 1 : ข้อมูลทั่วไป</div>
        <table class="w-full border-collapse border border-black text-sm mb-6">
            <tbody>
                <tr>
                    <th class="w-[25%] border border-black p-2 bg-blue-100 text-left align-top">1. ชื่อวิชา</th>
                    <td class="border border-black p-2">{{ $data->course_name_th ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="border border-black p-2 bg-blue-100 text-left align-top">2. รหัสวิชา</th>
                    <td class="border border-black p-2">{{ $data->course_code ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="border border-black p-2 bg-blue-100 text-left align-top">3. จำนวนหน่วยกิต</th>
                    <td class="border border-black p-2">{{ $data->credit ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="border border-black p-2 bg-blue-100 text-left align-top">4. หลักสูตร</th>
                    <td class="border border-black p-2">{{ $data->curriculum_name ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="border border-black p-2 bg-blue-100 text-left align-top">5. ประเภทของรายวิชา</th>
                    <td class="border border-black p-2">
                        {!! !empty($data->is_specific) ? '☑ วิชาเฉพาะ<br>' : '☐ วิชาเฉพาะ<br>' !!}
                        {!! !empty($data->is_core) ? '☑ แกน<br>' : '☐ แกน<br>' !!}
                        {!! !empty($data->is_major_required) ? '☑ เอกบังคับ<br>' : '☐ เอกบังคับ<br>' !!}
                        {!! !empty($data->is_major_elective) ? '☑ เอกเลือก<br>' : '☐ เอกเลือก<br>' !!}
                        {!! !empty($data->is_free_elective) ? '☑ วิชาเลือกเสรี' : '☐ วิชาเลือกเสรี' !!}
                    </td>
                </tr>
                <tr>
                    <th class="border border-black p-2 bg-blue-100 text-left align-top">6. วิชาบังคับก่อน</th>
                    <td class="border border-black p-2">{{ $data->prerequisites ?? 'ไม่มี' }}</td>
                </tr>
                <tr>
                    <th class="border border-black p-2 bg-blue-100 text-left align-top">7. ผู้สอน</th>
                    <td class="border border-black p-2">{{ $data->instructor_name ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="border border-black p-2 bg-blue-100 text-left align-top">8. การแก้ไขล่าสุด</th>
                    <td colspan="3" class="border border-black p-2.5" id="revision_section"> ภาคการศึกษาที่
                        <label class="inline-flex items-center ml-4">
                            <input type="checkbox" class="mr-1.5 scale-125" id="revision_term_1" disabled
                                @if($data->term == 1) checked @endif>1
                        </label>
                        <label class="inline-flex items-center ml-4 mr-4">
                            <input type="checkbox" class="mr-1.5 scale-125" id="revision_term_2" disabled
                                @if($data->term == 2) checked @endif>2
                        </label>
                        <label class="inline-flex items-center">ปีการศึกษา <span id="revision_year_display" class="font-semibold ml-1.5">{{ $data->year }}</span></label>
                    </td>
                </tr>
                <tr class="bg-gray-100 font-bold">
                    <td colspan="2" class="border border-black p-2 text-center">9. จำนวนชั่วโมงที่ใช้ภาคการศึกษา</td>
                </tr>
                <tr>
                    <td colspan="2" class="border border-black p-0">
                        <table class="w-full text-center">
                            <tr>
                                <th class="w-1/4 border-r border-black p-2 bg-blue-50 font-normal">ภาคทฤษฎี<br><span class="font-bold">{{ $data->hours_theory ?? '-' }}</span> ชม.</th>
                                <th class="w-1/4 border-r border-black p-2 bg-blue-50 font-normal">ภาคปฏิบัติ<br><span class="font-bold">{{ $data->hours_practice ?? '-' }}</span> ชม.</th>
                                <th class="w-1/4 border-r border-black p-2 bg-blue-50 font-normal">ศึกษาด้วยตนเอง<br><span class="font-bold">{{ $data->hours_self_study ?? '-' }}</span> ชม.</th>
                                <th class="w-1/4 p-2 bg-blue-50 font-normal">ทัศนศึกษา/ฝึกงาน<br><span class="font-bold">{{ $data->hours_field_trip ?? '-' }}</span> ชม.</th>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- ============================== หมวด 2 ============================== --}}
        <div class="heading-blue">หมวดที่ 2 : คำอธิบายรายวิชาและผลลัพธ์ระดับรายวิชา (CLOs)</div>
        <div class="font-bold text-base mb-2">2.1 คำอธิบายรายวิชา</div>
        <div class="text-content text-sm mb-4">{{ $data->course_detail_th ?? '-' }}</div>
        <div class="text-content text-sm mb-6">{{ $data->course_detail_en ?? '-' }}</div>

        <div class="font-bold text-base mb-2">2.2 ผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs)</div>
        <div id="preview-s22" class="mb-6">กำลังโหลดข้อมูล...</div>

        {{-- ============================== หมวด 3 ============================== --}}
        <div class="heading-blue">หมวดที่ 3 : การปรับปรุงรายวิชาตามข้อเสนอแนะจาก มคอ.5</div>
        <table class="w-full border-collapse border border-black text-sm mb-6">
            <thead class="bg-blue-100 font-bold">
                <tr>
                    <th class="w-1/2 border border-black p-2 text-left">ข้อเสนอแนะ</th>
                    <th class="w-1/2 border border-black p-2 text-left">การปรับปรุง</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-black p-2 align-top whitespace-pre-wrap">{{ $data->feedback ?? '-' }}</td>
                    <td class="border border-black p-2 align-top whitespace-pre-wrap">{{ $data->improvement ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ============================== หมวด 4 ============================== --}}
        <div class="heading-blue">หมวดที่ 4 : ข้อตกลงร่วมกันระหว่างผู้สอนและผู้เรียน</div>
        <div class="text-content text-sm mb-6">{{ $data->agreement ?? 'ไม่มีข้อมูล' }}</div>

        {{-- ============================== หมวด 5 ============================== --}}
        <div class="heading-blue">หมวดที่ 5 : ความสอดคล้องระหว่าง PLOs, CLOs และ LLLs</div>
        <div class="font-bold text-base mb-2">5.1 ผลลัพธ์การเรียนรู้ของหลักสูตร</div>
        <table class="w-full border-collapse border border-black text-xs mb-6 text-center">
            <thead class="bg-blue-100 font-bold">
                <tr>
                    <th class="border border-black p-2 w-[10%]">PLOs</th>
                    <th class="border border-black p-2 w-[40%]">Outcome Statement</th>
                    <th class="border border-black p-2">Specific</th>
                    <th class="border border-black p-2">Generic</th>
                    <th class="border border-black p-2">Level</th>
                    <th class="border border-black p-2">Type</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($data->outcome_statement))
                    @foreach ($data->outcome_statement as $ploData)
                        <tr>
                            <td class="border border-black p-2 font-bold">{{ $ploData['plo'] ?? '-' }}</td>
                            <td class="border border-black p-2 text-left">{{ $ploData['outcome'] ?? '-' }}</td>
                            <td class="border border-black p-2">{!! !empty($ploData['specific']) && $ploData['specific'] == 1 ? '✓' : '' !!}</td>
                            <td class="border border-black p-2">{!! isset($ploData['specific']) && $ploData['specific'] == 0 ? '✓' : '' !!}</td>
                            <td class="border border-black p-2">{{ $ploData['level'] ?? '-' }}</td>
                            <td class="border border-black p-2">{{ $ploData['type'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="6" class="border border-black p-4 text-gray-500">ไม่มีข้อมูล</td></tr>
                @endif
            </tbody>
        </table>
        
        <div class="font-bold text-base mb-2 mt-6">5.2 มาตรฐานผลการเรียนรู้รายวิชา (Curriculum Mapping)</div>
        <div id="preview-s52" class="mb-6 overflow-x-auto">กำลังโหลดข้อมูล...</div>

        <div class="font-bold text-base mb-2">5.3 ความสอดคล้องของรายวิชากับ PLOs, CLOs และ LLLs</div>
        <div id="preview-s53" class="mb-6">กำลังโหลดข้อมูล...</div>

        {{-- ============================== หมวด 6,7,8 ============================== --}}
        <div class="heading-blue">หมวดที่ 6 : วิธีการสอนและการประเมินผล</div>
        <div id="preview-s6" class="mb-6">กำลังโหลดข้อมูล...</div>

        <div class="heading-blue">หมวดที่ 7 : แผนการสอน</div>
        <div id="preview-s7" class="mb-6 overflow-x-auto">กำลังโหลดข้อมูล...</div>

        <div class="heading-blue">หมวดที่ 8 : การประเมินผลและเกณฑ์รูบริค</div>
        <div class="font-bold text-base mb-2">8.1 กลยุทธ์การประเมิน</div>
        <div id="preview-s81" class="mb-6">กำลังโหลดข้อมูล...</div>
        <div class="font-bold text-base mb-2">8.2 เกณฑ์การประเมินรูบริค (Rubrics)</div>
        <div id="preview-s82" class="mb-6">กำลังโหลดข้อมูล...</div>

        {{-- ============================== หมวด 9 ============================== --}}
        <div class="heading-blue">หมวดที่ 9 : สื่อการเรียนรู้และงานวิจัย</div>
        
        <div class="font-bold text-base mb-2">9.2 งานวิจัยที่นำมาสอนในรายวิชา</div>
        <div class="text-content text-sm mb-4">{{ $data->research_subjects ?? '-' }}</div>

        <div class="font-bold text-base mb-2">9.3 การบริการวิชาการ</div>
        <div class="text-content text-sm mb-4">{{ $data->academic_service ?? '-' }}</div>

        <div class="font-bold text-base mb-2">9.4 งานทำนุบำรุงศิลปวัฒนธรรม</div>
        <div class="text-content text-sm mb-6">{{ $data->art_culture ?? '-' }}</div>

        {{-- ============================== หมวด 10 ============================== --}}
        <div class="heading-blue">หมวดที่ 10 : เกณฑ์การประเมิน</div>
        @php $grading = (object) ($data->grading_criteria ?? []); @endphp
        <table class="w-full border-collapse border border-black text-sm mb-6 text-center">
            <thead class="bg-blue-100 font-bold">
                <tr>
                    <th class="w-1/2 border border-black p-2">ระดับผลการศึกษา</th>
                    <th class="w-1/2 border border-black p-2">เกณฑ์การประเมินผล</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-black p-2 font-bold">{{ $grading->grade_A_level ?? 'A' }}</td>
                    <td class="border border-black p-2">{{ $grading->grade_A_criteria ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-bold">{{ $grading->grade_Bp_level ?? 'B+' }}</td>
                    <td class="border border-black p-2">{{ $grading->grade_Bp_criteria ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-bold">{{ $grading->grade_B_level ?? 'B' }}</td>
                    <td class="border border-black p-2">{{ $grading->grade_B_criteria ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-bold">{{ $grading->grade_Cp_level ?? 'C+' }}</td>
                    <td class="border border-black p-2">{{ $grading->grade_Cp_criteria ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-bold">{{ $grading->grade_C_level ?? 'C' }}</td>
                    <td class="border border-black p-2">{{ $grading->grade_C_criteria ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-bold">{{ $grading->grade_Dp_level ?? 'D+' }}</td>
                    <td class="border border-black p-2">{{ $grading->grade_Dp_criteria ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-bold">{{ $grading->grade_D_level ?? 'D' }}</td>
                    <td class="border border-black p-2">{{ $grading->grade_D_criteria ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-bold">{{ $grading->grade_F_level ?? 'F' }}</td>
                    <td class="border border-black p-2">{{ $grading->grade_F_criteria ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ============================== หมวด 11 ============================== --}}
        <div class="heading-blue">หมวดที่ 11 : ขั้นตอนการแก้ไขคะแนน</div>
        <div class="text-content text-sm mb-6">{{ $data->grade_correction ?? '-' }}</div>

        {{-- ============================== ลายเซ็นอาจารย์ ============================== --}}
        @php
            $thaiMonths = [
                1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
                4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
                7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
                10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
            ];
            $currentDay = date('j');
            $currentMonth = $thaiMonths[(int)date('n')];
            $currentYear = (int)date('Y');
            $currentYearBE = ($currentYear > 2400) ? $currentYear : $currentYear + 543;
            
            $currentDateText = "วันที่ {$currentDay} {$currentMonth} {$currentYearBE}";
            $instructorName = !empty($data->instructor_name) ? $data->instructor_name : '.........................................................';
        @endphp

        <div class="mt-16 flex justify-end text-sm text-black">
            <div class="w-3/4 text-center">
                <div class="mb-2">ลงชื่อ........................................................................</div>
                <div class="mb-2">ผู้รับผิดชอบรายวิชา/ผู้รายงาน {{ $instructorName }} {{ $currentDateText }}</div>
            </div>
        </div>

        <div class="text-center text-gray-400 text-sm mt-10 border-t pt-4 pb-4">
            -- จบการแสดงตัวอย่าง หากต้องการดูข้อมูลตารางแบบสมบูรณ์ กรุณาดาวน์โหลดไฟล์ .docx --
        </div>
    </div>

    <script>
        window.PREVIEW_PAGE_DATA = {
            curriculumMapData: @json($data->curriculum_map_data ?? []),
            aiText: @json($data->ai_text ?? '{}'),
            courseAccord: @json($data->course_accord ?? []),
            teachingMethods: @json($data->teaching_methods ?? null),
            planData: @json($data->plan_data ?? []),
            assessmentData: @json($data->assessment_data ?? []),
            rubricsData: @json($data->rubrics_data ?? []),
            referencesData: @json($data->references_data ?? []),
            ploCount: {{ count(isset($data->outcome_statement) && is_array($data->outcome_statement) ? $data->outcome_statement : []) ?: 4 }},
            lllData: {!! $data->lll_data ?? '[]' !!},
            levelOptions: @json($data->level_options ?? []),
        };

    </script>
</body>
</html>