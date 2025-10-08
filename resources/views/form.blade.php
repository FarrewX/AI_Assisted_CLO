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
    <div x-data="{ ploOpen: false }">

  <!-- ปุ่มซ้ายบน -->
  <div class="fixed top-4 left-4 flex gap-3 z-50">
      <!-- ปุ่ม back -->
      <button type="button" onclick="window.history.back()"
          class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md shadow">
          ← back
      </button>

      <!-- ปุ่มเปิด PLO -->
      <button @click="ploOpen = true"
          class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-md shadow">
          เปิด PLO
      </button>
  </div>

  <!-- Sidebar PLO -->
  <div class="fixed inset-y-0 left-0 w-72 z-50 flex" x-show="ploOpen"
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

  <!-- Modal Popup -->
  <div id="popup-modal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
      <div class="absolute inset-0 backdrop-blur-sm bg-black/30"></div>
      <div class="relative bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
          <div id="popup-message" class="mb-4 font-bold text-center"></div>
          <button id="popup-close"
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">ปิด</button>
      </div>
  </div>

  <!-- ฟอร์มตรงกลาง -->
  <div class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-xl shadow-md w-[500px] relative">
      <h2 class="text-center text-[30px] font-bold text-gray-800 mb-4">ELO_Generate</h2>

      <form id="eloForm" method="POST" class="space-y-4">
        @csrf
        <!-- รายวิชา -->
        <div>
          <label for="course" class="block font-semibold text-gray-700 mb-1">เลือกรายวิชา :</label>
          <select id="course" name="course"
            class="w-full p-2 border border-gray-300 rounded-md">
            <option value="" disabled selected>-- กรุณาเลือกรายวิชา --</option>
            @foreach ($courses as $course)
              <option value="{{ $course->course_id }}"
                data-coursename="{{ $course->course_name }}"
                data-year="{{ $course->year }}"
                data-term="{{ $course->term }}"
                data-TQF="{{ $course->TQF }}"
                data-detail="{{ $course->course_detail_th }}"
                data-coursetext="{{ $course->course_text }}">
                {{ $course->course_id }} - {{ $course->course_name }} (มคอ{{$course->TQF}} ภาคเรียน{{$course->term}}/{{ $course->year }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- รายละเอียด -->
        <div>
          <label for="prompt" class="block font-semibold text-gray-700 mb-1">รายละเอียดรายวิชา :</label>
          <textarea id="prompt" name="prompt" rows="4"
              placeholder="กรอกรายละเอียดรายวิชาที่ต้องการให้ AI สร้าง..."
              class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200"></textarea>
        </div>

        <!-- เลือกจำนวน CLO -->
        <div>
          <label for="numClo" class="block font-semibold text-gray-700 mb-1">จำนวน CLO ที่ต้องการ :</label>
          <select id="numClo" name="numClo"
              class="w-32 p-2 border border-gray-300 rounded-md">
            @for ($i = 1; $i <= 5; $i++)
              <option value="{{ $i }}">{{ $i }}</option>
            @endfor
          </select>
        </div>

        <!-- เลือก PLO -->
        <div id="selectPloboxes" class="mt-4">
          <div class="flex flex-wrap gap-4">
            @foreach($plos as $selectPlo)
              <label class="flex items-center space-x-2">
                <input type="checkbox" name="selectPlo[]" value="{{ $selectPlo->plo }}"
                       class="rounded selectPlo">
                <span>PLO {{ $selectPlo->plo }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <!-- ปุ่ม Generate -->
        <button type="button" onclick="openPreview()"
          class="w-full inline-flex items-center justify-center whitespace-nowrap bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-md shadow-md hover:shadow-lg transition">
          Generate
        </button>
      </form>
    </div>
  </div>

  <!-- Modal Preview -->
  <div id="previewModal"
       class="fixed inset-0 bg-gray-100 hidden flex items-center justify-center z-50"
       onclick="closeOnBackground(event)">
    <div class="bg-white w-[500px] rounded-lg shadow-lg p-6"
         onclick="event.stopPropagation()">
      <h3 class="text-lg font-bold mb-3">ยืนยันข้อมูลที่กรอก</h3>
      <div id="previewContent" class="space-y-2 text-sm text-gray-700"></div>
      <div class="flex justify-end gap-3 mt-4">
        <button onclick="closePreview()"
            class="px-4 py-2 bg-red-700 hover:bg-red-400 text-white rounded-md">ยกเลิก</button>
        <button onclick="submitForm()"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">ยืนยัน</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/json5@2.2.3/dist/index.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const courseId = params.get('course_id');
    const year = params.get('year');
    const term = params.get('term');
    const TQF = params.get('TQF');

    const courseSelect = document.getElementById('course');

    if (courseSelect && courseId) {
        // เลือก option ที่ตรง courseId + year + term + TQF
        const option = Array.from(courseSelect.options).find(opt =>
          opt.value.toString() === courseId.toString() &&
          opt.getAttribute('data-year').toString() === year.toString() &&
          opt.getAttribute('data-term').toString() === term.toString() &&
          opt.getAttribute('data-TQF').toString() === TQF.toString()
        );
        if (option) {
          option.selected = true;
          fetchPrompt(
              courseId,
              option.getAttribute('data-year'),
              option.getAttribute('data-term'),
              option.getAttribute('data-TQF')
          );
        }
    }
});

document.getElementById('course').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const courseId = this.value;
    const year = selectedOption.getAttribute('data-year');
    const term = selectedOption.getAttribute('data-term');
    const TQF = selectedOption.getAttribute('data-TQF');

    if (!courseId || !year) return;

    fetchPrompt(courseId, year, term, TQF);
});

function fetchPrompt(courseId, year, term, TQF) {
    if (!courseId || !year) return;

    fetch('/getprompt', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ course_id: courseId, year: year, term: term, TQF: TQF })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('prompt').value = data.prompt || '';
    })
    .catch(err => console.error(err));
}

function showPopup(message) {
    document.getElementById('popup-message').textContent = message;
    document.getElementById('popup-modal').classList.remove('hidden');
}
document.getElementById('popup-close').onclick = function() {
  document.getElementById('popup-modal').classList.add('hidden');
};

// Preview Modal
function openPreview() {
  let prompt = document.getElementById("prompt").value.trim();
  let courseSelect = document.getElementById("course");
  let numClo = document.getElementById("numClo").value;
  let ploChecked = Array.from(document.querySelectorAll('.selectPlo:checked'));
  let ploLabels = ploChecked.map(cb => {
    return cb.parentElement.querySelector('span').textContent.trim();
  });

  if (!courseSelect.value) { showPopup("กรุณาเลือกรายวิชา"); return; }
  if (!prompt) { showPopup("กรุณากรอกรายละเอียดรายวิชา"); return; }
  if (!numClo) { showPopup("กรุณาเลือกจำนวน CLO ที่ต้องการ"); return; }
  if (ploChecked.length === 0) { showPopup("กรุณาเลือกอย่างน้อย 1 PLO"); return; }

  document.getElementById("previewModal").classList.remove("hidden");
  // ดึงค่าจาก option ที่เลือก
  let selectedOption = courseSelect.options[courseSelect.selectedIndex];
  let selectedText = selectedOption.text.replace(/\(.*?\)/, "").trim();
  let year = selectedOption.getAttribute('data-year');
  let term = selectedOption.getAttribute('data-term');
  let TQF  = selectedOption.getAttribute('data-TQF');

  // preview main info
  let content = `
    <p><b>รายวิชา:</b> ${selectedText}</p>
    <p><b>ปีการศึกษา:</b> ${year}</p>
    <p><b>ภาคเรียน:</b> ${term}</p>
    <p><b>มคอ:</b> ${TQF}</p>
    <p><b>รายละเอียด:</b> ${prompt}</p>
    <p><b>จำนวน CLO:</b> ${numClo}</p>
    <p><b>PLO ที่เลือก:</b> ${ploLabels.join(', ')}</p>
  `;

  document.getElementById("previewContent").innerHTML = content;
}
function closePreview() { document.getElementById("previewModal").classList.add("hidden"); }
function closeOnBackground(event) { if (event.target.id === "previewModal") closePreview(); }

let aiCallCount = 0;

function submitForm() {
    let courseSelect = document.getElementById("course");
    let prompt = document.getElementById("prompt").value.trim();
    let numClo = document.getElementById("numClo").value;
    let ploChecked = Array.from(document.querySelectorAll('.selectPlo:checked'));
    let ploLabels = ploChecked.map(cb => cb.parentElement.querySelector('span').textContent.trim());

    // ดึงค่า course ที่เลือก
    let selectedOption = courseSelect.options[courseSelect.selectedIndex];
    let courseId = courseSelect.value;
    let coursename = selectedOption.getAttribute('data-coursename');
    let year = parseInt(selectedOption.getAttribute('data-year'), 10);
    let term = selectedOption.getAttribute('data-term');
    let TQF  = selectedOption.getAttribute('data-TQF');
    let selectedText = selectedOption.text.replace(/\(.*?\)/, "").trim();

    if (!courseId || !prompt) {
        alert("กรุณากรอกข้อมูลให้ครบ");
        return;
    }
    
    fetch('/saveprompt', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ course_id: courseId, year, term, TQF, prompt })
    })
    .then(res => res.json())
    .then(data => {
        //เรียก AI
        return fetch('/generate', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ courseId, prompt, coursename, numClo, ploLabels, year, term, TQF })
        },aiCallCount++);
    })
    .then(res => res.json())
    .then(data => { 
        // ดึงข้อความ AI
        let generatedText = data.response?.choices?.[0]?.text || '';
        console.log("Prompt string:", data.prompt_string);
        console.log("AI generated text:", generatedText);

        // พยายามจับ JSON/HJSON block ไม่ว่าจะอยู่ใน ```json หรือไม่
        const jsonMatch = generatedText.match(/```json([\s\S]*?)```/i) || generatedText.match(/\{[\s\S]*\}/);
        let jsonData = null;

        if (jsonMatch) {
            let jsonString = jsonMatch[1] ? jsonMatch[1].trim() : jsonMatch[0].trim();
            let cloCounter = 0;
            // ตัด ```json ออก
            jsonString = jsonString.replace(/^```json/i, '').replace(/```$/, '').trim();

            jsonString = jsonString
                .replace(/["“”]/g, '"')
                .replace(/['‘’]/g, "'")
                .replace(/\n\s*/g, ' ')
                .replace(/\s{2,}/g, ' ')
                .replace(/[“”]/g, '"')
                .replace(/[‘’]/g, "'")
                .replace(/\r?\n/g, ' ')
                .replace(/:\s*([A-Za-zก-๙0-9_]+)\s*([,}\]])/g, ': "$1"$2')
                .replace(/,\s*(?=[}\]])/g, '')
                .replace(/"([^"]*?)'([^"]*?)"/g, (m, g1, g2) => `"${g1}\\'"${g2}"`)
                .replace(/:\s*"([^"]*?)'([^"]*?)"/g, (m, g1, g2) => `: "${g1}\\'${g2}"`)
                .replace(/\\'"+s/g, "'s")
                .replace(/\\'"/g, "'")
                .replace(/"\\'s/g, "'s")
                .replace(/}\s*,\s*{\s*"/g, (match) => {
                    cloCounter++;
                    if (cloCounter <= numClo) {
                        return `}, "CLO ${cloCounter}": { "`;
                    }
                    return match;
                })
                .replace(
                    /"Assessment Method"\s*:\s*([^,\]}]+)/g,
                    (m, g1) => `"Assessment Method": "${g1.trim().replace(/[)\s]+$/g, '')}"`
                )
                .replace(/\)\s*,/g, ',')
                .replace(/\)\s*}/g, '}')
                .replace(/""([^"]+?)""/g, '"$1"')
                .replace(/"(\s*)"เหตุผล":/g, '", "เหตุผล":')
                .replace(/,\s*}/g, '}')
                .trim();

              if (!jsonString.startsWith('{')) jsonString = '{' + jsonString;
              if (!jsonString.endsWith('}')) jsonString = jsonString + '}';
              for (const key in jsonData) {
                if (jsonData[key]["Learning’s Level"]) {
                    jsonData[key]["Learning's Level"] = jsonData[key]["Learning’s Level"];
                    delete jsonData[key]["Learning’s Level"];
                }
              }

            try {
                jsonData = JSON5.parse(jsonString);
                console.log("✅ Parsed JSON5:", jsonData);
            } catch (e) {
                console.error("❌ JSON5 parse error:", e.message);
                console.log("💬 Raw string after normalization:", jsonString);
                jsonData = null; // fallback เก็บ text แทน
            }
        } else {
            console.warn("⚠️ ไม่พบ JSON/HJSON ในข้อความ AI");
        }

        const aiResponseToSave = jsonData ? JSON.stringify(jsonData, null, 2) : generatedText;

        return fetch('/generate/save', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                course_id: courseId,
                year,
                term,
                TQF,
                ai_response: aiResponseToSave
            })
        })
        .then(res => res.json())
        .then(saveData => {
            console.log("AI ถูกเรียกทั้งหมด:", aiCallCount);
        })
        .catch(err => console.error(err));
    })
    .catch(err => console.error(err));
}

// จำกัดจำนวนการเลือก PLO ตามจำนวน CLO ที่เลือก
document.getElementById('numClo').addEventListener('change', function() {
  updatePloCheckboxLimit();
});
function updatePloCheckboxLimit() {
  const max = parseInt(document.getElementById('numClo').value, 10) || 1;
  const checkboxes = document.querySelectorAll('.selectPlo');
  let checkedCount = 0;
  checkboxes.forEach(cb => {
    if (cb.checked) checkedCount++;
  });
  // ถ้าเลือกเกิน max ให้ uncheck
  if (checkedCount > max) {
    let count = 0;
    checkboxes.forEach(cb => {
      if (cb.checked) {
        count++;
        if (count > max) cb.checked = false;
      }
    });
  }
  // ปิดการเลือกถ้าเกิน max
  checkboxes.forEach(cb => {
    if (!cb.checked && document.querySelectorAll('.selectPlo:checked').length >= max) {
      cb.disabled = true;
    } else {
      cb.disabled = false;
    }
  });
}
// เรียกครั้งแรก
updatePloCheckboxLimit();
// อัปเดตเมื่อมีการคลิก checkbox
document.querySelectorAll('.selectPlo').forEach(cb => {
  cb.addEventListener('change', updatePloCheckboxLimit);
});
</script>
</body>
</html>
