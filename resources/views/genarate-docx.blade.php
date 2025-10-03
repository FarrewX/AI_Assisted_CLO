<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Preview — แบบฟอร์ม มคอ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
    
</head>
<body class="bg-gray-100 flex justify-center py-6">
    <!-- ปุ่มซ้ายบน -->
  <div class="fixed top-4 left-4 flex gap-3 z-50">
      <!-- ปุ่ม back -->
      <button type="button" onclick="window.history.back()"
          class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md shadow">
          ← back
      </button>
  </div>

    <!-- ปุ่มดาวน์โหลด -->
    <div class="absolute top-6 right-6">
        <a href="{{ route('export.docx') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700" title="ดาวน์โหลด .docx">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 14a1 1 0 011-1h3v2H5v1a1 1 0 11-2 0v-2zM7 8a1 1 0 011-1h4a1 1 0 011 1v4h2V8a3 3 0 00-3-3H8a3 3 0 00-3 3v4h2V8z" clip-rule="evenodd" />
            </svg>
            ดาวน์โหลด
        </a>
    </div>

    <div class="a4">
        {{-- Header --}}
        <h1 class="text-center text-24 font-bold mb-2">{{ $content['title'] }}</h1>
        <h2 class="text-center text-24 font-bold mb-4">{{ $content['subtitle'] }}</h2>

        {{-- หัวข้อย่อย --}}
        <h2 class="font-semibold mb-2">5.3 ความสอดคล้องของรายวิชากับ PLOs, CLOs และ LLLs</h2>
        <h3 class="mb-4">หลักสูตรฯ วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์</h3>

        {{-- ตาราง CLO ↔ PLO --}}
        <table class="doc-table mb-4">
            <tr class="bg-gray-200">
                {{-- <td colspan="5" class="font-bold text-center">รหัสวิชา10301351 ชื่อวิชา วิทยาการข้อมูล</td> --}}
            </tr>
            <tr>
                <th>รหัสวิชา10301351 ชื่อวิชา วิทยาการข้อมูล</th>
                <th>PLO1 (U)</th>
                <th>PLO2 (E)</th>
                <th>PLO3 (C)</th>
                <th>PLO4 (AP)</th>
            </tr>
            <tr>
                <td>CLO1 ประเมินวิธีการวิเคราะห์ข้อมูล เพื่อแก้ไขปัญหาทางวิทยาการข้อมูลได้อย่างเหมาะสม</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>CLO2 สร้างต้นแบบเพื่อการทำงานหรือการรู้จักจากชุดข้อมูลเพื่อประยุกต์ใช้ในการแก้ปัญหาจริง</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
                <td>CLO3 สร้างการทำงานหรือการรู้จักจากชุดข้อมูลเพื่อนำมาแก้ไขและปรับเปลี่ยนในอนาคต</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>

        </table>

        {{-- ประวัติการศึกษา --}}
        {{-- <h2 class="font-semibold mb-2">ประวัติการศึกษา</h2>
        <table class="doc-table mb-4">
            <tr class="bg-gray-100">
                <th>ระดับ</th>
                <th>สถาบัน</th>
                <th>ปีที่จบ</th>
            </tr>
            @foreach($content['education'] as $edu)
            <tr>
                <td>{{ $edu['degree'] }}</td>
                <td>{{ $edu['institution'] }}</td>
                <td>{{ $edu['year'] }}</td>
            </tr>
            @endforeach
        </table> --}}

        {{-- ลงชื่อ --}}
        {{-- <div class="mt-8 text-right">
            <p>ลงชื่อ ..................................</p>
            <p>(..................................)</p>
        </div> --}}
    </div>

</body>
</html>
