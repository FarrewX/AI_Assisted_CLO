<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Manage LLLs</title>
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/lll.js'])
        @endif

        <style>
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        </style>
    </head>
    <body data-current-year="{{ $selectedYear ?? '' }}" class="bg-[#f8fafc] text-gray-800 font-sans antialiased min-h-screen">
        @include('component.navbar')
        <div class="container mx-auto p-4 pt-24 max-w-6xl">
            <div class="mb-8 fixed top-23 left-5 flex gap-3 z-50">
                <button onclick="window.location.href='/management/curriculum'" class="group inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-blue-300 hover:bg-blue-50 text-gray-600 rounded-xl shadow-sm transition-all duration-300">
                    <div class="p-1 bg-gray-100 rounded-md group-hover:bg-blue-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 group-hover:text-blue-700 text-gray-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    </div>
                    <span class="font-medium">กลับไปหน้าจัดการหลักสูตร</span>
                </button>
            </div>

            <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">จัดการข้อมูล LLLs</h1>
                        <p class="text-sm text-gray-500 mt-1">จัดการข้อมูลปรัชญาของหลักสูตรตามปีการศึกษา</p>
                    </div>
                </div>

                <form method="GET" action="{{ url()->current() }}" class="relative w-full md:w-72 group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none transition-colors duration-300 group-hover:text-indigo-600 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <select id="year_select" name="year" onchange="this.form.submit()"
                        class="bg-white border-2 border-gray-100 text-gray-700 text-sm font-bold rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-11 pr-10 py-3 shadow-sm hover:border-indigo-300 transition-all duration-300 cursor-pointer appearance-none">
                        <option value="" hidden>-- เลือกปีหลักสูตร --</option>
                        @foreach($curriculum as $cur)
                            <option value="{{ $cur->curriculum_year }}" {{ (isset($selectedYear) && $selectedYear == $cur->curriculum_year) ? 'selected' : '' }}>
                                หลักสูตรปี {{ $cur->curriculum_year }}
                            </option>
                        @endforeach
                    </select>

                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-hover:text-indigo-500 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </form>
            </div>

            @if(isset($selectedYear) && $selectedYear)
                
                <div class="flex justify-between items-center mb-4 px-2">
                    <h2 class="text-lg font-bold text-gray-800">รายการ LLLs ปี {{ $selectedYear }}</h2>
                    <button id="add-lll-btn" type="button" 
                        style="background: linear-gradient(to right, #22c55e, #10b981); cursor: pointer;"
                        class="text-white px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 flex items-center gap-2 font-medium text-sm border-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>เพิ่ม LLL ใหม่</span>
                    </button>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto no-scrollbar">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-30">LLL No.</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-40">ชื่อทักษะ</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-40">สอดคล้องกับ PLO</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-28">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($llls as $lll)
                                <tr data-id="{{ $lll->id }}" 
                                    data-lll-num="{{ $lll->num_LLL }}" 
                                    data-desc="{{ $lll->name_LLL }}"
                                    data-check-lll="{{ $lll->check_LLL }}"
                                    class="hover:bg-indigo-50/50 transition duration-150">
                                    
                                    <td class="px-6 py-4 text-center font-black text-indigo-600 text-lg">
                                        LLL {{ $lll->num_LLL }}
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $lll->name_LLL }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-left">
                                        @if(!empty($lll->check_LLL))
                                            <div class="flex flex-wrap justify-center gap-1.5">
                                                @foreach(explode(',', $lll->check_LLL) as $plo_num)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                                        PLO {{ trim($plo_num) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded-md">- ไม่ได้ระบุ -</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                        <button type="button" class="edit-btn text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 p-2 rounded-lg transition" title="แก้ไข">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button type="button" class="delete-btn text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="ลบ">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                            <span class="text-base font-medium">ยังไม่มีข้อมูล LLLs ในปี {{ $selectedYear }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white border border-gray-200 border-dashed rounded-3xl p-12 text-center flex flex-col items-center justify-center min-h-[400px]">
                    <div class="bg-indigo-50 p-6 rounded-full mb-6">
                        <svg class="w-16 h-16 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ยังไม่ได้เลือกหลักสูตร</h3>
                    <p class="text-gray-500 max-w-md mx-auto">กรุณาเลือกปีการศึกษาจากเมนูด้านบน เพื่อดึงข้อมูล LLLs ขึ้นมาจัดการ</p>
                </div>
            @endif
        </div>

        @if(isset($selectedYear))
            @php
                $existingLlls = $llls->pluck('num_LLL')->toArray();
                $maxLll = count($existingLlls) > 0 ? max($existingLlls) : 0;
                $missingLlls = [];
                for ($i = 1; $i <= $maxLll; $i++) {
                    if (!in_array($i, $existingLlls)) $missingLlls[] = $i;
                }
                $nextLllNumber = $maxLll + 1;
            @endphp
            
            <div id="add-lll-modal" class="fixed inset-0 flex items-center justify-center bg-gray-900/60 z-50 hidden backdrop-blur-sm transition-opacity opacity-0">
                <div class="bg-white rounded-2xl shadow-2xl p-8 min-w-[400px] max-w-lg w-full transform scale-95 transition-transform duration-300">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 border-b border-gray-100 pb-4">เพิ่ม LLL ใหม่</h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block mb-1.5 text-sm font-semibold text-gray-700">LLL No.</label>
                            <select id="new-lll" class="border border-gray-300 px-4 py-2.5 w-full rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors">
                                @if(count($missingLlls) > 0)
                                    <optgroup label="เลขที่ขาดหายไป">
                                        @foreach($missingLlls as $missing)
                                            <option value="{{ $missing }}" class="text-red-600 font-semibold">LLL {{ $missing }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                <optgroup label="ลำดับถัดไป">
                                    <option value="{{ $nextLllNumber }}" selected class="text-green-600 font-bold">LLL {{ $nextLllNumber }} (ใหม่)</option>
                                </optgroup>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block mb-1.5 text-sm font-semibold text-gray-700">ชื่อทักษะ <span class="text-red-500">*</span></label>
                            <textarea id="new-desc" class="border border-gray-300 px-4 py-3 w-full rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-colors" rows="4" placeholder="พิมพ์รายละเอียด..."></textarea>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-semibold text-gray-700">สอดคล้องกับ PLO ข้อใดบ้าง (เลือกได้มากกว่า 1 ข้อ)</label>
                        <div class="grid grid-cols-3 gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 max-h-40 overflow-y-auto">
                            @if(isset($plos) && count($plos) > 0)
                                @foreach($plos as $plo)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="add_check_plo[]" value="{{ $plo->plo }}" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 cursor-pointer">
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">PLO {{ $plo->plo }}</span>
                                    </label>
                                @endforeach
                            @else
                                <span class="text-sm text-red-500 col-span-3">กรุณาเพิ่มข้อมูล PLOs ในปีนี้ก่อน</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-8 mt-2">
                        <button id="add-lll-cancel" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-xl transition-colors">ยกเลิก</button>
                        <button id="add-lll-save" style="background: linear-gradient(to right, #4f46e5, #4338ca);" class="px-5 py-2.5 text-white font-medium rounded-xl shadow-md transition-colors flex items-center gap-2">บันทึกข้อมูล</button>
                    </div>
                </div>
            </div>

            <div id="edit-lll-modal" class="fixed inset-0 flex items-center justify-center bg-gray-900/60 z-50 hidden backdrop-blur-sm transition-opacity opacity-0">
                <div class="bg-white rounded-2xl shadow-2xl p-8 min-w-[400px] max-w-lg w-full transform scale-95 transition-transform duration-300">
                    <h2 class="text-xl font-bold mb-6 text-gray-800 border-b border-gray-100 pb-4">แก้ไข LLL</h2>
                    <input type="hidden" id="edit-id">

                    <div class="space-y-5">
                        <div>
                            <label class="block mb-1.5 text-sm font-semibold text-gray-700">LLL No.</label>
                            <input id="edit-lll-num" type="number" class="border border-gray-200 px-4 py-2.5 w-full rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed font-bold" readonly>
                        </div>
                        
                        <div>
                            <label class="block mb-1.5 text-sm font-semibold text-gray-700">ชื่อทักษะ <span class="text-red-500">*</span></label>
                            <textarea id="edit-desc" class="border border-gray-300 px-4 py-3 w-full rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none transition-colors" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-semibold text-gray-700">สอดคล้องกับ PLO ข้อใดบ้าง (เลือกได้มากกว่า 1 ข้อ)</label>
                        <div class="grid grid-cols-3 gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 max-h-40 overflow-y-auto">
                            @if(isset($plos) && count($plos) > 0)
                                @foreach($plos as $plo)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="edit_check_plo[]" value="{{ $plo->plo }}" class="w-4 h-4 text-amber-500 rounded border-gray-300 focus:ring-amber-500 cursor-pointer">
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-amber-600">PLO {{ $plo->plo }}</span>
                                    </label>
                                @endforeach
                            @else
                                <span class="text-sm text-red-500 col-span-3">กรุณาเพิ่มข้อมูล PLOs ในปีนี้ก่อน</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-8 mt-2">
                        <button id="edit-lll-cancel" class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-xl transition-colors">ยกเลิก</button>
                        <button id="edit-lll-save" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl shadow-md transition-colors flex items-center gap-2">บันทึกการแก้ไข</button>
                    </div>
                </div>
            </div>
        @endif

        <div id="popup-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
            <div class="relative w-full max-w-sm p-6 mx-4 bg-white shadow-2xl rounded-3xl transform transition-all duration-300 scale-95" id="popup-content">
                <div id="popup-icon-container" class="mx-auto flex h-20 w-20 items-center justify-center rounded-full mb-5">
                    <svg id="icon-success" class="h-10 w-10 text-green-500 hidden" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    <svg id="icon-error" class="h-10 w-10 text-red-500 hidden" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </div>
                <div class="text-center">
                    <h3 id="popup-title" class="text-2xl font-black text-gray-900 mb-2 tracking-tight">แจ้งเตือน</h3>
                    <p id="popup-message" class="text-gray-500 text-sm mb-6 font-medium"></p>
                    <button id="popup-close" class="w-full inline-flex justify-center rounded-xl bg-gray-900 hover:bg-gray-800 px-4 py-3.5 text-sm font-bold text-white shadow-md transition-all duration-200 focus:outline-none">
                        ตกลง
                    </button>
                </div>
            </div>
        </div>

        <div id="confirm-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden bg-gray-900/50 backdrop-blur-sm transition-opacity duration-300 opacity-0">
            <div class="relative w-full max-w-sm p-6 mx-4 bg-white shadow-2xl rounded-3xl transform transition-all duration-300 scale-95" id="confirm-content">
                <div class="flex flex-col items-center text-center">
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-100 mb-5 text-red-600">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2 tracking-tight">ยืนยันการลบ?</h3>
                    <p class="text-sm text-gray-500 mb-2 font-medium">การกระทำนี้ไม่สามารถย้อนกลับได้</p>

                    <div class="mb-4 p-3 bg-gray-50 rounded-xl border border-gray-200 w-full">
                        <p class="text-sm text-gray-500 mb-1">รายการที่จะลบ</p>
                        <p id="confirm-lll-num" class="text-center font-extrabold text-red-600 text-2xl">-</p>
                    </div>
                    <p id="confirm-message" class="text-sm text-gray-500 mb-6 font-medium">คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?</p>
                    <div class="flex gap-3 w-full">
                        <button id="confirm-ok" class="flex-1 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-3 text-sm font-bold text-white shadow-md transition-all duration-200">
                            ใช่, ลบเลย
                        </button>
                        <button id="confirm-cancel" class="flex-1 rounded-xl bg-gray-100 hover:bg-gray-200 px-4 py-3 text-sm font-bold text-gray-700 transition-all duration-200">
                            ยกเลิก
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>