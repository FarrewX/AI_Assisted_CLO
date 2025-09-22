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
  @endif
  <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body>
  <button type="button" onclick="window.history.back()"
    class="mt-10 ml-10 top-4 right-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md shadow">
    ← back
  </button>

  <!-- Sidebar PLOs -->
  <div x-data="{ ploOpen: false }" class="mt-10 ml-10 top-10 right-4">
    <!-- ปุ่มเปิด PLO -->
    <button @click="ploOpen = true"
        class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-md shadow">
        เปิด PLO
    </button>

    <!-- Overlay + Sidebar -->
    <div
        class="fixed inset-y-25 left-0 w-72 z-50 flex"
        x-show="ploOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
      >
        <!-- พื้นหลังโปร่ง -->
        <div class="fixed inset-0 bg-opacity-30 pointer-events-none"></div>

        <!-- Sidebar PLO -->
        <div 
            class="relative w-72 bg-white shadow-lg h-full z-50 p-6"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
        >
            <!-- ปุ่มปิด -->
            <button @click="ploOpen = false" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900">
                ✕
            </button>

            <h2 class="text-lg font-bold mb-4">PLOs</h2>
            <ul class="space-y-3">
                @foreach($plos as $plo)
                    <li class="border-b pb-2">
                        <span class="font-semibold">PLO {{ $plo->plo }}</span><br>
                        <span class="text-sm text-gray-600">{{ $plo->description }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
  </div>

  <!-- Form -->
  <div class="flex justify-center items-center mt-30">
    <div class="bg-white p-6 rounded-xl shadow-md w-[400px] relative">
      <h2 class="text-center text-[30px] font-bold text-gray-800 mb-4">ELO_Generate</h2>

      <form id="eloForm" action="/generate" method="POST" class="space-y-4">
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
                <option value="{{ $course->course_id }}" 
                        data-detail="{{ $course->course_detail_th }}">
                {{ $course->course_id }} - {{ $course->course_name }} (ปี {{ $course->year }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- รายละเอียด -->
        <div>
          <label for="prompt" class="block font-semibold text-gray-700 mb-1">รายละเอียดรายวิชา :</label>
          <textarea id="prompt" name="prompt" rows="4" placeholder="กรอกรายละเอียดรายวิชาที่ต้องการให้ AI สร้าง..." class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200"></textarea>
        </div>

        <!-- ปุ่ม -->
        <button type="button" onclick="openPreview()"
          class="w-full inline-flex items-center justify-center whitespace-nowrap bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-md shadow-md hover:shadow-lg transition">
          Generate
        </button>
      </form>
    </div>
  </div>

  <!-- Modal -->
  <div id="previewModal"
     class="fixed inset-0 bg-gray-100 hidden flex items-center justify-center z-50"
     onclick="closeOnBackground(event)">
    <div class="bg-white w-[400px] rounded-lg shadow-lg p-6" onclick="event.stopPropagation()">
      <h3 class="text-lg font-bold mb-3">ยืนยันข้อมูลที่กรอก</h3>
      <div id="previewContent" class="space-y-2 text-sm text-gray-700"></div>
      <div class="flex justify-end gap-3 mt-4">
        <button onclick="closePreview()" class="px-4 py-2 bg-red-700 hover:bg-red-400 text-white rounded-md">ยกเลิก</button>
        <button onclick="submitForm()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">ยืนยัน</button>
      </div>
    </div>
  </div>

<script>
document.getElementById('course').addEventListener('change', function () {
  let selectedOption = this.options[this.selectedIndex];
  let detail = selectedOption.getAttribute('data-detail') || '';
  document.getElementById('prompt').value = detail;
});

function openPreview() {
  let prompt = document.getElementById("prompt").value.trim();
  let sec_clo = document.querySelector('input[name="sec_clo"]:checked');
  let courseSelect = document.getElementById("course");

  if (!sec_clo) {
    alert("กรุณาเลือก มคอ.3 หรือ มคอ.5");
    return;
  }
  if (!courseSelect.value) {
    alert("กรุณาเลือกรายวิชา");
    return;
  }
  if (!prompt) {
    alert("กรุณากรอกรายละเอียดรายวิชา");
    return;
  }

  // ---- แสดง popup ----
  document.getElementById("previewModal").classList.remove("hidden");

  // ---- ใส่ข้อมูล preview ----
  let selectedText = courseSelect.options[courseSelect.selectedIndex].text;
  document.getElementById("previewContent").innerHTML = `
    <p><b>มคอ:</b> ${sec_clo.value}</p>
    <p><b>รายวิชา:</b> ${selectedText}</p>
    <p><b>รายละเอียด:</b> ${prompt}</p>
  `;
}

function closePreview() {
  document.getElementById("previewModal").classList.add("hidden");
}

function closeOnBackground(event) {
  if (event.target.id === "previewModal") {
    closePreview();
  }
}

function submitForm() {
  document.getElementById("eloForm").submit();
}
</script>
</body>
</html>
