<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AI-Assisted CLO') }} | สถานะการดำเนินการ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ล็อค Scrollbar ให้แสดงตลอดเวลาเพื่อป้องกันหน้าขยับ (Layout Shift) */
        html { overflow-y: scroll; }
    </style>
</head>
<body class="font-sans antialiased text-slate-grey">
    @include('component.navbar')

    <div class="max-w-6xl mx-auto pt-28 pb-10 px-4"> 
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-deep-navy">
                    <i class="fa-solid fa-list-check text-baltic-blue mr-2"></i> รายชื่อที่ยังไม่เริ่มดำเนินการ
                </h1>
                <p class="text-slate-grey text-sm mt-1">ตรวจสอบรายชื่อวิชาและอาจารย์ที่ยังไม่ได้เริ่มต้นใช้งานระบบ</p>
            </div>
            
            <a href="{{ route('email.form') }}" 
               class="bg-baltic-blue hover:bg-sky-surge text-white px-6 py-2.5 rounded-full font-bold shadow-lg transition duration-200 transform hover:scale-105 flex items-center focus:ring-4 focus:ring-baltic-blue/30 outline-none">
                <i class="fa-solid fa-paper-plane mr-2"></i> ไปหน้าส่งแจ้งเตือน
            </a>
        </div>

        <div class="bg-pure-white shadow-xl rounded-2xl overflow-hidden border border-pale-sky">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-pale-sky/40 text-deep-navy border-b border-pale-sky">
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider">รหัสวิชา</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider">อาจารย์ผู้สอน</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider text-center">ปีการศึกษา</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider text-center">มคอ.</th>
                        <th class="px-6 py-4 font-bold uppercase text-xs tracking-wider text-center">สถานะปัจจุบัน</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pale-sky/50">
                    @forelse($courses as $item)
                        @php
                            $stepCount = collect([$item->status?->startprompt, $item->status?->generated, $item->status?->success])->filter()->count();
                            
                            // ยังคงเก็บสีแจ้งเตือนมาตรฐาน (แดง/ส้ม/เขียว) ไว้ เพื่อให้ผู้ใช้แยกสถานะได้เร็วที่สุด
                            $statusConfig = match($stepCount) {
                                0 => ['text' => 'ยังไม่เริ่ม', 'badge_text' => 'text-slate-grey', 'badge_bg' => 'bg-pale-sky/30', 'border' => 'border-pale-sky', 'dot' => 'bg-slate-grey', 'pulse' => 'animate-pulse'],
                                1 => ['text' => 'เริ่มทำแล้ว', 'badge_text' => 'text-red-700', 'badge_bg' => 'bg-red-50', 'border' => 'border-red-200', 'dot' => 'bg-red-500', 'pulse' => 'animate-pulse'],
                                2 => ['text' => 'อยู่ระหว่างการสร้าง', 'badge_text' => 'text-amber-700', 'badge_bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500', 'pulse' => 'animate-pulse'],
                                3 => ['text' => 'เสร็จสมบูรณ์', 'badge_text' => 'text-green-700', 'badge_bg' => 'bg-green-50', 'border' => 'border-green-200', 'dot' => 'bg-green-500', 'pulse' => 'animate-pulse'],
                            };
                        @endphp

                        <tr class="hover:bg-pale-sky/20 transition duration-150">
                            <td class="px-6 py-4 font-bold text-baltic-blue">
                                {{ $item->curriculum_course->course->course_code }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-pale-sky/40 flex items-center justify-center text-baltic-blue mr-3 border border-pale-sky">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-deep-navy">{{ $item->user->name ?? 'ไม่ระบุชื่อ' }}</div>
                                        <div class="text-xs text-slate-grey">{{ $item->user->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center text-slate-grey text-sm font-medium">
                                {{ $item->term }}/{{ $item->year }}
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-sky-surge/20 text-baltic-blue rounded-full text-xs font-bold border border-sky-surge/30">
                                    {{ $item->TQF }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold {{ $statusConfig['badge_bg'] }} {{ $statusConfig['badge_text'] }} border {{ $statusConfig['border'] }} shadow-sm">
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
                                    <div class="bg-pale-sky/40 p-5 rounded-full mb-4 border border-pale-sky">
                                        <i class="fa-solid fa-circle-check text-5xl text-baltic-blue"></i>
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
        
        <div class="mt-6 text-right text-xs text-slate-grey/80 italic">
            <i class="fa-solid fa-circle-info mr-1 text-sky-surge"> ข้อมูลจะแสดงเฉพาะอาจารย์ที่มีสถานะ "ยังไม่เสร็จ" เท่านั้น</i>
        </div>
    </div>
</body>
</html>