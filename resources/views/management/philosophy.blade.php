<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Philosophy | ELO Generator</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/philosophy.js'])
    @endif

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="text-slate-grey font-sans antialiased min-h-screen">
    @include('component.navbar')
    
    <div class="container mx-auto p-4 pt-24 max-w-4xl relative">
        
        <div class="mb-8 hidden lg:flex fixed top-24 left-5 gap-3 z-40">
            <button onclick="window.location.href='/management/curriculum'" class="group inline-flex items-center gap-2 px-4 py-2 bg-pure-white border border-cloud-blue hover:border-royal-blue hover:bg-cloud-blue/10 text-deep-navy rounded-xl shadow-sm transition-all duration-300">
                <div class="p-1 bg-cloud-blue/30 rounded-md group-hover:bg-royal-blue group-hover:text-pure-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </div>
                <span class="font-medium text-sm">กลับไปหน้าจัดการหลักสูตร</span>
            </button>
        </div>

        <div class="mb-4 lg:hidden">
            <button onclick="window.location.href='/management/curriculum'" class="inline-flex items-center gap-2 text-sm font-medium text-slate-grey hover:text-royal-blue transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                กลับไปหน้าจัดการหลักสูตร
            </button>
        </div>

        <div class="mb-8 bg-pure-white p-6 rounded-2xl shadow-sm border border-cloud-blue flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="bg-cloud-blue/40 p-3 rounded-xl text-royal-blue border border-cloud-blue/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-deep-navy tracking-tight">ปรัชญาหลักสูตร</h1>
                    <p class="text-sm text-slate-grey mt-1">จัดการข้อมูลปรัชญาของหลักสูตรตามปีการศึกษา</p>
                </div>
            </div>

            <form method="GET" action="{{ url()->current() }}" class="relative w-full md:w-72 group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none transition-colors duration-300 group-hover:text-royal-blue text-slate-grey">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                <select id="year_select" name="year" onchange="this.form.submit()"
                    class="bg-pure-white border-2 border-cloud-blue text-deep-navy text-sm font-bold rounded-xl focus:outline-none focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue block w-full pl-11 pr-10 py-3 shadow-sm hover:border-royal-blue/50 transition-all duration-300 cursor-pointer appearance-none">
                    <option value="" hidden>-- เลือกปีหลักสูตร --</option>
                    @foreach($curriculum as $cur)
                        <option value="{{ $cur->curriculum_year }}" {{ (isset($selectedYear) && $selectedYear == $cur->curriculum_year) ? 'selected' : '' }}>
                            หลักสูตรปี {{ $cur->curriculum_year }}
                        </option>
                    @endforeach
                </select>

                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-grey group-hover:text-royal-blue transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </form>
        </div>

        @if(isset($selectedYear) && $selectedYear)
            <div class="bg-pure-white rounded-3xl shadow-sm border border-cloud-blue overflow-hidden relative">
                <div class="h-2 w-full bg-royal-blue"></div>
                
                <form id="form-philosophy" class="p-7" method="POST" action="{{ route('philosophy.update') }}">
                    <input type="hidden" id="current_year" name="year" value="{{ $selectedYear }}">
                    
                    <div class="space-y-7">
                        <div class="relative group">
                            <label class="flex items-center gap-2 text-sm font-bold text-deep-navy mb-2 ml-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-cloud-blue/50 text-royal-blue text-xs border border-cloud-blue">1</span>
                                ปรัชญามหาวิทยาลัยแม่โจ้
                            </label>
                            <textarea id="mju_philosophy" name="mju_philosophy" rows="3" placeholder="ระบุปรัชญามหาวิทยาลัย..." class="w-full border border-cloud-blue rounded-2xl p-4 text-deep-navy bg-cloud-blue/10 focus:bg-pure-white focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue outline-none transition-all duration-300 resize-y shadow-inner">{{ $philosophyData->mju_philosophy ?? '' }}</textarea>
                        </div>
                        
                        <div class="relative group">
                            <label class="flex items-center gap-2 text-sm font-bold text-deep-navy mb-2 ml-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-cloud-blue/50 text-royal-blue text-xs border border-cloud-blue">2</span>
                                ปรัชญาการศึกษา มหาวิทยาลัยแม่โจ้
                            </label>
                            <textarea id="education_philosophy" name="education_philosophy" rows="3" placeholder="ระบุปรัชญาการศึกษา..." class="w-full border border-cloud-blue rounded-2xl p-4 text-deep-navy bg-cloud-blue/10 focus:bg-pure-white focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue outline-none transition-all duration-300 resize-y shadow-inner">{{ $philosophyData->education_philosophy ?? '' }}</textarea>
                        </div>

                        <div class="relative group">
                            <label class="flex items-center gap-2 text-sm font-bold text-deep-navy mb-2 ml-1">
                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-cloud-blue/50 text-royal-blue text-xs border border-cloud-blue">3</span>
                                ปรัชญาหลักสูตร
                            </label>
                            <textarea id="curriculum_philosophy" name="curriculum_philosophy" rows="4" placeholder="ระบุปรัชญาหลักสูตร..." class="w-full border border-cloud-blue rounded-2xl p-4 text-deep-navy bg-cloud-blue/10 focus:bg-pure-white focus:ring-4 focus:ring-royal-blue/20 focus:border-royal-blue outline-none transition-all duration-300 resize-y shadow-inner">{{ $philosophyData->curriculum_philosophy ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-cloud-blue flex justify-end">
                        <button type="submit" id="btn-save" 
                            class="bg-royal-blue hover:bg-deep-navy text-pure-white px-8 py-3 rounded-xl shadow-lg shadow-royal-blue/20 font-semibold flex items-center gap-2 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-royal-blue/30 border-0 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>บันทึกข้อมูล</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-pure-white border-2 border-cloud-blue border-dashed rounded-3xl p-12 text-center flex flex-col items-center justify-center min-h-[400px]">
                <div class="bg-cloud-blue/30 p-6 rounded-full mb-6 border border-cloud-blue">
                    <svg class="w-16 h-16 text-royal-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-deep-navy mb-2">ยังไม่ได้เลือกหลักสูตร</h3>
                <p class="text-slate-grey max-w-md mx-auto">กรุณาเลือกปีการศึกษาจากเมนูด้านบนขวา เพื่อดึงข้อมูลปรัชญาของหลักสูตรนั้นขึ้นมาจัดการ</p>
            </div>
        @endif
    </div>

    <div id="popup-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-deep-navy/70 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="relative w-full max-w-sm p-6 mx-4 bg-pure-white shadow-2xl rounded-2xl transform transition-all duration-300 scale-95 opacity-0 border border-cloud-blue" id="popup-content">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50 mb-5 border border-green-200">
                <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <div class="text-center">
                <h3 class="text-xl font-extrabold text-deep-navy mb-2">บันทึกสำเร็จ!</h3>
                <p class="text-slate-grey text-sm mb-6">ข้อมูลปรัชญาและวัตถุประสงค์ถูกอัปเดตลงระบบเรียบร้อยแล้ว</p>
                <button id="popup-close" class="w-full inline-flex justify-center rounded-xl bg-royal-blue hover:bg-deep-navy px-4 py-3 text-sm font-semibold text-pure-white shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-royal-blue/30">
                    ตกลง
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>