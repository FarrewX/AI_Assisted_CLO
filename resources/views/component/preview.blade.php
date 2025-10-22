<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แบบฟอร์ม มคอ.3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
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
<div class="flex justify-center py-6 bg-gray-100 font-sans text-base">
    <div class="fixed top-4 right-4 flex gap-3 z-50">
        <button type="button" onclick="window.history.back()"
            class="px-4 py-2 bg-blue-300 hover:bg-blue-400 text-gray-900 rounded-md shadow">
            <a href="/export-docx" class="download-button">
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
                    <span class="px-2 min-w-[150px] border-b border-dotted border-gray-500 text-center">วิทยาศาสตร์</span>
                    <!-- <span class="px-2 min-w-[150px] border-b border-dotted border-gray-500 text-center">{{--{{ faculty }}--}}</span> -->

                    <label class="ml-2 whitespace-nowrap font-semibold">สาขาวิชา</label>
                    <span class="px-2 min-w-[200px] border-b border-dotted border-gray-500 text-center">วิทยาการคอมพิวเตอร์</span>
                    <!-- <span class="px-2 min-w-[200px] border-b border-dotted border-gray-500 text-center">{{--{{ major }}--}}</span> -->

                    <label class="ml-2 whitespace-nowrap font-semibold">หลักสูตรปรับปรุง พ.ศ.</label>
                    <span name="curriculum_year" id="curriculum_year" class="w-[70px] border-b border-dotted border-gray-500 text-center">2565</span>
                    <!-- <span name="curriculum_year" id="curriculum_year" class="w-[70px] border-b border-dotted border-gray-500 text-center">{{--{{ improved_course }}--}}</span> -->
                </div>
            </div>

            <div class="flex flex-wrap items-baseline">
                <div class="flex items-baseline mr-6 mb-2">
                    <label class="whitespace-nowrap font-semibold">วิทยาเขต</label>
                    <span class="ml-2 px-2 min-w-[200px] border-b border-dotted border-gray-500 text-center">เชียงใหม่</span>
                    <div class="flex items-baseline mb-2">
                        <label class="whitespace-nowrap font-semibold">ภาคการศึกษา/ปีการศึกษา</label>
                        <div name="term"  id="term" class="ml-2 w-[100px] bg-transparent border-b border-dotted border-gray-500 appearance-none text-center">
                            <span value=""></span>
                        </div> /
                        <div name="academic_year" id="academic_year" 
                            class="ml-2 w-[115px] bg-transparent border-b border-dotted border-gray-500 appearance-none text-center">
                            <span value=""></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col">
                <h3 class="text-[20px] font-semibold">ปรัชญามหาวิทยาลัยแม่โจ้</h3>
                <span class="leading-relaxed" id="philosophy">
                    {{-- {{ philosophy }} --}}
                    มุ่งมั่นพัฒนาบัณฑิตสู่ความเป็นผู้อุดมด้วยปัญญา อดทน สู้งาน เป็นผู้มีคุณธรรมและจริยธรรม เพื่อ
                    ความเจริญรุ่งเรืองวัฒนาของสังคมไทยที่มีการเกษตรเป็นรากฐาน 
                </span>
                <h3 class="text-[20px] font-semibold">ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้</h3>
                <span class="leading-relaxed" id="philosophy_education">
                    {{-- {{ philosophy_education }} --}}
                    จัดการศึกษาเพื่อเสริมสร้างปัญญา ในรูปแบบการเรียนรู ้จากการปฏิบัติที ่บูรณาการกับการทำงานตาม
                    อมตะโอวาท งานหนักไม่เคยฆ่าคน มุ่งให้ผู้เรียน มีทักษะการเรียนรู้ตลอดชีวิต
                </span>
                <h3 class="text-[20px] font-semibold">ปรัชญาหลักสูตร</h3>
                <span class="leading-relaxed" id="philosophy_curriculum">
                    {{-- {{ philosophy_curriculum }} --}}
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
                        <td colspan="3" class="border border-black p-2.5 align-top" id="course_name">วิทยาการข้อมูล Datascience</td>
                        <!-- <td colspan="3" class="border border-black p-2.5 align-top">{{-- {{ course_name }} --}}</td> -->
                    </tr>
                    <tr>
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center">2. รหัสวิชา</th>
                        <td colspan="3" class="border border-black p-2.5 align-top" id="course_id">10301351</td>
                        <!-- <td colspan="3" class="border border-black p-2.5 align-top">{{-- {{ course_id }} --}}</td> -->
                    </tr>
                    <tr>
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center">3. จำนวนหน่วยกิต</th>
                        <td colspan="3" class="border border-black align-top"> 
                            <input type="text" name="credits" id="credits" 
                                class="w-full p-2.5 border-none focus:outline-none focus:ring-0" 
                                value="">
                        </td>
                    </tr>
                    <tr>
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center">4. หลักสูตร</th>
                        <td colspan="3" class="border border-black align-top">
                            <input type="text" name="curriculum_name" id="curriculum_name" 
                                class="w-full p-2.5 border-none focus:outline-none focus:ring-0" 
                                value="">
                        </td>
                    </tr>
                    <tr> 
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center">5. ประเภทของรายวิชา</th> 
                        <td colspan="3" class="border border-black p-2.5 align-top" id="course_type_section"> 
                            <label class="inline-flex items-center"><input type="checkbox" checked class="mr-1.5 scale-125" value="specific"> วิชาเฉพาะ</label> 
                            <label class="inline-flex items-center ml-4"><input type="checkbox" class="mr-1.5 scale-125" value="core"> กลุ่มวิชาแกน</label> 
                            <label class="inline-flex items-center ml-4"><input type="checkbox" class="mr-1.5 scale-125" value="major_required"> เอกบังคับ</label> 
                            <label class="inline-flex items-center ml-4"><input type="checkbox" class="mr-1.5 scale-125" value="major_elective"> เอกเลือก</label> 
                            <label class="inline-flex items-center ml-4"><input type="checkbox" class="mr-1.5 scale-125" value="free_elective"> วิชาเลือกเสรี</label> 
                        </td> 
                    </tr> 
                    <tr> 
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center">6. วิชาบังคับก่อน</th> 
                        <td colspan="3" class="border border-black p-2.5 align-top">
                            <input type="text" name="curriculum_name" id="prerequisites"
                                class="w-full border-none focus:outline-none focus:ring-0" 
                                value="">
                        </td> 
                    </tr> 
                    <tr> 
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center">7. ผู้สอน</th> 
                        <td colspan="3" class="border border-black p-2.5 align-top" id="instructors">Admin2</td>
                        <!-- <td colspan="3" class="border border-black p-2.5 align-top" id="instructors">{{-- {{ name }} --}}</td>  -->
                    </tr> 
                    <tr> 
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center">8.การแก้ไขล่าสุด</th> 
                        <td colspan="3" class="border border-black p-2.5" id="revision_section"> ภาคการศึกษาที่
                            <label class="inline-flex items-center ml-4"><input type="checkbox" class="mr-1.5 scale-125" id="revision_term_1">1</label>
                            <label class="inline-flex items-center ml-4 mr-4"><input type="checkbox" class="mr-1.5 scale-125" id="revision_term_2">2</label> 
                            <label class="inline-flex items-center">ปีการศึกษา <span id="revision_year_display" class="font-semibold ml-1.5"></span></label> 
                            
                        </td> 
                    </tr> 
                    <tr class="text-center font-semibold bg-gray-100"> 
                        <td colspan="4" class="border border-black p-2.5 align-top">9.จำนวนชั่วโมงที่ใช้ภาคการศึกษา</td> 
                    </tr> 
                    <tr> 
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center font-normal">
                            ภาคทฤษฎี 
                            <span id="hours_theory" contenteditable="true" class="font-bold inline-block px-2 py-0.5 bg-white border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-yellow-50">00</span> 
                            ชั่วโมง
                        </th> 
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center font-normal">
                            ภาคปฏิบัติ 
                            <span id="hours_practice" contenteditable="true" class="font-bold inline-block px-2 py-0.5 bg-white border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-yellow-50">00</span> 
                            ชั่วโมง
                        </th> 
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center font-normal">
                            การศึกษาด้วยตนเอง 
                            <span id="hours_self_study" contenteditable="true" class="font-bold inline-block px-2 py-0.5 bg-white border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-yellow-50">00</span> 
                            ชั่วโมง
                        </th> 
                        <th class="border border-black p-2.5 align-top bg-blue-100 text-center font-normal">
                            ทัศนศึกษา/ฝึกงาน 
                            <span id="hours_field_trip" contenteditable="true" class="font-bold inline-block px-2 py-0.5 bg-white border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-yellow-50">00</span> 
                            ชั่วโมง
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
                                name="course_description" 
                                rows="4" 
                                class="w-full mt-2 p-3 text-[15px] leading-relaxed border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all
                                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                            ><?php echo htmlspecialchars($course['description'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-4 border border-gray-300">
                            <b>2.2 ผลลัพธ์การเรียนรู้ระดับรายวิชา (Course learning Outcome) CLOs </b>
                            <br>
                            <textarea 
                                name="course_clos" 
                                rows="4" 
                                class="w-full mt-2 p-3 text-[15px] leading-relaxed border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all
                                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                            ><?php echo htmlspecialchars($course['clos'] ?? ''); ?></textarea>
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
                            <textarea name="feedback" rows="4" 
                                        class="w-full p-1 border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            <?php echo htmlspecialchars($course['feedback'] ?? ''); ?></textarea>
                        </td>
                        <td class="p-2 border border-gray-400 align-top">
                            <textarea name="improvement" rows="4" 
                                        class="w-full p-1 border border-gray-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            <?php echo htmlspecialchars($course['improvement'] ?? ''); ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
            {{-- =========================================================================================================================================================================================================================== --}}
            <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                หมวดที่ 4: ข้อตกลงร่วมกันระหว่างผู้สอนและผู้เรียน 
            </h3><br>
            <textarea 
                class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                placeholder='พิมพ์รายละเอียด'
            ></textarea>
            {{-- =========================================================================================================================================================================================================================== --}}
            <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                หมวดที่ 5: ความสอดคล้องระหว่างผลลัพธ์การเรียนรู้ระดับหลักสูตร (PLOs) กับ  ผลลัพธ์การเรียนรู้ระดับรายวิชา (CLOs) และผลทักษะการเรียนรู้ตลอดชีวิต (LLLs)
            </h3>
            <h4 class="mt-2 font-semibold">5.1 ผลลัพธ์การเรียนรู้ของหลักสูตร</h4>
            <h4 class="mt-2 font-semibold">หลักสูตรฯ วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์ </h4>
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
                <tbody>
                @for ($i = 1; $i <= 4; $i++)
                    <tr>
                        <td class="text-center border border-black p-2.5 align-top">{{ $i }}</td>
                        <td class="border border-black p-2.5 align-top">
                            <textarea name="plo{{ $i }}_outcome" rows="3" class="w-full border border-gray-300 rounded p-1"></textarea>
                        </td>
                        <td class="text-center border border-black p-2.5 align-top">
                            <input type="checkbox" name="plo{{ $i }}_specific" class="scale-125">
                        </td>
                        <td class="text-center border border-black p-2.5 align-top">
                            <input type="checkbox" name="plo{{ $i }}_generic" class="scale-125">
                        </td>
                        <td class="text-center border border-black p-2.5 align-top">
                            <select name="plo{{ $i }}_level" 
                            class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">--เลือก--</option>
                                <option value="R - Remember">R</option>
                                <option value="U - Understand">U</option>
                                <option value="AP - Apply">AP</option>
                                <option value="AN - Analyze">AN</option>
                                <option value="E - Evaluate">E</option>
                                <option value="C - Create">C</option>
                            </select>
                        </td>
                        <td class="text-center border border-black p-2.5 align-top">
                            <select name="plo{{ $i }}_type" 
                            class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">--เลือก--</option>
                                <option value="K - Knowledge">K</option>
                                <option value="S- Skill">S</option>
                                <option value="AR - Application">AR</option>
                                <option value="Rs - Responsibility">Rs</option>
                            </select>
                        </td>
                    </tr>
                @endfor
                </tbody>
            </table>
            <p class="text-xs">Bloom's Taxonomy : R-Remember, U-Understand,  AP-Apply,  AN-Analyze, E-Evaluate,  C-Create </p>
            
            <div id="section-5-2">
                <h3 class="font-bold text-lg">5.2 มาตรฐานผลการเรียนรู้รายวิชา (Curriculum Mapping)</h3>
                <p class="text-sm mb-2">หลักสูตรฯ วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์</p>
        
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-400 text-sm">
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
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">1</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">2</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">3</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">4</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">5</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">6</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">7</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">1</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">2</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">3</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">4</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">5</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">6</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">7</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">8</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-white">1</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-white">2</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-white">3</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-white">4</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">1</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">2</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">3</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">4</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">5</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-yellow-100">6</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-white">1</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-white">2</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-white">3</th>
                                <th class="border border-gray-400 p-2 w-8 font-normal bg-white">4</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr>
                                <td class="border border-gray-400 p-2 text-left align-top">
                                    10301351<br>วิทยาการข้อมูล
                                </td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
                                <td class="score-cell border border-gray-400 p-2 text-center align-middle cursor-pointer hover:bg-gray-100 transition-colors" data-state="0">&nbsp;</td>
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
                        <th class="border border-gray-400 p-2 bg-gray-200 font-bold">รหัส CLO</th>
                        <th class="border border-gray-400 p-2 bg-gray-200 font-bold">รหัสวิชา 10301351 วิชาการวิทยาการข้อมูล</th>
                        <th class="border border-gray-400 p-2 bg-gray-200 font-bold">PLO1 (U)</th>
                        <th class="border border-gray-400 p-2 bg-gray-200 font-bold">PLO2 (E)</th>
                        <th class="border border-gray-400 p-2 bg-gray-200 font-bold">PLO3 (C)</th>
                        <th class="border border-gray-400 p-2 bg-gray-200 font-bold">PLO4 (AP)</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            {{-- =========================================================================================================================================================================================================================== --}}
            <div class="max-w-7xl mx-auto space-y-4 mt-6"> 
                <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900" >
                    หมวดที่ 6 : ความสอดคล้องระหว่างผลการเรียนรู้ระดับรายวิชา (CLOs)  วิธีการสอน และการประเมินผล
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
                    </tbody>
                </table>
                <button id="addCloBtn_S6" class="mt-4 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700">
                    + เพิ่ม CLO ใหม่
                </button>
    
                <template id="cloRowTemplate_S6">
                    <tr class="bg-white">
                        <td class="clo-cell-s6 p-2 border border-gray-400 align-top text-sm" contenteditable="true"></td>
                        <td class="teaching-cell-s6 p-2 border border-gray-400 align-top text-sm"></td>
                        <td class="assessment-cell-s6 p-2 border border-gray-400 align-top text-sm"></td>
                    </tr>
                </template>
            </div>
            {{-- =========================================================================================================================================================================================================================== --}}
            <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                หมวดที่ 7: แผนการสอน
            </h3>
            <h3 class="mt-6 font-bold">7.1 แผนการสอน</h3>
            <div class="mb-4">
                จำนวนสัปดาห์: 
                <input type="number" id="weekCount" value="2" min="1" max="20" class="w-[70px] border border-gray-300 rounded px-2 py-1">
                <button id="generateLessonTableBtn" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">สร้างตาราง</button>
            </div>
            <table id="planTable" class="w-full border-collapse border border-black text-sm">
                <thead>
                    <tr>
                        <th class="w-[5%] border border-black p-2.5 align-top bg-blue-100 text-center">สัปดาห์ที่</th>
                        <th class="w-[15%] border border-black p-2.5 align-top bg-blue-100 text-center">หัวข้อ</th>
                        <th class="w-[20%] border border-black p-2.5 align-top bg-blue-100 text-center">วัตถุประสงค์รายสัปดาห์</th>
                        <th class="w-[25%] border border-black p-2.5 align-top bg-blue-100 text-center">กิจกรรมการเรียนรู้</th>
                        <th class="w-[20%] border border-black p-2.5 align-top bg-blue-100 text-center">สื่อ/เครื่องมือ</th>
                        <th class="w-[10%] border border-black p-2.5 align-top bg-blue-100 text-center">การประเมินผล</th>
                        <th class="w-[5%] border border-black p-2.5 align-top bg-blue-100 text-center">CLO</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
            <h2 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                หมวดที่ 8 : การประเมินการบรรลุผลลัพธ์การเรียนรู้รายวิชา (CLOs) </h2>
            <h3 class="mt-6 font-bold">8.1 กลยุทธ์การประเมิน</h3>
            <div class="mb-4">
                จำนวนสัปดาห์: 
                <input type="number" id="Assessment" value="2" min="1" max="20" class="w-[70px] border border-gray-300 rounded px-2 py-1">
                <button id="generateAssessmentTableBtn" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">สร้างตาราง</button>
            </div>
            <table id="assessmentTable" class="w-full border-collapse border border-black text-sm">
                <thead>
                    <tr>
                        <th class="w-[25%] border border-black p-2.5 align-top bg-blue-100 text-center">วิธีการประเมิน</th>
                        <th class="w-[40%] border border-black p-2.5 align-top bg-blue-100 text-center">เครื่องมือ / รายละเอียด</th>
                        <th class="w-[10%] border border-black p-2.5 align-top bg-blue-100 text-center">สัดส่วน (%)</th>
                        <th class="w-[25%] border border-black p-2.5 align-top bg-blue-100 text-center">ความสอดคล้องกับ CLOs</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        
            <h3 class="mt-6 font-bold">8.2 วิธีการประเมิน แบบรูบริค (Rubric) หรือ อื่นๆ (ถ้ามี) </h3>
            <div id="rubric-container" class="max-w-4xl mx-auto space-y-8">
                
                <div class="rubric-section">
                    <h3 class="text-base font-semibold mb-2 p-1" contenteditable="true">
                        ก. การประเมินการปฏิบัติงาน (Performance)
                    </h3>
                    <div class="overflow-x-auto border border-gray-400">
                        <table class="w-full border-collapse text-xs">
                            <thead class="bg-gray-100 text-center font-semibold">
                                <tr>
                                    <th class="border border-gray-400 p-1 w-32">ระดับ</th>
                                    <th class="border border-gray-400 p-1" contenteditable="true">
                                        คำอธิบายเกณฑ์การให้คะแนนการปฏิบัติงาน
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white rubric-tbody">
                                <tr>
                                    <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">5</td>
                                    <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">แสดงถึงความเข้าใจปัญหา ...</td>
                                </tr>
                                
                                <tr>
                                    <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">4</td>
                                    <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 4]</td>
                                </tr>
                                <tr>
                                    <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">3</td>
                                    <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 3]</td>
                                </tr>
                                <tr>
                                    <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">2</td>
                                    <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 2]</td>
                                </tr>
                                <tr>
                                    <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">1</td>
                                    <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 1]</td>
                                </tr>
                                <tr>
                                    <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">0</td>
                                    <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">ไม่ส่งผลงาน</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="max-w-4xl mx-auto mt-6 text-center">
                <button id="add-rubric-btn" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700">
                    + เพิ่มเกณฑ์การประเมินใหม่ (ข, ค, ...)
                </button>
            </div>
            <br>
            {{-- =========================================================================================================================================================================================================================== --}}
            <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                หมวดที่ 9: สื่อการเรียนรู้และงานวิจัย
            </h3>
            <h3 class="mt-6 font-bold">9.1 สื่อการเรียนรู้และสิ่งสนับสนุนการเรียนรู้ </h3>
            <ul id="referenceList" class="list-disc pl-8 max-w-2xl mt-4">
                <li class="mb-2.5 relative">
                    <input type="text" class="w-4/5 p-2 border border-gray-300 rounded-md text-[15px]" placeholder="พิมพ์รายละเอียด" />
                    <button class="remove-btn bg-red-500 text-white border-none rounded-md px-2.5 py-1.5 ml-2.5 cursor-pointer text-[13px] hover:bg-red-600">ลบ</button>
                </li>
            </ul>
            <button id="addReferenceItemBtn" class="mt-4 bg-blue-600 text-white border-none rounded-lg px-3.5 py-2 cursor-pointer text-[14px] hover:bg-blue-700">
                ➕ เพิ่มรายการใหม่
            </button>
            <br><br>
        
            <h3 class="mt-6 font-bold">9.2 งานวิจัยที่นำมาสอนในรายวิชา 
            </h3><br>
            <textarea 
                class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                placeholder='พิมพ์รายละเอียดเกี่ยวกับงานวิจัยที่นำมาสอนในรายวิชา...'
            ></textarea>
        
            <h3 class="mt-6 font-bold">9.3 การบริการวิชาการ  
                </h3><br>
            <textarea 
                class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                placeholder='พิมพ์รายละเอียดเกี่ยวกับการบริการวิชาการ...'
            ></textarea>
        
            <h3 class="mt-6 font-bold">9.4 งานทำนุบำรุงศิลปวัฒนธรรม 
            </h3><br>
            <textarea 
                class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                placeholder='พิมพ์รายละเอียดเกี่ยวกับงานทำนุบำรุงศิลปวัฒนธรรม...'
            ></textarea>
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
                        <tr>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">A</td>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">80.00% ขึ้นไป</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">B+</td>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">75.00% - 79.99%</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">B</td>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">70.00% - 74.99%</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">C+</td>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">65.00% - 69.99%</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">C</td>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">60.00% - 64.99%</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">D+</td>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">55.00% - 59.99%</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">D</td>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">50.00% - 54.99%</td>
                        </tr>
                        <tr>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">F</td>
                            <td class="border border-gray-400 p-2 align-middle text-center hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500" contenteditable="true">ต่ำกว่า 50.00%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- =========================================================================================================================================================================================================================== --}}
            <h3 class="text-center font-bold text-[#003366] mt-6 bg-blue-200 inline-block px-4 py-1.5 rounded text-gray-900">
                หมวด 11 : ขั้นตอนการแก้ไขคะแนน 
            </h3><br>
            <textarea 
                class="w-[90%] h-36 p-4 text-base border border-gray-300 rounded-lg bg-white resize-vertical shadow-sm transition-all mt-2 mb-6
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none"
                placeholder='พิมพ์รายละเอียด'
            ></textarea>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        (function() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const getYearStr = urlParams.get('year');
                const getTermStr = urlParams.get('term');
                
                let buddhistYearStr = '';

                if (getYearStr) {
                    const buddhistYear = parseInt(getYearStr, 10) + 543;
                    buddhistYearStr = buddhistYear.toString();
                }

                // ปีการศึกษาหน้าแรก
                const selectYear = document.getElementById('academic_year');
                if (buddhistYearStr && selectYear) {
                    selectYear.innerHTML = ''; 
                    const newspan = document.createElement('span');
                    newspan.value = buddhistYearStr;
                    newspan.textContent = buddhistYearStr;
                    selectYear.appendChild(newspan);
                    selectYear.value = buddhistYearStr;
                }

                // เทอมหน้าแรก
                const selectterm = document.getElementById('term');
                if (getTermStr && selectterm) {
                    selectterm.innerHTML = ''; 
                    const newspan = document.createElement('span');
                    newspan.value = getTermStr;
                    newspan.textContent = getTermStr;
                    selectterm.appendChild(newspan);
                    selectterm.value = getTermStr;
                }

                // การแก้ไขล่าสุด"
                const yearDisplay = document.getElementById('revision_year_display');
                const term1Checkbox = document.getElementById('revision_term_1');
                const term2Checkbox = document.getElementById('revision_term_2');

                if (buddhistYearStr && yearDisplay) {
                    yearDisplay.textContent = buddhistYearStr;
                }

                // ติ๊กถูกที่ช่องเทอม
                if (getTermStr && term1Checkbox && term2Checkbox) {
                    if (getTermStr === '1') {
                        term1Checkbox.checked = true;
                    } else if (getTermStr === '2') {
                        term2Checkbox.checked = true;
                    }
                }

            } catch (error) {
                console.error("Error setting academic year:", error);
                const selectYear = document.getElementById('academic_year');
                if (selectYear) {
                    selectYear.innerHTML = '<span value="">Error</span>';
                }
            }
        })();
        
        try {
            const stateSymbols_s5_2 = { 
                '0': '&nbsp;', 
                '1': '<span class="text-xl font-bold">●</span>', 
                '2': '<span class="text-xl">○</span>'  
            };

            const section5_2_container = document.getElementById('section-5-2');
            if (section5_2_container) { 
                const cells_s5_2 = section5_2_container.querySelectorAll('.score-cell');
                if (cells_s5_2.length > 0) {
                    cells_s5_2.forEach(cell => {
                        cell.dataset.state = '0';
                        cell.innerHTML = stateSymbols_s5_2['0'];

                        cell.addEventListener('click', () => {
                            let currentState = parseInt(cell.dataset.state || '0');
                            let nextState = (currentState + 1) % 3;
                            cell.dataset.state = nextState;
                            cell.innerHTML = stateSymbols_s5_2[nextState];
                        });
                    });
                }
            }
        } catch (e) {
            console.error("Error setting up Section 5.2 (Curriculum Map Click):", e);
        }
    });

    // --- หมวดที่ 5.3 : CLO/PLO Mapping Table ---
    try {
        const data = [
            { code: "CLO1", desc: "ประเมินวิธีการวิเคราะห์ข้อมูล เพื่อแก้ไขปัญหาทางวิทยาการข้อมูลได้อย่างเหมาะสม" },
            { code: "CLO2", desc: "สร้างต้นแบบเพื่อการทำนายหรือการรู้จำจากชุดข้อมูลเพื่อประยุกต์ใช้ในการแก้ปัญหาจริง" },
            { code: "CLO3", desc: "สื่อสารผลลัพธ์การวิเคราะห์ข้อมูลให้ผู้อื่นเข้าใจและนำไปประยุกต์ใช้อย่างมีจริยธรรม" },
            { code: "LLL1", desc: "Creativity ความคิดสร้างสรรค์" },
            { code: "LLL2", desc: "Problem Solving การแก้ปัญหา" },
            // ... (LLL3-LLL10) ...
            { code: "LLL10", desc:"Reflection การสะท้อนทักษะความรู้ " }
        ];
        const options = ["", "R", "U", "AP", "AN", "E", "C"];
        
        // (FIXED) ย้าย const tbody มาไว้ข้างในนี้
        const tbody = document.querySelector("#ploTable tbody"); 
        
        if(tbody) { // เช็คก่อนว่าหา tbody เจอ
            data.forEach(item => {
                const tr = document.createElement("tr");
                
                // (ใช้ class ของ Tailwind แทน inline style)
                tr.innerHTML = `
                    <td class="border border-gray-400 p-2 text-center font-bold">${item.code}</td>
                    <td class="border border-gray-400 p-2 text-left">${item.desc}</td>
                `;

                for (let i = 0; i < 4; i++) {
                    const td = document.createElement("td");
                    td.className = "border border-gray-400 p-2 text-center";
                    
                    const checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.className = "mr-1.5 scale-125"; // Tailwind class

                    const select = document.createElement("select");
                    select.className = "w-[70px] p-1 border rounded"; // Tailwind class
                    options.forEach(opt => {
                        const op = document.createElement("option");
                        op.value = opt;
                        op.textContent = opt;
                        select.appendChild(op);
                    });

                    td.appendChild(checkbox);
                    td.appendChild(select);
                    tr.appendChild(td);
                }
                tbody.appendChild(tr);
            });
        }
    } catch (e) {
        console.error("Error in Section 5.3 (CLO/PLO Map):", e);
    }

    // --- หมวดที่ 6 : CLO Table (Add Row) ---
    try {
        const data_s6_teachingOptions = {
            onSite: {
                label: "On-site โดยมีการเรียนการสอนแบบ",
                items: [
                    "บรรยาย (Lecture)", "ฝึกปฏิบัติ (Laboratory Model)", "เรียนรู้จากการลงมือทำ (Learning by Doing)",
                    "การเรียนรู้โดยใช้กิจกรรมเป็นฐาน (Activity-based Learning)", "การเรียนรู้โดยใช้วิจัยเป็นฐาน (Research-based Learning)",
                    "ถามตอบสะท้อนคิด (Refractive Learning)", "นำเสนออภิปรายกลุ่ม (Discussion Group)",
                    "เรียนรู้จากการสืบเสาะหาความรู้ (Inquiry-based Learning)", "การเรียนรู้แบบร่วมมือร่วมใจ (Cooperative Learning)",
                    "การเรียนรู้แบบร่วมมือ (Collaborative Learning)", "การเรียนรู้โดยใช้โครงการเป็นฐาน (Project-based Learning: PBL)",
                    "การเรียนรู้โดยใช้ปัญหาเป็นฐาน (Problem-based Learning)", "วิเคราะห์โจทย์ปัญหา (Problem Solving)",
                    "เรียนรู้การตัดสินใจ (Decision Making)", "ศึกษาค้นคว้าจากกรณีศึกษา (Case Study)",
                    "เรียนรู้ผ่านการเกม/การเล่น (Game/Play Learning)", "ศึกษาดูงาน (Field Trips)", "อื่น ๆ....................................."
                ],
                indent: true
            },
            online: {
                label: "Online โดยมีการเรียนการสอนแบบ",
                items: ["บรรยาย (Lecture)"],
                indent: true
            }
        };
        const data_s6_assessmentOptions = {
            exam: {
                label: "คะแนนจากแบบทดสอบสัมฤทธิ์ผล (ข้อสอบ)",
                items: [ "ทดสอบย่อย (Quiz)", "ทดสอบกลางภาค (Midterm)", "ทดสอบปลายภาค (Final)" ]
            },
            performance: {
                label: "คะแนนจากผลงานที่ได้รับมอบหมาย (Performance) โดยใช้เกณฑ์ Rubric Score การทำงานกลุ่มและเดี่ยว",
                items: [ "การทำงานเป็นทีม (Team Work)", "โปรแกรม ซอฟต์แวร์", "ผลงาน ชิ้นงาน", "รายงาน (Report)", "การนำเสนอ (Presentation)", "แฟ้มสะสมงาน (Portfolio)", "รายงานการศึกษาด้วยตนเอง (Self-Study Report)" ]
            },
            behavior: {
                label: "คะแนนจากผลการพฤติกรรม",
                items: [ "ความรับผิดชอบ การมีส่วนร่วม", "ประเมินผลการงานที่ส่ง" ]
            }
        };
        
        // (ฟังก์ชันทั้งหมดของหมวด 6 ถูกย้ายมาไว้ข้างในนี้)
        function initializeCloTable_Section6() {
            const tableBody = document.getElementById('cloTableBody_S6');
            const addBtn = document.getElementById('addCloBtn_S6');
            const template = document.getElementById('cloRowTemplate_S6');
            if (!tableBody || !addBtn || !template) return;

            function _s6_createCheckboxHtml(optionsObject) {
                let html = '';
                for (const key in optionsObject) {
                    const category = optionsObject[key];
                    html += `<div class="font-semibold mt-2">${category.label}</div>`;
                    category.items.forEach(itemText => {
                        const indentClass = category.indent ? 'ml-4' : '';
                        html += `
                            <label class="flex items-start ${indentClass} hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" class="mt-1 scale-125 mr-1.5">
                                <span class="ml-2">${itemText}</span>
                            </label>
                        `;
                    });
                }
                return html;
            }

            function _s6_addNewCloRow() {
                const newRow = template.content.cloneNode(true);
                const cloCount = tableBody.children.length + 1;
                newRow.querySelector('.clo-cell-s6').textContent = `CLO${cloCount} [คลิกเพื่อแก้ไข]`;
                tableBody.appendChild(newRow);
            }

            const teachingHtml = _s6_createCheckboxHtml(data_s6_teachingOptions);
            const assessmentHtml = _s6_createCheckboxHtml(data_s6_assessmentOptions);
            template.content.querySelector('.teaching-cell-s6').innerHTML = teachingHtml;
            template.content.querySelector('.assessment-cell-s6').innerHTML = assessmentHtml;
            
            _s6_addNewCloRow();
            addBtn.addEventListener('click', _s6_addNewCloRow);
        }
        
        initializeCloTable_Section6(); // เรียกใช้ฟังก์ชัน

    } catch (e) {
        console.error("Error in Section 6 (CLO Table):", e);
    }

    // --- หมวดที่ 7.1 : แผนการสอน (Lesson Plan) ---
    try {
    // ---- ฟังก์ชั่นหมวดที่ 7.1 : แผนการสอน ----
    function generateTableLesson() {
        const tbody = document.querySelector("#planTable tbody");
        if (!tbody) {
            console.warn("Table body for lesson plan (#planTable tbody) not found.");
            return; // หยุดทำงานถ้าหา tbody ไม่เจอ
        }

        tbody.innerHTML = ""; // ล้างตารางก่อนสร้างใหม่

        const weekCountInput = document.getElementById("weekCount");
        // ถ้าหา input ไม่เจอ หรือค่าไม่ใช่ตัวเลข ให้ใช้ค่า default เป็น 2
        const weekCount = (weekCountInput && !isNaN(parseInt(weekCountInput.value))) ? parseInt(weekCountInput.value) : 2;

        for (let i = 1; i <= weekCount; i++) {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="border border-black p-2.5 align-top text-center">${i}</td>
                <td class="border border-black p-1 align-top">
                    <textarea name="topic_${i}" placeholder="ใส่หัวข้อสัปดาห์ ${i}" class="w-full h-16 border border-gray-300 rounded p-1"></textarea>
                </td>
                <td class="border border-black p-1 align-top">
                    <textarea name="objective_${i}" placeholder="ใส่วัตถุประสงค์" class="w-full h-16 border border-gray-300 rounded p-1"></textarea>
                </td>
                <td class="border border-black p-1 align-top">
                    <textarea name="activity_${i}" placeholder="ใส่กิจกรรมการเรียนรู้" class="w-full h-16 border border-gray-300 rounded p-1"></textarea>
                </td>
                <td class="border border-black p-1 align-top">
                    <textarea name="tool_${i}" placeholder="ใส่สื่อ/เครื่องมือ" class="w-full h-16 border border-gray-300 rounded p-1"></textarea>
                </td>
                <td class="border border-black p-1 align-top">
                    <textarea name="assessment_${i}" placeholder="ใส่การประเมินผล" class="w-full h-16 border border-gray-300 rounded p-1"></textarea>
                </td>
                <td class="border border-black p-2.5 align-top text-center">
                    <select name="clo_${i}" class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">--เลือก--</option>
                        <option value="CLO1">CLO 1</option>
                        <option value="CLO2">CLO 2</option>
                        <option value="CLO3">CLO 3</option>
                        <option value="CLO4">CLO 4</option>
                        <option value="CLO5">CLO 5</option>
                    </select>
                </td>
            `;
            tbody.appendChild(row);
        }
    }

    // --- ผูก Event Listener กับปุ่ม "สร้างตาราง" ---
    // (ย้ายมาจากโค้ดก่อนหน้า และเปลี่ยน onclick เป็น addEventListener)
    const lessonBtn = document.getElementById('generateLessonTableBtn');
    if (lessonBtn) {
        lessonBtn.addEventListener('click', generateTableLesson);
    } else {
        console.warn("Button to generate lesson table (#generateLessonTableBtn) not found.");
    }

    // --- เรียกใช้ฟังก์ชันครั้งแรกเมื่อหน้าเว็บโหลดเสร็จ ---
    // (ย้ายมาไว้ข้างใน try...catch และเรียกหลังจากผูกปุ่ม)
    generateTableLesson();

} catch (e) {
    console.error("Error in Section 7.1 (Lesson Plan):", e);
}
    
    // --- หมวดที่ 8.1 : กลยุทธ์การประเมิน ---
    try {
        // (FIXED) ย้ายฟังก์ชันมาไว้ข้างใน
        function generateTableAssessment() {
            const tbody = document.querySelector("#assessmentTable tbody");
            if(!tbody) return;
            tbody.innerHTML = ""; // ล้างตารางก่อน
            const Assessment = parseInt(document.getElementById("Assessment").value);

            for (let i = 1; i <= Assessment; i++) {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td class="border border-black p-1 align-top"><textarea name="method[]" placeholder="เช่น แบบฝึกหัดในห้องเรียน / Quiz / สอบกลางภาค" class="w-full h-16 border border-gray-300 rounded p-1"></textarea></td>
                    <td class="border border-black p-1 align-top"><textarea name="tool[]" placeholder="เช่น แบบฝึกหัด Lab, การนำเสนอ, โครงการ, รายงาน" class="w-full h-16 border border-gray-300 rounded p-1"></textarea></td>
                    <td class="border border-black p-1 align-top"><input type="number" name="percent[]" min="0" max="100" placeholder="%" class="w-full text-center border border-gray-300 rounded p-1"></td>
                    <td class="border border-black p-1 align-top">
                        <select name="clo[]" class="w-full mb-1 p-1 border rounded">
                            <option value="">--เลือก CLO--</option>
                            <option value="CLO1">CLO1</option>
                            <option value="CLO2">CLO2</option>
                            <option value="CLO3">CLO3</option>
                            <option value="CLO4">CLO4</option>
                            <option value="CLO5">CLO5</option>
                        </select>
                        <textarea name="clo_desc[]" placeholder="ระบุรายละเอียดความสอดคล้อง เช่น CLO1: การวิเคราะห์ข้อมูล" class="w-full h-12 border border-gray-300 rounded p-1"></textarea>
                    </td>
                `;
                tbody.appendChild(row);
            }
        }
        
        // (FIXED) เปลี่ยน onclick เป็น addEventListener
        const assessmentBtn = document.getElementById('generateAssessmentTableBtn');
        if(assessmentBtn) {
            assessmentBtn.addEventListener('click', generateTableAssessment);
            generateTableAssessment(); // เรียกใช้ครั้งแรก
        }
    } catch (e) {
        console.error("Error in Section 8.1 (Assessment Strategy):", e);
    }

    // --- หมวดที่ 8.2 : Rubrics (Add Section) ---
    try {
        const newRubricBodyTemplate = `
            <tr>
                <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">5</td>
                <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 5]</td>
            </tr>
            <tr>
                <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">4</td>
                <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 4]</td>
            </tr>
            <tr>
                <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">3</td>
                <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 3]</td>
            </tr>
            <tr>
                <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">2</td>
                <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 2]</td>
            </tr>
            <tr>
                <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">1</td>
                <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">[ใส่คำอธิบายสำหรับระดับ 1]</td>
            </tr>
            <tr>
                <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true">0</td>
                <td class="description-cell border border-gray-400 p-1 align-top hover:bg-gray-50" contenteditable="true">ไม่ส่งผลงาน</td>
            </tr>
        `;
        
        const rubricContainer = document.getElementById('rubric-container');
        const addRubricBtn = document.getElementById('add-rubric-btn');

        // --- [เพิ่มใหม่] ฟังก์ชันสำหรับอัปเดตลำดับ ก, ข, ค ---
        function updateRubricLetters() {
            const allRubrics = rubricContainer.querySelectorAll('.rubric-section');
            allRubrics.forEach((rubric, index) => {
                const letter = String.fromCharCode(0x0E01 + index); // 0=ก, 1=ข
                const titleElement = rubric.querySelector('h3');
                const thElement = rubric.querySelector('thead th:nth-child(2)');

                if (titleElement && thElement) {
                    // ดึงข้อความเดิม (หลัง ". ")
                    let currentTitleText = titleElement.textContent;
                    if (currentTitleText.match(/^[ก-ฮ]\.\s/)) { // ถ้ามี ก. อยู่แล้ว
                        currentTitleText = currentTitleText.substring(currentTitleText.indexOf('.') + 2);
                    } else if (currentTitleText.startsWith('[หัวข้อใหม่')) {
                        currentTitleText = "[หัวข้อใหม่ (คลิกเพื่อแก้ไข)]";
                    }
                    // (ถ้าไม่ตรงเงื่อนไขเลย ก็คือข้อความจาก template เดิม)

                    // อัปเดตหัวข้อ
                    titleElement.textContent = `${letter}. ${currentTitleText}`;
                    // อัปเดตหัวตาราง
                    thElement.textContent = `[คำอธิบายเกณฑ์ฯ ของหัวข้อ ${letter}]`;
                }
            });
        }

        if(rubricContainer && addRubricBtn) {
            
            // --- [แก้ไข] อัปเดต Listener ของปุ่ม "เพิ่ม" ---
            addRubricBtn.addEventListener('click', () => {
                const template = rubricContainer.querySelector('.rubric-section'); 
                if (!template) return;
                
                const newRubric = template.cloneNode(true);
                
                // --- [เพิ่มใหม่] สร้างปุ่มลบและจัด Layout ---
                // (เช็คก่อนว่าปุ่มลบยังไม่มี เผื่อ template มีอยู่แล้ว)
                if (!newRubric.querySelector('.delete-rubric-btn')) {
                    const deleteButton = document.createElement('button');
                    deleteButton.className = "delete-rubric-btn px-3 py-1 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 ml-4";
                    deleteButton.textContent = "ลบ";
                    
                    const titleElement = newRubric.querySelector('h3');
                    if (titleElement) {
                        // สร้าง div มาครอบ h3 และปุ่มลบ
                        const headerWrapper = document.createElement('div');
                        headerWrapper.className = "flex items-center justify-between mb-2";
                        
                        // ย้าย h3 เข้าไปใน wrapper
                        titleElement.parentNode.insertBefore(headerWrapper, titleElement);
                        headerWrapper.appendChild(titleElement);
                        // เพิ่มปุ่มลบเข้าไปใน wrapper
                        headerWrapper.appendChild(deleteButton);
                    }
                }
                
                // ตั้งค่าข้อความเริ่มต้นสำหรับอันใหม่
                newRubric.querySelector('h3').textContent = `[หัวข้อใหม่ (คลิกเพื่อแก้ไข)]`;
                newRubric.querySelector('.rubric-tbody').innerHTML = newRubricBodyTemplate;
                
                rubricContainer.appendChild(newRubric);
                
                // [แก้ไข] เรียกใช้ฟังก์ชันอัปเดต ก, ข, ค ทั้งหมด
                updateRubricLetters();
            });

            // --- [เพิ่มใหม่] Listener สำหรับ "ปุ่มลบ" (ใช้ Event Delegation) ---
            rubricContainer.addEventListener('click', (event) => {
                // เช็คว่าที่คลิกคือปุ่มลบ (คลาส .delete-rubric-btn)
                if (event.target.classList.contains('delete-rubric-btn')) {
                    
                    // ป้องกันการลบหัวข้อสุดท้าย (ก.)
                    const totalRubrics = rubricContainer.querySelectorAll('.rubric-section').length;
                    if (totalRubrics <= 1) {
                        alert("อย่างน้อยต้องมี 1 หัวข้อเกณฑ์การประเมิน");
                        return;
                    }
                    
                    // ค้นหา .rubric-section ที่ใกล้ที่สุด
                    const rubricToRemove = event.target.closest('.rubric-section');
                    
                    // ยืนยันก่อนลบ
                    if (rubricToRemove && confirm('คุณแน่ใจหรือไม่ว่าต้องการลบหัวข้อนี้?')) {
                        rubricToRemove.remove();
                        // อัปเดต ก, ข, ค ที่เหลือใหม่
                        updateRubricLetters(); 
                    }
                }
            });

            // --- [เพิ่มใหม่] เพิ่มปุ่มลบให้กับหัวข้อแรก (ก.) ที่มีอยู่แล้ว (ถ้ายังไม่มี) ---
            const firstRubric = rubricContainer.querySelector('.rubric-section');
            if (firstRubric && !firstRubric.querySelector('.delete-rubric-btn')) {
                const deleteButton = document.createElement('button');
                deleteButton.className = "delete-rubric-btn px-3 py-1 bg-red-600 text-white font-semibold rounded-lg shadow-md hover:bg-red-700 ml-4";
                deleteButton.textContent = "ลบ";
                
                const titleElement = firstRubric.querySelector('h3');
                if (titleElement) {
                    const headerWrapper = document.createElement('div');
                    headerWrapper.className = "flex items-center justify-between mb-2";
                    
                    titleElement.parentNode.insertBefore(headerWrapper, titleElement);
                    headerWrapper.appendChild(titleElement);
                    headerWrapper.appendChild(deleteButton);
                }
            }
            
            // --- [เพิ่มใหม่] เรียกอัปเดต ก, ข, ค ครั้งแรกตอนโหลดหน้า ---
            updateRubricLetters();

        }
    } catch (e) {
        console.error("Error in Section 8.2 (Rubrics):", e);
    }

    // --- หมวดที่ 9.1 : สื่อการเรียนรู้ (Add/Remove List) ---
    try {
        const referenceList = document.getElementById('referenceList');
        const addReferenceBtn = document.getElementById('addReferenceItemBtn');

        function addNewReferenceItem() {
            const li = document.createElement('li');
            li.className = "mb-2.5 relative";
            li.innerHTML = `
                <input type="text" class="w-4/5 p-2 border border-gray-300 rounded-md text-[15px]" placeholder="พิมพ์รายการใหม่ที่นี่...">
                <button class="remove-btn bg-red-500 text-white border-none rounded-md px-2.5 py-1.5 ml-2.5 cursor-pointer text-[13px] hover:bg-red-600">ลบ</button>
            `;
            referenceList.appendChild(li);
        }

        if (referenceList && addReferenceBtn) {
            
            addReferenceBtn.addEventListener('click', addNewReferenceItem);
            
            referenceList.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-btn')) {
                    
                    // เช็คก่อนว่ามีมากกว่า 1 รายการหรือไม่
                    const itemCount = referenceList.children.length;
                    if (itemCount > 1) {
                        // ถ้ามีมากกว่า 1 ลบได้
                        event.target.parentElement.remove();
                    } else {
                        // ถ้าเหลือ 1 อัน ลบไม่ได้
                        alert("อย่างน้อยต้องมี 1 รายการ");
                    }
                }
            });

            // ถ้า list ว่างเปล่า ให้เพิ่ม 1 รายการทันที
            if (referenceList.children.length === 0) {
                addNewReferenceItem();
            }

        }
    } catch (e) {
        console.error("Error in Section 9.1 (Reference List):", e);
    }
</script>

</body>
</html>