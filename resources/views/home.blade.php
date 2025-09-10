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
                    + สร้าง CLO ใหม่
                </a>
                <a href="{{ url('/clo/all') }}" 
                class="px-6 py-3 bg-emerald-400 text-white font-bold rounded-lg shadow hover:bg-emerald-500">
                    CLO ทั้งหมดที่ดาวน์โหลด
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

                @forelse ($courses as $course)
                    @php
                        $total = 4; // มี 4 step
                        $completed = 0;
                        foreach (['startprompt','generated','downloaded','success'] as $status) {
                            if ($course->$status) $completed++;
                        }
                        $progress = ($completed / $total) * 100;

                        $status_text = match($completed) {
                            0 => 'ยังไม่เริ่ม',
                            1 => 'เริ่มแล้ว',
                            2 => 'กำลังดำเนินการ',
                            3 => 'เกือบเสร็จ',
                            4 => 'เสร็จสิ้น',
                            default => ''
                        };
                    @endphp

                    <div class="mb-6">
                        <div class="flex justify-between">
                            <span>• {{ $course->course_name }}</span>
                            <span class="text-gray-500 text-sm">- {{ $status_text }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 mt-1">
                            <div class="bg-green-500 h-3 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">คุณยังไม่มีรายวิชาในระบบ</p>
                @endforelse
            </div>
        </div>
    </body>
</html>
