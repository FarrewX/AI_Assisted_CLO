<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Courses</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single { height: 42px; display: flex; align-items: center; border-color: #d1d5db; border-radius: 0.5rem; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    </style>
</head>
<body class="bg-gray-50">
    @include('component.navbar')

    <div class="container mx-auto p-4 pt-20">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">จัดการรายวิชาในหลักสูตร</h1>
            
            <form method="GET" action="{{ route('addcourse.list') }}" class="w-full md:w-1/2">
                <select id="curriculum_select" name="curriculum_id" class="w-full">
                    <option value="">-- กรุณาเลือกหลักสูตรเพื่อจัดการ --</option>
                    @foreach($curricula as $cur)
                        <option value="{{ $cur->id }}" {{ (isset($selectedCurriculumId) && $selectedCurriculumId == $cur->id) ? 'selected' : '' }}>
                            {{ $cur->curriculum_year }} : {{ $cur->curriculum_name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if(isset($selectedCurriculumId) && $selectedCurriculumId)
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6 flex flex-wrap gap-4 justify-between items-center">
                <button id="btn-add" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow flex items-center gap-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                    เพิ่มรายวิชาใหม่
                </button>

                <form action="{{ route('addcourse.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="curriculum_id" value="{{ $selectedCurriculumId }}">
                    
                    <div class="relative">
                        <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg shadow transition">
                        Import CSV
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัสวิชา</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อวิชา (TH)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อวิชา (EN)</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">หน่วยกิต</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($courses as $c)
                        <tr class="hover:bg-gray-50 transition" 
                            data-id="{{ $c->id }}"
                            data-code="{{ $c->course_code }}"
                            data-name-th="{{ $c->course_name_th }}"
                            data-name-en="{{ $c->course_name_en }}"
                            data-detail-th="{{ $c->course_detail_th }}"
                            data-detail-en="{{ $c->course_detail_en }}"
                            data-credit="{{ $c->credit }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $c->course_code }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $c->course_name_th }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $c->course_name_en ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ $c->credit ?? '-' }}</td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <button class="btn-edit text-indigo-600 hover:text-indigo-900 font-semibold transition">แก้ไข</button>
                                <button class="btn-delete text-red-600 hover:text-red-900 font-semibold transition">ลบ</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                ยังไม่มีรายวิชาในหลักสูตรนี้
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $courses->appends(['curriculum_id' => $selectedCurriculumId])->links() }}
            </div>
        @else
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-8 rounded shadow-sm text-center">
                <p class="font-bold text-lg mb-2">กรุณาเลือกหลักสูตร</p>
                <p>เลือกหลักสูตรจากรายการด้านบน เพื่อจัดการรายวิชาในหลักสูตรนั้น</p>
            </div>
        @endif
    </div>

    <div id="addcourse-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg transform transition-all scale-100 max-h-[90vh] overflow-y-auto">
            <h2 id="modal-title" class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">เพิ่มรายวิชาใหม่</h2>
            
            <form id="addcourse-form" method="POST">
                @csrf
                <input type="hidden" id="method-field" name="_method" value="POST">
                <input type="hidden" name="curriculum_id" value="{{ $selectedCurriculumId ?? '' }}">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รหัสวิชา <span class="text-red-500">*</span></label>
                        <input type="text" name="course_code" id="input-code" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border" required placeholder="e.g. CS101">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">หน่วยกิต</label>
                        <input type="text" name="credit" id="input-credit" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border" placeholder="e.g. 3(2-2-5)">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อวิชา (TH) <span class="text-red-500">*</span></label>
                    <input type="text" name="course_name_th" id="input-name-th" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อวิชา (EN) <span class="text-red-500">*</span></label>
                    <input type="text" name="course_name_en" id="input-name-en" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด (TH)</label>
                    <textarea name="course_detail_th" id="input-detail-th" rows="6" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด (EN)</label>
                    <textarea name="course_detail_en" id="input-detail-en" rows="7" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 px-3 py-2 border"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" id="btn-close-modal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">ยกเลิก</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full text-center">
            <h3 class="mb-5 text-lg font-normal text-gray-500">ยืนยันการลบรายวิชานี้?</h3>
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="curriculum_id" value="{{ $selectedCurriculumId ?? '' }}">
                <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">ลบ</button>
                <button type="button" id="btn-close-delete" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">ยกเลิก</button>
            </form>
        </div>
    </div>

    @if(session('imported_courses'))
    <div id="import-result-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[60] backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-4xl transform transition-all scale-100 flex flex-col max-h-[90vh]">
            
            <div class="flex justify-between items-center mb-4 border-b pb-2">
                <div class="flex items-center gap-2">
                    <div class="bg-green-100 p-2 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">นำเข้าข้อมูลสำเร็จ</h2>
                </div>
                <button onclick="document.getElementById('import-result-modal').remove()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="mb-2 text-sm text-gray-600">
                รายการรายวิชาทั้งหมดที่ถูกนำเข้าหรืออัพเดทล่าสุด (จำนวน: {{ count(session('imported_courses')) }} รายการ):
            </div>

            <div class="overflow-y-auto flex-1 border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">รหัสวิชา</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ชื่อไทย</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ชื่ออังกฤษ</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">หน่วยกิต</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(session('imported_courses') as $course)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $course->course_code }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $course->course_name_th }}</td>
                            <td class="px-4 py-2 text-gray-600">{{ $course->course_name_en }}</td>
                            <td class="px-4 py-2 text-center text-gray-600">{{ $course->credit }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-end">
                <button onclick="document.getElementById('import-result-modal').remove()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow transition">
                    ตกลง
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(session('success') || session('error'))
    <div id="toast-notification" class="fixed bottom-5 right-5 bg-white p-4 rounded-lg shadow-lg border-l-4 {{ session('error') ? 'border-red-500' : 'border-green-500' }}">
        <div class="text-sm font-bold {{ session('error') ? 'text-red-600' : 'text-green-600' }}">
            {{ session('success') ?? session('error') }}
        </div>
    </div>
    <script>setTimeout(() => { document.getElementById('toast-notification')?.remove(); }, 3000);</script>
    @endif

    @if ($errors->any())
        <div class="fixed top-20 right-5 z-50 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg max-w-md">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-bold">เกิดข้อผิดพลาดในการบันทึก:</p>
                    <ul class="list-disc ml-5 mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold ml-4">X</button>
            </div>
        </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select2
            $('#curriculum_select').select2({
                placeholder: "-- ค้นหาและเลือกหลักสูตร --",
                allowClear: true,
                width: '100%'
            }).on('select2:select', function (e) {
                $(this).closest('form').submit();
            });

            // Modal Logic
            const modal = document.getElementById('addcourse-modal');
            const form = document.getElementById('addcourse-form');
            const modalTitle = document.getElementById('modal-title');
            const methodField = document.getElementById('method-field');
            const toggleModal = (id, show) => document.getElementById(id).classList.toggle('hidden', !show);

            document.getElementById('btn-add').onclick = () => {
                form.action = "{{ route('addcourse.create') }}";
                methodField.value = "POST";
                modalTitle.textContent = "เพิ่มรายวิชาใหม่";
                form.reset();
                // set default curriculum_id again because reset() clears hidden inputs
                form.querySelector('input[name="curriculum_id"]').value = "{{ $selectedCurriculumId ?? '' }}";
                toggleModal('addcourse-modal', true);
            };

            document.addEventListener('click', (e) => {
                if(e.target.classList.contains('btn-edit')) {
                    const tr = e.target.closest('tr');
                    const data = tr.dataset;
                    form.action = `/management/addcourses/${data.id}`; 
                    methodField.value = "PUT";
                    modalTitle.textContent = `แก้ไขรายวิชา: ${data.code}`;
                    
                    document.getElementById('input-code').value = data.code;
                    document.getElementById('input-name-th').value = data.nameTh;
                    document.getElementById('input-name-en').value = data.nameEn || '';
                    document.getElementById('input-detail-th').value = data.detailTh || '';
                    document.getElementById('input-detail-en').value = data.detailEn || '';
                    document.getElementById('input-credit').value = data.credit || '';
                    toggleModal('addcourse-modal', true);
                }
                if(e.target.classList.contains('btn-delete')) {
                    const tr = e.target.closest('tr');
                    const deleteForm = document.getElementById('delete-form');
                    deleteForm.action = `/management/addcourses/${tr.dataset.id}`;
                    toggleModal('delete-modal', true);
                }
            });

            document.getElementById('btn-close-modal').onclick = () => toggleModal('addcourse-modal', false);
            document.getElementById('btn-close-delete').onclick = () => toggleModal('delete-modal', false);
        });
    </script>
</body>
</html>