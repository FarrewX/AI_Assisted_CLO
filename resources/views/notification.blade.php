<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ELO_Generator</title>

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif
</head>
<body>
  @include('component.navbar')

  <div class="bg-white mt-20">
    <div class="w-[90%] max-w-[1000px] mx-auto">
      <!-- ปุ่มส่งแจ้งเตือน -->
      <button 
          class="float-right font-bold px-5 py-2 rounded transition mb-3
                {{ $courses->isEmpty() ? 'bg-gray-400 cursor-not-allowed' : 'bg-orange-500 hover:bg-orange-600 text-white' }}"
          onclick="{{ $courses->isEmpty() ? 'return false;' : "location.href='send-email'" }}"
          {{ $courses->isEmpty() ? 'disabled' : '' }}>
          ส่งแจ้งเตือน
      </button>
  
      <!-- ตารางรายวิชา -->
      <table class="w-full border border-gray-300 border-collapse bg-white mt-5">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-3 border border-gray-300 text-left">รหัสวิชา</th>
            <th class="px-4 py-3 border border-gray-300 text-left">รายชื่อวิชา</th>
            <th class="px-4 py-3 border border-gray-300 text-left">อาจารย์ผู้สอน</th>
            <th class="px-4 py-3 border border-gray-300 text-left">ปีการศึกษา</th>
            <th class="px-4 py-3 border border-gray-300 text-left">มคอ.</th>
            <th class="px-4 py-3 border border-gray-300 text-left">สถานะ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($courses as $course)
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

            <tr>
              <td class="px-4 py-3 border border-gray-300">{{ $course->course_id }}</td>
              <td class="px-4 py-3 border border-gray-300">{{ $course->course_name }}</td>
              <td class="px-4 py-3 border border-gray-300">{{ $course->name }}</td>
              <td class="px-4 py-3 border border-gray-300">{{ $course->term }}/{{ $course->year }}</td>
              <td class="px-4 py-3 border border-gray-300">{{ $course->TQF }}</td>
              <td class="px-4 py-3 border border-gray-300">{{ $status_text }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                ไม่พบข้อมูล
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

    </div>
  </div>
</body>
</html>
