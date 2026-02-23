<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานะการดำเนินการ | ELO Generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ล็อค Scrollbar ให้แสดงตลอดเวลาเพื่อป้องกันหน้าขยับ (Layout Shift) */
        html { overflow-y: scroll; }
    </style>
</head>
<body class="bg-gray-50 font-sans"> 
    @include('component.navbar')

    <div class="max-w-6xl mx-auto pt-28 pb-10 px-4"> 
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fa-solid fa-list-check text-blue-500 mr-2"></i> รายชื่อที่ยังไม่เริ่มดำเนินการ
                </h1>
                <p class="text-gray-500 text-sm mt-1">ตรวจสอบรายชื่อวิชาและอาจารย์ที่ยังไม่ได้เริ่มต้นใช้งานระบบ</p>
            </div>
            
            <a href="{{ route('email.form') }}" 
               class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-full font-bold shadow-lg transition duration-200 transform hover:scale-105 flex items-center">
                <i class="fa-solid fa-paper-plane mr-2"></i> ไปหน้าส่งแจ้งเตือน
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-900 text-white">
                        <th class="px-6 py-4 font-semibold uppercase text-xs tracking-wider">รหัสวิชา</th>
                        <th class="px-6 py-4 font-semibold uppercase text-xs tracking-wider">อาจารย์ผู้สอน</th>
                        <th class="px-6 py-4 font-semibold uppercase text-xs tracking-wider text-center">ปีการศึกษา</th>
                        <th class="px-6 py-4 font-semibold uppercase text-xs tracking-wider text-center">มคอ.</th>
                        <th class="px-6 py-4 font-semibold uppercase text-xs tracking-wider text-center">สถานะปัจจุบัน</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $item)
                        @php
                            $stepCount = collect([$item->status?->startprompt, $item->status?->generated, $item->status?->success])->filter()->count();
                            
                            $statusConfig = match($stepCount) {
                                0 => ['text' => 'ยังไม่เริ่ม', 'badge_text' => 'text-gray-800', 'badge_bg' => 'bg-gray-200', 'border' => 'border-gray-200', 'dot' => 'bg-gray-500', 'pulse' => 'animate-pulse',],
                                1 => ['text' => 'เริ่มทำแล้ว', 'badge_text' => 'text-red-800', 'badge_bg' => 'bg-red-200', 'border' => 'border-red-200', 'dot' => 'bg-red-500', 'pulse' => 'animate-pulse',],
                                2 => ['text' => 'อยู่ระหว่างการสร้าง', 'badge_text' => 'text-orange-800', 'badge_bg' => 'bg-orange-200', 'border' => 'border-orange-200', 'dot' => 'bg-orange-500', 'pulse' => 'animate-pulse',],
                                3 => ['text' => 'เสร็จสมบูรณ์', 'badge_text' => 'text-green-800', 'badge_bg' => 'bg-green-200', 'border' => 'border-green-200', 'dot' => 'bg-green-500', 'pulse' => 'animate-pulse',],
                            };
                        @endphp

                        <tr class="hover:bg-blue-50 transition duration-150">
                            <td class="px-6 py-4 font-medium text-gray-700">
                                {{ $item->curriculum_course->course->course_code }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 mr-3 border border-blue-200">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $item->user->name ?? 'ไม่ระบุชื่อ' }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600 text-sm font-medium">{{ $item->term }}/{{ $item->year }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold border border-blue-200">{{ $item->TQF }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusConfig['badge_bg'] }} {{ $statusConfig['badge_text'] }} {{ $statusConfig['border'] }} shadow-sm">
                                        <span class="h-2 w-2 mr-2 rounded-full {{ $statusConfig['dot'] }} {{ $statusConfig['pulse'] }}"></span>
                                        {{ $statusConfig['text'] }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-green-100 p-4 rounded-full mb-4">
                                        <i class="fa-solid fa-circle-check text-5xl text-green-500"></i>
                                    </div>
                                    <p class="text-xl font-bold text-gray-800">ไม่มีอาจารย์ที่ยังไม่เริ่มดำเนินการ</p>
                                    <p class="text-gray-500 mt-1">อาจารย์ทุกคนดำเนินการในระบบเรียบร้อยแล้ว</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>  
            </table>
        </div>
        
        <div class="mt-6 text-right text-xs text-gray-400 italic">
            <i class="fa-solid fa-circle-info mr-1"></i> ข้อมูลอ้างอิงจากความสัมพันธ์ของตาราง courseyears และ statuses ในปัจจุบัน
        </div>
    </div>
</body>
</html>