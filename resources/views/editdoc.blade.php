<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แบบฟอร์ม มคอ.3</title>
    <meta name="viewport" content="width=1920mm, height: 1080mm, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['"TH Sarabun New"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body>
    <div class="flex justify-center py-6 bg-gray-100 font-sans text-bases">
        <div class="fixed top-4 right-4 flex gap-3 z-50">
            <button type="button" onclick="window.history.back()"
                class="px-4 py-2 bg-blue-300 hover:bg-blue-400 text-gray-900 rounded-md shadow">
                <a href="/export-docx?course_id={{ $data->course_id ?? '' }}&year={{ $data->year ?? '' }}&term={{ $data->term ?? '' }}&TQF={{ $data->TQF ?? 3 }}" class="download-button">
                    ดาวน์โหลดไฟล์ .docx 
                </a>
            </button>
        </div>
        <div class="bg-white w-[210mm] min-h-[297mm] p-[25mm] shadow-md mb-5 mx-auto">
            <div class="flex justify-center mb-4">
                <img src="/image/mjulogo.jpg" 
                    alt="โลโก้ MJU" 
                    class="h-20 w-20 rounded-full object-cover">
            </div>
        
            <h1 class="text-center text-[20px] font-bold">มหาวิทยาลัยแม่โจ้</h1>
            <h2 class="text-center font-semibold mb-6">มคอ. 3 รายละเอียดรายวิชา</h2>

            <div class="justify-center mb-2 text-[16px]">
                <div class="flex flex-wrap items-baseline">
                    <div class="flex items-baseline mr-6 mb-2">
                        <label class="whitespace-nowrap font-semibold">คณะ</label>
                        <input name="faculty" class="px-2 min-w-[150px] border-b border-dotted border-gray-500 text-center" value="{{ $data->faculty ?? 'ระบุคณะ' }}"></input>
                        <label class="ml-2 whitespace-nowrap font-semibold">สาขาวิชา</label>
                        <input name="major" class="px-2 min-w-[200px] border-b border-dotted border-gray-500 text-center" value="{{ $data->major ?? 'ระบุสาขา' }}"></input>
                        <label class="ml-2 whitespace-nowrap font-semibold">หลักสูตรปรับปรุง พ.ศ.</label>
                        <input name="curriculum_year" id="curriculum_year" class="w-[70px] border-b border-dotted border-gray-500 text-center" 
                            value="{{ ($data->curriculum_year ?? null) ? $data->curriculum_year : '' }}"
                            placeholder="ระบุปี พ.ศ."></input>
                        </div>
                </div>

                <div class="flex flex-wrap items-baseline">
                    <div class="flex items-baseline mr-6 mb-2">
                        <label class="whitespace-nowrap font-semibold">วิทยาเขต</label>
                        <input name="campus" class="ml-2 px-2 min-w-[200px] border-b border-dotted border-gray-500 text-center" value="{{ $data->campus ?? 'ระบุวิทยาเขต' }}"></input>
                        <div class="flex items-baseline mb-2">
                            <label class="whitespace-nowrap font-semibold">ภาคการศึกษา/ปีการศึกษา</label>
                            <div name="term" id="term" class="ml-2 w-[100px] bg-transparent border-b border-dotted border-gray-500 appearance-none text-center">
                                <span>{{ $data->term ?? '' }}</span>
                            </div> /
                            <div name="academic_year" id="academic_year" 
                                class="ml-2 w-[115px] bg-transparent border-b border-dotted border-gray-500 appearance-none text-center">
                                <span>{{ $data->year ? $data->year + 543 : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col">
                    <h3 class="text-[20px] font-semibold">ปรัชญามหาวิทยาลัยแม่โจ้</h3>
                    <span class="leading-relaxed" id="philosophy">
                        {{-- {{ philosophy ?? '' }} --}}
                        มุ่งมั่นพัฒนาบัณฑิตสู่ความเป็นผู้อุดมด้วยปัญญา อดทน สู้งาน เป็นผู้มีคุณธรรมและจริยธรรม เพื่อ
                        ความเจริญรุ่งเรืองวัฒนาของสังคมไทยที่มีการเกษตรเป็นรากฐาน 
                    </span>
                    <h3 class="text-[20px] font-semibold">ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้</h3>
                    <span class="leading-relaxed" id="philosophy_education">
                        {{-- {{ philosophy_education ?? '' }} --}}
                        จัดการศึกษาเพื่อเสริมสร้างปัญญา ในรูปแบบการเรียนรู ้จากการปฏิบัติที ่บูรณาการกับการทำงานตาม
                        อมตะโอวาท งานหนักไม่เคยฆ่าคน มุ่งให้ผู้เรียน มีทักษะการเรียนรู้ตลอดชีวิต
                    </span>
                    <h3 class="text-[20px] font-semibold">ปรัชญาหลักสูตร</h3>
                    <span class="leading-relaxed" id="philosophy_curriculum">
                        {{-- {{ philosophy_curriculum ?? '' }} --}}
                        จัดการศึกษาเพื่อการพัฒนาเทคโนโลยี และส่งเสริมการสร้างนวัตกรรม เรียนรู้จากการปฏิบัติที่บูรณาการ
                        กับการทำงาน
                    </span>
                </div>
                {{-- =========================================================================================================================================================================================================================== --}}
                <h3 class="mt-6 font-bold">หมวดที่ 1 : ข้อมูลทั่วไป</h3>
                <table class="w-full border-collapse border border-black mt-2 text-sm">
                    <tbody>
                        <tr class="bg-gray-100 font-semibold">
                            <th class="w-1/4 border border-black p-2.5 align-top bg-blue-100 text-center">1. ชื่อวิชา</th>
                            <td colspan="3" class="border border-black p-2.5 align-top" id="course_name_th">{{ $data->course_name_th }}</td>
                            </tr>
                        <tr>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">2. รหัสวิชา</th>
                            <td colspan="3" class="border border-black p-2.5 align-top" id="course_id">{{ $data->course_id }}</td>
                            </tr>
                        <tr>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">3. จำนวนหน่วยกิต</th>
                            <td colspan="3" class="border border-black align-top"> 
                                <input type="text" name="credits" id="credits" 
                                    class="w-full p-2.5 border-none focus:outline-none focus:ring-0" 
                                    value="{{ $data->credits ?? '' }}">
                            </td>
                        </tr>
                        <tr>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">4. หลักสูตร</th>
                            <td colspan="3" class="border border-black align-top">
                                <input type="text" name="curriculum_name" id="curriculum_name" 
                                    class="w-full p-2.5 border-none focus:outline-none focus:ring-0" 
                                    value="{{ $data->curriculum_name ?? '' }}">
                            </td>
                        </tr>
                        <tr>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">5. ประเภทของรายวิชา</th>
                            <td colspan="3" class="border border-black p-2.5 align-top" id="course_type_section">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_specific" class="mr-1.5 scale-125" value="1"
                                        @if(!empty($data->is_specific)) checked @endif> วิชาเฉพาะ
                                </label>
                                <label class="inline-flex items-center ml-4">
                                    <input type="checkbox" name="is_core" class="mr-1.5 scale-125" value="1"
                                        @if(!empty($data->is_core)) checked @endif> กลุ่มวิชาแกน
                                </label>
                                <label class="inline-flex items-center ml-4">
                                    <input type="checkbox" name="is_major_required" class="mr-1.5 scale-125" value="1"
                                        @if(!empty($data->is_major_required)) checked @endif> เอกบังคับ
                                </label>
                                <label class="inline-flex items-center ml-4">
                                    <input type="checkbox" name="is_major_elective" class="mr-1.5 scale-125" value="1"
                                        @if(!empty($data->is_major_elective)) checked @endif> เอกเลือก
                                </label>
                                <label class="inline-flex items-center ml-4">
                                    <input type="checkbox" name="is_free_elective" class="mr-1.5 scale-125" value="1"
                                        @if(!empty($data->is_free_elective)) checked @endif> วิชาเลือกเสรี
                                </label>
                            </td>
                        </tr> 
                        <tr> 
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">6. วิชาบังคับก่อน</th> 
                            <td colspan="3" class="border border-black p-2.5 align-top">
                                <input type="text" name="prerequisites" id="prerequisites"
                                    class="w-full border-none focus:outline-none focus:ring-0" 
                                    value="{{ $data->prerequisites ?? '' }}">
                            </td> 
                        </tr> 
                        <tr> 
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">7. ผู้สอน</th>
                            <td colspan="3" class="border border-black p-2.5 align-top" id="instructors">{{ $data->instructorName ?? '' }}</td>
                            </tr> 
                        <tr>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">8.การแก้ไขล่าสุด</th>
                            <td colspan="3" class="border border-black p-2.5" id="revision_section"> ภาคการศึกษาที่
                                <label class="inline-flex items-center ml-4">
                                    <input type="checkbox" class="mr-1.5 scale-125" id="revision_term_1" disabled
                                        @if($data->term == 1) checked @endif>1
                                </label>
                                <label class="inline-flex items-center ml-4 mr-4">
                                    <input type="checkbox" class="mr-1.5 scale-125" id="revision_term_2" disabled
                                        @if($data->term == 2) checked @endif>2
                                </label>
                                <label class="inline-flex items-center">ปีการศึกษา <span id="revision_year_display" class="font-semibold ml-1.5">{{ $data->year ? $data->year + 543 : '' }}</span></label>
                            </td>
                        </tr>
                        <tr class="text-center font-semibold bg-gray-100"> 
                            <td colspan="4" class="border border-black p-2.5 align-top">9.จำนวนชั่วโมงที่ใช้ภาคการศึกษา</td> 
                        </tr> 
                        <tr>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center font-normal">
                                ภาคทฤษฎี
                                <span id="hours_theory" contenteditable="true" class="font-bold inline-block px-2 w-8 py-0.5 bg-white border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-yellow-50">
                                    {{ $data->hours_theory ?? '' }}
                                </span> ชั่วโมง
                            </th>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center font-normal">
                                ภาคปฏิบัติ
                                <span id="hours_practice" contenteditable="true" class="font-bold inline-block px-2 w-8 py-0.5 bg-white border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-yellow-50">
                                    {{ $data->hours_practice ?? '' }}
                                </span> ชั่วโมง
                            </th>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center font-normal">
                                การศึกษาด้วยตนเอง
                                <span id="hours_self_study" contenteditable="true" class="font-bold inline-block px-2 w-8 py-0.5 bg-white border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-yellow-50">
                                    {{ $data->hours_self_study ?? '' }}
                                </span> ชั่วโมง
                            </th>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center font-normal">
                                ทัศนศึกษา/ฝึกงาน
                                <span id="hours_field_trip" contenteditable="true" class="font-bold inline-block px-2 w-8 py-0.5 bg-white border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-yellow-50">
                                    {{ $data->hours_field_trip ?? '' }}
                                </span> ชั่วโมง
                            </th>
                        </tr>
                    </tbody>
                </table>
                {{-- =========================================================================================================================================================================================================================== --}}
                <h3 class="mt-6 font-bold bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                    หมวดที่ 2 : คำอธิบายรายวิชาและผลลัพธ์ระดับรายวิชา (CLOs)
                </h3>
                <table class="w-full border-collapse mt-2.5 text-[15px] bg-white shadow-sm border border-gray-300">
                    <tbody>
                        <tr>
                            <td class="p-4 border border-gray-300">
                                <b>2.1 คำอธิบายรายวิชา</b>
                                <br>
                                <textarea
                                    rows="4"
                                    class="w-full mt-2 p-3 text-[15px] leading-relaxed border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all
                                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                                >
                            {{ $data->course_detail_th ?? ($data->course_detail_th ?? '') }}
                            {{ $data->course_detail_en ?? ($data->course_detail_en ?? '') }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 border border-gray-300">
                                <b>2.2 ผลลัพธ์การเรียนรู้ระดับรายวิชา (Course learning Outcome) CLOs </b>
                                <br>
                                <textarea
                                    name="ai_text"
                                    rows="4"
                                    class="w-full mt-2 p-3 text-[15px] leading-relaxed border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all
                                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                                >{{ $data->ai_text ?? '' }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                {{-- =========================================================================================================================================================================================================================== --}}
                <table class="w-full border-collapse border border-gray-400 mt-6 text-sm">
                    <thead class="font-semibold">
                        <tr class="bg-blue-100">
                            <th colspan="2" class="p-2 text-left border border-gray-400">
                                หมวดที่ 3 : การปรับปรุงรายวิชาตามข้อเสนอแนะจาก มคอ.5
                            </th>
                        </tr>
                        <tr class="bg-gray-200">
                            <th class="w-1/2 p-2 text-left border border-gray-400">ข้อเสนอแนะ</th>
                            <th class="w-1/2 p-2 text-left border border-gray-400">การปรับปรุง</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border border-gray-400 align-top">
                                <textarea name="feedback" rows="4" id="feedback"
                                        class="w-full p-1 border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                >{{ $data->feedback ?? '' }}</textarea>
                            </td>
                            <td class="p-2 border border-gray-400 align-top">
                                <textarea name="improvement" rows="4" id="improvement"
                                        class="w-full p-1 border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                >{{ $data->improvement ?? '' }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                {{-- =========================================================================================================================================================================================================================== --}}
                <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                    หมวดที่ 4: ข้อตกลงร่วมกันระหว่างผู้สอนและผู้เรียน 
                </h3><br>
                <textarea
                    name="agreement"
                    class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                    placeholder='พิมพ์รายละเอียด'
                >{{ $data->agreement ?? '' }}</textarea>
                {{-- =========================================================================================================================================================================================================================== --}}
                <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                    หมวดที่ 5: ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับหลักสูตร (PLOs) กับ  ผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) และผลทักษะการเรียนรู้ตลอดชีวิต (LLLs)
                </h3>
                <div>
                    <h4 class="mt-2 font-semibold">5.1 ผลลัพธ์การเรียนรู้ของหลักสูตร</h4>
                    <h4 class="mt-2 font-semibold">หลักสูตรฯ วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์</h4>
                    <div class="flex justify-end -mt-10 ">
                        <button type="button" id="addPloRowBtn"
                            class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
                            + เพิ่มแถว PLO
                        </button>
                    </div>
                </div>
                <table class="w-full border-collapse border border-gray-400 mt-2 text-sm">
                    <thead class="bg-gray-100 font-semibold text-center">
                        <tr>
                            <th class="w-[8%] border border-black p-2.5 align-top bg-blue-100 text-center">PLOs</th>
                            <th class="w-[45%] border border-black p-2.5 align-top bg-blue-100 text-center">Outcome Statement</th>
                            <th class="w-[8%] border border-black p-2.5 align-top bg-blue-100 text-center">Specific LO</th>
                            <th class="w-[8%] border border-black p-2.5 align-top bg-blue-100 text-center">Generic LO</th>
                            <th class="w-[8%] border border-black p-2.5 align-top bg-blue-100 text-center">Level</th>
                            <th class="w-[15%] border border-black p-2.5 align-top bg-blue-100 text-center">Type</th>
                        </tr>
                    </thead>
                    @php
                        $outcomeStatements = isset($data->outcome_statement) && is_array($data->outcome_statement) ? $data->outcome_statement : [];
                        if (empty($outcomeStatements)) {
                            $maxPlos = 4;
                        } else {
                            $maxPlos = max(array_keys($outcomeStatements));
                        }
                    @endphp
                    <tbody id="plosTableBody" data-max-plo="{{ $maxPlos }}">
                        @for ($i = 1; $i <= $maxPlos; $i++)
                            @php
                                $ploData = $outcomeStatements[$i] ?? [
                                    'outcome' => '',
                                    'specific' => false,
                                    'generic' => false,
                                    'level' => '',
                                    'type' => ''
                                ];
                            @endphp
                            <tr>
                                <td class="text-center border border-black p-2.5 align-top">{{ $i }}</td>
                                <td class="border border-black p-2.5 align-top">
                                    <textarea name="plo{{ $i }}_outcome" rows="3" class="w-full border border-gray-300 rounded p-1">{{ $ploData['outcome'] ?? '' }}</textarea>
                                </td>
                                <td class="text-center border border-black p-2.5 align-top">
                                    <input type="checkbox" name="plo{{ $i }}_specific" class="scale-125" value="1" {{ ($ploData['specific'] ?? false) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center border border-black p-2.5 align-top">
                                    <input type="checkbox" name="plo{{ $i }}_generic" class="scale-125" value="1" {{ ($ploData['generic'] ?? false) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center border border-black p-2.5 align-top">
                                    <select name="plo{{ $i }}_level"
                                    class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">--เลือก--</option>
                                        @foreach (['R', 'U', 'AP', 'AN', 'E', 'C'] as $levelValue)
                                            <option value="{{ $levelValue }}" {{ ($ploData['level'] ?? '') === $levelValue ? 'selected' : '' }}>
                                                {{ explode(' - ', $levelValue)[0] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center border border-black p-2.5 align-top">
                                    <select name="plo{{ $i }}_type"
                                    class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="">--เลือก--</option>
                                        @foreach (['K', 'S', 'AR', 'Rs'] as $typeValue)
                                            <option value="{{ $typeValue }}" {{ ($ploData['type'] ?? '') === $typeValue ? 'selected' : '' }}>
                                                {{ explode(' - ', $typeValue)[0] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
                <p class="text-xs">Bloom's Taxonomy : R-Remember, U-Understand,  AP-Apply,  AN-Analyze, E-Evaluate,  C-Create </p>
                
                <div id="section-5-2">
                    <h3 class="font-bold text-lg">5.2 มาตรฐานผลการเรียนรู้รายวิชา (Curriculum Mapping)</h3>
                    <p class="text-sm mb-2">หลักสูตรฯ วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-400 text-sm" id="curriculum-mapping-table">
                            <thead class="text-center font-semibold align-middle">
                                <tr class="bg-white">
                                    <th rowspan="2" class="border border-gray-400 p-2 align-middle w-48 bg-white">รายวิชา</th>
                                    <th colspan="7" class="border border-gray-400 p-2 align-middle text-xs bg-yellow-100">ด้านคุณธรรมและจริยธรรม</th>
                                    <th colspan="8" class="border border-gray-400 p-2 align-middle text-xs bg-yellow-100">ด้านความรู้</th>
                                    <th colspan="4" class="border border-gray-400 p-2 align-middle text-xs bg-white">ทักษะทางปัญญา</th>
                                    <th colspan="6" class="border border-gray-400 p-2 align-middle text-xs bg-yellow-100">ทักษะระหว่างบุคคลและ<br>ความรับผิดชอบ</th>
                                    <th colspan="4" class="border border-gray-400 p-2 align-middle text-xs bg-white">ทักษะวิเคราะห์<br>เชิงตัวเลข การสื่อสารและ<br>การใช้เทคโนโลยีสารสนเทศ</th>
                                </tr>
                                <tr class="bg-white">
                                    @php $cols = [7, 8, 4, 6, 4]; $bgColors = ['bg-yellow-100', 'bg-yellow-100', 'bg-white', 'bg-yellow-100', 'bg-white']; @endphp
                                    @foreach($cols as $index => $count)
                                        @for($j=1; $j<=$count; $j++)
                                            <th class="border border-gray-400 p-2 w-8 font-normal {{ $bgColors[$index] }}">{{ $j }}</th>
                                        @endfor
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr>
                                    <td class="border border-gray-400 p-2 text-left align-top">
                                        {{ $data->course_id ?? '' }}<br>{{ $data->course_name_th ?? ($data->course_name_en ?? '') }}
                                    </td>
                                    {{-- สร้าง Cells ว่างๆ ไว้ก่อน รอ JS มาใส่ข้อมูล --}}
                                    @php $totalCols = array_sum($cols); @endphp
                                    @for($k=0; $k<$totalCols; $k++)
                                        <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0" data-row="0" data-col="{{ $k }}">&nbsp;</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            
                <h3 class="mt-6 font-bold">5.3 ความสอดคล้องของรายวิชากับ PLOs, CLOs และ LLLs</h3>
                <h4 class="mt-2 font-semibold">หลักสูตรฯ วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์</h4>
                <table id="ploTable" class="w-full border-collapse border border-gray-300 text-sm mt-4">
                    <thead>
                        <tr>
                            <th class="border border-gray-400 p-2 bg-gray-200 font-bold w-[10%]">รหัส</th>
                            <th class="border border-gray-400 p-2 bg-gray-200 font-bold w-[40%]">คำอธิบาย CLOs/LLLs</th>
                            @foreach ($outcomeStatements as $p => $ploData)
                                @php
                                    $ploLevel = '';
                                    // We already have $ploData from the loop
                                    if(isset($ploData['level'])) {
                                        // Handle cases where level might be just "R" or "R - Remember"
                                        $levelParts = explode(' - ', $ploData['level']);
                                        $ploLevel = $levelParts[0] ?? '';
                                    }
                                @endphp
                                {{-- Use $p (the key, e.g., 1, 2, 5) for the PLO number --}}
                                <th class="border border-gray-400 p-2 bg-gray-200 font-bold w-[12.5%]">PLO{{ $p }} {{ $ploLevel ? "($ploLevel)" : '' }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                {{-- =========================================================================================================================================================================================================================== --}}
                <div class="max-w-7xl mx-auto space-y-4 mt-6" id="section-6-container">
                    <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900" >
                        หมวดที่ 6 : ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs)  วิธีการสอน และการประเมินผล
                    </h3>
                    <table id="cloTable_S6" class="w-full border-collapse border border-gray-400">
                        <thead class="bg-gray-200 font-semibold text-left">
                            <tr>
                                <th class="w-[20%] p-2 border border-gray-400 align-top">CLO#</th>
                                <th class="w-[40%] p-2 border border-gray-400 align-top">วิธีการสอน (Active Learning)</th>
                                <th class="w-[40%] p-2 border border-gray-400 align-top">การประเมินผล</th>
                            </tr>
                        </thead>
                        <tbody id="cloTableBody_S6" class="bg-white">
                            {{-- Rows will be added by JavaScript --}}
                        </tbody>
                    </table>
                    <button id="addCloBtn_S6" class="mt-4 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700">
                        + เพิ่ม CLO ใหม่
                    </button>

                    <template id="cloRowTemplate_S6">
                        <tr class="bg-white clo-row">
                            <td class="clo-cell-s6 p-2 border border-gray-400 align-top text-sm" contenteditable="true" data-field=""></td>
                            <td class="teaching-cell-s6 p-2 border border-gray-400 align-top text-sm"></td>
                            <td class="assessment-cell-s6 p-2 border border-gray-400 align-top text-sm"></td>
                        </tr>
                    </template>
                </div>
                {{-- =========================================================================================================================================================================================================================== --}}
                <div id="section-7-container">
                    <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                        หมวดที่ 7: แผนการสอน
                    </h3>
                    <h3 class="mt-6 font-bold">7.1 แผนการสอน</h3>
                    <div class="mb-4">
                        จำนวนสัปดาห์:
                        <input type="number" id="weekCount" value="10" min="1" max="20" class="w-[70px] border border-gray-300 rounded px-2 py-1">
                        <button id="generateLessonTableBtn" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">สร้างตาราง</button>
                    </div>
                    <table id="planTable" class="w-full border-collapse border border-black text-sm">
                        <thead>
                            <tr>
                                <th class="w-[5%] border border-black p-2.5 align-top bg-blue-100 text-center">สัปดาห์ที่</th>
                                <th class="w-[15%] border border-black p-2.5 align-top bg-blue-100 text-center">หัวข้อ</th>
                                <th class="w-[20%] border border-black p-2.5 align-top bg-blue-100 text-center">วัตถุประสงค์รายสัปดาห์</th>
                                <th class="w-[25%] border border-black p-2.5 align-top bg-blue-100 text-center">กิจกรรมการเรียนรู้</th>
                                <th class="w-[15%] border border-black p-2.5 align-top bg-blue-100 text-center">สื่อ/เครื่องมือ</th>
                                <th class="w-[10%] border border-black p-2.5 align-top bg-blue-100 text-center">การประเมินผล</th>
                                <th class="w-[10%] border border-black p-2.5 align-top bg-blue-100 text-center">CLO</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Rows will be added/loaded by JavaScript --}}
                        </tbody>
                    </table>
                </div>
                {{-- =========================================================================================================================================================================================================================== --}}
                <div id="section-8-container">
                    <h2 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                        หมวดที่ 8 : การประเมินการบรรลุผลลัพธ์การเรียนรู้รายวิชา (CLOs) </h2>
                    <h3 class="mt-6 font-bold">8.1 กลยุทธ์การประเมิน</h3>
                    <div class="mb-4">
                        จำนวนรายการประเมิน:
                        {{-- Changed default value to 3 --}}
                        <input type="number" id="AssessmentCount" value="3" min="1" max="20" class="w-[70px] border border-gray-300 rounded px-2 py-1">
                        <button id="generateAssessmentTableBtn" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">สร้างตาราง</button>
                    </div>
                    <table id="assessmentTable" class="w-full border-collapse border border-black text-sm">
                        <thead>
                            <tr>
                                <th class="w-[25%] border border-black p-2.5 align-top bg-blue-100 text-center">วิธีการประเมิน</th>
                                <th class="w-[35%] border border-black p-2.5 align-top bg-blue-100 text-center">เครื่องมือ / รายละเอียด</th> {{-- Adjusted width --}}
                                <th class="w-[10%] border border-black p-2.5 align-top bg-blue-100 text-center">สัดส่วน (%)</th>
                                <th class="w-[30%] border border-black p-2.5 align-top bg-blue-100 text-center">ความสอดคล้องกับ CLOs</th> {{-- Adjusted width --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Rows will be added/loaded by JavaScript --}}
                        </tbody>
                    </table>

                    <h3 class="mt-6 font-bold">8.2 วิธีการประเมิน แบบรูบริค (Rubric) หรือ อื่นๆ (ถ้ามี) </h3>
                    <div id="rubric-container" class="max-w-4xl mx-auto space-y-8">
                        <div class="rubric-section hidden" id="rubric-template">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-base font-semibold p-1 rubric-title" contenteditable="true" data-field="">
                                    ก. [หัวข้อเกณฑ์]
                                </h3>
                                <button class="delete-rubric-btn px-3 py-1 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 ml-4">ลบ</button>
                            </div>
                            <div class="overflow-x-auto border border-gray-400">
                                <table class="w-full border-collapse text-xs rubric-table">
                                    <thead class="bg-gray-100 text-center font-semibold">
                                        <tr>
                                            <th class="border border-gray-400 p-1 w-32">ระดับ</th>
                                            <th class="border border-gray-400 p-1 rubric-header" contenteditable="true" data-field="">
                                                คำอธิบายเกณฑ์
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white rubric-tbody">
                                        {{-- Rubric rows will be added by JS --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-4xl mx-auto mt-6 text-center">
                        <button id="add-rubric-btn" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700">
                            + เพิ่มเกณฑ์การประเมินใหม่
                        </button>
                    </div>
                    <br>
                </div>
                {{-- =========================================================================================================================================================================================================================== --}}
                <div id="section-9-container">
                    <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                        หมวดที่ 9: สื่อการเรียนรู้และงานวิจัย
                    </h3>
                    <h3 class="mt-6 font-bold">9.1 สื่อการเรียนรู้และสิ่งสนับสนุนการเรียนรู้ </h3>
                    <ul id="referenceList" class="list-disc pl-8 max-w-2xl mt-4">
                        {{-- Items will be loaded/added by JavaScript --}}
                    </ul>
                    <button id="addReferenceItemBtn" class="mt-4 bg-blue-600 text-white border-none rounded-lg px-3.5 py-2 cursor-pointer text-[14px] hover:bg-blue-700">
                        ➕ เพิ่มรายการใหม่
                    </button>
                    <br><br>

                    <h3 class="mt-6 font-bold">9.2 งานวิจัยที่นำมาสอนในรายวิชา</h3><br>
                    <textarea
                        name="research_subjects"
                        class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                            focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                        placeholder='พิมพ์รายละเอียดเกี่ยวกับงานวิจัยที่นำมาสอนในรายวิชา...'
                    >{{ $data->research_subjects ?? '' }}</textarea>

                    <h3 class="mt-6 font-bold">9.3 การบริการวิชาการ</h3><br>
                    <textarea
                        name="academic_service"
                        class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                            focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                        placeholder='พิมพ์รายละเอียดเกี่ยวกับการบริการวิชาการ...'
                    >{{ $data->academic_service ?? '' }}</textarea>

                    <h3 class="mt-6 font-bold">9.4 งานทำนุบำรุงศิลปวัฒนธรรม</h3><br>
                    <textarea
                        name="art_culture"
                        class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                            focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                        placeholder='พิมพ์รายละเอียดเกี่ยวกับงานทำนุบำรุงศิลปวัฒนธรรม...'
                    >{{ $data->art_culture ?? '' }}</textarea>
                </div>
                {{-- =========================================================================================================================================================================================================================== --}}
                <div class="mt-8">
                    <h2 class="w-full bg-blue-100 text-gray-800 font-semibold text-left p-2 border border-gray-400">
                        หมวดที่ 10 : เกณฑ์การประเมิน
                    </h2>
                    <table id="gradeTable" class="w-full border-collapse border border-gray-400 text-sm">
                        <thead class="bg-gray-200 font-semibold text-center">
                            <tr>
                                <th class="w-1/2 border border-gray-400 p-2 align-middle">ระดับผลการศึกษา</th>
                                <th class="w-1/2 border border-gray-400 p-2 align-middle">เกณฑ์การประเมินผล</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @php
                                $gradingCriteria = (object) ($data->grading_criteria ?? []);
                            @endphp
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_A_level">
                                    {{ $gradingCriteria->grade_A_level ?? 'A' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_A_criteria">
                                    {{ $gradingCriteria->grade_A_criteria ?? '80.00% ขึ้นไป' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Bp_level">
                                    {{ $gradingCriteria->grade_Bp_level ?? 'B+' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Bp_criteria">
                                    {{ $gradingCriteria->grade_Bp_criteria ?? '75.00% - 79.99%' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_B_level">
                                    {{ $gradingCriteria->grade_B_level ?? 'B' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_B_criteria">
                                    {{ $gradingCriteria->grade_B_criteria ?? '70.00% - 74.99%' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Cp_level">
                                    {{ $gradingCriteria->grade_Cp_level ?? 'C+' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Cp_criteria">
                                    {{ $gradingCriteria->grade_Cp_criteria ?? '65.00% - 69.99%' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_C_level">
                                    {{ $gradingCriteria->grade_C_level ?? 'C' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_C_criteria">
                                   {{ $gradingCriteria->grade_C_criteria ?? '60.00% - 64.99%' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Dp_level">
                                    {{ $gradingCriteria->grade_Dp_level ?? 'D+' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Dp_criteria">
                                    {{ $gradingCriteria->grade_Dp_criteria ?? '55.00% - 59.99%' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_D_level">
                                    {{ $gradingCriteria->grade_D_level ?? 'D' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_D_criteria">
                                    {{ $gradingCriteria->grade_D_criteria ?? '50.00% - 54.99%' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_F_level">
                                    {{ $gradingCriteria->grade_F_level ?? 'F' }}</td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_F_criteria">
                                   {{ $gradingCriteria->grade_F_criteria ?? 'ต่ำกว่า 50.00%' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- =========================================================================================================================================================================================================================== --}}
                <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                    หมวด 11 : ขั้นตอนการแก้ไขคะแนน
                </h3><br>
                <textarea
                    name="grade_correction"
                    class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                    placeholder='พิมพ์รายละเอียด'
                >{{ $data->grade_correction ?? '' }}</textarea>
            </div>
        </div>
    </div> 
    <script> 
    document.addEventListener('DOMContentLoaded', () => {
        async function saveData(fieldName, value, targetElement = null) {
            const saveUrl = '/savedataedit';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const urlParams = new URLSearchParams(window.location.search);
            const courseId = urlParams.get('course_id');
            const year = urlParams.get('year');
            const term = urlParams.get('term');
            const tqf = urlParams.get('TQF');
            
            // Find the element for feedback, try different selectors
            let element = document.querySelector(`[data-field="${CSS.escape(fieldName)}"]`);
            if (!element) element = document.querySelector(`[name="${CSS.escape(fieldName)}"]`);
            if (!element) element = document.querySelector(`[id="${CSS.escape(fieldName)}"]`);

            if (!element && fieldName.startsWith('curriculum_map_r')) {
                const matches = fieldName.match(/curriculum_map_r(\d+)_c(\d+)/);
                if (matches) element = document.querySelector(`#curriculum-mapping-table .score-cell[data-row="${matches[1]}"][data-col="${matches[2]}"]`);
            }

            // Special handling for checkboxes inside a cell for Section 6
            if (fieldName.startsWith('s6_r') && fieldName.includes('_cb')) {
                // Find the checkbox by name
                element = document.querySelector(`input[name="${CSS.escape(fieldName)}"]`);
            }
            // Special handling for section 6 trigger
            if (!element && fieldName === 'section6_data') {
                element = document.getElementById('cloTable_S6');
            }
            // Special handling for section 7 trigger
            if (!element && fieldName === 'section7_data') {
                element = document.getElementById('planTable');
            }
            // Special handling for section 8.1 trigger
            if (!element && fieldName === 'section8_1_data') {
                element = document.getElementById('assessmentTable');
            }
            // Special handling for section 8.2 trigger (Rubrics)
            if (!element && fieldName === 'section8_2_data') {
                element = document.getElementById('rubric-container');
            }
            // Special handling for section 9.1 trigger (References)
            if (!element && fieldName === 'section9_1_data') {
                element = document.getElementById('referenceList');
            }
            if (!element && fieldName.startsWith('plo')) {
                element = document.getElementById('plosTableBody');
            }
            
            console.log('Saving:', { 
                field: fieldName, 
                value: value, 
                course_id: courseId,
                year: year, 
                term: term, 
                TQF: tqf 
            });
            
            // แสดง feedback (เช่น ไฮไลต์สีเหลือง "กำลังบันทึก")
            let originalColor = '';
            // Give feedback on the element or its parent row/container if element itself is hidden/small
            let feedbackElement = null
            // Priority 1: Use the element that triggered the event, if passed
            if (targetElement) {
                 // Find the specific cell, list item, or rubric section
                 feedbackElement = targetElement.closest('td') || targetElement.closest('li') || targetElement.closest('.rubric-section');
                 // If it's still nothing (e.g., a simple input), use the element itself
                 if (!feedbackElement) {
                     feedbackElement = targetElement;
                 }
            }

            if (!feedbackElement) {
                feedbackElement = element; // Use the element found by fieldName (which might be the table)
            }

             // ตรวจสอบว่า formContainer ถูกกำหนดค่าแล้ว
            const formContainer = document.getElementById('tqf-form-container'); 
            if (!feedbackElement && formContainer) feedbackElement = formContainer; 

            if (feedbackElement) {
                originalColor = feedbackElement.style.backgroundColor;
                feedbackElement.style.transition = 'background-color 0.1s ease';
                feedbackElement.style.backgroundColor = '#fffbdd';
            }

            try {
                const response = await fetch(saveUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        course_id: courseId, year: year, term: term, TQF: tqf,
                        field: fieldName,
                        value: typeof value === 'object' ? JSON.stringify(value) : value
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Server error response:', errorData);
                    const errorMessages = errorData.errors ? Object.values(errorData.errors).flat().join(' ') : (errorData.error || 'Unknown server error');
                    throw new Error(`Server error: ${response.status} - ${errorData.message || 'Error'} (${errorMessages})`);
                }

                const result = await response.json();
                console.log('Save successful:', result);

                // เปลี่ยนเป็นสีเขียวเมื่อสำเร็จ
                if (feedbackElement) {
                    feedbackElement.style.backgroundColor = '#d4edda'; // Light green
                    setTimeout(() => {
                         if(feedbackElement) { // Check if element still exists
                            feedbackElement.style.backgroundColor = originalColor;
                            feedbackElement.style.transition = '';
                         }
                    }, 1000);
                }

            } catch (error) {
                console.error('Save failed:', error);
                if (feedbackElement) {
                    feedbackElement.style.backgroundColor = '#f8d7da'; // Light red
                    setTimeout(() => {
                         if(feedbackElement) { // Check if element still exists
                             feedbackElement.style.backgroundColor = originalColor;
                             feedbackElement.style.transition = '';
                         }
                    }, 2000);
                }
            }
        }

        function getFieldName(element) {
            // --- Check if the element is within Section 6 table FIRST ---
            const section6Container = element.closest('#section-6-container');
            if (section6Container) {
                return 'section6_data_trigger';
            }
            // --- Check if the element is within Section 7 table ---
            const section7Container = element.closest('#section-7-container');
            if (section7Container) {
                if(element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') {
                return 'section7_data_trigger';
                }
            }
            // --- Check if the element is within Section 8 container ---
            const section8Container = element.closest('#section-8-container');
            if (section8Container) {
                if (element.closest('#assessmentTable')) {
                    if(element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') {
                        return 'section8_1_data_trigger';
                    }
                }
                if (element.closest('#rubric-container')) {
                    if (element.hasAttribute('contenteditable') || element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') {
                        return 'section8_2_data_trigger';
                    }
                }
            }
            // --- Check if the element is within Section 9 container ---
            const section9Container = element.closest('#section-9-container');
            if (section9Container) {
                if (element.closest('#referenceList') && (element.tagName === 'INPUT' || element.classList.contains('remove-btn'))) {
                    return 'section9_1_data_trigger';
                }
                if (element.tagName === 'TEXTAREA' && element.name && !element.closest('#referenceList')) {
                    return element.name;
                }
            }

            // Priority: data-field > name > id (for other sections)
            if (element.dataset.field) return element.dataset.field;
            if (element.name) return element.name;
            if (element.id) return element.id;

             // Fallback
            const cell = element.closest('td, th');
            const row = element.closest('tr');
            const table = element.closest('table');
            if (!table || !row || !cell) return null;

            const tableId = table.id || 'unknown_table';
            const parentElement = row.parentElement;
            const rowIndex = Array.from(parentElement.children).indexOf(row);
            const cellIndex = Array.from(row.children).indexOf(cell);
            const query = element.tagName;
            const elementsInCell = Array.from(cell.querySelectorAll(query + ', [contenteditable="true"]'));
            const elementIndex = elementsInCell.length > 1 ? elementsInCell.indexOf(element) : 0;

            // Specific names for JSON tables
            if (tableId === 'curriculum-mapping-table' && element.classList.contains('score-cell')) {
                const mapRowIndex = element.dataset.row;
                const mapColIndex = element.dataset.col;
                return `curriculum_map_r${mapRowIndex}_c${mapColIndex}`;
            }
            if (tableId === 'ploTable' || tableId === 'plosTableBody') {
                //Get code from the first cell
                const codeCell = row.cells[0];
                const code = codeCell ? codeCell.textContent.trim() : null;
                if (!code) return null; // Can't get code, stop

                if (element.tagName === 'SPAN' && element.hasAttribute('contenteditable')) {
                    return `cloLll_desc_${code}`;
                } else { // Handle checkbox and select
                    const mapColIndex = cellIndex - 2; // Col index (0-3)
                    if (mapColIndex >= 0) {
                        const inputType = element.type === 'checkbox' ? 'check' : 'level';
                        // Use code instead of rowIndex
                        return `plo_map_${code}_c${mapColIndex}_${inputType}`;
                    }
                }
                if(element.name && element.name.startsWith('plo')) return element.name;
            }
            if (tableId && tableId.startsWith('rubricTable_') && element.hasAttribute('contenteditable')) { if(element.dataset.field) return element.dataset.field; }
            if (element.name && element.name.startsWith('reference_') && element.tagName === 'INPUT') { return element.name; }
            if (tableId === 'gradeTable' && element.hasAttribute('contenteditable')) { if(element.dataset.field) return element.dataset.field; }

            console.warn("Using fallback naming for element:", element);
            return `${tableId}_r${rowIndex}_c${cellIndex}_e${elementIndex}`;
        }

        //จัดการ event 'change' สำหรับ input, textarea, select 
        function handleFormChange(event) {
            const target = event.target;
            const nodeName = target.nodeName;
            let fieldName = getFieldName(target);

            if (!fieldName) { console.warn("Could not determine field name on change:", target); return; }

            // --- Trigger checks ---
            if (fieldName === 'section6_data_trigger') {
                const section6JSON = getSection6Data();
                saveData('section6_data', section6JSON, target);
                return;
            }
            if (fieldName === 'section7_data_trigger') {
                const section7JSON = getSection7Data();
                saveData('section7_data', section7JSON, target);
                return;
            }
            if (fieldName === 'section8_1_data_trigger') {
                const section8_1JSON = getSection8_1Data();
                saveData('section8_1_data', section8_1JSON, target);
                return;
            }
            if (fieldName === 'section8_2_data_trigger') {
                const section8_2JSON = getSection8_2Data();
                saveData('section8_2_data', section8_2JSON, target);
                return;
            }
            if (fieldName === 'section9_1_data_trigger') {
                return;
            }

            // --- Individual field saving ---
            let value;
            if (nodeName === 'INPUT') {
                value = (target.type === 'checkbox') ? target.checked : target.value;
            } else if (nodeName === 'TEXTAREA' || nodeName === 'SELECT') {
                value = target.value;
            } else { return; }
            saveData(fieldName, value, target);
        }
        
        //จัดการ event 'blur' สำหรับ [contenteditable=true]
        function handleFormBlur(event) {
            const target = event.target;
            if (target instanceof HTMLElement && target.isContentEditable) {
                let fieldName = getFieldName(target);
                if (!fieldName) { console.warn("Could not determine field name on blur:", target); return; }

                // --- Trigger checks ---
                if (fieldName === 'section6_data_trigger') {
                    if (target.classList.contains('clo-cell-s6')) {
                        const section6JSON = getSection6Data(); saveData('section6_data', section6JSON, target);
                    } return;
                }
                if (fieldName === 'section7_data_trigger') { return; }
                if (fieldName === 'section8_1_data_trigger') { return; }
                if (fieldName === 'section8_2_data_trigger') {
                    const section8_2JSON = getSection8_2Data(); saveData('section8_2_data', section8_2JSON, target);
                    return;
                }
                if (fieldName === 'section9_1_data_trigger') {
                    const section9_1JSON = getSection9_1Data(); saveData('section9_1_data', section9_1JSON, target);
                    return;
                }

                // --- Individual field saving ---
                const value = target.textContent.trim();
                saveData(fieldName, value, target);
            }
            if (target.tagName === 'INPUT' && target.closest('#referenceList')) {
                const section9_1JSON = getSection9_1Data();
                saveData('section9_1_data', section9_1JSON, target);
            }
            if (target.tagName === 'TEXTAREA' && target.closest('#section-9-container')) {
                if (!target.closest('#referenceList')) {
                    if (target.name) {
                        saveData(target.name, target.value, target);
                    }
                }
            }
        }

        // 3. ผูก Event Listeners เข้ากับ Container หลัก
        const formContainer = document.querySelector('.w-\\[210mm\\]');
        if (formContainer) {
            // สำหรับ <input>, <textarea>, <select>
            formContainer.addEventListener('change', handleFormChange);
            
            // สำหรับ [contenteditable=true]
            // เราใช้ 'blur' และตั้งค่า useCapture=true
            // เพื่อให้ event ทำงานที่ div นี้ก่อน แม้ว่า element ข้างในจะ blur ก็ตาม
            formContainer.addEventListener('blur', handleFormBlur, true);
        }

        // Section 5.1
        try {
            const addRowBtn = document.getElementById('addPloRowBtn');
            const tableBody = document.getElementById('plosTableBody');

            if (addRowBtn && tableBody) {
                addRowBtn.addEventListener('click', function() {
                    let currentRowIndex = parseInt(tableBody.dataset.maxPlo, 10);
                    let newRowIndex = currentRowIndex + 1;
                    tableBody.dataset.maxPlo = newRowIndex; // Update max index

                    const newRow = document.createElement('tr');
                    const rowHtmlTemplate = `
                        <td class="text-center border border-black p-2.5 align-top">${newRowIndex}</td>
                        <td class="border border-black p-2.5 align-top">
                            <textarea name="plo${newRowIndex}_outcome" rows="3" class="w-full border border-gray-300 rounded p-1"></textarea>
                        </td>
                        <td class="text-center border border-black p-2.5 align-top">
                            <input type="checkbox" name="plo${newRowIndex}_specific" class="scale-125" value="1">
                        </td>
                        <td class="text-center border border-black p-2.5 align-top">
                            <input type="checkbox" name="plo${newRowIndex}_generic" class="scale-125" value="1">
                        </td>
                        <td class="text-center border border-black p-2.5 align-top">
                            <select name="plo${newRowIndex}_level" class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">--เลือก--</option>
                                <option value="R">R</option> <option value="U">U</option>
                                <option value="AP">AP</option> <option value="AN">AN</option>
                                <option value="E">E</option> <option value="C">C</option>
                            </select>
                        </td>
                        <td class="text-center border border-black p-2.5 align-top">
                            <select name="plo${newRowIndex}_type" class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">--เลือก--</option>
                                <option value="K">K</option> <option value="S">S</option>
                                <option value="AR">AR</option> <option value="Rs">Rs</option>
                            </select>
                        </td>
                    `;
                    newRow.innerHTML = rowHtmlTemplate;
                    tableBody.appendChild(newRow);

                    // Automatically save the new empty row structure?
                    const firstInput = newRow.querySelector('textarea[name^="plo"]');
                    if(firstInput) {
                        saveData(firstInput.name, firstInput.value);
                    }
                });
            } else {
                console.warn("Could not find Add PLO button or table body.");
            }
        } catch(e) {
            console.error("Error setting up Section 5.1 Add Row:", e);
        }
        
        // Section 5.2
        try {
            const curriculumMapData = @json($data->curriculum_map_data ?? []);
            const stateSymbols_s5_2 = { '0': '&nbsp;', '1': '<span class="text-xl font-bold">●</span>', '2': '<span class="text-xl">○</span>' };
            const mappingTable = document.getElementById('curriculum-mapping-table');

            // ตรวจสอบว่าข้อมูลเป็น Array และมี Object ตัวแรกอยู่หรือไม่
            if (mappingTable && Array.isArray(curriculumMapData) && curriculumMapData.length > 0 && typeof curriculumMapData[0] === 'object') {
                
                const rowData = curriculumMapData[0] || {}; // ใช้ || {} เผื่อเป็น null

                const cells = mappingTable.querySelectorAll('.score-cell');
                cells.forEach(cell => {
                    const rowIndex = parseInt(cell.dataset.row); 
                    const colIndex = parseInt(cell.dataset.col); 
                    
                    // ดึงค่า state จาก Object โดยใช้ colIndex เป็น Key (แปลงเป็น String)
                    const state = rowData[String(colIndex)] ?? 0; // ถ้า Key ไม่มีอยู่ ให้ใช้ค่า Default เป็น 0

                    cell.dataset.state = state;
                    cell.innerHTML = stateSymbols_s5_2[state] || '&nbsp;';

                    cell.addEventListener('click', () => {
                        let currentState = parseInt(cell.dataset.state || '0');
                        let nextState = (currentState + 1) % 3;
                        cell.dataset.state = nextState;
                        cell.innerHTML = stateSymbols_s5_2[nextState];
                        const fieldName = `curriculum_map_r${rowIndex}_c${colIndex}`;
                        saveData(fieldName, nextState); 
                    });
                });
            } else { // Fallback กรณีข้อมูลไม่ถูกต้อง หรือไม่มี
                 console.warn("Curriculum mapping table or data not found/invalid format:", curriculumMapData);
                 const cells = mappingTable ? mappingTable.querySelectorAll('.score-cell') : [];
                 cells.forEach(cell => {
                     cell.dataset.state = '0';
                     cell.innerHTML = stateSymbols_s5_2['0'];
                     
                     cell.addEventListener('click', () => {
                         let currentState = parseInt(cell.dataset.state || '0');
                         let nextState = (currentState + 1) % 3;
                         cell.dataset.state = nextState;
                         cell.innerHTML = stateSymbols_s5_2[nextState];
                         const rowIndex = parseInt(cell.dataset.row);
                         const colIndex = parseInt(cell.dataset.col);
                         const fieldName = `curriculum_map_r${rowIndex}_c${colIndex}`;
                         saveData(fieldName, nextState);
                     });
                 });
            }
        } catch (e) {
            console.error("Error initializing Section 5.2 (Curriculum Map Display):", e);
        }

        // Section 5.3
        try {
            // Get ai_text data
            const aiTextJson = @json($data->ai_text ?? '{}');
            let aiTextData = {};
            let cloData = [];

            try {
                if (typeof aiTextJson === 'string' && aiTextJson.trim() !== '') {
                    aiTextData = JSON.parse(aiTextJson);
                } else if (typeof aiTextJson === 'object' && aiTextJson !== null) {
                    aiTextData = aiTextJson; // Already an object
                }
            } catch (parseError) {
                console.error("Error parsing ai_text JSON:", parseError, aiTextJson);
            }

            // Build CLO data from ai_text
            if (Object.keys(aiTextData).length > 0) {
                Object.keys(aiTextData).forEach(key => {
                    // key is "CLO 1", "CLO 2" etc.
                    const details = aiTextData[key];
                    if (details && typeof details === 'object' && details.CLO) {
                        cloData.push({
                            code: key.replace(/\s/g, ''), // Normalize "CLO 1" to "CLO1"
                            description: details.CLO
                        });
                    }
                });
                
                // Sort CLO data numerically
                cloData.sort((a, b) => {
                    const numA = parseInt(a.code.replace('CLO', '')) || 0;
                    const numB = parseInt(b.code.replace('CLO', '')) || 0;
                    return numA - numB;
                });
                
            } else {
                console.warn("ai_text is empty or invalid, using fallback CLOs for 5.3");
            }

            // Define LLL data
            const lllData = [
                { code: "LLL1", description: "Creativity ความคิดสร้างสรรค์" },
                { code: "LLL2", description: "Problem Solving การแก้ปัญหา" },
                { code: "LLL3", description: "Critical Thinking การคิดเชิงวิพากษ์" },
                { code: "LLL4", description: "Leadership การเป็นผู้นำ" },
                { code: "LLL5", description: "Communication การสื่อสาร" },
                { code: "LLL6", description: "Collaboration การประสานงาน" },
                { code: "LLL7", description: "Information Management การจัดการข้อมูล " },
                { code: "LLL8", description: "Adaptability การปรับตัว" },
                { code: "LLL9", description: "Curiosity ความอยากรู้อยากเห็น" },
                { code: "LLL10", description: "Reflection การสะท้อนทักษะความรู้" }
            ];
            
            // Combine lists
            const cloLllData = [...cloData, ...lllData];

            const ploCount = {{ count($outcomeStatements) }};

            const levelOptions = ["", "R", "U", "AP", "AN", "E", "C"];
            const courseAccordData = @json($data->course_accord ?? []);
            const tbody = document.querySelector("#ploTable tbody");

            if (tbody) {
                tbody.innerHTML = '';
                cloLllData.forEach((item, rowIndex) => {
                    const tr = document.createElement("tr");
                    const itemCode = item.code ?? `Item ${rowIndex+1}`;
                    const itemDesc = item.description ?? '';

                    tr.innerHTML = `
                        <td class="border border-gray-400 p-2 text-center font-bold">${itemCode}</td>
                        <td class="border border-gray-400 p-2 text-left">
                           <span
                                class="inline-block w-full hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500 p-1 rounded"
                                contenteditable="true"
                                data-field="cloLll_desc_${itemCode}"
                           >${itemDesc}</span>
                        </td>
                    `;

                    for (let colIndex = 0; colIndex < ploCount; colIndex++) {
                        const td = document.createElement("td");
                        td.className = "border border-gray-400 p-2 text-center";
                        const ploDbIndex = colIndex + 1;
                        let savedCellData = { check: false, level: '' };

                        if (courseAccordData && courseAccordData[itemCode] !== undefined) {
                            const rowData = courseAccordData[itemCode];
                            
                            if (rowData && rowData[ploDbIndex] !== undefined && typeof rowData[ploDbIndex] === 'object' && rowData[ploDbIndex] !== null) {
                                 savedCellData = {
                                     check: rowData[ploDbIndex].check ?? false,
                                     level: rowData[ploDbIndex].level ?? ''
                                 };
                            }
                        }

                        const checkbox = document.createElement("input");
                        checkbox.type = "checkbox";
                        checkbox.className = "mr-1.5 scale-125 plo-map-checkbox";
                        checkbox.name = `plo_map_${itemCode}_c${colIndex}_check`; 
                        checkbox.checked = savedCellData.check;

                        const select = document.createElement("select");
                        select.className = "w-[70px] p-1 border rounded plo-map-level";
                        select.name = `plo_map_${itemCode}_c${colIndex}_level`;
                        levelOptions.forEach(opt => {
                            const op = document.createElement("option");
                            op.value = opt;
                            op.textContent = opt;
                            if (savedCellData.level === opt) op.selected = true;
                            select.appendChild(op);
                        });

                        td.appendChild(checkbox);
                        td.appendChild(select);
                        tr.appendChild(td);
                    }
                    tbody.appendChild(tr);
                });
            } else {
                 console.warn("Table body for PLO/CLO map (#ploTable tbody) not found.");
            }
        } catch (s5Error) {
            console.error("Error initializing Section 5.3 (PLO/CLO Map Display & Edit):", s5Error);
        }

        // Section 6
        try {
            const data_s6_teachingOptions = { onSite: { label: "On-site โดยมีการเรียนการสอนแบบ", items: ["บรรยาย (Lecture)","ฝึกปฏิบัติ (Laboratory Model)","เรียนรู้จากการลงมือทำ (Learning by Doing)","การเรียนรู้โดยใช้กิจกรรมเป็นฐาน (Activity-based Learning)","การเรียนรู้โดยใช้วิจัยเป็นฐาน (Research-based Learning)","ถามตอบสะท้อนคิด (Refractive Learning)","นำเสนออภิปรายกลุ่ม (Discussion Group)","เรียนรู้จากการสืบเสาะหาความรู้ (Inquiry-based Learning)","การเรียนรู้แบบร่วมมือร่วมใจ (Cooperative Learning)","การเรียนรู้แบบร่วมมือ (Collaborative Learning)","การเรียนรู้โดยใช้โครงการเป็นฐาน (Project-based Learning: PBL)","การเรียนรู้โดยใช้ปัญหาเป็นฐาน (Problem-based Learning)","วิเคราะห์โจทย์ปัญหา (Problem Solving)","เรียนรู้การตัดสินใจ (Decision Making)","ศึกษาค้นคว้าจากกรณีศึกษา (Case Study)","เรียนรู้ผ่านการเกม/การเล่น (Game/Play Learning)","ศึกษาดูงาน (Field Trips)","อื่น ๆ....................................." ], indent: true }, online: { label: "Online โดยมีการเรียนการสอนแบบ", items: ["บรรยาย (Lecture)"], indent: true } };
            const data_s6_assessmentOptions = { exam: { label: "คะแนนจากแบบทดสอบสัมฤทธิ์ผล (ข้อสอบ)", items: ["ทดสอบย่อย (Quiz)","ทดสอบกลางภาค (Midterm)","ทดสอบปลายภาค (Final)" ] }, performance: { label: "คะแนนจากผลงานที่ได้รับมอบหมาย (Performance) โดยใช้เกณฑ์ Rubric Score การทำงานกลุ่มและเดี่ยว", items: ["การทำงานเป็นทีม (Team Work)","โปรแกรม ซอฟต์แวร์","ผลงาน ชิ้นงาน","รายงาน (Report)","การนำเสนอ (Presentation)","แฟ้มสะสมงาน (Portfolio)","รายงานการศึกษาด้วยตนเอง (Self-Study Report)" ] }, behavior: { label: "คะแนนจากผลการพฤติกรรม", items: ["ความรับผิดชอบ การมีส่วนร่วม","ประเมินผลการงานที่ส่ง" ] } };

            function getSection6Data() {
                const sectionData = {};
                const tableBody = document.getElementById('cloTableBody_S6');
                if (!tableBody) return sectionData;

                const rows = tableBody.querySelectorAll('tr.clo-row');

                rows.forEach((row, rowIndex) => {
                    const cloCell = row.querySelector('.clo-cell-s6');
                    let cloKey = cloCell ? cloCell.textContent.trim().replace(/ \[คลิกเพื่อแก้ไข\]$/, '').trim().replace(/\s/g, '') : `CLO${rowIndex + 1}`;
                    if (!cloKey) cloKey = `CLO${rowIndex + 1}`;

                    const teachingMethods = [];
                    row.querySelectorAll('.teaching-cell-s6 input[type="checkbox"]:checked').forEach(cb => {
                        const labelSpan = cb.closest('label')?.querySelector('span');
                        if (labelSpan) teachingMethods.push(labelSpan.textContent.trim());
                    });

                    const assessmentMethods = [];
                    row.querySelectorAll('.assessment-cell-s6 input[type="checkbox"]:checked').forEach(cb => {
                        const labelSpan = cb.closest('label')?.querySelector('span');
                        if (labelSpan) assessmentMethods.push(labelSpan.textContent.trim());
                    });

                    sectionData[cloKey] = [
                        { "วิธีการสอน": teachingMethods },
                        { "การประเมินผล": assessmentMethods }
                    ];
                });
                console.log("Prepared Section 6 Data:", sectionData);
                return sectionData;
            }

            function initializeCloTable_Section6() {
                const tableBody = document.getElementById('cloTableBody_S6');
                const addBtn = document.getElementById('addCloBtn_S6');
                const template = document.getElementById('cloRowTemplate_S6');
                if (!tableBody || !addBtn || !template) {
                    console.warn("Section 6 elements not found.");
                    return;
                }

                let section6Data = {};
                try {
                    const jsonDataString = @json($data->teaching_methods ?? null);
                    if (typeof jsonDataString === 'string' && jsonDataString.length > 0 && jsonDataString !== 'null') {
                        section6Data = JSON.parse(jsonDataString);
                    } else if (typeof jsonDataString === 'object' && jsonDataString !== null) {
                        section6Data = jsonDataString;
                    }
                    if (typeof section6Data !== 'object' || section6Data === null) section6Data = {};
                    console.log("Loaded Section 6 Data:", section6Data);
                } catch (parseError) {
                    console.error("Error parsing section 6 JSON data:", parseError, @json($data->teaching_methods ?? null));
                    section6Data = {};
                }

                let cloKeysFromAI = [];
                try {
                    const aiTextJson = @json($data->ai_text ?? '{}');
                    let aiTextData = {};
                    
                    if (typeof aiTextJson === 'string' && aiTextJson.trim() !== '') {
                        aiTextData = JSON.parse(aiTextJson);
                    } else if (typeof aiTextJson === 'object' && aiTextData !== null) {
                        aiTextData = aiTextJson;
                    }
                    
                    if (Object.keys(aiTextData).length > 0) {
                        Object.keys(aiTextData).forEach(key => {
                            const details = aiTextData[key];
                            if (details && typeof details === 'object' && details.CLO) {
                                // Normalize key ("CLO 1" -> "CLO1")
                                cloKeysFromAI.push(key.replace(/\s/g, ''));
                            }
                        });
                        
                        // Sort keys numerically
                        cloKeysFromAI.sort((a, b) => {
                            const numA = parseInt(a.replace('CLO', '')) || 0;
                            const numB = parseInt(b.replace('CLO', '')) || 0;
                            return numA - numB;
                        });
                    }
                    console.log("Loaded CLO Keys from ai_text for S6:", cloKeysFromAI);
                } catch (e) {
                    console.error("Error parsing ai_text for S6:", e, @json($data->ai_text ?? '{}'));
                }

                // rowData (e.g., [ {"วิธีการสอน":...}, {"การประเมินผล":...} ])
                function _s6_createCheckboxHtml(optionsObject, type, rowData) {
                    let html = '';
                    let checkedItems = [];
                    const cloEntry = rowData;

                    if(cloEntry && Array.isArray(cloEntry)) {
                        const typeKey = (type === 'teach') ? 'วิธีการสอน' : 'การประเมินผล';
                        const relevantObject = cloEntry.find(obj => obj && typeof obj === 'object' && obj.hasOwnProperty(typeKey));
                        if(relevantObject && Array.isArray(relevantObject[typeKey])) {
                            checkedItems = relevantObject[typeKey].map(item => String(item).trim());
                        }
                    }

                    for (const key in optionsObject) {
                        const category = optionsObject[key];
                        html += `<div class="font-semibold mt-2">${category.label}</div>`;
                        category.items.forEach(itemText => {
                            const indentClass = category.indent ? 'ml-4' : '';
                            const isChecked = checkedItems.includes(String(itemText).trim());
                            html += `
                                <label class="flex items-start ${indentClass} hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" class="mt-1 scale-125 mr-1.5 s6-checkbox" ${isChecked ? 'checked' : ''}>
                                    <span class="ml-2">${itemText}</span>
                                </label>
                            `;
                        });
                    }
                    return html;
                }

                function _s6_addNewCloRow(cloKey = null, rowData = null, rowIndex = -1) {
                    if (rowIndex === -1) rowIndex = tableBody.children.length;
                    if (!cloKey) cloKey = `CLO${rowIndex + 1}`;

                    const newRow = template.content.cloneNode(true);
                    const cloRowElement = newRow.querySelector('tr');
                    if (!cloRowElement) return;

                    const cloCell = newRow.querySelector('.clo-cell-s6');
                    const teachingCell = newRow.querySelector('.teaching-cell-s6');
                    const assessmentCell = newRow.querySelector('.assessment-cell-s6');

                    cloCell.textContent = cloKey;

                    teachingCell.innerHTML = _s6_createCheckboxHtml(data_s6_teachingOptions, 'teach', rowData);
                    assessmentCell.innerHTML = _s6_createCheckboxHtml(data_s6_assessmentOptions, 'assess', rowData);

                    tableBody.appendChild(newRow);
                }

                tableBody.innerHTML = '';
                
                if (cloKeysFromAI.length > 0) {
                    cloKeysFromAI.forEach((cloKey, index) => { // cloKey is "CLO1"
                        const savedData = section6Data[cloKey] || null; 
                        _s6_addNewCloRow(cloKey, savedData, index);
                    });
                } else {
                    const loadedCloKeys = Object.keys(section6Data);
                    if (loadedCloKeys.length > 0) {
                        loadedCloKeys.sort((a, b) => {
                             const numA = parseInt(a.replace('CLO', '')) || 0;
                             const numB = parseInt(b.replace('CLO', '')) || 0;
                             return numA - numB;
                        });
                        loadedCloKeys.forEach((key, index) => {
                            _s6_addNewCloRow(key, section6Data[key], index);
                        });
                    } else {
                        _s6_addNewCloRow(null, null, 0); 
                    }
                }

                addBtn.addEventListener('click', (event) => {
                    _s6_addNewCloRow();
                    const section6JSON = getSection6Data();
                    saveData('section6_data', section6JSON, event.target);
                });
            }
            initializeCloTable_Section6();
        } catch (e) {
            console.error("Error initializing Section 6 (CLO Table):", e);
        }

        // Section 7
        try {
            function getSection7Data() {
                const planData = [];
                const tableBody = document.querySelector("#planTable tbody");
                if (!tableBody) return planData;

                const rows = tableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    const weekNumber = index + 1; // Get week number from row index
                    const rowData = {
                        week: weekNumber,
                        topic: row.querySelector(`textarea[name="plan_topic_${weekNumber}"]`)?.value || '',
                        objective: row.querySelector(`textarea[name="plan_objective_${weekNumber}"]`)?.value || '',
                        activity: row.querySelector(`textarea[name="plan_activity_${weekNumber}"]`)?.value || '',
                        tool: row.querySelector(`textarea[name="plan_tool_${weekNumber}"]`)?.value || '',
                        assessment: row.querySelector(`textarea[name="plan_assessment_${weekNumber}"]`)?.value || '',
                        clo: row.querySelector(`select[name="plan_clo_${weekNumber}"]`)?.value || ''
                    };
                    planData.push(rowData);
                });
                console.log("Prepared Section 7 Data:", planData);
                return planData;
            }

            function createCloOptions(cloKeys, selectedClo) {
                let optionsHtml = '<option value="">--เลือก--</option>';
                
                // ตรวจสอบว่า cloKeys เป็น Array และมีข้อมูล
                if (!Array.isArray(cloKeys) || cloKeys.length === 0) {
                    console.warn("S7: No CLO keys from ai_text, using default fallback.");
                    // Fallback ถ้า ai_text ว่าง
                    optionsHtml += '<option value="CLO1" ' + (selectedClo === 'CLO1' ? 'selected' : '') + '>CLO 1</option>';
                    optionsHtml += '<option value="CLO2" ' + (selectedClo === 'CLO2' ? 'selected' : '') + '>CLO 2</option>';
                    optionsHtml += '<option value="CLO3" ' + (selectedClo === 'CLO3' ? 'selected' : '') + '>CLO 3</option>';
                } else {
                    // วน Loop สร้าง <option> จาก Keys ที่ได้
                    cloKeys.forEach(cloKey => { // cloKey is "CLO1"
                        const isSelected = (selectedClo === cloKey) ? 'selected' : '';
                        // "CLO1" -> "CLO 1" (สำหรับแสดงผล)
                        const cleanKey = cloKey.replace(/(\d+)$/, ' $1'); 
                        optionsHtml += `<option value="${cloKey}" ${isSelected}>${cleanKey}</option>`; 
                    });
                }
                return optionsHtml;
            }

            function generateTableLesson(forceInputCount = false) {
                const tbody = document.querySelector("#planTable tbody");
                if (!tbody) { console.warn("Table body for lesson plan (#planTable tbody) not found."); return; }
                tbody.innerHTML = "";
                const weekCountInput = document.getElementById("weekCount");
                
                // Load data
                const loadedPlanData = @json($data->plan_data ?? []);

                // Find the last week that actually has data
                let lastWeekWithData = 0;
                if (Array.isArray(loadedPlanData) && loadedPlanData.length > 0) {
                    // Loop backwards from the end
                    for (let i = loadedPlanData.length - 1; i >= 0; i--) {
                        const weekData = loadedPlanData[i] || {};
                        // Check if all fields (except week) are empty
                        const isEmpty = !(weekData.topic || weekData.objective || weekData.activity || weekData.tool || weekData.assessment || weekData.clo);
                        
                        if (!isEmpty) {
                            lastWeekWithData = parseInt(weekData.week || 0);
                            break; // Found the last non-empty week
                        }
                    }
                }
                
                // Determine minimum required weeks (based on data)
                const minWeekFromData = lastWeekWithData > 0 ? lastWeekWithData : 0; // e.g., 6 (or 0 if empty)

                // Get count from input field
                const inputWeekCount = (weekCountInput && !isNaN(parseInt(weekCountInput.value))) ? parseInt(weekCountInput.value) : 10; // Default 10

                // Determine final weekCount
                let weekCount;
                if(forceInputCount) {
                    // If button clicked (Req 3: Cannot go below data)
                    weekCount = Math.max(inputWeekCount, minWeekFromData); 
                } else {
                    // On initial load (Req 2: Start at input default OR data max, whichever is higher)
                    weekCount = Math.max(inputWeekCount, minWeekFromData);
                }
                
                // Enforce limits
                if (weekCount > 20) weekCount = 20;
                if (weekCount < 1) weekCount = 1;
                if (weekCountInput && parseInt(weekCountInput.value) !== weekCount) { // sync input
                    weekCountInput.value = weekCount;
                }

                let cloKeysFromAI = [];
                try {
                    const aiTextJson = @json($data->ai_text ?? '{}');
                    let aiTextData = {};
                    if (typeof aiTextJson === 'string' && aiTextJson.trim() !== '') {
                        aiTextData = JSON.parse(aiTextJson);
                    } else if (typeof aiTextJson === 'object' && aiTextJson !== null) {
                        aiTextData = aiTextJson;
                    }
                    
                    if (Object.keys(aiTextData).length > 0) {
                        Object.keys(aiTextData).forEach(key => {
                            const details = aiTextData[key];
                            if (details && typeof details === 'object' && details.CLO) {
                                cloKeysFromAI.push(key.replace(/\s/g, '')); // "CLO 1" -> "CLO1"
                            }
                        });
                        cloKeysFromAI.sort((a, b) => {
                            const numA = parseInt(a.replace('CLO', '')) || 0;
                            const numB = parseInt(b.replace('CLO', '')) || 0;
                            return numA - numB;
                        });
                    }
                } catch (e) {
                    console.error("Error parsing ai_text for S7:", e, @json($data->ai_text ?? '{}'));
                }

                for (let i = 1; i <= weekCount; i++) {
                    const weekData = loadedPlanData.find(item => parseInt(item.week) === i) || {};
                    const row = document.createElement("tr");

                    // Call the helper function
                    const cloOptions = createCloOptions(cloKeysFromAI, weekData.clo || '');

                    row.innerHTML = `
                        <td class="border border-black p-2.5 align-top text-center">${i}</td>
                        <td class="border border-black p-1 align-top">
                            <textarea name="plan_topic_${i}" placeholder="ใส่หัวข้อสัปดาห์ ${i}" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.topic || ''}</textarea>
                        </td>
                        <td class="border border-black p-1 align-top">
                            <textarea name="plan_objective_${i}" placeholder="ใส่วัตถุประสงค์" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.objective || ''}</textarea>
                        </td>
                        <td class="border border-black p-1 align-top">
                            <textarea name="plan_activity_${i}" placeholder="ใส่กิจกรรมการเรียนรู้" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.activity || ''}</textarea>
                        </td>
                        <td class="border border-black p-1 align-top">
                            <textarea name="plan_tool_${i}" placeholder="ใส่สื่อ/เครื่องมือ" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.tool || ''}</textarea>
                        </td>
                        <td class="border border-black p-1 align-top">
                            <textarea name="plan_assessment_${i}" placeholder="ใส่การประเมินผล" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.assessment || ''}</textarea>
                        </td>
                        <td class="border border-black p-2.5 align-top text-center">
                            <select name="plan_clo_${i}" class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                ${cloOptions}
                            </select>
                        </td>
                    `;
                    tbody.appendChild(row);
                }
            }
            const lessonBtn = document.getElementById('generateLessonTableBtn');
            if (lessonBtn) {
                lessonBtn.addEventListener('click', (event) => {
                    generateTableLesson(true);
                    const section7JSON = getSection7Data();
                    saveData('section7_data', section7JSON, event.target);
                });
            } else {
                console.warn("Button to generate lesson table (#generateLessonTableBtn) not found.");
            }
            generateTableLesson(false);
        } catch (e) {
            console.error("Error initializing Section 7 (Lesson Plan):", e);
        }
        
        // Section 8.1
        try {
            function getSection8_1Data() {
                const assessmentData = [];
                const tableBody = document.querySelector("#assessmentTable tbody");
                if (!tableBody) return assessmentData;

                const rows = tableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    // Find inputs by index-based name
                    const rowData = {
                        method: row.querySelector(`textarea[name="assess_method_${index}"]`)?.value || '',
                        tool: row.querySelector(`textarea[name="assess_tool_${index}"]`)?.value || '',
                        percent: row.querySelector(`input[name="assess_percent_${index}"]`)?.value || '',
                        clo: row.querySelector(`select[name="assess_clo_${index}"]`)?.value || '',
                        clo_desc: row.querySelector(`textarea[name="assess_clo_desc_${index}"]`)?.value || ''
                    };
                    assessmentData.push(rowData);
                });
                console.log("Prepared Section 8.1 Data:", assessmentData);
                return assessmentData;
            }

            function createCloOptions_S8(cloKeys, selectedClo) {
                let optionsHtml = '<option value="">--เลือก--</option>';
                
                if (!Array.isArray(cloKeys) || cloKeys.length === 0) {
                    console.warn("S8: No CLO keys from ai_text, using default fallback.");
                    // Fallback ถ้า ai_text ว่าง
                    optionsHtml += '<option value="CLO1" ' + (selectedClo === 'CLO1' ? 'selected' : '') + '>CLO 1</option>';
                    optionsHtml += '<option value="CLO2" ' + (selectedClo === 'CLO2' ? 'selected' : '') + '>CLO 2</option>';
                    optionsHtml += '<option value="CLO3" ' + (selectedClo === 'CLO3' ? 'selected' : '') + '>CLO 3</option>';
                } else {
                    // วน Loop สร้าง <option> จาก Keys ที่ได้
                    cloKeys.forEach(cloKey => { // cloKey is "CLO1"
                        const isSelected = (selectedClo === cloKey) ? 'selected' : '';
                        // "CLO1" -> "CLO 1" (สำหรับแสดงผล)
                        const cleanKey = cloKey.replace(/(\d+)$/, ' $1'); 
                        optionsHtml += `<option value="${cloKey}" ${isSelected}>${cleanKey}</option>`; 
                    });
                }
                return optionsHtml;
            }

            function generateTableAssessment(forceInputCount = false) {
                const tbody = document.querySelector("#assessmentTable tbody");
                if(!tbody) { console.warn("Assessment table body not found."); return; }
                tbody.innerHTML = "";
                const assessmentCountInput = document.getElementById("AssessmentCount");
                
                // Load data *first*
                const loadedAssessmentData = @json($data->assessment_data ?? []);

                // Find the last item that actually has data
                let lastItemWithData = 0;
                if (Array.isArray(loadedAssessmentData) && loadedAssessmentData.length > 0) {
                    for (let i = loadedAssessmentData.length - 1; i >= 0; i--) {
                        const itemData = loadedAssessmentData[i] || {};
                        const isEmpty = !(itemData.method || itemData.tool || itemData.percent || itemData.clo || itemData.clo_desc);
                        
                        if (!isEmpty) {
                            lastItemWithData = i + 1; 
                            break; 
                        }
                    }
                }
                
                // Determine minimum required items
                const minItemFromData = lastItemWithData > 0 ? lastItemWithData : 0; 
                
                // Get count from input field
                const inputCount = (assessmentCountInput && !isNaN(parseInt(assessmentCountInput.value))) ? parseInt(assessmentCountInput.value) : 3; // Default 3

                // Determine final itemCount
                let assessmentCount;
                if (forceInputCount) {
                     // If button clicked (Req 3: Cannot go below data)
                     assessmentCount = Math.max(inputCount, minItemFromData);
                } else {
                      // On initial load (Req 2: Start at input default OR data max)
                     assessmentCount = Math.max(inputCount, minItemFromData);
                }

                // Enforce limits
                if (assessmentCount > 20) assessmentCount = 20;
                if (assessmentCount < 1) assessmentCount = 1;
                if (assessmentCountInput) assessmentCountInput.value = assessmentCount; // Sync input

                let cloKeysFromAI = [];
                try {
                    const aiTextJson = @json($data->ai_text ?? '{}');
                    let aiTextData = {};
                    if (typeof aiTextJson === 'string' && aiTextJson.trim() !== '') {
                        aiTextData = JSON.parse(aiTextJson);
                    } else if (typeof aiTextJson === 'object' && aiTextJson !== null) {
                        aiTextData = aiTextJson;
                    }
                    
                    if (Object.keys(aiTextData).length > 0) {
                        Object.keys(aiTextData).forEach(key => {
                            const details = aiTextData[key];
                            if (details && typeof details === 'object' && details.CLO) {
                                cloKeysFromAI.push(key.replace(/\s/g, '')); // "CLO 1" -> "CLO1"
                            }
                        });
                        cloKeysFromAI.sort((a, b) => {
                            const numA = parseInt(a.replace('CLO', '')) || 0;
                            const numB = parseInt(b.replace('CLO', '')) || 0;
                            return numA - numB;
                        });
                    }
                } catch (e) {
                    console.error("Error parsing ai_text for S8:", e, @json($data->ai_text ?? '{}'));
                }

                for (let i = 0; i < assessmentCount; i++) {
                      const rowData = loadedAssessmentData[i] || {};
                      const row = document.createElement("tr");
                      
                      // Call the helper function
                      const cloOptions = createCloOptions_S8(cloKeysFromAI, rowData.clo || '');
                      
                      row.innerHTML = `
                        <td class="border border-black p-1 align-top"><textarea name="assess_method_${i}" placeholder="เช่น แบบฝึกหัด, Quiz, สอบกลางภาค" class="w-full h-16 border border-gray-300 rounded p-1">${rowData.method || ''}</textarea></td>
                        <td class="border border-black p-1 align-top"><textarea name="assess_tool_${i}" placeholder="เช่น Lab, การนำเสนอ, โครงการ, รายงาน" class="w-full h-16 border border-gray-300 rounded p-1">${rowData.tool || ''}</textarea></td>
                        <td class="border border-black p-1 align-top"><input type="number" name="assess_percent_${i}" min="0" max="100" placeholder="%" class="w-full text-center border border-gray-300 rounded p-1" value="${rowData.percent || ''}"></td>
                        <td class="border border-black p-1 align-top">
                            <select name="assess_clo_${i}" class="w-full mb-1 p-1 border rounded">
                                ${cloOptions}
                            </select>
                            <textarea name="assess_clo_desc_${i}" placeholder="ระบุรายละเอียดความสอดคล้อง" class="w-full h-12 border border-gray-300 rounded p-1">${rowData.clo_desc || ''}</textarea>
                        </td>
                    `;
                    tbody.appendChild(row);
                }
             }
            const assessmentBtn = document.getElementById('generateAssessmentTableBtn');
            if(assessmentBtn) {
                assessmentBtn.addEventListener('click', () => {
                    generateTableAssessment(true);
                    const section8_1JSON = getSection8_1Data();
                    saveData('section8_1_data', section8_1JSON);
                });
                generateTableAssessment(false);
            } else {
                console.warn("Generate assessment table button not found.");
            }
        } catch (e) {
            console.error("Error initializing Section 8.1 (Assessment Strategy):", e);
        }

        // Section 8.2
        try {
            function getSection8_2Data() {
                const rubricsData = [];
                const rubricContainer = document.getElementById('rubric-container');
                if (!rubricContainer) return rubricsData;

                const sections = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)');
                sections.forEach((section, rubricIndex) => {
                    const titleElement = section.querySelector('.rubric-title');
                    const headerElement = section.querySelector('.rubric-header');
                    const tableBody = section.querySelector('.rubric-tbody');
                    const rubric = {
                        title: titleElement ? titleElement.textContent.trim().replace(/^[ก-ฮ]\.\s*/, '') : '',
                        header: headerElement ? headerElement.textContent.trim() : '',
                        // Initialize rows as an Array
                        rows: []
                    };

                    if (tableBody) {
                        // Create a temporary array to hold descriptions, indexed by level
                        let descriptions = new Array(6).fill(''); // Array[6] (for levels 0-5)
                        
                        tableBody.querySelectorAll('tr').forEach((row, rowIndex) => {
                            const levelCell = row.querySelector('.level-cell');
                            const descCell = row.querySelector('.description-cell');
                            if (levelCell && descCell) {
                                const level = parseInt(levelCell.textContent.trim(), 10);
                                const description = descCell.textContent.trim();
                                if (level >= 0 && level <= 5) {
                                    descriptions[level] = description; 
                                }
                            }
                        });
                        // Assign the sequential array (index 0 = level 0, index 5 = level 5)
                        rubric.rows = descriptions;
                    }
                    rubricsData.push(rubric);
                });
                console.log("Prepared Section 8.2 Data:", rubricsData);
                return rubricsData;
            }

            const rubricContainer = document.getElementById('rubric-container');
            const addRubricBtn = document.getElementById('add-rubric-btn');
            const rubricTemplate = document.getElementById('rubric-template');
            const loadedRubricsData = @json($data->rubrics_data ?? []); // Load data
            const rubricLevels = [5, 4, 3, 2, 1, 0];

            function createRubricRow(rubricIndex, rowIndex, level, description) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true" data-field="rubric_${rubricIndex}_r${rowIndex}_level">${level}</td>
                <td class="description-cell border border-gray-400 p-1 align-top hover:bg-yellow-50" contenteditable="true" data-field="rubric_${rubricIndex}_r${rowIndex}_desc">${description || (level === 0 ? 'ไม่ส่งผลงาน' : '[ใส่คำอธิบาย]') }</td>
            `;
                return tr;
            }

            function addOrLoadRubric(rubricData = null, rubricIndex = -1) {
                if (!rubricTemplate) return;
                if (rubricIndex === -1) rubricIndex = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)').length;

                const newRubric = rubricTemplate.cloneNode(true);
                newRubric.classList.remove('hidden'); newRubric.removeAttribute('id');
                const titleElement = newRubric.querySelector('.rubric-title');
                const headerElement = newRubric.querySelector('.rubric-header');
                const tableBody = newRubric.querySelector('.rubric-tbody');
                const table = newRubric.querySelector('.rubric-table');
                table.id = `rubricTable_${rubricIndex}`;

                const letter = String.fromCharCode(0x0E01 + rubricIndex);
                titleElement.textContent = rubricData?.title ? `${letter}. ${rubricData.title}` : `${letter}. [หัวข้อเกณฑ์ใหม่]`;
                titleElement.dataset.field = `rubric_${rubricIndex}_title`;
                headerElement.textContent = rubricData?.header || `คำอธิบายเกณฑ์ฯ ${letter}`;
                headerElement.dataset.field = `rubric_${rubricIndex}_header`;

                tableBody.innerHTML = '';
                let rowsData = rubricData?.rows || {};
                
                // ตรวจสอบว่าเป็น Format เก่า (Array of strings) หรือไม่
                if (Array.isArray(rowsData) && (rowsData.length === 0 || typeof rowsData[0] === 'string')) {
                    // นี่คือ Format เก่า: ["Desc 0", "Desc 1", ..., "Desc 5"]
                    const tempRows = {};
                    // แปลงให้เป็น Format ใหม่ (Object): { "0": "Desc 0", "1": "Desc 1", ... }
                    rowsData.forEach((description, level) => {
                        tempRows[String(level)] = description;
                    });
                    rowsData = tempRows; // ตอนนี้ rowsData เป็น Object แล้ว
                
                } 
                // ตรวจสอบว่าเป็น Format เก่ามากๆ (Array of Objects) หรือไม่
                else if (Array.isArray(rowsData)) {
                    const tempRows = {}; 
                    rowsData.forEach(r => { if(r && r.level !== undefined) tempRows[r.level] = r.description ?? ''; });
                    rowsData = tempRows;
                } 
                // ถ้าไม่ใช่ทั้งสองแบบ หรือเป็น null ให้ใช้ Object ว่าง
                else if (typeof rowsData !== 'object' || rowsData === null) { 
                    rowsData = {}; // ทำให้เป็น Object ว่าง
                }


                rubricLevels.forEach((level, rowIndex) => { 
                    const currentDescription = rowsData[String(level)] ?? '';
                    const row = createRubricRow(rubricIndex, rowIndex, level, currentDescription);
                    tableBody.appendChild(row);
                });
                rubricContainer.appendChild(newRubric);
            }

            // Load existing rubrics
            rubricContainer.innerHTML = '';
            if (Array.isArray(loadedRubricsData) && loadedRubricsData.length > 0) {
                loadedRubricsData.forEach((rubric, index) => addOrLoadRubric(rubric, index));
            } else {
                addOrLoadRubric({ title: 'การประเมินการปฏิบัติงาน (Performance)' }, 0);
            }
            updateRubricLetters();

            // Add/Delete Buttons
            if (addRubricBtn) {
                addRubricBtn.addEventListener('click', () => { addOrLoadRubric(); updateRubricLetters(); const d = getSection8_2Data(); saveData('section8_2_data', d); });
            }
            rubricContainer.addEventListener('click', (event) => {
                if (event.target.classList.contains('delete-rubric-btn')) {
                    const totalRubrics = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)').length;
                    if (totalRubrics <= 1) { alert("อย่างน้อยต้องมี 1 หัวข้อ"); return; }
                    const rubricToRemove = event.target.closest('.rubric-section');
                    if (rubricToRemove && confirm('ต้องการลบหัวข้อนี้?')) {
                        rubricToRemove.remove(); updateRubricLetters(); const d = getSection8_2Data(); saveData('section8_2_data', d);
                    }
                }
            });

            function updateRubricLetters() {
                const allRubrics = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)');
                allRubrics.forEach((rubric, index) => {
                    const letter = String.fromCharCode(0x0E01 + index);
                    const titleElement = rubric.querySelector('.rubric-title');
                    const headerElement = rubric.querySelector('.rubric-header');
                    if (titleElement) { let txt = titleElement.textContent.trim().replace(/^[ก-ฮ]\.\s*/, ''); titleElement.textContent = `${letter}. ${txt}`; titleElement.dataset.field = `rubric_${index}_title`; }
                    if (headerElement) { headerElement.dataset.field = `rubric_${index}_header`; }
                    rubric.querySelectorAll('.rubric-tbody tr').forEach((row, rowIndex) => {
                        const levelCell = row.querySelector('.level-cell'); const descCell = row.querySelector('.description-cell');
                        if(levelCell) levelCell.dataset.field = `rubric_${index}_r${rowIndex}_level`;
                        if(descCell) descCell.dataset.field = `rubric_${index}_r${rowIndex}_desc`;
                    });
                });
            }

        } catch (e) { console.error("Error initializing Section 8.2 (Rubrics):", e); }

        // Section 9.1
        try {
            // Function to gather Section 9.1 data
            function getSection9_1Data() {
                const referencesData = [];
                const referenceList = document.getElementById('referenceList');
                if (!referenceList) return referencesData;
                referenceList.querySelectorAll('li input[type="text"]').forEach(input => {
                    if (input.value.trim()) {
                        referencesData.push(input.value.trim());
                    }
                });
                console.log("Prepared Section 9.1 Data:", referencesData);
                return referencesData;
            }

            const referenceList = document.getElementById('referenceList');
            const addReferenceBtn = document.getElementById('addReferenceItemBtn');
            const loadedReferencesData = @json($data->references_data ?? []);

            function createReferenceItem(value = '', index = -1) {
                if (index === -1) index = referenceList.children.length;
                const li = document.createElement('li');
                li.className = "mb-2.5 relative";
                const escapedValue = String(value || '').replace(/"/g, '&quot;');
                li.innerHTML = `
                <input type="text" name="reference_${index}" class="w-4/5 p-2 border border-gray-300 rounded-md text-[15px]" placeholder="พิมพ์รายการใหม่..." value="${escapedValue}">
                <button class="remove-btn bg-red-500 text-white border-none rounded-md px-2.5 py-1.5 ml-2.5 cursor-pointer text-[13px] hover:bg-red-600">ลบ</button>
            `;
                referenceList.appendChild(li);
            }

            referenceList.innerHTML = '';
            if (Array.isArray(loadedReferencesData) && loadedReferencesData.length > 0) {
                loadedReferencesData.forEach((refText, index) => createReferenceItem(refText, index));
            } else {
                createReferenceItem('', 0);
            }

            // Add/Delete Buttons
            if (addReferenceBtn) {
                addReferenceBtn.addEventListener('click', () => { createReferenceItem(); const d = getSection9_1Data(); saveData('section9_1_data', d); }); // Save after adding
            }
            if (referenceList) {
                referenceList.addEventListener('click', (event) => {
                    if (event.target.classList.contains('remove-btn')) {
                        const itemCount = referenceList.children.length;
                        if (itemCount > 1) {
                            event.target.parentElement.remove();
                            // Re-index names AND save
                            referenceList.querySelectorAll('li input').forEach((input, index) => { input.name = `reference_${index}`; });
                            const d = getSection9_1Data(); saveData('section9_1_data', d); // Save after deleting
                        } else { alert("อย่างน้อยต้องมี 1 รายการ"); }
                    }
                });
                referenceList.addEventListener('change', (event) => {
                    if(event.target.tagName === 'INPUT'){ const d = getSection9_1Data(); saveData('section9_1_data', d); }
                });
                referenceList.addEventListener('blur', (event) => {
                    if(event.target.tagName === 'INPUT'){ const d = getSection9_1Data(); saveData('section9_1_data', d); }
                }, true);
            }
        } catch (e) { console.error("Error initializing Section 9.1 (Reference List):", e); }
    });
</script>

</body>
</html>