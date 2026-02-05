<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Curriculum</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50">
    @include('component.navbar')

    <div class="container mx-auto p-4 pt-20">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">จัดการหลักสูตร (Curriculum)</h1>
            <button id="btn-add" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2 transition transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                เพิ่มหลักสูตรใหม่
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ปีหลักสูตร</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ชื่อหลักสูตร</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">คณะ</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">สาขาวิชา</th>
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
                            data-major="{{ $cur->major }}">
                            
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
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                @if($isTrashed)
                                    <span class="text-red-400 italic font-normal text-xs uppercase tracking-wider">ยกเลิกการใช้งาน</span>
                                    {{-- (Optional) คุณอาจจะเพิ่มปุ่ม Restore ตรงนี้ในอนาคต --}}
                                @else
                                    <button class="btn-edit text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 py-1 rounded-md transition">
                                        แก้ไข
                                    </button>
                                    <button class="btn-delete text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition">
                                        ลบ
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
    <div id="toast-notification" class="fixed bottom-5 right-5 z-50 transform transition-all duration-300 translate-y-0 opacity-100">
        <div class="flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-lg border-l-4 {{ session('error') ? 'border-red-500' : 'border-green-500' }}" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 {{ session('error') ? 'text-red-500 bg-red-100' : 'text-green-500 bg-green-100' }} rounded-lg">
                @if(session('error'))
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                @else
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                @endif
            </div>
            <div class="ml-3 text-sm font-normal text-gray-800">{{ session('success') ?? session('error') }}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8" onclick="this.parentElement.remove()">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </button>
        </div>
    </div>
    <script>setTimeout(() => { document.getElementById('toast-notification')?.remove(); }, 4000);</script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('curriculum-modal');
            const form = document.getElementById('curriculum-form');
            const modalTitle = document.getElementById('modal-title');
            const methodField = document.getElementById('method-field');
            
            const toggleModal = (id, show) => {
                const el = document.getElementById(id);
                if(show) {
                    el.classList.remove('hidden');
                    setTimeout(() => el.children[0].classList.replace('scale-95', 'scale-100'), 10);
                } else {
                    el.children[0].classList.replace('scale-100', 'scale-95');
                    setTimeout(() => el.classList.add('hidden'), 200);
                }
            };

            // 1. ปุ่มเพิ่มหลักสูตร
            document.getElementById('btn-add').onclick = () => {
                form.action = "{{ route('curriculum.store') }}";
                methodField.value = "POST";
                modalTitle.textContent = "เพิ่มหลักสูตรใหม่";
                form.reset();
                toggleModal('curriculum-modal', true);
            };

            // 2. Event Delegation สำหรับปุ่มแก้ไขและลบ
            document.addEventListener('click', (e) => {
                // Edit
                if(e.target.closest('.btn-edit')) {
                    const tr = e.target.closest('tr');
                    const data = tr.dataset;

                    form.action = `/management/curriculum/${data.id}`;
                    methodField.value = "PUT";
                    modalTitle.textContent = `แก้ไขหลักสูตร: ${data.name}`;
                    
                    document.getElementById('input-year').value = data.year;
                    document.getElementById('input-name').value = data.name;
                    document.getElementById('input-faculty').value = data.faculty;
                    document.getElementById('input-major').value = data.major;

                    toggleModal('curriculum-modal', true);
                }

                // Delete
                if(e.target.closest('.btn-delete')) {
                    const tr = e.target.closest('tr');
                    const deleteForm = document.getElementById('delete-form');
                    deleteForm.action = `/management/curriculum/${tr.dataset.id}`;
                    toggleModal('delete-modal', true);
                }
            });

            // Close Buttons
            const closeActions = [
                document.getElementById('btn-close-modal'),
                document.getElementById('btn-close-x'),
                document.getElementById('btn-close-delete')
            ];
            
            closeActions.forEach(btn => {
                if(btn) {
                    btn.onclick = () => {
                        toggleModal('curriculum-modal', false);
                        toggleModal('delete-modal', false);
                    };
                }
            });

            // ปิด Modal เมื่อคลิกพื้นหลัง
            window.onclick = (e) => {
                if (e.target == modal) toggleModal('curriculum-modal', false);
                if (e.target == document.getElementById('delete-modal')) toggleModal('delete-modal', false);
            };
        });
    </script>
</body>
</html>