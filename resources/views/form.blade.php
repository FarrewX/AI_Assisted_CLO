<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ELO_Generate</title>
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/form.js'])
  @endif
  <script src="//unpkg.com/alpinejs" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/json5@2.2.3/dist/index.min.js"></script>
  <style>
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    .animate-spin {
      animation: spin 1s linear infinite;
    }
  </style>
</head>
<body>
  <div x-data="{ ploOpen: false }">
  <div class="fixed top-4 left-4 flex gap-3 z-50">
      <button type="button" onclick="window.history.back()"
          class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md shadow">
          ← back
      </button>

      <button @click="ploOpen = true"
          class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-md shadow">
          เปิด PLO
      </button>
  </div>

  <div class="fixed inset-y-0 left-0 w-72 z-50 flex" x-show="ploOpen" style="display: none;"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0">
      <div class="fixed inset-0 bg-opacity-30 pointer-events-none"></div>
      <div class="relative w-72 bg-white shadow-lg h-full z-50 p-6 overflow-y-auto max-h-screen"
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="-translate-x-full"
          x-transition:enter-end="translate-x-0"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="translate-x-0"
          x-transition:leave-end="-translate-x-full">
          <button @click="ploOpen = false"
              class="absolute top-4 right-4 text-gray-600 hover:text-gray-900">✕</button>
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

  <div id="popup-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
      <div class="absolute inset-0 backdrop-blur-sm bg-black/30"></div>
      <div class="relative bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
          <div id="popup-message" class="mb-4 font-bold text-center"></div>
          <button id="popup-close"
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">ปิด</button>
      </div>
  </div>

  <div class="flex items-center justify-center min-h-screen pt-16">
    <div class="bg-white p-6 rounded-xl shadow-md w-[600px] relative">
      <h2 class="text-center text-[30px] font-bold text-gray-800 mb-4">ELO_Generate</h2>

      <form id="eloForm" method="POST" class="space-y-4">
        @csrf
        <div>
          <label for="course" class="block font-semibold text-gray-700 mb-1">เลือกรายวิชา :</label>
          <select id="course" name="course" class="w-full p-2 border border-gray-300 rounded-md text-sm">
            @if(isset($course_options))
                @foreach ($course_options as $course)
                    <option value="{{ $course->course_pk }}"
                        data-cc-id="{{ $course->cc_id }}"
                        data-coursecode="{{ $course->course_code }}"
                        data-coursename="{{ $course->course_name_th }}"
                        data-year="{{ $course->year }}" 
                        data-term="{{ $course->term }}"
                        data-TQF="{{ $course->TQF }}"
                        data-detail="{{ $course->course_detail_th }}"
                        data-coursetext="{{ $course->course_text }}">
                        {{ $course->course_code }} {{ $course->course_name_th }} (ภาค{{$course->term}}/{{ $course->year }}) [หลักสูตร {{ $course->curriculum_year }}]
                    </option>
                @endforeach
            @endif
        </select>
        </div>

        <div>
          <label for="prompt" class="block font-semibold text-gray-700 mb-1">รายละเอียดรายวิชา :</label>
          <textarea id="prompt" name="prompt" rows="5"
              placeholder="กรอกรายละเอียดรายวิชาที่ต้องการให้ AI สร้าง..."
              class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200"></textarea>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div>
              <label for="numClo" class="block font-semibold text-gray-700 mb-1">จำนวน CLO ที่ต้องการ :</label>
              <select id="numClo" name="numClo" class="w-full p-2 border border-gray-300 rounded-md">
                @for ($i = 1; $i <= 5; $i++)
                  <option value="{{ $i }}" {{ $i == 0 ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
              </select>
            </div>

            <div id="selectPloboxes">
              <label class="block font-semibold text-gray-700 mb-2">เลือก PLO ที่เกี่ยวข้อง :</label>
              <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-3 border rounded-md bg-gray-50">
                @if(isset($plos) && count($plos) > 0)
                    @foreach($plos as $selectPlo)
                      <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-1 rounded">
                        <input type="checkbox" name="selectPlo[]" value="{{ $selectPlo->plo }}"
                              class="rounded selectPlo text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="text-sm font-medium">PLO {{ $selectPlo->plo }}</span>
                      </label>
                    @endforeach
                @else
                    <p class="text-red-500 text-sm">ไม่พบข้อมูล PLO</p>
                @endif
              </div>
            </div>
        </div>

        <button type="button" onclick="openPreview()"
          class="w-full mt-4 inline-flex items-center justify-center whitespace-nowrap bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-md shadow-md hover:shadow-lg transition">
          Generate AI
        </button>
      </form>
    </div>
  </div>

  <div id="previewModal"
       class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50"
       onclick="closeOnBackground(event)">
    <div class="bg-white w-[500px] rounded-xl shadow-2xl p-6 transform transition-all scale-100"
         onclick="event.stopPropagation()">
      <h3 class="text-xl font-bold mb-4 border-b pb-2 text-gray-800">ยืนยันข้อมูล</h3>
      <div id="previewContent" class="space-y-3 text-sm text-gray-700"></div>
      <div class="flex justify-end gap-3 mt-6">
        <button onclick="closePreview()"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition">ยกเลิก</button>
        <button onclick="submitForm()"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow transition">ยืนยันและสร้าง</button>
      </div>
    </div>
  </div>

  <div id="loadingPopup" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-8 flex flex-col items-center">
      <div class="loader border-4 border-blue-200 border-t-blue-600 rounded-full w-12 h-12 animate-spin mb-4"></div>
      <p class="text-gray-800 font-bold text-lg">กำลังประมวลผลด้วย AI...</p>
      <p class="text-gray-500 text-sm mt-1">กรุณารอสักครู่ ห้ามปิดหน้านี้</p>
    </div>
  </div>
</div>
</body>
</html>