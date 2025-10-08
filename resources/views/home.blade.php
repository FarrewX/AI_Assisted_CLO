<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ELO_Generator</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
    </head>
    <body>
        @include('component.navbar')
        <div class="bg-white flex flex-col items-center" style="margin-top: 100px;">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-xl font-bold">ยินดีต้อนรับ</h1>
                <h2 class="text-lg font-bold">อาจารย์ {{ Auth::user()->name ?? 'สมมติ' }}</h2>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-6 mb-10">
                <a href="{{ url('/form') }}" 
                class="px-6 py-3 bg-sky-300 text-black font-bold rounded-lg shadow hover:bg-sky-400">
                    + สร้าง TQF ใหม่
                </a>
                <a href="{{ url('/TQF/all') }}" 
                class="px-6 py-3 bg-emerald-400 text-white font-bold rounded-lg shadow hover:bg-emerald-500">
                    TQF ทั้งหมดที่ดาวน์โหลด
                </a>
            </div>

            <!-- รายวิชา -->
            <div class="w-full max-w-2xl px-4">
                <h3 class="text-md mb-4">รายวิชาที่เปิดสอน ปี
                    <form method="GET" action="{{ route('home') }}" class="inline">
                        <select name="year" id="year" onchange="this.form.submit()" class="">
                            @foreach(range(date('Y') + 1, date('Y') - 5) as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </h3>

                @forelse ($courses->groupBy('course_id') as $courseId => $courseGroup)
                    @php
                        $courseRow = $courseGroup->first();
                        $termsGroup = $courseGroup->groupBy('term');
                    @endphp

                    <div class="mb-6 border p-3 rounded-lg bg-gray-50">
                        <div class="font-semibold mb-2">• {{ $courseRow->course_name }}</div>

                        @foreach($termsGroup as $term => $TQFItems)
                            <div class="ml-4 mb-2">
                                <div class="font-medium text-gray-700">ภาคเรียน: {{ $term }}</div>
                                <ul class="ml-4 list-disc">
                                    @foreach($TQFItems as $item)
                                        <li class="mb-3">
                                            @php
                                                $stepCount = collect([$item->startprompt, $item->generated, $item->downloaded, $item->success])->filter()->count();
                                                $progress = $stepCount / 4 * 100;
                                                $status_text = match($stepCount) {
                                                    0 => 'ยังไม่เริ่ม',
                                                    1 => 'prompt แล้ว',
                                                    2 => 'generated แล้ว',
                                                    3 => 'ดาวน์โหลดแล้ว',
                                                    4 => 'เสร็จสมบูรณ์',
                                                    default => ''
                                                };
                                                
                                                // ลิงก์แต่ละสถานะ
                                                $link = match($stepCount) {
                                                    0 => url('/form?course_id=' . $item->course_id . '&year=' . $item->year . '&term=' . $item->term . '&TQF=' . $item->TQF),
                                                    1 => url('/form?course_id=' . $item->course_id . '&year=' . $item->year . '&term=' . $item->term . '&TQF=' . $item->TQF),
                                                    2 => url('/preview?course_id=' . $item->course_id . '&year=' . $item->year . '&term=' . $item->term . '&TQF=' . $item->TQF),
                                                    3 => url(),
                                                    4 => null,
                                                    default => null
                                                };

                                                $btnLabel = match($stepCount) {
                                                    0 => 'เริ่มต้น',
                                                    1 => 'สร้างต่อ',
                                                    2 => 'แก้ไข',
                                                    3 => 'ตรวจสอบ',
                                                    4 => 'preview',
                                                    default => ''
                                                };
                                            @endphp

                                            <div class="flex items-center justify-between right-0">
                                                <span class="text-gray-1000 text-s">TQF: {{ $item->TQF }} 
                                                    <span class="text-gray-500 text-sm">- {{ $status_text }}</span>
                                                </span>
                                                @if ($link)
                                                    <a href="{{ $link }}"
                                                    class="text-sm bg-blue-400 hover:bg-blue-500 text-white px-3 py-1 rounded transition">
                                                        {{ $btnLabel }}
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="bg-gray-100 rounded-full h-2 mt-1">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul> <br>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-gray-500">คุณยังไม่มีรายวิชาในระบบ</p>
                @endforelse

            </div>
        </div>
    </body>
</html>
