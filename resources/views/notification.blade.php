<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานะการดำเนินการ | AI-Assisted CLO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html { overflow-y: scroll; }
    </style>
</head>
<body class="font-sans antialiased">
    @include('component.navbar')

    <div class="max-w-6xl mx-auto pt-28 pb-10 px-4"> 
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-deep-navy">
                    <i class="fa-solid fa-list-check text-royal-blue mr-2"></i> รายชื่อที่ยังไม่เริ่มดำเนินการ
                </h1>
                <p class="text-slate-grey text-sm mt-1">ตรวจสอบรายชื่อวิชาและอาจารย์ที่ยังไม่ได้เริ่มต้นใช้งานระบบ</p>
            </div>
            
            <a href="{{ route('email.form') }}" 
               class="bg-royal-blue hover:bg-deep-navy text-white px-6 py-2.5 rounded-full font-bold shadow-md transition duration-300 transform hover:-translate-y-0.5 flex items-center">
                <i class="fa-solid fa-paper-plane mr-2"></i> ไปหน้าส่งแจ้งเตือน
            </a>
        </div>

        <div class="bg-pure-white shadow-sm rounded-2xl overflow-hidden border border-cloud-blue">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-cloud-blue/30 text-deep-navy border-b border-cloud-blue">
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider">รหัสวิชา</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider">อาจารย์ผู้สอน</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider text-center">ปีการศึกษา</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider text-center">มคอ.</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider text-center">สถานะปัจจุบัน</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cloud-blue/50">
                    @forelse($courses as $item)
                        @php
                            $stepCount = collect([$item->status?->startprompt, $item->status?->generated, $item->status?->success])->filter()->count();
                            
                            // คงสีสถานะ (Semantic Colors) ไว้เพื่อให้สื่อความหมายได้ชัดเจนในการทำงาน
                            $statusConfig = match($stepCount) {
                                0 => ['text' => 'ยังไม่เริ่ม', 'badge_text' => 'text-gray-800', 'badge_bg' => 'bg-gray-100', 'border' => 'border-gray-200', 'dot' => 'bg-gray-500', 'pulse' => 'animate-pulse',],
                                1 => ['text' => 'เริ่มทำแล้ว', 'badge_text' => 'text-red-800', 'badge_bg' => 'bg-red-100', 'border' => 'border-red-200', 'dot' => 'bg-red-500', 'pulse' => 'animate-pulse',],
                                2 => ['text' => 'อยู่ระหว่างการสร้าง', 'badge_text' => 'text-orange-800', 'badge_bg' => 'bg-orange-100', 'border' => 'border-orange-200', 'dot' => 'bg-orange-500', 'pulse' => 'animate-pulse',],
                                3 => ['text' => 'เสร็จสมบูรณ์', 'badge_text' => 'text-green-800', 'badge_bg' => 'bg-green-100', 'border' => 'border-green-200', 'dot' => 'bg-green-500', 'pulse' => '',],
                            };
                        @endphp

                        <tr class="hover:bg-cloud-blue/10 transition duration-150">
                            <td class="px-6 py-4 font-semibold text-deep-navy">
                                {{ $item->curriculum_course->course->course_code }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-cloud-blue/40 flex items-center justify-center text-royal-blue mr-3 border border-cloud-blue">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-deep-navy">{{ $item->user->name ?? 'ไม่ระบุชื่อ' }}</div>
                                        <div class="text-xs text-slate-grey">{{ $item->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-grey text-sm font-medium">{{ $item->term }}/{{ $item->year }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-cloud-blue/40 text-royal-blue rounded-full text-xs font-bold border border-cloud-blue">{{ $item->TQF }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusConfig['badge_bg'] }} {{ $statusConfig['badge_text'] }} border {{ $statusConfig['border'] }} shadow-sm">
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
                                    <div class="bg-cloud-blue/40 p-4 rounded-full mb-4">
                                        <i class="fa-solid fa-circle-check text-5xl text-royal-blue"></i>
                                    </div>
                                    <p class="text-xl font-bold text-deep-navy">ไม่มีอาจารย์ที่ยังไม่เริ่มดำเนินการ</p>
                                    <p class="text-slate-grey mt-1">อาจารย์ทุกคนดำเนินการในระบบเรียบร้อยแล้ว</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>  
            </table>
        </div>
        
        <div class="mt-6 text-right text-xs text-slate-grey/70 italic">
            <i class="fa-solid fa-circle-info mr-1"></i> ข้อมูลอ้างอิงจากความสัมพันธ์ของตาราง courseyears และ statuses ในปัจจุบัน
        </div>
    </div>
</body>
</html>