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
    <body class="bg-white min-h-screen flex flex-col items-center py-10 " style="height: 80vh; margin-top: 100px;">
        @include('component.navbar')
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
            <h3 class="text-md font-semibold mb-4">รายวิชาที่เปิดสอน ปี ล่าสุด</h3>

            <div class="mb-6">
                <div class="flex justify-between">
                    <span>• AI</span>
                    <span class="text-gray-500 text-sm">- prompt แล้ว</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 mt-1">
                    <div class="bg-green-500 h-3 rounded-full" style="width: 70%"></div>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between">
                    <span>• Cloud</span>
                    <span class="text-gray-500 text-sm">- กรอกข้อมูลแล้ว</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 mt-1">
                    <div class="bg-green-500 h-3 rounded-full" style="width: 50%"></div>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between">
                    <span>• T</span>
                    <span class="text-gray-500 text-sm">- กรอกข้อมูลแล้ว</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 mt-1">
                    <div class="bg-green-500 h-3 rounded-full" style="width: 50%"></div>
                </div>
            </div>
        </div>

    </body>
</html>
