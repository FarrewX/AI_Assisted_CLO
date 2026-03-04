<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AI-Assisted CLO</title>

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
                <h2 class="text-xl text-deep-navy/80 mt-1">อาจารย์ {{ Auth::user()->name ?? 'ผู้ใช้งาน' }}</h2>
            </div>

            <div class="w-full max-w-3xl bg-pure-white rounded-xl shadow-sm border border-cloud-blue p-6">
                
                <div class="flex items-center justify-between mb-6 border-b border-cloud-blue pb-4">
                    <h3 class="text-lg font-bold text-deep-navy">รายวิชาที่เปิดสอน</h3>
                    
                    <form method="GET" action="{{ route('home') }}" class="flex items-center space-x-2">
                        <label for="year" class="text-sm text-slate-grey">ปีการศึกษา:</label>
                        <select name="year" id="year" onchange="this.form.submit()" 
                            class="border-cloud-blue text-slate-grey rounded-md shadow-sm focus:border-royal-blue focus:ring focus:ring-royal-blue/30 py-1 pl-3 pr-8 text-sm bg-pure-white">
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
                        $termsGroup = $courseGroup->groupBy('term');
                    @endphp

                    <div class="mb-6 last:mb-0 border border-cloud-blue rounded-lg overflow-hidden">
                        
                        <div class="bg-cloud-blue/30 px-4 py-3 border-b border-cloud-blue flex items-center">
                            <span class="bg-royal-blue/10 text-royal-blue text-xs font-semibold mr-2 px-2.5 py-0.5 rounded border border-royal-blue/20">
                                {{ $courseCode }}
                            </span>
                            <span class="font-semibold text-deep-navy">{{ $courseName }}</span>
                        </div>

                        <div class="p-4 bg-pure-white">
                            @foreach($termsGroup as $term => $TQFItems)
                                <div class="mb-4 last:mb-0">
                                    <div class="text-sm font-bold text-slate-grey mb-2">ภาคเรียนที่ {{ $term }}</div>
                                    <div class="space-y-3">
                                        @foreach($TQFItems as $item)
                                            @php
                                                $stepCount = collect([$item->startprompt, $item->generated, $item->success])->filter()->count();
                                                
                                                $statusConfig = match($stepCount) {
                                                    0 => ['text' => 'ยังไม่เริ่ม', 'color' => 'text-gray-500','btn' => 'เริ่มต้น', 'url' => '/form'],
                                                    1 => ['text' => 'เริ่มทำแล้ว', 'color' => 'text-red-500', 'btn' => 'สร้างต่อ', 'url' => '/form'],
                                                    2 => ['text' => 'อยู่ระหว่างการสร้าง', 'color' => 'text-orange-500', 'btn' => 'แก้ไข', 'url' => '/editdoc'],
                                                    3 => ['text' => 'เสร็จสมบูรณ์', 'color' => 'text-green-600', 'btn' => 'ตรวจสอบ', 'url' => '/editdoc'],
                                                    default => ['text' => '-', 'color' => 'text-gray-400', 'btn' => '', 'url' => '#']
                                                };

                                                $displayYear = ($item->year < 2400) ? $item->year + 543 : $item->year;

                                                $url = url($statusConfig['url']) . '?' . http_build_query([
                                                    'CC_id' => $item->CC_id,
                                                    'year' => $displayYear,
                                                    'term' => $item->term,
                                                    'TQF' => $item->TQF
                                                ]);
                                            @endphp

                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-pure-white p-3 rounded-md border border-cloud-blue hover:shadow-md transition">
                                                <div class="flex-1 mb-2 sm:mb-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-deep-navy">มคอ.{{ $item->TQF }}</span>
                                                        <span class="text-xs {{ $statusConfig['color'] }} border border-cloud-blue px-2 py-0.5 rounded-full bg-pure-white">
                                                            {{ $statusConfig['text'] }}
                                                        </span>
                                                    </div>                                      
                                                </div>
                                                <div>
                                                    <a href="{{ $url }}" class="inline-block text-sm bg-royal-blue/100 text-white font-medium hover:bg-deep-navy px-4 py-2 rounded-md transition shadow-sm">
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
                    <div class="text-center py-10">
                        <div class="text-cloud-blue mb-2">
                            <svg class="w-12 h-12 mx-auto text-slate-grey/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <p class="text-slate-grey text-lg">
                            ไม่พบรายวิชาในปีการศึกษา {{ request('year', date('Y') + 543) }}
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </body>
</html>