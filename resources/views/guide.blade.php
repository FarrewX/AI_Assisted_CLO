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
  <div class="flex items-center justify-center p-5 mt-20">
    <div class="bg-white p-10 rounded-xl shadow-lg max-w-lg w-full text-center">
      <h1 class="text-xl font-bold mb-6">📌 วิธีการใช้งาน</h1>
      <ul class="list-disc pl-5 text-left text-base font-medium leading-relaxed">
        <li class="mb-3">เลือกประเภทผลลัพธ์ที่ต้องการสร้าง (มคอ.3 หรือ มคอ.5)</li>
        <li class="mb-3">เลือกรายวิชาที่ต้องการ</li>
        <li class="mb-3">กรอกคำอธิบายรายวิชาหรือเป้าหมายการสอน</li>
        <li class="mb-3">กดปุ่ม Generate เพื่อให้ระบบสร้างผลลัพธ์การเรียนรู้</li>
        <li class="mb-3">ตรวจสอบผลลัพธ์ในหน้าแสดงตัวอย่าง (Preview)</li>
        <li class="mb-3">สามารถกลับมาแก้ไข prompt หรือแก้ไขข้อมูลที่ AI Gen ออกมาได้</li>
      </ul>
    </div>
  </div>
</body>
</html>