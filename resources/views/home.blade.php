<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ELO_Generator</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
    </head>
    <body class="bg-gray-50">
        @include('component.navbar')

        <div class="flex flex-col items-center min-h-screen pt-28 pb-10 px-4">
            
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">ยินดีต้อนรับ</h1>
                <h2 class="text-xl text-gray-600 mt-1">อาจารย์ {{ Auth::user()->name ?? 'ผู้ใช้งาน' }}</h2>
            </div>

            <div class="flex flex-wrap justify-center gap-4 mb-10 w-full max-w-2xl">
                <a href="{{ url('/form') }}" 
                   class="flex-1 min-w-[200px] text-center px-6 py-4 bg-sky-500 text-white font-bold rounded-xl shadow-md hover:bg-sky-600 transition transform hover:-translate-y-1">
                    + สร้าง TQF ใหม่
                </a>
                <a href="{{ url('/TQF/all') }}" 
                   class="flex-1 min-w-[200px] text-center px-6 py-4 bg-emerald-500 text-white font-bold rounded-xl shadow-md hover:bg-emerald-600 transition transform hover:-translate-y-1">
                    📂 TQF ทั้งหมดที่ดาวน์โหลด
                </a>
            </div>

            <div class="w-full max-w-3xl bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6 border-b pb-4">
                    <h3 class="text-lg font-bold text-gray-800">รายวิชาที่เปิดสอน</h3>
                    
                    <form method="GET" action="{{ route('home') }}" class="flex items-center space-x-2">
                        <label for="year" class="text-sm text-gray-600">ปีการศึกษา:</label>
                        <select name="year" id="year" onchange="this.form.submit()" 
                                class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-1 pl-3 pr-8 text-sm bg-gray-50">
                            @foreach(range(date('Y') + 1, date('Y') - 5) as $y)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @forelse ($courses->groupBy('CC_id') as $ccId => $courseGroup)
                    @php
                        $courseRow = $courseGroup->first();
                        // ดึงชื่อวิชาจาก Relation (ป้องกัน Error ถ้าไม่มีข้อมูล)
                        $courseName = $courseRow->curriculum_course->course->course_name_th 
                                      ?? $courseRow->curriculum_course->course->course_name_en 
                                      ?? 'รหัสวิชา: ' . ($courseRow->curriculum_course->course_id ?? '-');
                        
                        $courseCode = $courseRow->curriculum_course->course->course_code ?? '';
                        $termsGroup = $courseGroup->groupBy('term');
                    @endphp

                    <div class="mb-6 last:mb-0 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 flex items-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                {{ $courseCode }}
                            </span>
                            <span class="font-semibold text-gray-700">{{ $courseName }}</span>
                        </div>

                        <div class="p-4 bg-white">
                            @foreach($termsGroup as $term => $TQFItems)
                                <div class="mb-4 last:mb-0">
                                    <div class="text-sm font-bold text-gray-500 mb-2">ภาคเรียนที่ {{ $term }}</div>
                                    <div class="space-y-3">
                                        @foreach($TQFItems as $item)
                                            @php
                                                // คำนวณสถานะ
                                                $stepCount = collect([$item->startprompt, $item->generated, $item->success])->filter()->count();
                                                $progress = ($stepCount / 3) * 100;
                                                
                                                $statusConfig = match($stepCount) {
                                                    0 => ['text' => 'ยังไม่เริ่ม', 'color' => 'text-gray-500', 'bg' => 'bg-gray-200', 'btn' => 'เริ่มต้น', 'url' => '/form'],
                                                    1 => ['text' => 'Prompt แล้ว', 'color' => 'text-orange-500', 'bg' => 'bg-orange-500', 'btn' => 'สร้างต่อ', 'url' => '/form'],
                                                    2 => ['text' => 'Generated แล้ว', 'color' => 'text-blue-500', 'bg' => 'bg-blue-500', 'btn' => 'แก้ไข', 'url' => '/editdoc'],
                                                    3 => ['text' => 'เสร็จสมบูรณ์', 'color' => 'text-green-600', 'bg' => 'bg-green-500', 'btn' => 'ตรวจสอบ', 'url' => '/editdoc'], // หรือหน้า view
                                                    default => ['text' => '-', 'color' => 'text-gray-400', 'bg' => 'bg-gray-200', 'btn' => '', 'url' => '#']
                                                };

                                                // สร้าง URL พร้อม Query String
                                                $url = url($statusConfig['url']) . '?' . http_build_query([
                                                    'course_id' => $item->curriculum_course->course_id ?? '', // แก้ให้ดึง course_id ที่ถูกต้อง
                                                    'year' => $item->year,
                                                    'term' => $item->term,
                                                    'TQF' => $item->TQF
                                                ]);
                                            @endphp

                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-gray-50 p-3 rounded-md border border-gray-100 hover:shadow-sm transition">
                                                <div class="flex-1 mb-2 sm:mb-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-gray-800">TQF {{ $item->TQF }}</span>
                                                        <span class="text-xs {{ $statusConfig['color'] }} border px-2 py-0.5 rounded-full bg-white">
                                                            {{ $statusConfig['text'] }}
                                                        </span>
                                                    </div>
                                                    <div class="w-full sm:w-48 bg-gray-200 rounded-full h-1.5 mt-2">
                                                        <div class="{{ $statusConfig['bg'] }} h-1.5 rounded-full transition-all duration-500" 
                                                             style="width: {{ $progress }}%"></div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <a href="{{ $url }}" 
                                                       class="inline-block text-sm bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 hover:border-blue-300 px-4 py-2 rounded-md transition shadow-sm">
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
                        <div class="text-gray-400 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <p class="text-gray-500 text-lg">ไม่พบรายวิชาในปีการศึกษา {{ request('year', date('Y')) }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </body>
</html>