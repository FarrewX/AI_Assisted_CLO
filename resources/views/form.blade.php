<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ELO_Generate</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    @endif
</head>
<body>
  <button type="button" onclick="window.history.back()"
    class="mt-10 ml-10 top-4 right-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md shadow">
    ← back
  </button>
  <div class="flex justify-center items-center mt-30">
    <div class="bg-white p-6 rounded-xl shadow-md w-[400px] relative">
      <h2 class="text-center text-[30px] font-bold text-gray-800 mb-4">ELO_Generate</h2>
  
      <form action="/generate" method="POST" class="space-y-4">
        @csrf
        <!-- เลือก มคอ -->
        <div class="flex items-center gap-6">
          <div class="flex items-center gap-2">
            <input type="radio" id="clo3" name="sec_clo" value="clo3" class="w-4 h-4 text-blue-600">
            <label for="clo3" class="text-gray-700">มคอ.3</label>
          </div>
          <div class="flex items-center gap-2">
            <input type="radio" id="clo5" name="sec_clo" value="clo5" class="w-4 h-4 text-blue-600">
            <label for="clo5" class="text-gray-700">มคอ.5</label>
          </div>
        </div>
  
        <!-- รายวิชา -->
        <div>
          <label for="course" class="block font-semibold text-gray-700 mb-1">เลือกรายวิชา :</label>
          <select id="course" name="course" class="w-full p-2 border border-gray-300 rounded-md">
            <option value="" disabled selected>-- กรุณาเลือกรายวิชา --</option>
            @foreach ($courses as $course)
              <option value="{{ $course->user_id }}">{{ $course->course_name }}</option>
            @endforeach
          </select>
        </div>
  
        <!-- รายละเอียด -->
        <div>
          <label for="prompt" class="block font-semibold text-gray-700 mb-1">รายละเอียดรายวิชา :</label>
          <textarea id="prompt" name="prompt" rows="4" placeholder="กรอกรายละเอียดรายวิชาที่ต้องการให้ AI สร้าง..." class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200"></textarea>
        </div>
  
        <!-- ปุ่ม -->
        <button type="submit" onclick="createPreview()" 
                class="w-full inline-flex items-center justify-center whitespace-nowrap bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-md shadow-md hover:shadow-lg transition">
                Generate
        </button>
      </form>
    </div>
  </div>
</body>

<script> 
function createPreview() {
  let prompt = document.getElementById("prompt").value;
  let sec_clo = document.querySelector('input[name="sec_clo"]:checked');
  let course = document.getElementById("course").value;

  if (prompt.trim() === "") {
    alert("กรุณากรอกข้อความก่อนกด Generate");
    return;
  }
  if (!course) {
    alert("กรุณาเลือกรายวิชา");
    return;
  }
  if (!sec_clo) {
    alert("กรุณาเลือก มคอ.3 หรือ มคอ.5");
    return;
  }

  let previewHtml = `
    <p><strong>Section CLO:</strong> ${sec_clo ? sec_clo.value : 'Not selected'}</p>
    <p><strong>Course:</strong> ${course}</p>
    <p><strong>รายละเอียด:</strong> ${prompt}</p>
  `;

  document.getElementById("preview").innerHTML = previewHtml;
  document.getElementById("preview").style.display = "block";
}
</script>