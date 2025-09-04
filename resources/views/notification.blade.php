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
  <div class="bg-white mt-20">
    <div class="w-[90%] max-w-[1000px] mx-auto">
      <button 
        class="float-right bg-orange-500 hover:bg-orange-600 text-white font-bold px-5 py-2 rounded transition mb-3"
        onclick="location.href='message'">
        ส่งแจ้งเตือน
      </button>
  
      <table class="w-full border border-gray-300 border-collapse bg-white mt-5">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-3 border border-gray-300 text-left">รหัสวิชา</th>
            <th class="px-4 py-3 border border-gray-300 text-left">รายชื่อวิชา</th>
            <th class="px-4 py-3 border border-gray-300 text-left">อาจารย์รายวิชา</th>
            <th class="px-4 py-3 border border-gray-300 text-left">สถานะงาน</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="px-4 py-3 border border-gray-300">00000001</td>
            <td class="px-4 py-3 border border-gray-300">การออกแบบอัลกอริทึมและการเขียนโปรแกรมเชิงขั้นตอน...</td>
            <td class="px-4 py-3 border border-gray-300">อาจารย์เอ</td>
            <td class="px-4 py-3 border border-gray-300">ยังไม่เสร็จ</td>
          </tr>
          <tr>
            <td class="px-4 py-3 border border-gray-300">00000002</td>
            <td class="px-4 py-3 border border-gray-300">หลักการและเทคนิคการพัฒนาโปรแกรมคอมพิวเตอร์...</td>
            <td class="px-4 py-3 border border-gray-300">อาจารย์บี</td>
            <td class="px-4 py-3 border border-gray-300">ยังไม่เริ่ม</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>