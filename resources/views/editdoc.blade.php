<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แบบฟอร์ม มคอ.3</title>
    <meta name="viewport" content="width=1920mm, height: 1080mm, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/editdoc.js'])
    @endif
</head>
<body>
    <div id="tqf-form-container" class="flex justify-center py-6 bg-gray-100 font-sans text-bases">
        <div class="fixed top-4 right-4 flex gap-3 z-50">
            <button type="button" onclick="window.history.back()"
                class="px-4 py-2 bg-blue-300 hover:bg-blue-400 text-gray-900 rounded-md shadow">
                <a href="/export-docx?course_code={{ $data->course_code ?? '' }}&year={{ $data->year ?? '' }}&term={{ $data->term ?? '' }}&TQF={{ $data->TQF ?? 3 }}" class="download-button">
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
                        <input name="faculty" class="px-2 min-w-[150px] border-b border-dotted border-gray-500 text-center" value="{{ $data->faculty }}" disabled></input>
                        <label class="ml-2 whitespace-nowrap font-semibold">สาขาวิชา</label>
                        <input name="major" class="px-2 min-w-[200px] border-b border-dotted border-gray-500 text-center" value="{{ $data->major }}" disabled></input>
                        <label class="ml-2 whitespace-nowrap font-semibold">หลักสูตรปรับปรุง พ.ศ.</label>
                        <input name="curriculum_year" class="w-[70px] border-b border-dotted border-gray-500 text-center" 
                            value="{{ $data->curriculum_year }}" disabled></input>
                        </div>
                </div>

                <div class="flex flex-wrap items-baseline">
                    <div class="flex items-baseline mr-6 mb-2">
                        <label class="whitespace-nowrap font-semibold">วิทยาเขต</label>
                        <input name="campus" class="ml-2 px-2 min-w-[200px] border-b border-dotted border-gray-500 text-center" value="{{ $data->campus }}" disabled></input>
                        <div class="flex items-baseline mb-2">
                            <label class="whitespace-nowrap font-semibold">ภาคการศึกษา/ปีการศึกษา</label>
                            <div name="term" id="term" class="ml-2 w-[100px] bg-transparent border-b border-dotted border-gray-500 appearance-none text-center">
                                <span>{{ $data->term }}</span>
                            </div> /
                            <div name="academic_year" id="academic_year" 
                                class="ml-2 w-[115px] bg-transparent border-b border-dotted border-gray-500 appearance-none text-center">
                                <span>{{ $data->year }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col">
                    <h3 class="text-[20px] font-semibold">ปรัชญามหาวิทยาลัยแม่โจ้</h3>
                    <span class="leading-relaxed" id="philosophy">
                        {{ $data->philosophy ?? 'ไม่พบข้อมูลปรัชญา' }}
                    </span>
                    <h3 class="text-[20px] font-semibold">ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้</h3>
                    <span class="leading-relaxed" id="philosophy_education">
                        {{ $data->philosophy_education ?? 'ไม่พบข้อมูลปรัชญาการศึกษา' }}
                    </span>
                    <h3 class="text-[20px] font-semibold">ปรัชญาหลักสูตร</h3>
                    <span class="leading-relaxed" id="philosophy_curriculum">
                        {{ $data->philosophy_curriculum ?? 'ไม่พบข้อมูลปรัชญาหลักสูตร' }}
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
                            <td colspan="3" class="border border-black p-2.5 align-top" id="course_code">{{ $data->course_code }}</td>
                            </tr>
                        <tr>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">3. จำนวนหน่วยกิต</th>
                            <td colspan="3" class="border border-black align-top">
                                <input type="text" name="credit" id="credit" 
                                    class="w-full p-2.5 border-none focus:outline-none focus:ring-0" 
                                    value="{{ $data->credit ?? '' }}" disabled>
                            </td>
                        </tr>
                        <tr>
                            <th class="border border-black p-2.5 align-top bg-blue-100 text-center">4. หลักสูตร</th>
                            <td colspan="3" class="border border-black align-top">
                                <input type="text" name="curriculum_name" id="curriculum_name" 
                                    class="w-full p-2.5 border-none focus:outline-none focus:ring-0" 
                                    value="{{ $data->curriculum_name ?? '' }}" disabled>
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
                            <td colspan="3" class="border border-black p-2.5 align-top" id="instructors"><input type="text" name="instructor_name" id="instructor_name"
                                    class="w-full border-none focus:outline-none focus:ring-0" 
                                    value="{{ $data->instructor_name ?? '' }}"></td>
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
                                <label class="inline-flex items-center">ปีการศึกษา <span id="revision_year_display" class="font-semibold ml-1.5">{{ $data->year }}</span></label>
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
                                >     {{ $data->course_detail_th ?? ($data->course_detail_th ?? '') }}
                                </textarea>
                                <textarea
                                    rows="5"
                                    class="w-full mt-2 p-3 text-[15px] leading-relaxed border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all
                                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                                >     {{ $data->course_detail_en ?? ($data->course_detail_en ?? '') }}
                                </textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 border border-gray-300">
                                <b>2.2 ผลลัพธ์การเรียนรู้ระดับรายวิชา (Course learning Outcome) CLOs </b>
                                <br>
                                <div id="clo-input-container" class="mt-2 space-y-2">
                                    @php
                                        $aiTextRaw = $data->ai_text ?? '{}';
                                        $closArray = json_decode($aiTextRaw, true);
                                        
                                        if (is_array($closArray) && json_last_error() === JSON_ERROR_NONE) {
                                            // เรียงลำดับ CLO
                                            uksort($closArray, function($a, $b) {
                                                preg_match('/(\d+)/', $a, $matchesA);
                                                preg_match('/(\d+)/', $b, $matchesB);
                                                return ((int)($matchesA[1] ?? 9999)) <=> ((int)($matchesB[1] ?? 9999));
                                            });
                                        } else {
                                            $closArray = [];
                                        }
                                    @endphp

                                    @foreach ($closArray as $key => $details)
                                        <div class="flex gap-2 clo-item-row">
                                            <input type="text" 
                                                class="w-20 p-2 text-sm font-bold border border-gray-300 rounded bg-gray-100 clo-key" 
                                                value="{{ str_replace(' ', '', $key) }}" readonly>
                                            
                                            <input type="text"
                                                class="clo-edit-input flex-1 p-2 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 clo-description"
                                                data-original-key="{{ $key }}"
                                                value="{{ $details['CLO'] ?? '' }}">
                                        </div>
                                    @endforeach
                                </div>
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
                <div class="flex items-center gap-4 mt-6 mb-2">
                    <h3 class="font-bold text-[#003366] bg-blue-200 px-4 py-1.5 rounded text-gray-900 m-0">
                        หมวดที่ 4: ข้อตกลงร่วมกันระหว่างผู้สอนและผู้เรียน 
                    </h3>
                    
                    @if(!empty($data->has_previous_agreement))
                        <button type="button" id="fetchPrevAgreementBtn" class="ml-auto px-3 py-1.5 bg-green-500 text-white text-sm font-semibold rounded hover:bg-green-600 shadow-sm transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            ดึงข้อมูลเก่า
                        </button>
                    @endif
                </div>

                <textarea
                    name="agreement"
                    class="w-[100%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mb-6 focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                    placeholder='พิมพ์รายละเอียด'
                >{{ $data->agreement ?? '' }}</textarea>
                {{-- =========================================================================================================================================================================================================================== --}}
                <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                    หมวดที่ 5: ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับหลักสูตร (PLOs) กับ  ผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) และผลทักษะการเรียนรู้ตลอดชีวิต (LLLs)
                </h3>
                <div>
                    <h4 class="mt-2 font-semibold">5.1 ผลลัพธ์การเรียนรู้ของหลักสูตร</h4>
                    <h4 class="mt-2 font-semibold">หลักสูตรฯ วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์</h4>
                </div>
                <table class="w-full border-collapse border border-gray-400 mt-2 text-sm">
                    <thead class="bg-gray-100 font-semibold text-center">
                        <tr>
                            <th class="w-[8%] border border-black p-2.5 align-top bg-blue-100 text-center">PLOs</th>
                            <th class="w-[45%] border border-black p-2.5 align-top bg-blue-100 text-center">Outcome Statement</th>
                            <th class="w-[10%] border border-black p-2.5 align-top bg-blue-100 text-center">Specific LO</th>
                            <th class="w-[10%] border border-black p-2.5 align-top bg-blue-100 text-center">Generic LO</th>
                            <th class="w-[12%] border border-black p-2.5 align-top bg-blue-100 text-center">Level</th>
                            <th class="w-[15%] border border-black p-2.5 align-top bg-blue-100 text-center">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($data->outcome_statement))
                            @foreach ($data->outcome_statement as $ploData)
                                <tr>
                                    <td class="text-center border border-black p-2.5 align-top font-bold">
                                        {{ $ploData['plo'] }}
                                    </td>
                                    
                                    <td class="border border-black p-2.5 align-top">
                                        {{ $ploData['outcome'] }}
                                    </td>
                                    <td class="text-center border border-black p-2.5 align-top text-gray-700">
                                        {!! $ploData['specific'] == 1 ? '<span class="font-bold text-lg">✓</span>' : '' !!}
                                    </td>

                                    <td class="text-center border border-black p-2.5 align-top text-gray-700">
                                        {!! $ploData['specific'] == 0 ? '<span class="font-bold text-lg">✓</span>' : '' !!}
                                    </td>                                  
                                    <td class="text-center border border-black p-2.5 align-top">
                                        {{ $ploData['level'] }}
                                    </td>
                                    
                                    <td class="text-center border border-black p-2.5 align-top">
                                        {{ $ploData['type'] }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center border border-black p-4 text-gray-500">ไม่มีข้อมูลจากฐานข้อมูล</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <p class="text-s mt-2 text-gray-600">Bloom's Taxonomy : R-Remember, U-Understand, AP-Apply, AN-Analyze, E-Evaluate, C-Create </p>
                <p class="text-s mb-2 text-gray-600">Type : K-Knowledge, S-Skill, AR-Application and Responsibility </p>
                
                <div id="section-5-2">
                    <div class="flex items-center gap-4 mb-2">
                        <h3 class="font-bold text-lg m-0">5.2 มาตรฐานผลการเรียนรู้รายวิชา (Curriculum Mapping)</h3>
                        @if(!empty($data->has_previous_curriculum_map))
                            <button type="button" id="fetchPrevMapBtn" class="ml-auto px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 shadow-sm transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                ดึงข้อมูลเก่า
                            </button>
                        @endif
                    </div>
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
                                        {{ $data->course_code ?? '' }}<br>{{ $data->course_name_th ?? ($data->course_name_en ?? '') }}
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
                            
                            @if(!empty($data->outcome_statement))
                                @foreach ($data->outcome_statement as $p => $ploData)
                                    @php
                                        $ploLevel = '';
                                        if(isset($ploData['level'])) {
                                            $levelParts = explode(' - ', $ploData['level']);
                                            $ploLevel = $levelParts[0] ?? '';
                                        }
                                    @endphp
                                    <th class="border border-gray-400 p-2 bg-gray-200 font-bold w-[12.5%]">
                                        PLO{{ $p }} {{ $ploLevel ? "($ploLevel)" : '' }}
                                    </th>
                                @endforeach
                            @endif
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
                    
                    <div class="flex items-center gap-4 mt-6 mb-4">
                        <h3 class="font-bold m-0">7.1 แผนการสอน</h3>
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        จำนวนสัปดาห์:
                        <input type="number" id="weekCount" value="10" min="1" max="20" class="w-[70px] border border-gray-300 rounded px-2 py-1">
                        <button id="generateLessonTableBtn" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">สร้างตาราง</button>
                        @if(!empty($data->has_previous_lesson_plan))
                            <button type="button" id="fetchPrevLessonPlanBtn" class="ml-auto px-3 py-1.5 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 shadow-sm transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                ดึงข้อมูลเก่า
                            </button>
                        @endif
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
                    <div class="mb-4 flex gap-2">
                        จำนวนรายการประเมิน:
                        <input type="number" id="AssessmentCount" value="3" min="1" max="20" class="w-[70px] border border-gray-300 rounded px-2 py-1">
                        <button id="generateAssessmentTableBtn" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">สร้างตาราง</button>
                         @if(!empty($data->has_previous_assessment_data))
                            <button type="button" id="fetchPrevAssessmentBtn" class="ml-auto px-3 py-1.5 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 shadow-sm transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                ดึงข้อมูลเก่า
                            </button>
                        @endif
                    </div>
                    <table id="assessmentTable" class="w-full border-collapse border border-black text-sm">
                        <thead>
                            <tr>
                                <th class="w-[25%] border border-black p-2.5 align-top bg-blue-100 text-center">วิธีการประเมิน</th>
                                <th class="w-[35%] border border-black p-2.5 align-top bg-blue-100 text-center">เครื่องมือ / รายละเอียด</th>
                                <th class="w-[10%] border border-black p-2.5 align-top bg-blue-100 text-center">สัดส่วน (%)</th>
                                <th class="w-[30%] border border-black p-2.5 align-top bg-blue-100 text-center">ความสอดคล้องกับ CLOs</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Rows will be added/loaded by JavaScript --}}
                        </tbody>
                    </table>

                    <div class="flex items-center gap-4 mt-6 mb-4">
                        <h3 class="font-bold m-0">8.2 วิธีการประเมิน แบบรูบริค (Rubric) หรือ อื่นๆ (ถ้ามี)</h3>
                        
                        @if(!empty($data->has_previous_rubrics_data))
                            <button type="button" id="fetchPrevRubricsBtn" class="ml-auto px-3 py-1.5 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 shadow-sm transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                ดึงข้อมูลเก่า
                            </button>
                        @endif
                    </div>
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
                    <div class="flex items-center justify-between bg-blue-100 border border-gray-400 p-2">
                        <h2 class="text-gray-800 font-semibold m-0">
                            หมวดที่ 10 : เกณฑ์การประเมิน
                        </h2>
                        @if(!empty($data->has_previous_grading_criteria))
                            <button type="button" id="fetchPrevGradingBtn" class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 shadow-sm transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                ดึงข้อมูลเก่า
                            </button>
                        @endif
                    </div>
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
                                <td class="border border-gray-400 p-2 align-middle text-center bg-gray-200" data-field="grade_A_level">
                                    {{ $gradingCriteria->grade_A_level ?? '' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_A_criteria">
                                    {{ $gradingCriteria->grade_A_criteria ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center bg-gray-200" data-field="grade_Bp_level">
                                    {{ $gradingCriteria->grade_Bp_level ?? '' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Bp_criteria">
                                    {{ $gradingCriteria->grade_Bp_criteria ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center bg-gray-200" data-field="grade_B_level">
                                    {{ $gradingCriteria->grade_B_level ?? '' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_B_criteria">
                                    {{ $gradingCriteria->grade_B_criteria ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center bg-gray-200" data-field="grade_Cp_level">
                                    {{ $gradingCriteria->grade_Cp_level ?? '' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Cp_criteria">
                                    {{ $gradingCriteria->grade_Cp_criteria ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center bg-gray-200" data-field="grade_C_level">
                                    {{ $gradingCriteria->grade_C_level ?? '' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_C_criteria">
                                   {{ $gradingCriteria->grade_C_criteria ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center bg-gray-200" data-field="grade_Dp_level">
                                    {{ $gradingCriteria->grade_Dp_level ?? '' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_Dp_criteria">
                                    {{ $gradingCriteria->grade_Dp_criteria ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center bg-gray-200" data-field="grade_D_level">
                                    {{ $gradingCriteria->grade_D_level ?? '' }}
                                </td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_D_criteria">
                                    {{ $gradingCriteria->grade_D_criteria ?? '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-400 p-2 align-middle text-center bg-gray-200" data-field="grade_F_level">
                                    {{ $gradingCriteria->grade_F_level ?? '' }}</td>
                                <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true" data-field="grade_F_criteria">
                                   {{ $gradingCriteria->grade_F_criteria ?? '' }}
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
        window.pageData = {
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