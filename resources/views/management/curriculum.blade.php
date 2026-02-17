<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Curriculum</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/management/curriculum.js'])
    @endif
</head>
<body class="bg-gray-50">
    @include('component.navbar')

    <div class="container mx-auto p-4 pt-20">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">จัดการหลักสูตร (Curriculum)</h1>
            <button id="btn-add" 
                data-route-store="{{ route('curriculum.store') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2 transition transform hover:-translate-y-0.5">
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

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ปีหลักสูตร</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ชื่อหลักสูตร</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">คณะ</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">สาขาวิชา</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">วิทยาเขต</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($curricula as $cur)
                        @php $isTrashed = $cur->trashed(); @endphp

                        <tr class="{{ $isTrashed ? 'bg-gray-50' : 'hover:bg-blue-50' }} transition duration-150"
                            data-id="{{ $cur->id }}"
                            data-year="{{ $cur->curriculum_year }}"
                            data-name="{{ $cur->curriculum_name }}"
                            data-faculty="{{ $cur->faculty }}"
                            data-major="{{ $cur->major }}"
                            data-campus="{{ $cur->campus }}">
                            
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $isTrashed ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                {{ $cur->curriculum_year }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isTrashed ? 'text-gray-400 line-through' : 'text-gray-700' }}">
                                {{ $cur->curriculum_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isTrashed ? 'text-gray-400 line-through' : 'text-gray-600' }}">
                                {{ $cur->faculty }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isTrashed ? 'text-gray-400 line-through' : 'text-gray-600' }}">
                                {{ $cur->major }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $isTrashed ? 'text-gray-400 line-through' : 'text-gray-600' }}">
                                {{ $cur->campus }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                @if($isTrashed)
                                    <span class="text-red-400 italic font-normal text-xs uppercase tracking-wider">ยกเลิกการใช้งาน</span>
                                @else
                                    <button class="btn-edit text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 py-1 rounded-md transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button class="btn-delete text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 bg-gray-50">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
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

    <div id="curriculum-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50 hidden backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform transition-all scale-100">
            <div class="flex justify-between items-center border-b pb-4 mb-6">
                <h2 id="modal-title" class="text-2xl font-bold text-gray-800">เพิ่มหลักสูตรใหม่</h2>
                <button type="button" id="btn-close-x" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form id="curriculum-form" method="POST">
                @csrf
                <input type="hidden" id="method-field" name="_method" value="POST">

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ปีหลักสูตร (พ.ศ.) <span class="text-red-500">*</span></label>
                        <input type="text" name="curriculum_year" id="input-year" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2 border transition" 
                               placeholder="เช่น 2565" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ชื่อหลักสูตร <span class="text-red-500">*</span></label>
                        <input type="text" name="curriculum_name" id="input-name" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2 border transition" 
                               placeholder="เช่น วิทยาศาสตรบัณฑิต สาขาวิชาวิทยาการคอมพิวเตอร์" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">คณะ <span class="text-red-500">*</span></label>
                            <input type="text" name="faculty" id="input-faculty" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2 border transition" 
                                   placeholder="เช่น วิทยาศาสตร์" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">สาขาวิชา <span class="text-red-500">*</span></label>
                            <input type="text" name="major" id="input-major" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2 border transition" 
                                   placeholder="เช่น วิทยาการคอมพิวเตอร์" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">วิทยาเขต <span class="text-red-500">*</span></label>
                            <input type="text" name="campus" id="input-campus" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2 border transition" 
                                   placeholder="เช่น เชียงใหม่" required>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" id="btn-close-modal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-100 transition">
                        ยกเลิก
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:ring-4 focus:ring-blue-300 transition">
                        บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50 hidden backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full text-center transform transition-all scale-100">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">ยืนยันการลบ?</h3>
            <p class="text-sm text-gray-500 mb-6">คุณแน่ใจหรือไม่ที่จะลบหลักสูตรนี้? การกระทำนี้ไม่สามารถย้อนกลับได้</p>
            
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="button" id="btn-close-delete" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        ยกเลิก
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 shadow-md transition">
                        ใช่, ลบเลย
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success') || session('error'))
        <div id="popup-notification" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300">
            
            <div class="relative w-full max-w-sm p-6 mx-4 bg-white shadow-2xl rounded-2xl transform transition-all scale-100">
                
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition focus:outline-none" onclick="document.getElementById('popup-notification').remove()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 {{ session('error') ? 'bg-red-100' : 'bg-green-100' }}">
                        @if(session('error'))
                            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        @else
                            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                        {{ session('error') ? 'เกิดข้อผิดพลาด' : 'ทำรายการสำเร็จ' }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">
                        {{ session('success') ?? session('error') }}
                    </p>

                    <button onclick="document.getElementById('popup-notification').remove()" 
                        class="w-full inline-flex justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                        {{ session('error') ? 'bg-red-600 hover:bg-red-500 focus-visible:outline-red-600' : 'bg-green-600 hover:bg-green-500 focus-visible:outline-green-600' }}">
                        ตกลง
                    </button>
                </div>
            </div>
        </div>
    @endif
</body>
</html>