<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ELO_Generator - Manage PLOs</title>
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container .select2-selection--single {
                height: 42px;
                border-color: #d1d5db;
                border-radius: 0.375rem;
                display: flex;
                align-items: center;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 40px;
            }
        </style>
    </head>
    <body>
        @include('component.navbar')

        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4">จัดการ PLOs (ค้นหาตามปีหลักสูตร)</h1>

            <form method="GET" action="{{ url()->current() }}" class="mb-6">
                <label for="year_select" class="block text-sm font-medium text-gray-700 mb-2">เลือกปีหลักสูตร</label>
                <select id="year_select" name="year" class="w-full md:w-1/2">
                    <option value="">-- เลือกปี พ.ศ. --</option>
                    @foreach($curriculum as $cur)
                        <option value="{{ $cur->curriculum_year }}" {{ (isset($selectedYear) && $selectedYear == $cur->curriculum_year) ? 'selected' : '' }}>
                            ปี {{ $cur->curriculum_year }} : {{ $cur->curriculum_name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <hr class="mb-6 border-gray-300">

            @if(isset($selectedYear) && $selectedYear)
                
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">รายการ PLOs ของหลักสูตรปี {{ $selectedYear }}</h2>
                    <button id="add-plo-btn" type="button" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow transition">
                        + เพิ่ม PLO
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 bg-white shadow-sm rounded-lg overflow-hidden">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 w-20 text-center">PLO</th>
                                <th class="border px-4 py-2 text-left">Description</th>
                                <th class="border px-4 py-2 w-40 text-left">Domain</th>
                                <th class="border px-4 py-2 w-48 text-left">Learning Level</th>
                                <th class="border px-4 py-2 w-40 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plos as $plo)
                            <tr data-id="{{ $plo->id }}" 
                                data-plo-num="{{ $plo->plo }}" 
                                data-domain="{{ $plo->domain ?? '' }}" 
                                data-level="{{ $plo->learning_level ?? '' }}" 
                                class="hover:bg-gray-50 transition">
                                <td class="px-4 py-2 text-center font-bold text-gray-700">
                                    {{ $plo->plo }}
                                </td>
                                <td class="px-4 py-2">
                                    <div class="desc-text whitespace-pre-wrap">{{ $plo->description }}</div>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $plo->domain }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $plo->learning_level }}
                                </td>
                                <td class="px-4 py-2 text-center space-x-1">
                                    <button type="button" class="edit-btn bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition">แก้ไข</button>
                                    <button type="button" class="delete-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">ลบ</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    ยังไม่มีข้อมูล PLO ในปี {{ $selectedYear }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @else
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p class="font-bold">คำแนะนำ</p>
                    <p>กรุณาเลือกปีการศึกษาจากช่องด้านบนเพื่อจัดการข้อมูล PLO</p>
                </div>
            @endif
        </div>

        @if(isset($selectedYear))
            @php
                $existingPlos = $plos->pluck('plo')->toArray();
                $maxPlo = count($existingPlos) > 0 ? max($existingPlos) : 0;
                $missingPlos = [];
                for ($i = 1; $i <= $maxPlo; $i++) {
                    if (!in_array($i, $existingPlos)) $missingPlos[] = $i;
                }
                $nextPloNumber = $maxPlo + 1;
            @endphp
            
            <div id="add-plo-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden backdrop-blur-sm">
                <div class="bg-white rounded-lg shadow-2xl p-6 min-w-[400px] max-w-lg w-full">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">เพิ่ม PLO ใหม่ (ปี {{ $selectedYear }})</h2>
                    
                    <div class="mb-3">
                        <label class="block mb-1 font-semibold text-gray-700">PLO No.</label>
                        <select id="new-plo" class="border px-3 py-2 w-full rounded bg-white focus:ring-2 focus:ring-blue-300 outline-none">
                            @if(count($missingPlos) > 0)
                                <optgroup label="เลขที่ขาดหายไป">
                                    @foreach($missingPlos as $missing)
                                        <option value="{{ $missing }}" class="text-red-600 font-semibold">PLO {{ $missing }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            <optgroup label="ลำดับถัดไป">
                                <option value="{{ $nextPloNumber }}" selected class="text-green-600 font-bold">PLO {{ $nextPloNumber }} (ใหม่)</option>
                            </optgroup>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="block mb-1 font-semibold text-gray-700">Description <span class="text-red-500">*</span></label>
                        <textarea id="new-desc" class="border px-3 py-2 w-full rounded focus:ring-2 focus:ring-blue-300 outline-none" rows="4"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-1 font-semibold text-gray-700">Domain</label>
                            <input type="text" id="new-domain" class="border px-3 py-2 w-full rounded focus:ring-2 focus:ring-blue-300 outline-none">
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold text-gray-700">Level</label>
                            <input type="text" id="new-level" class="border px-3 py-2 w-full rounded focus:ring-2 focus:ring-blue-300 outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button id="add-plo-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">ยกเลิก</button>
                        <button id="add-plo-save" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded shadow">บันทึก</button>
                    </div>
                </div>
            </div>

            <div id="edit-plo-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden backdrop-blur-sm">
                <div class="bg-white rounded-lg shadow-2xl p-6 min-w-[400px] max-w-lg w-full">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">แก้ไข PLO</h2>
                    
                    <input type="hidden" id="edit-id">

                    <div class="mb-3">
                        <label class="block mb-1 font-semibold text-gray-700">PLO No.</label>
                        <input id="edit-plo-num" type="number" class="border px-3 py-2 w-full rounded bg-gray-100 text-gray-600 cursor-not-allowed" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="block mb-1 font-semibold text-gray-700">Description</label>
                        <textarea id="edit-desc" class="border px-3 py-2 w-full rounded focus:ring-2 focus:ring-yellow-300 outline-none" rows="4"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-1 font-semibold text-gray-700">Domain</label>
                            <input type="text" id="edit-domain" class="border px-3 py-2 w-full rounded focus:ring-2 focus:ring-yellow-300 outline-none">
                        </div>
                        <div>
                            <label class="block mb-1 font-semibold text-gray-700">Level</label>
                            <input type="text" id="edit-level" class="border px-3 py-2 w-full rounded focus:ring-2 focus:ring-yellow-300 outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button id="edit-plo-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">ยกเลิก</button>
                        <button id="edit-plo-save" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded shadow">บันทึกการแก้ไข</button>
                    </div>
                </div>
            </div>
        @endif

        <div id="popup-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-[60] hidden">
            <div class="bg-white rounded shadow-lg p-6 min-w-[300px] text-center transform transition-all scale-100">
                <div id="popup-message" class="mb-4 text-lg font-bold"></div>
                <button id="popup-close" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded shadow">ตกลง</button>
            </div>
        </div>

        <div id="confirm-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-[60] hidden">
            <div class="bg-white rounded shadow-lg p-6 min-w-[300px] text-center">
                <div class="mb-4 text-lg font-semibold text-gray-800" id="confirm-message">ยืนยันการทำรายการ?</div>
                <div class="flex justify-center space-x-3">
                    <button id="confirm-cancel" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">ยกเลิก</button>
                    <button id="confirm-ok" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">ยืนยัน</button>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <script>
            // 1. Setup Select2
            $(document).ready(function() {
                $('#year_select').select2({
                    placeholder: "-- เลือกปี พ.ศ. --",
                    width: '100%',
                    minimumResultsForSearch: Infinity 
                }).on('select2:select', function (e) {
                    $(this).closest('form').submit();
                });
            });

            // 2. Helper Functions (จัดการ Modal)
            const toggleModal = (id, show) => {
                const el = document.getElementById(id);
                if (el) {
                    if(show) el.classList.remove('hidden');
                    else el.classList.add('hidden');
                } else {
                    console.error('ไม่พบ Modal ID: ' + id);
                }
            };

            const showPopup = (msg, isSuccess = false) => {
                const popupMsg = document.getElementById('popup-message');
                if(popupMsg) {
                    popupMsg.textContent = msg;
                    // เปลี่ยนสีข้อความตามผลลัพธ์
                    popupMsg.className = isSuccess ? 'mb-4 text-lg text-green-600 font-bold' : 'mb-4 text-lg text-red-600 font-bold';
                }
                toggleModal('popup-modal', true);
            };

            const closePopupBtn = document.getElementById('popup-close');
            if(closePopupBtn) closePopupBtn.onclick = () => toggleModal('popup-modal', false);

            let confirmCallback = null;
            const showConfirm = (msg, cb) => {
                document.getElementById('confirm-message').textContent = msg;
                confirmCallback = cb;
                toggleModal('confirm-modal', true);
            };
            
            const confirmOkBtn = document.getElementById('confirm-ok');
            const confirmCancelBtn = document.getElementById('confirm-cancel');
            if(confirmOkBtn) {
                confirmOkBtn.onclick = () => {
                    toggleModal('confirm-modal', false);
                    if(confirmCallback) confirmCallback();
                };
            }
            if(confirmCancelBtn) {
                confirmCancelBtn.onclick = () => {
                    toggleModal('confirm-modal', false);
                    confirmCallback = null;
                };
            }

            // 3. Main Logic (ทำงานเมื่อโหลดหน้าเสร็จ)
            document.addEventListener('DOMContentLoaded', function() {
                
                @if(isset($selectedYear))
                const CURRENT_YEAR = "{{ $selectedYear }}";

                // --- LOGIC: ADD PLO ---
                const addBtn = document.getElementById('add-plo-btn');
                const addCancel = document.getElementById('add-plo-cancel');
                const addSave = document.getElementById('add-plo-save');

                if(addBtn) addBtn.onclick = () => toggleModal('add-plo-modal', true);
                if(addCancel) addCancel.onclick = () => toggleModal('add-plo-modal', false);

                if(addSave) {
                    addSave.onclick = () => {
                        const ploInput = document.getElementById('new-plo');
                        const descInput = document.getElementById('new-desc');
                        const domainInput = document.getElementById('new-domain');
                        const levelInput = document.getElementById('new-level');

                        const ploNum = ploInput ? ploInput.value : '';
                        const desc = descInput ? descInput.value.trim() : '';
                        const domain = domainInput ? domainInput.value.trim() : '';
                        const level = levelInput ? levelInput.value.trim() : '';

                        if(!desc) { showPopup('กรุณากรอก Description'); return; }

                        axios.post('/management/plos/create', {
                            curriculum_year_ref: CURRENT_YEAR,
                            plo: ploNum,
                            description: desc,
                            domain: domain,
                            learning_level: level,
                            _token: '{{ csrf_token() }}'
                        })
                        .then(res => {
                            showPopup('บันทึกข้อมูลสำเร็จ', true);
                            setTimeout(() => location.reload(), 1500);
                        })
                        .catch(err => {
                            console.error(err);
                            showPopup('เกิดข้อผิดพลาด: ' + (err.response?.data?.message || err.message));
                        });
                    };
                }

                // --- LOGIC: EDIT & DELETE (Event Delegation) ---
                document.addEventListener('click', function(e) {
                    
                    // 1. ปุ่ม EDIT
                    const editBtn = e.target.closest('.edit-btn');
                    if (editBtn) {
                        e.preventDefault(); // ป้องกันการทำงานซ้ำซ้อน
                        const tr = editBtn.closest('tr');
                        if(!tr) return;

                        // ดึงข้อมูลจาก Data Attribute
                        const id = tr.dataset.id;
                        const ploNum = tr.dataset.ploNum;
                        const domain = tr.dataset.domain;
                        const level = tr.dataset.level;
                        
                        // ดึง Description
                        const descEl = tr.querySelector('.desc-text');
                        const desc = descEl ? descEl.innerText : tr.children[1].innerText.trim();

                        // ตรวจสอบว่ามี Input ID เหล่านี้จริงหรือไม่
                        const editIdInput = document.getElementById('edit-id');
                        const editPloInput = document.getElementById('edit-plo-num');
                        const editDescInput = document.getElementById('edit-desc');
                        const editDomainInput = document.getElementById('edit-domain');
                        const editLevelInput = document.getElementById('edit-level');

                        if(editIdInput) editIdInput.value = id;
                        if(editPloInput) editPloInput.value = ploNum;
                        if(editDescInput) editDescInput.value = desc;
                        if(editDomainInput) editDomainInput.value = domain;
                        if(editLevelInput) editLevelInput.value = level;

                        toggleModal('edit-plo-modal', true);
                        return;
                    }

                    // 2. ปุ่ม DELETE
                    const deleteBtn = e.target.closest('.delete-btn');
                    if (deleteBtn) {
                        e.preventDefault();
                        const tr = deleteBtn.closest('tr');
                        const id = tr.dataset.id;
                        const ploNum = tr.dataset.ploNum;

                        showConfirm(`ต้องการลบ PLO ${ploNum} หรือไม่?`, () => {
                            axios.delete(`/management/plos/delete/${id}`, {
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            })
                            .then(res => {
                                showPopup('ลบข้อมูลสำเร็จ', true);
                                tr.remove();
                            })
                            .catch(err => {
                                console.error(err);
                                showPopup('เกิดข้อผิดพลาดในการลบ');
                            });
                        });
                        return;
                    }
                });

                // --- LOGIC: SAVE EDIT ---
                const editCancel = document.getElementById('edit-plo-cancel');
                const editSave = document.getElementById('edit-plo-save');

                if(editCancel) editCancel.onclick = () => toggleModal('edit-plo-modal', false);

                if(editSave) {
                    editSave.onclick = () => {
                        const id = document.getElementById('edit-id').value;
                        const desc = document.getElementById('edit-desc').value.trim();
                        const domain = document.getElementById('edit-domain').value.trim();
                        const level = document.getElementById('edit-level').value.trim();

                        showConfirm('ต้องการบันทึกการแก้ไขหรือไม่?', () => {
                            axios.post(`/management/plos/update/${id}`, { 
                                description: desc,
                                domain: domain,
                                learning_level: level,
                                _token: '{{ csrf_token() }}'
                            })
                            .then(res => {
                                showPopup('แก้ไขข้อมูลสำเร็จ', true);
                                setTimeout(() => location.reload(), 1500);
                            })
                            .catch(err => {
                                console.error(err);
                                showPopup('เกิดข้อผิดพลาดในการแก้ไข: ' + (err.response?.data?.message || err.message));
                            });
                        });
                    };
                }
                @endif
            });
        </script>
    </body>
</html>