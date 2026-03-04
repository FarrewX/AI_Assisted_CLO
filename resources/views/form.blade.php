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
<body class="font-sans antialiased">
  <div x-data="{ ploOpen: false }">
    
    <div class="fixed top-4 left-4 flex gap-3 z-40">
      <a href="/" class="inline-flex items-center gap-2 px-4 py-2 bg-pure-white border border-cloud-blue text-deep-navy font-medium rounded-lg shadow-sm transform transition-all duration-200 hover:-translate-y-0.5 hover:bg-cloud-blue/20 hover:text-royal-blue">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
          </svg>
          หน้าหลัก
      </a>
      <button @click="ploOpen = true"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-royal-blue text-pure-white font-semibold rounded-lg shadow-md hover:bg-deep-navy hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-royal-blue focus:ring-offset-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
              <path fill-rule="evenodd" d="M2.625 6.75a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875 0A.75.75 0 018.25 6h12a.75.75 0 010 1.5h-12a.75.75 0 01-.75-.75zM2.625 12a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875 0A.75.75 0 018.25 11.25h12a.75.75 0 010 1.5h-12A.75.75 0 017.5 12.75zM2.625 17.25a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0zm4.875 0a.75.75 0 01.75-.75h12a.75.75 0 010 1.5h-12a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
          </svg>
          เปิด PLO
      </button>
    </div>

  <div class="fixed inset-y-0 left-0 w-80 max-w-[85vw] bg-cloud-blue/10 h-full shadow-2xl flex flex-col z-50 border-r border-cloud-blue backdrop-blur-md"
     x-show="ploOpen" 
     style="display: none;"
     x-transition:enter="transform transition ease-in-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transform transition ease-in-out duration-300"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full">
     
    <div class="flex items-center justify-between px-6 py-4 bg-pure-white border-b border-cloud-blue shadow-sm">
        <h2 class="text-xl font-bold text-deep-navy flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-royal-blue">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            รายการ PLOs
        </h2>
        <button @click="ploOpen = false" class="p-2 text-slate-grey hover:text-red-500 hover:bg-red-50 rounded-full transition-colors focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-4">
        @foreach($plos as $plo)
            <div class="bg-pure-white p-4 rounded-xl shadow-sm border border-cloud-blue hover:shadow-md transition-shadow">
                <div class="inline-block px-2.5 py-1 bg-cloud-blue/50 text-royal-blue border border-cloud-blue text-xs font-bold rounded-md mb-2">
                    PLO {{ $plo->plo }}
                </div>
                <p class="text-sm text-slate-grey leading-relaxed">{{ $plo->description }}</p>
            </div>
        @endforeach
    </div>
</div>

  <div id="popup-modal" class="fixed inset-0 flex items-center justify-center z-[60] hidden">
    <div class="absolute inset-0 bg-deep-navy/60 backdrop-blur-sm transition-opacity"></div>
    
    <div class="relative bg-pure-white rounded-2xl shadow-2xl p-6 sm:p-8 min-w-[300px] max-w-sm w-full mx-4 text-center transform scale-100 transition-all border border-cloud-blue">
        
        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-cloud-blue/40 border border-cloud-blue mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7 text-royal-blue">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
        </div>

        <h3 class="text-xl font-bold text-deep-navy mb-2">แจ้งเตือน</h3>
        <div id="popup-message" class="mb-6 text-slate-grey text-sm leading-relaxed"></div>
        
        <button id="popup-close" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-royal-blue px-4 py-3 text-sm font-semibold text-pure-white shadow-sm hover:bg-deep-navy focus:outline-none focus:ring-2 focus:ring-royal-blue focus:ring-offset-2 transition-colors">
            รับทราบ / ปิด
        </button>
    </div>
  </div>

  <div class="flex items-center justify-center min-h-screen py-16 px-4">
    <div class="bg-pure-white p-6 md:p-8 rounded-2xl shadow-lg border border-cloud-blue w-full max-w-[600px] relative">
      <h2 class="text-center text-3xl font-extrabold text-deep-navy mb-6 tracking-tight">ELO<span class="text-royal-blue">_</span>Generate</h2>

      <form id="eloForm" method="POST" class="space-y-5">
        @csrf
        <div>
          <label for="course" class="block font-semibold text-deep-navy mb-1.5">เลือกรายวิชา :</label>
          <select id="course" name="course" class="w-full p-2.5 border border-cloud-blue text-slate-grey bg-cloud-blue/10 rounded-lg text-sm focus:outline-none" disabled>
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
          <label for="prompt" class="block font-semibold text-deep-navy mb-1.5">รายละเอียดรายวิชา :</label>
          <textarea id="prompt" name="prompt" rows="5"
              placeholder="กรอกรายละเอียดรายวิชาที่ต้องการให้ AI สร้าง..."
              class="w-full p-3 border border-cloud-blue text-deep-navy bg-pure-white rounded-lg focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all"></textarea>
        </div>

        <div class="grid grid-cols-1 gap-5">
            <div>
              <label for="numClo" class="block font-semibold text-deep-navy mb-1.5">จำนวน CLO ที่ต้องการ :</label>
              <select id="numClo" name="numClo" class="w-full p-2.5 border border-cloud-blue text-deep-navy bg-pure-white rounded-lg focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all">
                @for ($i = 1; $i <= 5; $i++)
                  <option value="{{ $i }}" {{ $i == 0 ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
              </select>
            </div>

            <div id="selectPloboxes">
              <label class="block font-semibold text-deep-navy mb-2">เลือก PLO ที่เกี่ยวข้อง :</label>
              <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 border border-cloud-blue rounded-lg bg-cloud-blue/10">
                @if(isset($plos) && count($plos) > 0)
                    @foreach($plos as $selectPlo)
                      <label class="flex items-center space-x-3 cursor-pointer hover:bg-cloud-blue/30 p-2 rounded-md transition-colors">
                        <input type="checkbox" name="selectPlo[]" value="{{ $selectPlo->plo }}"
                              class="w-4 h-4 rounded selectPlo text-royal-blue focus:ring-royal-blue border-cloud-blue cursor-pointer">
                        <span class="text-sm font-semibold text-deep-navy">PLO {{ $selectPlo->plo }}</span>
                      </label>
                    @endforeach
                @else
                    <p class="text-red-500 text-sm p-2">ไม่พบข้อมูล PLO</p>
                @endif
              </div>
            </div>
        </div>

        <button type="button" onclick="openPreview()"
          class="w-full mt-6 inline-flex items-center justify-center whitespace-nowrap bg-royal-blue hover:bg-deep-navy text-pure-white font-bold px-4 py-3.5 rounded-xl shadow-lg shadow-royal-blue/20 hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
          <i class="fa-solid fa-wand-magic-sparkles mr-2"></i> Generate AI
        </button>
      </form>
    </div>
  </div>

  <div id="previewModal"
     class="fixed inset-0 bg-deep-navy/60 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity duration-300"
     onclick="closeOnBackground(event)">
    <div class="bg-pure-white w-full max-w-md rounded-2xl shadow-2xl border border-cloud-blue p-8 transform transition-all scale-100 mx-4"
         onclick="event.stopPropagation()">
        <h3 class="text-2xl font-bold mb-6 text-deep-navy leading-tight border-b border-cloud-blue pb-4">ยืนยันข้อมูล</h3>
        <div id="previewContent" class="space-y-4 text-base text-slate-grey leading-relaxed max-h-[60vh] overflow-y-auto pr-2"></div>
        <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-cloud-blue">
            <button onclick="closePreview()"
                    class="px-5 py-2.5 bg-cloud-blue/50 hover:bg-cloud-blue text-deep-navy font-medium rounded-xl transition-colors duration-200 focus:outline-none">
                ยกเลิก
            </button>
            <button onclick="submitForm()"
                    class="px-5 py-2.5 bg-royal-blue hover:bg-deep-navy text-pure-white font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-royal-blue focus:ring-offset-2">
                ยืนยันและสร้าง
            </button>
        </div>
    </div>
</div>

  <div id="loadingPopup" class="hidden fixed inset-0 bg-deep-navy/70 flex items-center justify-center z-[70] backdrop-blur-md">
    <div class="bg-pure-white rounded-2xl shadow-2xl p-10 flex flex-col items-center border border-cloud-blue">
      <div class="loader border-4 border-cloud-blue border-t-royal-blue rounded-full w-14 h-14 animate-spin mb-5 shadow-sm"></div>
      <p class="text-deep-navy font-extrabold text-xl tracking-wide">กำลังประมวลผลด้วย AI...</p>
      <p class="text-slate-grey text-sm mt-2">กรุณารอสักครู่ ห้ามปิดหน้านี้</p>
    </div>
  </div>
</div>
</body>
</html>