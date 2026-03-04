<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Manage Professor Course | ELO Generator</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/professor_course.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
        
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            /* ปรับแต่ง Select2 ให้เข้ากับธีม */
            .select2-container .select2-selection--single { 
                height: 44px; 
                display: flex; 
                align-items: center; 
                border-color: #E0E1DD !important; /* Cloud Blue */
                border-radius: 0.5rem; 
                background-color: #FFFFFF;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow { 
                height: 42px; 
            }
            .select2-container--default .select2-selection--single:focus,
            .select2-container--default.select2-container--open .select2-selection--single {
                border-color: #415A77 !important; /* Royal Blue */
                box-shadow: 0 0 0 4px rgba(65, 90, 119, 0.15); /* Ring Royal Blue */
            }
        </style>
    </head>
    
    <body class="font-sans text-slate-grey antialiased min-h-screen">
        @include('component.navbar')

        <div class="container mx-auto px-4 py-8 pt-24 max-w-6xl">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-deep-navy">จัดการอาจารย์รายวิชา</h1>
                    <p class="text-slate-grey text-sm mt-1">กำหนดอาจารย์ผู้สอนรายวิชาในหลักสูตร</p>
                </div>
            </div>

            <div class="bg-pure-white p-6 rounded-2xl shadow-sm border border-cloud-blue mb-8 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-royal-blue"></div>
                <form method="GET" action="{{ route('courses') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="block text-sm font-bold text-deep-navy mb-2">เลือกหลักสูตร</label>
                        <select id="curriculum" name="curriculum_id" class="w-full">
                            <option value="">-- เลือกหลักสูตร --</option>
                            @foreach($curriculum as $cur)
                                <option value="{{ $cur->id }}" {{ $curriculumId == $cur->id ? 'selected' : '' }}>
                                    {{ $cur->curriculum_year }} - {{ $cur->curriculum_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(request('curriculum_id'))
                        <div>
                            <label class="block text-sm font-bold text-deep-navy mb-2">เลือกรายวิชา</label>
                            @if(count($courses) > 0)
                                <select id="course" name="course_code" class="w-full">
                                    <option value="">-- เลือกรายวิชา --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_code') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_code }} {{ $course->course_name_th }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-200 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    ไม่พบรายวิชาในหลักสูตรนี้
                                </div>
                            @endif
                        </div>
                    @endif
                </form>
            </div>

            @if(isset($professor))
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <div class="bg-pure-white rounded-2xl shadow-sm border border-cloud-blue overflow-hidden">
                            <div class="px-6 py-4 border-b border-cloud-blue flex justify-between items-center bg-cloud-blue/30">
                                <h2 class="font-bold text-deep-navy">รายชื่ออาจารย์ผู้สอน</h2>
                                <span class="bg-cloud-blue/50 text-royal-blue text-xs font-bold px-3 py-1 rounded-full border border-cloud-blue">{{ count($professor) }} ท่าน</span>
                            </div>
                            
                            <div class="overflow-x-auto no-scrollbar">
                                <table class="min-w-full divide-y divide-cloud-blue">
                                    <thead class="bg-pure-white">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">อาจารย์</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-deep-navy uppercase tracking-wider">ปีการศึกษา</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-deep-navy uppercase tracking-wider">ภาคเรียน</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-deep-navy uppercase tracking-wider">มคอ.</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-deep-navy uppercase tracking-wider">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-pure-white divide-y divide-cloud-blue/50">
                                        @forelse($professor as $t)
                                            <tr class="hover:bg-cloud-blue/10 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full bg-cloud-blue/40 flex items-center justify-center text-royal-blue font-bold text-xs mr-3 border border-cloud-blue/50">
                                                            {{ substr($t->user->name ?? 'U', 0, 1) }}
                                                        </div>
                                                        <div class="text-sm font-bold text-deep-navy">{{ $t->user->name ?? 'Unknown' }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-grey">
                                                    {{ $t->year < 2400 ? $t->year + 543 : $t->year }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-grey">
                                                    {{ $t->term }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-cloud-blue/40 text-royal-blue border border-cloud-blue">
                                                        {{ $t->TQF }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                                    <button type="button" class="edit-btn text-amber-500 hover:text-amber-700 transition p-2 rounded-lg hover:bg-amber-50"
                                                        data-id="{{ $t->id }}"
                                                        data-user-id="{{ $t->user_id }}"
                                                        data-year="{{ $t->year < 2400 ? $t->year + 543 : $t->year }}"
                                                        data-term="{{ $t->term }}"
                                                        data-tqf="{{ $t->TQF }}"
                                                        data-update-url="{{ route('professor.update', [$courseId, $t->id]) }}"
                                                        title="แก้ไข">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    </button>
                                                    
                                                    <form method="POST" action="{{ route('professor.destroy', [$courseId, $t->id]) }}" class="inline-block delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="delete-btn text-red-500 hover:text-red-700 transition p-2 rounded-lg hover:bg-red-50"
                                                            data-name="{{ $t->user->name ?? 'Unknown' }}"
                                                            data-year="{{ $t->year < 2400 ? $t->year + 543 : $t->year }}"
                                                            data-term="{{ $t->term }}"
                                                            title="ลบ">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center text-slate-grey bg-cloud-blue/10">
                                                    <div class="flex flex-col items-center justify-center">
                                                        <svg class="w-12 h-12 text-cloud-blue mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                                        <p class="font-medium">ยังไม่มีข้อมูลอาจารย์ผู้สอนในรายวิชานี้</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-pure-white rounded-2xl shadow-sm border border-cloud-blue p-6 sticky top-24">
                            <h2 class="text-lg font-bold text-deep-navy mb-5 flex items-center gap-2 border-b border-cloud-blue pb-3">
                                <div class="bg-cloud-blue/40 text-royal-blue p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                                </div>
                                เพิ่มอาจารย์ผู้สอน
                            </h2>
                            
                            <form method="POST" action="{{ route('professor.store', $courseId) }}" class="space-y-5">
                                @csrf
                                <input type="hidden" name="curriculum_id" value="{{ $curriculumId }}">
                                
                                <div>
                                    <label class="block text-sm font-bold text-deep-navy mb-1.5">เลือกอาจารย์ <span class="text-red-500">*</span></label>
                                    <select name="user_id" class="w-full border-cloud-blue rounded-xl focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue py-2.5 px-3 text-deep-navy font-medium outline-none transition-all">
                                        @foreach($users as $user)
                                            <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-deep-navy mb-1.5">ปีการศึกษา <span class="text-red-500">*</span></label>
                                        <input type="number" name="year" class="w-full border-cloud-blue rounded-xl focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue py-2.5 px-3 text-deep-navy font-medium outline-none transition-all" 
                                               value="{{ date('Y') + 543 }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-deep-navy mb-1.5">ภาคเรียน <span class="text-red-500">*</span></label>
                                        <select name="term" class="w-full border-cloud-blue rounded-xl focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue py-2.5 px-3 text-deep-navy font-medium outline-none transition-all">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-deep-navy mb-1.5">มคอ. <span class="text-red-500">*</span></label>
                                    <select name="TQF" class="w-full border-cloud-blue rounded-xl focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue py-2.5 px-3 text-deep-navy font-medium outline-none transition-all bg-cloud-blue/10">
                                        <option value="3">มคอ. 3</option>
                                        <option value="5" disabled class="text-slate-grey">มคอ. 5</option>
                                    </select>
                                </div>

                                <button type="submit" class="w-full mt-2 bg-royal-blue hover:bg-deep-navy text-pure-white font-bold py-3 px-4 rounded-xl shadow-md shadow-royal-blue/20 transition-all transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-royal-blue/30">
                                    บันทึกข้อมูล
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div id="edit-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/70 z-50 hidden backdrop-blur-sm transition-opacity duration-300 opacity-0">
            <div class="bg-pure-white rounded-2xl shadow-2xl p-8 w-full max-w-sm transform transition-all scale-95 opacity-0 duration-300 border border-cloud-blue">
                <div class="text-center mb-6 border-b border-cloud-blue pb-4">
                    <h3 class="text-2xl font-bold text-deep-navy">แก้ไขข้อมูล</h3>
                    <p class="text-sm text-slate-grey mt-1">แก้ไขรายละเอียดการสอน</p>
                </div>
                
                <form id="edit-form" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="curriculum_id" value="{{ $curriculumId ?? '' }}">
                    
                    <div>
                        <label class="block text-sm font-bold text-deep-navy mb-1.5 text-left">อาจารย์ผู้สอน</label>
                        <select name="user_id" id="edit-user-input" class="w-full border-cloud-blue rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 py-2.5 px-3 text-deep-navy font-medium outline-none transition-all bg-cloud-blue/10">
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-deep-navy mb-1.5 text-left">ปีการศึกษา</label>
                            <input type="number" name="year" id="edit-year-input" class="w-full border-cloud-blue rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 py-2.5 px-3 text-deep-navy font-medium outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-deep-navy mb-1.5 text-left">ภาคเรียน</label>
                            <select name="term" id="edit-term-input" class="w-full border-cloud-blue rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 py-2.5 px-3 text-deep-navy font-medium outline-none transition-all">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-deep-navy mb-1.5 text-left">มคอ.</label>
                        <select name="TQF" id="edit-TQF-input" class="w-full border-cloud-blue rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 py-2.5 px-3 text-deep-navy font-medium outline-none transition-all bg-cloud-blue/10 cursor-not-allowed" style="pointer-events:none;">
                            <option value="3">มคอ. 3</option>
                            <option value="5" disabled>มคอ. 5</option>
                        </select>
                    </div>

                    <div class="flex gap-3 mt-8 pt-2">
                        <button type="button" id="edit-cancel" class="flex-1 bg-cloud-blue/30 hover:bg-cloud-blue text-deep-navy py-2.5 rounded-xl font-medium transition-colors">ยกเลิก</button>
                        <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-pure-white py-2.5 rounded-xl font-medium shadow-md transition-colors">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/80 z-50 hidden backdrop-blur-sm transition-opacity">
            <div class="bg-pure-white rounded-2xl shadow-xl p-8 max-w-sm w-full text-center transform scale-95 transition-all border border-cloud-blue">
                <div class="w-16 h-16 bg-red-50 border border-red-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </div>
                <h3 class="mb-2 text-xl font-bold text-deep-navy">ยืนยันการลบ?</h3>
                <p class="text-slate-grey mb-4 text-sm font-medium">คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้? การกระทำนี้ไม่สามารถย้อนกลับได้</p>

                <div class="mb-6 p-4 bg-cloud-blue/20 rounded-xl border border-cloud-blue">
                    <p id="delete-course-name" class="font-bold text-red-600 text-sm break-words"></p>
                </div>

                <div class="flex gap-3 justify-center">
                    <button id="delete-confirm" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-pure-white font-bold rounded-xl shadow-sm transition-colors">ใช่, ลบเลย</button>
                    <button id="delete-cancel" class="flex-1 px-4 py-2.5 bg-cloud-blue/40 hover:bg-cloud-blue text-deep-navy font-bold rounded-xl transition-colors">ยกเลิก</button>
                </div>
            </div>
        </div>

        @if ($errors->any())
        <div id="error-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/70 z-[60] backdrop-blur-sm transition-opacity">
            <div class="bg-pure-white rounded-2xl shadow-2xl p-8 w-full max-w-sm text-center mx-4 transform scale-95 border border-cloud-blue">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-5 border border-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-deep-navy mb-3">เกิดข้อผิดพลาด</h3>
                <ul class="text-sm text-red-600 bg-red-50 p-4 rounded-xl text-left list-disc pl-5 space-y-1.5 mb-8 border border-red-100 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button onclick="closeSessionModal()" class="w-full bg-red-600 hover:bg-red-700 text-pure-white py-3 rounded-xl font-bold shadow-sm transition-colors">ตกลง</button>
            </div>
        </div>
        @endif

        @if(session('success') || session('error'))
        <div id="session-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/70 z-[60] backdrop-blur-sm transition-opacity">
            <div class="bg-pure-white rounded-2xl shadow-2xl p-8 w-full max-w-sm text-center mx-4 transform scale-95 border border-cloud-blue">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-5 border {{ session('error') ? 'bg-red-50 border-red-100' : 'bg-green-50 border-green-100' }}">
                    @if(session('error'))
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    @else
                        <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    @endif
                </div>
                <h3 class="text-xl font-bold text-deep-navy mb-2">{{ session('error') ? 'เกิดข้อผิดพลาด' : 'ทำรายการสำเร็จ' }}</h3>
                <p class="text-slate-grey text-sm mb-8 font-medium">{{ session('success') ?? session('error') }}</p>
                <button onclick="closeSessionModal()" class="w-full py-3 rounded-xl font-bold shadow-sm transition-colors text-pure-white focus:outline-none focus:ring-4 {{ session('error') ? 'bg-red-600 hover:bg-red-700 focus:ring-red-200' : 'bg-royal-blue hover:bg-deep-navy focus:ring-royal-blue/30' }}">
                    ตกลง
                </button>
            </div>
        </div>
        @endif

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </body>
</html>