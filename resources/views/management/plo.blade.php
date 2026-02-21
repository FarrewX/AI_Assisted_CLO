<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>ELO_Generator - Manage PLOs</title>
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/plo.js'])
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
    <body data-current-year="{{ $selectedYear ?? '' }}">
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
                                <th class="border px-4 py-2 w-40 text-left">Specific LO</th>
                                <th class="border px-4 py-2 w-40 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plos as $plo)
                            <tr data-id="{{ $plo->id }}" 
                                data-plo-num="{{ $plo->plo }}" 
                                data-domain="{{ $plo->domain ?? '' }}" 
                                data-level="{{ $plo->learning_level ?? '' }}" 
                                data-specific-lo="{{ $plo->specific_lo ? '1' : '0' }}"
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
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    @if($plo->specific_lo)
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Specific LO</span>
                                    @else
                                        <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Generic LO</span>
                                    @endif
                                <td class="px-4 py-2 text-center space-x-1">
                                    <button type="button" class="edit-btn text-amber-500 hover:text-amber-700 transition p-1 rounded-md hover:bg-amber-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 25 25" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button type="button" class="delete-btn text-red-500 hover:text-red-600 transition p-1 rounded-md hover:bg-red-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
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
                        <div class="flex items-center gap-2 pt-6">
                            <label for="new-specific-lo" class="font-semibold text-gray-700 cursor-pointer select-none">Specific LO</label>
                            <input type="radio" id="new-specific-lo" name="lo_type" value="1" class="w-5 h-5 cursor-pointer">
                        </div>
                        <div class="flex items-center gap-2 pt-6">
                            <label for="new-generic-lo" class="font-semibold text-gray-700 cursor-pointer select-none">Generic LO</label>
                            <input type="radio" id="new-generic-lo" name="lo_type" value="0" class="w-5 h-5 cursor-pointer">
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

                    <div class="flex items-center gap-4 pt-6">
                        <div class="flex items-center gap-2">
                            <input type="radio" id="edit-specific-lo" name="lo_type" value="1" class="border w-5 h-5 cursor-pointer focus:ring-2 focus:ring-blue-300">
                            <label for="edit-specific-lo" class="font-semibold text-gray-700 cursor-pointer select-none mr-4">
                                Specific LO
                            </label>

                            <input type="radio" id="edit-generic-lo" name="lo_type" value="0" class="border w-5 h-5 cursor-pointer focus:ring-2 focus:ring-blue-300">
                            <label for="edit-generic-lo" class="font-semibold text-gray-700 cursor-pointer select-none">
                                Generic LO
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button id="edit-plo-cancel" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded">ยกเลิก</button>
                        <button id="edit-plo-save" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded shadow">บันทึกการแก้ไข</button>
                    </div>
                </div>
            </div>
        @endif

        <div id="popup-modal" class="fixed inset-0 z-[60] flex items-center justify-center hidden bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300">
            <div class="relative w-full max-w-sm p-6 mx-4 bg-white shadow-2xl rounded-2xl transform transition-all scale-100">
                
                <div id="popup-icon-container" class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-4 transition-colors duration-300">
                    
                    <svg id="icon-success" class="h-8 w-8 text-green-600 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    
                    <svg id="icon-error" class="h-8 w-8 text-red-600 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>

                <div class="text-center">
                    <h3 id="popup-title" class="text-lg font-bold text-gray-900 mb-1">แจ้งเตือน</h3>
                    
                    <p id="popup-message" class="text-gray-500 text-sm mb-6"></p>
                    
                    <button id="popup-close" class="w-full inline-flex justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-all duration-200">
                        ตกลง
                    </button>
                </div>
            </div>
        </div>

        <div id="confirm-modal" class="fixed inset-0 z-[60] flex items-center justify-center hidden bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300">
            <div class="relative w-full max-w-sm p-6 mx-4 bg-white shadow-2xl rounded-2xl transform transition-all scale-100">
                
                <div class="flex flex-col items-center text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 mb-4">
                        <svg class="h-8 w-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-2">ยืนยันการทำรายการ</h3>
                    <p id="confirm-message" class="text-sm text-gray-500 mb-6">คุณแน่ใจหรือไม่ที่จะดำเนินการนี้? การกระทำนี้อาจไม่สามารถย้อนกลับได้</p>

                    <div class="flex gap-3 w-full">
                        <button id="confirm-cancel" class="flex-1 rounded-xl bg-white border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 hover:text-gray-900 transition-all duration-200">
                            ยกเลิก
                        </button>
                        <button id="confirm-ok" class="flex-1 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 hover:shadow-md transition-all duration-200">
                            ยืนยัน
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    </body>
</html>
