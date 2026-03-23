<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <title>{{ config('app.name', 'AI-Assisted CLO') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            @endif
    </head>
    <body class="font-sans antialiased">
        @include('component.navbar')

        <div class="flex flex-col items-center min-h-screen pt-28 pb-10 px-4">
            
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-deep-navy">ยินดีต้อนรับ</h1>
                <h2 class="text-xl text-sky-surge mt-1">
                    @if(Auth::user()->role_id == 1)
                        ผู้ดูแลระบบ: {{ Auth::user()->name ?? 'ผู้ใช้งาน' }}
                    @else
                        อาจารย์: {{ Auth::user()->name ?? 'ผู้ใช้งาน' }}
                    @endif
                </h2>
            </div>

            <div class="w-full max-w-3xl bg-pure-white rounded-2xl shadow-lg border border-pale-sky p-6 md:p-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 border-b border-pale-sky pb-5 gap-4">
                    <h3 class="text-xl font-bold text-deep-navy flex items-center gap-2">
                        <i class="fa-solid fa-book-open text-sky-surge"></i> รายวิชาที่เปิดสอน
                    </h3>
                    
                    <form method="GET" action="{{ route('home') }}" class="flex items-center space-x-2 w-full sm:w-auto">
                        <label for="year" class="text-sm font-bold text-deep-navy whitespace-nowrap">ปีการศึกษา:</label>
                        <select name="year" id="year" onchange="this.form.submit()" 
                            class="w-full sm:w-auto border-pale-sky rounded-xl shadow-sm focus:ring-4 focus:ring-baltic-blue/20 focus:border-baltic-blue py-1.5 pl-3 pr-8 text-sm bg-pure-white text-deep-navy font-medium outline-none transition-all">
                            @php
                                $currentYearBE = date('Y') + 543;
                                $displayYear = $selectedYear ?? $currentYearBE; 
                            @endphp
                            @foreach(range($currentYearBE + 1, $currentYearBE - 5) as $y)
                                <option value="{{ $y }}" {{ $displayYear == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @forelse ($courses->groupBy('CC_id') as $ccId => $courseGroup)
                    @php
                        $courseRow = $courseGroup->first();
                        $courseName = $courseRow->course_name_th 
                                      ?? $courseRow->course_name_en 
                                      ?? 'รหัสวิชา: ' . ($courseRow->course_code ?? '-');
                        $courseCode = $courseRow->course_code ?? '';
                        $curriculumYear = $courseRow->curriculum_year ?? '-';
                        $termsGroup = $courseGroup->groupBy('term');
                    @endphp

                    <div class="mb-6 last:mb-0 border border-pale-sky rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-pale-sky/40 px-5 py-3.5 border-b border-pale-sky flex items-center flex-wrap gap-2">
                            <span class="bg-pure-white text-baltic-blue text-xs font-bold px-3 py-1 rounded-md border border-sky-surge/50 shadow-sm">
                                {{ $courseCode }}
                            </span>
                            <span class="font-bold text-deep-navy">{{ $courseName }}</span>
                            
                            <span class="text-xs text-baltic-blue font-semibold bg-pure-white/80 px-2.5 py-1 rounded-lg border border-sky-surge/30 ml-auto sm:ml-2">
                                <i class="fa-solid fa-graduation-cap mr-1"></i> หลักสูตร {{ $curriculumYear }}
                            </span>
                        </div>
                        <div class="p-5 bg-pure-white">
                            @foreach($termsGroup as $term => $TQFItems)
                                <div class="mb-5 last:mb-0">
                                    <div class="text-sm font-bold text-slate-grey mb-3 flex items-center gap-2">
                                        <i class="fa-regular fa-calendar text-sky-surge"></i> ภาคเรียนที่ {{ $term }}
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($TQFItems as $item)
                                            @php
                                                $stepCount = collect([$item->startprompt, $item->generated, $item->success])->filter()->count();
                                                
                                                $statusConfig = match($stepCount) {
                                                    0 => ['text' => 'ยังไม่เริ่ม', 'color' => 'text-slate-grey', 'btn' => 'เริ่มต้น', 'url' => '/form'],
                                                    1 => ['text' => 'เริ่มทำแล้ว', 'color' => 'text-red-600', 'btn' => 'สร้างต่อ', 'url' => '/form'],
                                                    2 => ['text' => 'อยู่ระหว่างการสร้าง', 'color' => 'text-amber-600', 'btn' => 'แก้ไข', 'url' => '/editdoc'],
                                                    3 => ['text' => 'เสร็จสมบูรณ์', 'color' => 'text-green-600', 'btn' => 'ตรวจสอบ', 'url' => '/editdoc'],
                                                    default => ['text' => '-', 'color' => 'text-slate-grey', 'btn' => '', 'url' => '#']
                                                };

                                                $displayYear = ($item->year < 2400) ? $item->year + 543 : $item->year;

                                                $url = url($statusConfig['url']) . '?' . http_build_query([
                                                    'CC_id' => $item->CC_id,
                                                    'year' => $displayYear,
                                                    'term' => $item->term,
                                                    'TQF' => $item->TQF
                                                ]);
                                            @endphp

                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-pale-sky/10 p-4 rounded-xl border border-pale-sky hover:border-sky-surge/50 hover:shadow-md transition-all duration-200">
                                                <div class="flex-1 mb-3 sm:mb-0">
                                                    <div class="flex items-center flex-wrap gap-2">
                                                        <span class="font-bold text-deep-navy text-lg">มคอ.{{ $item->TQF }}</span>
                                                        <span class="text-xs font-bold {{ $statusConfig['color'] }} border border-pale-sky px-2.5 py-1 rounded-full bg-pure-white shadow-sm">
                                                            {{ $statusConfig['text'] }}
                                                        </span>
                                                    </div>
                                                    
                                                    @if(Auth::user()->role_id == 1)
                                                    <div class="text-sm text-slate-grey mt-2 flex items-center gap-1.5">
                                                        <i class="fa-solid fa-user-tie text-sky-surge"></i>
                                                        <span class="font-medium">ผู้สอน: {{ $item->instructor_name ?? 'ไม่ระบุชื่อ' }}</span>
                                                    </div>
                                                    @endif

                                                </div>
                                                <div>
                                                    <a href="{{ $url }}" class="inline-flex items-center justify-center text-sm font-bold bg-[#035AA6] hover:bg-[#6CBAD9] border border-cloud-blue text-white hover:text-pure-white hover:border-sky-surge px-5 py-2.5 rounded-lg transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-baltic-blue/30 w-full sm:w-auto">
                                                        {{ $statusConfig['btn'] }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="text-pale-sky mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <p class="text-slate-grey text-lg font-medium">
                            ไม่พบรายวิชาในปีการศึกษา <span class="text-deep-navy font-bold">{{ request('year', date('Y') + 543) }}</span>
                        </p>
                        <p class="text-sm text-slate-grey/70 mt-2">กรุณาเลือกปีการศึกษาอื่น หรือติดต่อผู้ดูแลระบบ</p>
                    </div>
                @endforelse
            </div>
        </div>
    </body>
</html>