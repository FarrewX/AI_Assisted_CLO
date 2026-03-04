<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Courses | ELO Generator</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/addcourse.js'])
    @endif
</head>
<body class="font-sans antialiased text-slate-grey">
    @include('component.navbar')

    <div class="container mx-auto p-4 pt-24 max-w-7xl">
        
        <div class="mb-2 bg-pure-white p-6 rounded-2xl shadow-sm border border-cloud-blue flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-cloud-blue/40 p-3 rounded-xl text-royal-blue border border-cloud-blue/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-deep-navy tracking-tight">จัดการรายวิชาในหลักสูตร</h1>
                    <p class="text-sm text-slate-grey mt-1">Curriculum Courses Management</p>
                </div>
            </div>

            <form method="GET" action="{{ route('addcourse.list') }}" class="relative w-full md:w-96 group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none transition-colors group-hover:text-royal-blue text-slate-grey">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                <select id="curriculum_select" name="curriculum_id" onchange="this.form.submit()"
                    class="bg-pure-white border-2 border-cloud-blue text-deep-navy text-sm font-bold rounded-xl focus:outline-none focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue block w-full pl-11 pr-10 py-3 shadow-sm hover:border-royal-blue/50 transition-all cursor-pointer appearance-none">
                    <option value="" hidden>-- เลือกหลักสูตรเพื่อจัดการ --</option>
                    @foreach($curricula as $cur)
                        <option value="{{ $cur->id }}" {{ (isset($selectedCurriculumId) && $selectedCurriculumId == $cur->id) ? 'selected' : '' }}>
                            {{ $cur->curriculum_year }} : {{ Str::limit($cur->curriculum_name, 40) }}
                        </option>
                    @endforeach
                </select>

                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-grey group-hover:text-royal-blue transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </form>
        </div>

        @if(isset($selectedCurriculumId) && $selectedCurriculumId)
            <div class="bg-pure-white p-5 rounded-2xl shadow-sm border border-cloud-blue mb-2 flex flex-col lg:flex-row gap-4 justify-between items-center">
                
                <button id="btn-add" 
                    data-route-create="{{ route('addcourse.create') }}"
                    data-curriculum-id="{{ $selectedCurriculumId ?? '' }}"
                    class="w-full lg:w-auto bg-royal-blue hover:bg-deep-navy text-pure-white px-5 py-2.5 rounded-xl shadow-sm flex items-center justify-center gap-2 transition font-semibold text-sm whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                    เพิ่มรายวิชาใหม่
                </button>
                
                <div class="relative w-full flex-1 max-w-lg mx-auto">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-grey" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="course-search-input" 
                        class="block w-full pl-11 pr-4 py-2.5 border-2 border-cloud-blue rounded-xl leading-5 bg-pure-white hover:bg-cloud-blue/10 focus:bg-pure-white placeholder-slate-grey/70 focus:outline-none focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue sm:text-sm transition ease-in-out shadow-sm font-medium text-deep-navy" 
                        placeholder="ค้นหารายวิชา (รหัส, ชื่อไทย, ชื่ออังกฤษ)...">
                </div>

                <form action="{{ route('addcourse.import') }}" method="POST" enctype="multipart/form-data" class="w-full lg:w-auto flex flex-col sm:flex-row items-center gap-3">
                    @csrf
                    <input type="hidden" name="curriculum_id" value="{{ $selectedCurriculumId }}">
                    
                    <div class="relative w-full sm:w-auto flex-1">
                        <input type="file" name="csv_file" accept=".csv" required 
                            class="block w-full text-sm text-slate-grey file:mr-2 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-cloud-blue/40 file:text-royal-blue hover:file:bg-cloud-blue cursor-pointer transition">
                    </div>
                    <button type="submit" class="w-full sm:w-auto bg-deep-navy hover:bg-royal-blue text-pure-white px-5 py-2.5 rounded-xl shadow-sm transition font-semibold text-sm whitespace-nowrap">
                        Import CSV
                    </button>
                </form>
            </div>

            <div class="bg-pure-white rounded-2xl shadow-sm border border-cloud-blue overflow-hidden">
                <table id="courses-table" class="min-w-full divide-y divide-cloud-blue">
                    <thead class="bg-cloud-blue/30">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">รหัสวิชา</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">ชื่อวิชา (TH)</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">ชื่อวิชา (EN)</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-deep-navy uppercase tracking-wider">หน่วยกิต</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-deep-navy uppercase tracking-wider w-32">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-pure-white divide-y divide-cloud-blue/50">
                        @forelse($courses as $c)
                        <tr class="hover:bg-cloud-blue/10 transition" 
                            data-id="{{ $c->id }}"
                            data-code="{{ $c->course_code }}"
                            data-name-th="{{ $c->course_name_th }}"
                            data-name-en="{{ $c->course_name_en }}"
                            data-detail-th="{{ $c->course_detail_th }}"
                            data-detail-en="{{ $c->course_detail_en }}"
                            data-credit="{{ $c->credit }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-royal-blue">{{ $c->course_code }}</td>
                            <td class="px-6 py-4 text-deep-navy font-medium">{{ $c->course_name_th }}</td>
                            <td class="px-6 py-4 text-slate-grey text-sm">{{ $c->course_name_en ?? '-' }}</td>
                            <td class="px-6 py-4 text-center font-medium text-slate-grey">{{ $c->credit ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <button class="btn-edit text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 transition p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button class="btn-delete text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 transition p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-row">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-grey bg-cloud-blue/10">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-cloud-blue mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    <p class="font-medium">ยังไม่มีรายวิชาในหลักสูตรนี้</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                {{ $courses->appends(['curriculum_id' => $selectedCurriculumId])->links() }}
            </div>
        @else
            <div class="bg-cloud-blue/20 border-2 border-cloud-blue p-10 rounded-2xl text-center">
                <div class="bg-pure-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-cloud-blue">
                    <svg class="w-8 h-8 text-royal-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                </div>
                <p class="font-bold text-xl text-deep-navy mb-2">กรุณาเลือกหลักสูตร</p>
                <p class="text-slate-grey">เลือกหลักสูตรจากรายการด้านบน เพื่อจัดการรายวิชาในหลักสูตรนั้น</p>
            </div>
        @endif
    </div>

    <div id="addcourse-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/60 z-50 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-pure-white rounded-xl shadow-2xl p-6 w-full max-w-lg transform transition-all scale-95 max-h-[90vh] overflow-y-auto border border-cloud-blue">
            <h2 id="modal-title" class="text-xl font-bold mb-4 text-deep-navy border-b border-cloud-blue pb-2">เพิ่มรายวิชาใหม่</h2>
            
            <form id="addcourse-form" method="POST">
                @csrf
                <input type="hidden" id="method-field" name="_method" value="POST">
                <input type="hidden" name="curriculum_id" value="{{ $selectedCurriculumId ?? '' }}">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-deep-navy mb-1">รหัสวิชา <span class="text-red-500">*</span></label>
                        <input type="text" name="course_code" id="input-code" class="w-full border-cloud-blue text-deep-navy rounded-lg shadow-sm focus:ring-royal-blue focus:border-royal-blue px-3 py-2 border" required placeholder="e.g. CS101">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-deep-navy mb-1">หน่วยกิต</label>
                        <input type="text" name="credit" id="input-credit" class="w-full border-cloud-blue text-deep-navy rounded-lg shadow-sm focus:ring-royal-blue focus:border-royal-blue px-3 py-2 border" placeholder="e.g. 3(2-2-5)">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-deep-navy mb-1">ชื่อวิชา (TH) <span class="text-red-500">*</span></label>
                    <input type="text" name="course_name_th" id="input-name-th" class="w-full border-cloud-blue text-deep-navy rounded-lg shadow-sm focus:ring-royal-blue focus:border-royal-blue px-3 py-2 border" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-deep-navy mb-1">ชื่อวิชา (EN) <span class="text-red-500">*</span></label>
                    <input type="text" name="course_name_en" id="input-name-en" class="w-full border-cloud-blue text-deep-navy rounded-lg shadow-sm focus:ring-royal-blue focus:border-royal-blue px-3 py-2 border" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-deep-navy mb-1">รายละเอียด (TH)</label>
                    <textarea name="course_detail_th" id="input-detail-th" rows="6" class="w-full border-cloud-blue text-deep-navy rounded-lg shadow-sm focus:ring-royal-blue focus:border-royal-blue px-3 py-2 border"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-deep-navy mb-1">รายละเอียด (EN)</label>
                    <textarea name="course_detail_en" id="input-detail-en" rows="7" class="w-full border-cloud-blue text-deep-navy rounded-lg shadow-sm focus:ring-royal-blue focus:border-royal-blue px-3 py-2 border"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-cloud-blue mt-4">
                    <button type="button" id="btn-close-modal" class="px-4 py-2 bg-cloud-blue/50 hover:bg-cloud-blue text-deep-navy rounded-lg transition-colors">ยกเลิก</button>
                    <button type="submit" class="px-4 py-2 bg-royal-blue hover:bg-deep-navy text-pure-white rounded-lg shadow transition-colors">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/80 z-50 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-pure-white rounded-2xl shadow-xl p-8 max-w-sm w-full text-center transform scale-95 transition-all border border-cloud-blue">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-200">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="mb-2 text-xl font-bold text-deep-navy">ยืนยันการลบ?</h3>
            <p class="text-slate-grey mb-3 text-sm">การกระทำนี้ไม่สามารถย้อนกลับได้ คุณแน่ใจหรือไม่ที่จะลบรายวิชานี้?</p>
            
            <div class="mb-6 p-3 bg-cloud-blue/20 rounded-xl border border-cloud-blue">
                <p id="delete-course-name" class="font-bold text-red-600 text-sm break-words">-</p>
            </div>
            
            <form id="delete-form" method="POST" class="flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <input type="hidden" name="curriculum_id" value="{{ $selectedCurriculumId ?? '' }}">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl shadow-sm transition">ใช่, ลบเลย</button>
                <button type="button" id="btn-close-delete" class="flex-1 px-4 py-2.5 bg-pure-white border border-cloud-blue hover:bg-cloud-blue/30 text-deep-navy font-medium rounded-xl transition">ยกเลิก</button>
            </form>
        </div>
    </div>

    @if(session('imported_courses'))
    <div id="import-result-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/80 z-[60] backdrop-blur-sm transition-opacity">
        <div class="bg-pure-white rounded-xl shadow-2xl p-6 w-full max-w-4xl transform transition-all scale-95 flex flex-col max-h-[90vh] border border-cloud-blue">
            
            <div class="flex justify-between items-center mb-4 border-b border-cloud-blue pb-2">
                <div class="flex items-center gap-2">
                    <div class="bg-green-100 p-2 rounded-full border border-green-200">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-deep-navy">นำเข้าข้อมูลสำเร็จ</h2>
                </div>
                <button onclick="document.getElementById('import-result-modal').remove()" class="text-slate-grey hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="mb-2 text-sm text-slate-grey">
                รายการรายวิชาทั้งหมดที่ถูกนำเข้าหรืออัพเดทล่าสุด (จำนวน: {{ count(session('imported_courses')) }} รายการ):
            </div>

            <div class="overflow-y-auto flex-1 border border-cloud-blue rounded-lg">
                <table class="min-w-full divide-y divide-cloud-blue">
                    <thead class="bg-cloud-blue/30 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-deep-navy uppercase">รหัสวิชา</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-deep-navy uppercase">ชื่อไทย</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-deep-navy uppercase">ชื่ออังกฤษ</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-deep-navy uppercase">หน่วยกิต</th>
                        </tr>
                    </thead>
                    <tbody class="bg-pure-white divide-y divide-cloud-blue/50">
                        @foreach(session('imported_courses') as $course)
                        <tr class="hover:bg-cloud-blue/10">
                            <td class="px-4 py-2 font-medium text-deep-navy">{{ $course->course_code }}</td>
                            <td class="px-4 py-2 text-slate-grey">{{ $course->course_name_th }}</td>
                            <td class="px-4 py-2 text-slate-grey">{{ $course->course_name_en }}</td>
                            <td class="px-4 py-2 text-center text-slate-grey">{{ $course->credit }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <button onclick="document.getElementById('import-result-modal').remove()" class="bg-royal-blue hover:bg-deep-navy text-pure-white px-6 py-2 rounded-lg shadow transition">
                    ตกลง
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(session('success') || session('error'))
        <div id="popup-notification" class="fixed inset-0 z-[100] flex items-center justify-center bg-deep-navy/60 backdrop-blur-sm transition-opacity">
            <div class="relative w-full max-w-sm p-6 mx-4 bg-pure-white shadow-2xl rounded-2xl border border-cloud-blue transform transition-all scale-95">
                
                <button type="button" class="absolute top-4 right-4 text-slate-grey hover:text-red-500 transition focus:outline-none" onclick="document.getElementById('popup-notification').remove()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 border {{ session('error') ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
                        @if(session('error'))
                            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        @else
                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-deep-navy mb-1">
                        {{ session('error') ? 'เกิดข้อผิดพลาด' : 'ทำรายการสำเร็จ' }}
                    </h3>
                    <p class="text-sm text-slate-grey mb-6">
                        {{ session('success') ?? session('error') }}
                    </p>

                    <button onclick="document.getElementById('popup-notification').remove()" 
                        class="w-full inline-flex justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-pure-white shadow-sm transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                        {{ session('error') ? 'bg-red-600 hover:bg-red-500 focus-visible:outline-red-600' : 'bg-royal-blue hover:bg-deep-navy focus-visible:outline-royal-blue' }}">
                        ตกลง
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>
</html>