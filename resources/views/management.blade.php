<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'AI-Assisted CLO') }} | ศูนย์จัดการระบบ</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        @endif
</head>
<body class="font-sans text-slate-grey antialiased">
    @include('component.navbar')

    <div class="min-h-screen pt-28 pb-12 px-4 sm:px-6 lg:px-8 flex flex-col items-center">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-deep-navy tracking-tight">
                ศูนย์จัดการระบบ
            </h1>
            <p class="mt-2 text-slate-grey/80 text-lg font-medium">
                เลือกเมนูที่ต้องการจัดการ
            </p>
        </div>

        <div class="w-full max-w-6xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">

            <a href="{{ url('/management/curriculum') }}" 
            class="group bg-pure-white p-6 rounded-2xl shadow-sm border border-cloud-blue hover:shadow-lg hover:border-baltic-blue/50 transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center gap-4 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-sky-surge/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="p-4 bg-cloud-blue/40 text-baltic-blue rounded-full group-hover:bg-baltic-blue group-hover:text-pure-white transition-colors duration-300 relative z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-deep-navy group-hover:text-baltic-blue transition-colors">หลักสูตร</h3>
                    <p class="text-sm text-slate-grey mt-1">Curriculum Management</p>
                </div>
            </a>

            <a href="{{ url('/management/addcourses') }}"
            class="group bg-pure-white p-6 rounded-2xl shadow-sm border border-cloud-blue hover:shadow-lg hover:border-baltic-blue/50 transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center gap-4 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-sky-surge/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="p-4 bg-cloud-blue/40 text-baltic-blue rounded-full group-hover:bg-baltic-blue group-hover:text-pure-white transition-colors duration-300 relative z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-deep-navy group-hover:text-baltic-blue transition-colors">จัดการรายวิชา</h3>
                    <p class="text-sm text-slate-grey mt-1">แก้ไข/เพิ่มวิชาลงหลักสูตร</p>
                </div>
            </a>

            <a href="{{ url('/management/courses') }}" 
            class="group bg-pure-white p-6 rounded-2xl shadow-sm border border-cloud-blue hover:shadow-lg hover:border-baltic-blue/50 transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center gap-4 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-sky-surge/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="p-4 bg-cloud-blue/40 text-baltic-blue rounded-full group-hover:bg-baltic-blue group-hover:text-pure-white transition-colors duration-300 relative z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-deep-navy group-hover:text-baltic-blue transition-colors">อาจารย์ผู้สอน</h3>
                    <p class="text-sm text-slate-grey mt-1">จัดการอาจารย์ในรายวิชา</p>
                </div>
            </a>

            <a href="{{ url('/management/email') }}"
            class="group bg-pure-white p-6 rounded-2xl shadow-sm border border-cloud-blue hover:shadow-lg hover:border-baltic-blue/50 transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center gap-4 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-b from-sky-surge/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="p-4 bg-cloud-blue/40 text-baltic-blue rounded-full group-hover:bg-baltic-blue group-hover:text-pure-white transition-colors duration-300 relative z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-deep-navy group-hover:text-baltic-blue transition-colors">ตั้งค่าอีเมล</h3>
                    <p class="text-sm text-slate-grey mt-1">จัดการอีเมลแจ้งเตือน</p>
                </div>
            </a>

            <a href="{{ url('/management/users') }}" 
            class="group bg-pure-white p-6 rounded-2xl shadow-sm border border-cloud-blue hover:shadow-lg hover:border-baltic-blue/50 transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center gap-4 cursor-pointer relative overflow-hidden sm:col-span-2 lg:col-span-2 max-w-xl mx-auto w-full">
                <div class="absolute inset-0 bg-gradient-to-b from-sky-surge/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="p-4 bg-cloud-blue/40 text-baltic-blue rounded-full group-hover:bg-baltic-blue group-hover:text-pure-white transition-colors duration-300 relative z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xl font-bold text-deep-navy group-hover:text-baltic-blue transition-colors">ผู้ใช้งาน</h3>
                    <p class="text-sm text-slate-grey mt-1">จัดการบัญชีผู้ใช้ในระบบ</p>
                </div>
            </a>

        </div>
    </div>
</body>
</html>