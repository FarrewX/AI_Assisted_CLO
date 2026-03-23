<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <title>{{ config('app.name', 'AI-Assisted CLO') }} | Generate CLO</title>
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
<body class="font-sans antialiased">
  <div x-data="{ ploOpen: false }">
    <div class="fixed top-4 left-4 flex gap-3 z-50">
      <a href="/" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-900 rounded-md shadow transform transition-all duration-200 ease-in-out hover:-translate-y-0.5">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
          </svg>
          หน้าหลัก
      </a>
      <button @click="ploOpen = true"
        style="background: linear-gradient(to right, #6CBAD9, #035AA6); cursor: pointer;"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#035AA6] hover:bg-[#6CBAD9] text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-baltic-blue focus:ring-offset-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
              <path fill-rule="evenodd" d="M2.625 6.75a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875 0A.75.75 0 018.25 6h12a.75.75 0 010 1.5h-12a.75.75 0 01-.75-.75zM2.625 12a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875 0A.75.75 0 018.25 11.25h12a.75.75 0 010 1.5h-12A.75.75 0 017.5 12.75zM2.625 17.25a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875 0a.75.75 0 01.75-.75h12a.75.75 0 010 1.5h-12a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
          </svg>
          เปิด PLO
      </button>
    </div>

  <div class="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-gray-50 h-full shadow-2xl flex flex-col z-50 border-r border-gray-300"
     x-show="ploOpen" 
     style="display: none;"
     x-transition:enter="transform transition ease-in-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transform transition ease-in-out duration-300"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full">
     
    <div class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 shadow-sm">
        <button class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-baltic-blue">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            รายการ PLOs
        </button>
        <button @click="ploOpen = false" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-colors focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4">
        @foreach($plos as $plo)
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="inline-block px-2.5 py-1 bg-pale-sky text-baltic-blue text-xs font-bold rounded-md mb-2">
                    PLO {{ $plo->plo }}
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $plo->description }}</p>
            </div>
        @endforeach
    </div>
  </div>

  <div id="popup-modal" class="fixed inset-0 flex items-center justify-center z-[60] hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
    
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 sm:p-8 min-w-[300px] max-w-sm w-full mx-4 text-center transform scale-100 transition-all">
        
        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-blue-100 mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-blue-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
        </div>

        <h3 class="text-xl font-bold text-gray-900 mb-2">แจ้งเตือน</h3>
        <div id="popup-message" class="mb-6 text-gray-600 text-sm leading-relaxed"></div>
        
        <button id="popup-close" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            รับทราบ / ปิด
        </button>
    </div>
  </div>

  <div class="flex items-center justify-center min-h-screen pt-8">
    <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-[600px] relative">
      <h2 class="text-center text-[30px] font-bold text-gray-800 mb-4">ELO_Generate</h2>

      <form id="eloForm" method="POST" class="space-y-4">
        @csrf
        <div>
          <label for="course" class="block font-semibold text-gray-700 mb-1">เลือกรายวิชา :</label>
          <select id="course" name="course" class="w-full p-2 border border-gray-300 rounded-md text-sm" disabled>
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
                        {{ $course->course_code }} {{ $course->course_name_th }} [{{ $course->curriculum_year }}] (ภาคเรียน{{$course->term}}/{{ $course->year }}) 
                        @if(Auth::user()->role_id == 1) 
                            [ผู้สอน: {{ $course->instructor_name }}]
                        @endif
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
              <label class="block font-semibold mb-2">เลือก PLO ที่เกี่ยวข้อง :</label>
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
          class="w-full mt-4 inline-flex items-center justify-center whitespace-nowrap bg-[#035AA6] hover:bg-[#6CBAD9] text-white font-bold px-4 py-2.5 rounded-md shadow-md hover:shadow-lg transition">
          Generate AI
        </button>
      </form>
    </div>
  </div>

  <div id="previewModal"
     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity duration-300"
     onclick="closeOnBackground(event)">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-gray-100 p-8 transform transition-all scale-100 mx-4"
         onclick="event.stopPropagation()">
        <h3 class="text-2xl font-bold mb-6 text-gray-900 leading-tight"> ยืนยันข้อมูล</h3>
        <div id="previewContent" class="space-y-4 text-base text-gray-600 leading-relaxed"></div>
        <div class="flex justify-end gap-4 mt-8">
            <button onclick="closePreview()"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                ยกเลิก
            </button>
            <button onclick="submitForm()"
                    class="px-5 py-2.5 bg-[#035AA6] hover:bg-[#6CBAD9] text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                ยืนยันและสร้าง
            </button>
        </div>
    </div>
</div>

  <div id="loadingPopup" class="hidden fixed inset-0 bg-pure-white bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-8 flex flex-col items-center">
      <div class="loader border-4 border-blue-200 border-t-blue-600 rounded-full w-12 h-12 animate-spin mb-4"></div>
      <p class="text-baltic-blue font-bold text-lg">กำลังประมวลผลด้วย AI...</p>
      <p class="text-baltic-blue text-sm mt-1">กรุณารอสักครู่ ห้ามปิดหน้านี้</p>
    </div>
  </div>
</div>
</body>
</html>