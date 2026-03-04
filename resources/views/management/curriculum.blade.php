<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Curriculum | ELO Generator</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/curriculum.js'])
    @endif
</head>
<body class="font-sans antialiased text-slate-grey">
    @include('component.navbar')

    <div class="container mx-auto p-4 pt-24 max-w-7xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-deep-navy">จัดการหลักสูตร (Curriculum)</h1>
            <button id="btn-add" 
                data-route-store="{{ route('curriculum.store') }}"
                class="bg-royal-blue hover:bg-deep-navy text-pure-white px-5 py-2.5 rounded-xl shadow-sm flex items-center gap-2 transition transform hover:-translate-y-0.5 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                เพิ่มหลักสูตรใหม่
            </button>
            @if(session('success') || session('error'))
            <div id="toast-notification" class="fixed bottom-5 right-5 z-50 ...">
                </div>
            @endif
        </div>

        <div class="bg-pure-white rounded-2xl shadow-sm overflow-hidden border border-cloud-blue">
            <table class="min-w-full divide-y divide-cloud-blue">
                <thead class="bg-cloud-blue/30">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">ปีหลักสูตร</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider w-1/4">ชื่อหลักสูตร</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">คณะ / สาขา</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-deep-navy uppercase tracking-wider">สถานะข้อมูลย่อย</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-deep-navy uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="bg-pure-white divide-y divide-cloud-blue/50">
                    @forelse($curricula as $cur)
                        @php 
                           $isTrashed = $cur->trashed(); 
                            
                            $philosophyRecord = $cur->philosophyData;
                            
                            $hasPhilosophy = $philosophyRecord && (
                                !empty($philosophyRecord->mju_philosophy) || 
                                !empty($philosophyRecord->education_philosophy) || 
                                !empty($philosophyRecord->curriculum_philosophy)
                            ); 

                            $hasPlo = $cur->plos()->exists(); 
                            $hasLlls = !empty($cur->llls) && count($cur->llls) > 0;
                            
                            $hasWarning = !$hasPhilosophy || !$hasPlo || !$hasLlls;
                        @endphp

                        <tr class="{{ $isTrashed ? 'bg-cloud-blue/20' : 'hover:bg-cloud-blue/10' }} transition duration-150"
                            data-id="{{ $cur->id }}"
                            data-year="{{ $cur->curriculum_year }}"
                            data-name="{{ $cur->curriculum_name }}"
                            data-faculty="{{ $cur->faculty }}"
                            data-major="{{ $cur->major }}"
                            data-campus="{{ $cur->campus }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $isTrashed ? 'text-slate-grey/50 line-through' : 'text-royal-blue' }}">
                                {{ $cur->curriculum_year }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium {{ $isTrashed ? 'text-slate-grey/50 line-through' : 'text-deep-navy' }}">
                                {{ $cur->curriculum_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isTrashed ? 'text-slate-grey/50 line-through' : 'text-slate-grey' }}">
                                <div class="font-semibold text-deep-navy">{{ $cur->faculty }}</div>
                                <div class="text-xs">{{ $cur->major }} ({{ $cur->campus }})</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-col gap-1.5 text-xs font-medium">
                                    <span class="flex items-center gap-1 {{ $hasPhilosophy ? 'text-royal-blue' : 'text-red-500' }}">
                                        @if($hasPhilosophy)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        @endif
                                        ปรัชญา (Philosophy)
                                    </span>
                                    <span class="flex items-center gap-1 {{ $hasPlo ? 'text-royal-blue' : 'text-red-500' }}">
                                        @if($hasPlo)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        @endif
                                        PLOs
                                    </span>
                                    <span class="flex items-center gap-1 {{ $hasLlls ? 'text-royal-blue' : 'text-red-500' }}">
                                        @if($hasLlls)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        @endif
                                        LLLs
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                @if($isTrashed)
                                    <span class="text-red-400 italic font-normal text-xs uppercase tracking-wider">ยกเลิกการใช้งาน</span>
                                @else
                                    <button class="btn-sub-settings relative text-royal-blue hover:text-deep-navy bg-cloud-blue/30 hover:bg-cloud-blue/50 px-3 py-1 rounded-md transition border border-cloud-blue/50"
                                            title="จัดการข้อมูลย่อยของหลักสูตร"
                                            data-id="{{ $cur->id }}"
                                            data-year="{{ $cur->curriculum_year }}" 
                                            data-name="{{ $cur->curriculum_name }}"
                                            data-has-philosophy="{{ $hasPhilosophy ? 'true' : 'false' }}"
                                            data-has-plo="{{ $hasPlo ? 'true' : 'false' }}"
                                            data-has-llls="{{ $hasLlls ? 'true' : 'false' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        @if($hasWarning)
                                            <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] text-pure-white justify-center items-center font-bold">!</span>
                                            </span>
                                        @endif
                                    </button>

                                    <button class="btn-edit text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 py-1 rounded-md transition" title="แก้ไขข้อมูลหลัก">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>

                                    <button class="btn-delete text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition" title="ลบหลักสูตร">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-grey bg-cloud-blue/10">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-cloud-blue mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>ยังไม่มีข้อมูลหลักสูตร</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $curricula->links() }}
        </div>
    </div>

    <div id="sub-settings-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/60 z-50 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-pure-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform transition-all scale-100 border border-cloud-blue">
            <div class="flex justify-between items-start border-b border-cloud-blue pb-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-deep-navy">จัดการข้อมูลหลักสูตร <span id="modal-curriculum-year-text" class="text-royal-blue"></span></h2>
                    <p id="sub-settings-title" class="text-sm text-slate-grey font-medium mt-1"></p>
                </div>
                <button type="button" id="btn-close-sub-settings" class="text-slate-grey hover:text-red-500 transition bg-cloud-blue/30 hover:bg-cloud-blue rounded-full p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="space-y-4">
                <a href="#" id="link-manage-philosophy" class="group block p-4 border border-cloud-blue rounded-xl hover:border-royal-blue hover:shadow-md transition bg-pure-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-royal-blue/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-center relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="bg-cloud-blue/40 text-royal-blue p-2.5 rounded-lg group-hover:bg-royal-blue group-hover:text-pure-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-deep-navy group-hover:text-royal-blue transition-colors">ปรัชญาหลักสูตร (Philosophy)</h3>
                                <p class="text-xs text-slate-grey mt-0.5">เพิ่ม/ลบ/แก้ไข ปรัชญาหลักสูตร</p>
                            </div>
                        </div>
                        <div id="status-philosophy"></div>
                    </div>
                </a>

                <a href="#" id="link-manage-plo" class="group block p-4 border border-cloud-blue rounded-xl hover:border-royal-blue hover:shadow-md transition bg-pure-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-royal-blue/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-center relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="bg-cloud-blue/40 text-royal-blue p-2.5 rounded-lg group-hover:bg-royal-blue group-hover:text-pure-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-deep-navy group-hover:text-royal-blue transition-colors">ผลลัพธ์การเรียนรู้ (PLOs)</h3>
                                <p class="text-xs text-slate-grey mt-0.5">เพิ่ม/ลบ/แก้ไข Program Learning Outcomes</p>
                            </div>
                        </div>
                        <div id="status-plo"></div>
                    </div>
                </a>

                <a href="#" id="link-manage-llls" class="group block p-4 border border-cloud-blue rounded-xl hover:border-royal-blue hover:shadow-md transition bg-pure-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-royal-blue/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex justify-between items-center relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="bg-cloud-blue/40 text-royal-blue p-2.5 rounded-lg group-hover:bg-royal-blue group-hover:text-pure-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-deep-navy group-hover:text-royal-blue transition-colors">ทักษะการเรียนรู้ตลอดชีวิต (LLLs)</h3>
                                <p class="text-xs text-slate-grey mt-0.5">เพิ่ม/ลบ/แก้ไข Lifelong Learning Skills</p>
                            </div>
                        </div>
                        <div id="status-llls"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div id="curriculum-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/60 z-50 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-pure-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform transition-all scale-100 border border-cloud-blue">
            <div class="flex justify-between items-center border-b border-cloud-blue pb-4 mb-6">
                <h2 id="modal-title" class="text-2xl font-bold text-deep-navy">เพิ่มหลักสูตรใหม่</h2>
                <button type="button" id="btn-close-x" class="text-slate-grey hover:text-red-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="curriculum-form" method="POST">
                @csrf
                <input type="hidden" id="method-field" name="_method" value="POST">

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-deep-navy mb-1.5">ปีหลักสูตร (พ.ศ.) <span class="text-red-500">*</span></label>
                        <input type="text" name="curriculum_year" id="input-year" 
                               class="w-full border-cloud-blue rounded-lg shadow-sm focus:ring-2 focus:ring-royal-blue/30 focus:border-royal-blue px-4 py-2.5 border transition text-deep-navy" 
                               placeholder="เช่น 2565" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-deep-navy mb-1.5">ชื่อหลักสูตร <span class="text-red-500">*</span></label>
                        <input type="text" name="curriculum_name" id="input-name" 
                               class="w-full border-cloud-blue rounded-lg shadow-sm focus:ring-2 focus:ring-royal-blue/30 focus:border-royal-blue px-4 py-2.5 border transition text-deep-navy" 
                               placeholder="เช่น วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-deep-navy mb-1.5">คณะ <span class="text-red-500">*</span></label>
                            <input type="text" name="faculty" id="input-faculty" 
                                   class="w-full border-cloud-blue rounded-lg shadow-sm focus:ring-2 focus:ring-royal-blue/30 focus:border-royal-blue px-4 py-2.5 border transition text-deep-navy" 
                                   placeholder="เช่น วิทยาศาสตร์" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-deep-navy mb-1.5">สาขาวิชา <span class="text-red-500">*</span></label>
                            <input type="text" name="major" id="input-major" 
                                   class="w-full border-cloud-blue rounded-lg shadow-sm focus:ring-2 focus:ring-royal-blue/30 focus:border-royal-blue px-4 py-2.5 border transition text-deep-navy" 
                                   placeholder="เช่น วิทยาการคอมพิวเตอร์" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-deep-navy mb-1.5">วิทยาเขต <span class="text-red-500">*</span></label>
                            <input type="text" name="campus" id="input-campus" 
                                   class="w-full border-cloud-blue rounded-lg shadow-sm focus:ring-2 focus:ring-royal-blue/30 focus:border-royal-blue px-4 py-2.5 border transition text-deep-navy" 
                                   placeholder="เช่น เชียงใหม่" required>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-cloud-blue">
                    <button type="button" id="btn-close-modal" class="px-5 py-2.5 bg-cloud-blue/30 text-deep-navy font-medium rounded-xl hover:bg-cloud-blue/60 transition">
                        ยกเลิก
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-royal-blue hover:bg-deep-navy text-pure-white font-medium rounded-xl shadow-sm transition">
                        บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-deep-navy/80 z-50 hidden backdrop-blur-sm">
        <div class="bg-pure-white rounded-2xl shadow-2xl p-6 max-w-sm w-full text-center transform transition-all scale-95 border border-cloud-blue">
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4 border border-red-200">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-deep-navy mb-2">ยืนยันการลบ?</h3>
            <p class="text-sm text-slate-grey mb-3">การกระทำนี้ไม่สามารถย้อนกลับได้</p>

            <div class="mb-5 p-3 bg-cloud-blue/20 rounded-xl border border-cloud-blue text-center">
                <p class="text-xs text-slate-grey mb-1">หลักสูตรที่จะลบ</p>
                <p id="delete-curriculum-info" class="font-bold text-red-600 text-sm break-words">-</p>
            </div>

            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="submit" class="w-full px-5 py-2.5 bg-red-600 text-pure-white font-medium rounded-xl hover:bg-red-700 shadow-sm transition">
                        ใช่, ลบเลย
                    </button>
                    <button type="button" id="btn-close-delete" class="w-full px-5 py-2.5 bg-cloud-blue/30 text-deep-navy font-medium rounded-xl hover:bg-cloud-blue/60 transition">
                        ยกเลิก
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>