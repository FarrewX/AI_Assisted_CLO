<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ELO_Generate</title>

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
        <div id="app">
            @include('component.navbar')
            <div class="container mx-auto px-4">
                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 80vh; margin-top: 100px;">
                    <h1 class="whitespace-nowrap font-bold" style="font-size:60px;">Welcome</h1>
                    <h2 class="whitespace-nowrap font-bold">To the ELO_Generate </h2>
                    <p class="text-gray-600 text-lg">ระบบสร้างผลลัพธ์การเรียนรู้ระดับรายวิชา (CLO) ด้วย AI</p>
                    <br>
                    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-xl font-semibold mb-4">📌 วิธีการใช้งาน</h2>
                        <ol class="list-decimal list-inside space-y-2 text-gray-800">
                            <li>เลือกประเภทผลลัพธ์ที่ต้องการสร้าง (มคอ.3 หรือ มคอ.5)</li>
                            <li>เลือกรายวิชาที่ต้องการ</li>
                            <li>กรอกคำอธิบายรายวิชาหรือเป้าหมายการสอน</li>
                            <li>กดปุ่ม Generate เพื่อให้ระบบสร้างผลลัพธ์การเรียนรู้</li>
                            <li>ตรวจสอบผลลัพธ์ในหน้าแสดงตัวอย่าง (Preview)</li>
                            <li>สามารถกลับมาแก้ไขpromptหรือแก้ไขข้อมูลที่ AI Gen ออกมาได้</li>
                        </ol>

                        @if(session('status'))
                            <div class="mt-6 p-4 bg-green-100 border border-green-300 rounded text-green-800">
                                ✅ <strong>Status:</strong> {{ session('status') }}
                            </div>
                        @endif
                        
                        <div class="text-center mt-8">
                            <a href="{{ route('form') }}"
                                class="inline-block bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded transition duration-200">
                                Get Started
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @vite(['resources/js/app.js'])
    </body>
</html>
