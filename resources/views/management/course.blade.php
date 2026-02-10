<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ELO_Generator</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    </head>
    <body>
        @include('component.navbar')

        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4">จัดการ Courses</h1>
            
            <form method="GET" action="{{ route('courses') }}" class="mb-6 space-y-4">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">เลือกหลักสูตร</label>
                    <select id="curriculum" name="curriculum_id" class="border px-3 py-2 rounded w-1/2" onchange="this.form.submit()">
                        <option value="">-- เลือกหลักสูตร --</option>
                        @foreach($curriculum as $cur)
                            <option value="{{ $cur->id }}" {{ $curriculumId == $cur->id ? 'selected' : '' }}>
                                {{ $cur->curriculum_year }} - {{ $cur->curriculum_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(request('curriculum_id') && count($courses) > 0)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">เลือกรายวิชา</label>
                    <select id="course" name="course_id" class="border px-3 py-2 rounded w-1/2" onchange="this.form.submit()">
                        <option value="">-- เลือกรายวิชา --</option>
                        @foreach($courses as $course)
                            {{-- ต้องใช้ id (PK) เป็น value และใช้ course_code ในการแสดงผล --}}
                            <option value="{{ $course->id }}" 
                                {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_code }} {{ $course->course_name_th }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @elseif(request('curriculum_id'))
                    <div class="text-red-500 text-sm mt-2">ไม่มีรายวิชาในหลักสูตรนี้</div>
                @endif

            </form>

            @if(isset($professor))
                <table class="min-w-full border border-gray-300 mb-6 mt-4">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2 w-300">อาจารย์</th>
                            <th class="border px-4 py-2 w-30">ปีการสอน</th>
                            <th class="border px-4 py-2 w-30">ภาคเรียน</th>
                            <th class="border px-4 py-2 w-30">มคอ.</th>
                            <th class="border px-4 py-2 w-45">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($professor as $t)
                            <tr>
                                <td class="border px-4 py-2">
                                    {{ $t->user ? $t->user->name : 'ไม่พบข้อมูลอาจารย์ (ID: '.$t->user_id.')' }}
                                </td>
                                
                                {{-- 🔥 จุดแก้ที่ 1: การแสดงผลในตาราง --}}
                                <td class="border px-4 py-2">
                                    <span class="year-display">
                                        {{ $t->year < 2400 ? $t->year + 543 : $t->year }}
                                    </span>
                                </td>

                                <td class="border px-4 py-2"><span class="term-display">{{ $t->term }}</span></td>
                                <td class="border px-4 py-2"><span class="TQF-display">{{ $t->TQF }}</span></td>
                                <td class="border px-4 py-2 text-center space-y-2 space-x-2">
                                    <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded edit-btn"
                                            data-id="{{ $t->id }}"
                                            data-name="{{ $t->user->name ?? '' }}"
                                            data-user-id="{{ $t->user_id }}"
                                            data-year="{{ $t->year < 2400 ? $t->year + 543 : $t->year }}"
                                            data-term="{{ $t->term }}"
                                            data-TQF="{{ $t->TQF }}"
                                            data-update-url="{{ route('professor.update', [$courseId, $t->id]) }}"
                                    >แก้ไข</button>

                                    <form method="POST" action="{{ route('professor.destroy', [$courseId, $t->id]) }}" class="inline-block ml-1 delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded delete-btn">
                                            ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    <h2 class="font-semibold mb-2">เพิ่มอาจารย์ในคอร์ส</h2>
                    <form method="POST" action="{{ route('professor.store', $courseId) }}">
                        @csrf
                        <input type="hidden" name="curriculum_id" value="{{ $curriculumId }}">
                        <select name="user_id" class="border px-2 py-1 rounded">
                            <!-- <option value="">-- เลือกอาจารย์ --</option> -->
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}"> 
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="year" class="border px-2 py-1 rounded w-24" 
                               placeholder="ปี" 
                               value="{{ date('Y') + 543 }}">

                        <select name="term" class="border px-2 py-1 rounded w-24">
                            <option value="">ภาคเรียน</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                        <select name="TQF" class="border px-2 py-1 rounded w-24">
                            <option value="">มคอ</option>
                            <option value="3">3</option>
                            <option value="5" disabled>5</option>
                        </select>
                        <button class="bg-green-500 text-white px-3 py-1 rounded ml-2">เพิ่ม</button>
                    </form>
                </div>
            @endif
            
            <div id="edit-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
                <div class="bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
                    <form id="edit-form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <input type="hidden" name="curriculum_id" value="{{ $curriculumId }}">
                            <div class="font-bold mb-2">แก้ไขข้อมูลอาจารย์</div>
                            <select name="user_id" id="edit-user-input" class="border px-2 py-1 rounded w-full mb-2">
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}"> 
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p>ภาคเรียน</p>
                            <select name="term" id="edit-term-input" class="border px-2 py-1 rounded w-full mb-2" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                            <p>ปีการศึกษา</p>
                            <input type="number" name="year" id="edit-year-input" class="border px-2 py-1 rounded w-full mb-2" required>
                            <p>มคอ</p>
                            <select name="TQF" id="edit-TQF-input" class="border px-2 py-1 rounded w-full mb-2" required>
                                <option value="3">3</option>
                                <option value="5" disabled>5</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">บันทึก</button>
                        <button type="button" id="edit-cancel" class="bg-gray-400 text-white px-4 py-2 rounded">ยกเลิก</button>
                    </form>
                </div>
            </div>

            <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
                <div class="bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
                    <div class="mb-4 font-bold">คุณแน่ใจหรือไม่ว่าต้องการลบอาจารย์นี้?</div>
                    <div class="flex justify-center gap-2">
                        <button id="delete-confirm" class="bg-red-500 text-white px-4 py-2 rounded">ลบ</button>
                        <button id="delete-cancel" class="bg-gray-400 text-white px-4 py-2 rounded">ยกเลิก</button>
                    </div>
                </div>
            </div>

            @if ($errors->any())
            <div id="error-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 animate-fade-in">
                <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full mx-4 transform transition-all scale-100">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <div class="text-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">เกิดข้อผิดพลาด!</h3>
                        <div class="mt-2 text-left">
                            <ul class="text-sm text-red-600 list-disc pl-5 space-y-1 bg-red-50 p-3 rounded-lg border border-red-100">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6">
                        <button type="button" onclick="document.getElementById('error-modal').remove()" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition">
                            ตกลง
                        </button>
                    </div>
                </div>
            </div>
            @endif

            @if(session('success') || session('error'))
                <div id="session-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 animate-fade-in">
                    <div class="bg-white rounded-xl shadow-2xl p-6 min-w-[300px] max-w-sm text-center transform transition-all scale-100 mx-4">
                        
                        @if(session('success'))
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">สำเร็จ</h3>
                            <p class="text-gray-600 mb-6">{{ session('success') }}</p>
                            <button onclick="closeSessionModal()" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                ตกลง
                            </button>
                        @endif

                        @if(session('error'))
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">ผิดพลาด</h3>
                            <p class="text-gray-600 mb-6">{{ session('error') }}</p>
                            <button onclick="closeSessionModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition">
                                ปิด
                            </button>
                        @endif

                    </div>
                </div>
            @endif
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

       <script>
            $(document).ready(function() {
                // 1. Setup Select2 ตัวกรองด้านบน
                $('#curriculum').select2({ placeholder: "-- เลือกหลักสูตร --", width: '100%', minimumResultsForSearch: Infinity })
                    .on('select2:select', function (e) { $(this).closest('form').submit(); });

                $('#course').select2({ placeholder: "-- เลือกรายวิชา --", allowClear: true, width: '100%' })
                    .on('select2:select', function (e) { $(this).closest('form').submit(); });

                // 2. Logic ปุ่มแก้ไข
                $('.edit-btn').on('click', function() {
                    const btn = $(this);
                    
                    // ดึงค่า
                    const userId = btn.attr('data-user-id'); // ใช้ attr เพื่อความชัวร์
                    const year = btn.data('year');
                    const term = btn.data('term');
                    const TQF = btn.data('tqf');
                    const url = btn.data('update-url');

                    // ใส่ค่าลง Input
                    $('#edit-year-input').val(year);
                    $('#edit-term-input').val(term);
                    $('#edit-TQF-input').val(TQF);
                    
                    // 🔥 เลือกอาจารย์คนปัจจุบัน
                    $('#edit-user-input').val(userId);

                    // ตั้งค่า Action Form
                    $('#edit-form').attr('action', url);

                    // เปิด Modal
                    $('#edit-modal').removeClass('hidden');
                });

                // ปุ่มปิด Modal แก้ไข
                $('#edit-cancel').on('click', function() {
                    $('#edit-modal').addClass('hidden');
                });

                // 3. Logic ปุ่มลบ
                let deleteModal = $('#delete-modal');
                let currentDeleteForm = null;

                $('.delete-btn').on('click', function() {
                    currentDeleteForm = $(this).closest('form');
                    deleteModal.removeClass('hidden');
                });

                $('#delete-confirm').on('click', function() {
                    if (currentDeleteForm) currentDeleteForm.submit();
                });

                $('#delete-cancel').on('click', function() {
                    deleteModal.addClass('hidden');
                    currentDeleteForm = null;
                });

                // 4. Session Popup Logic
                @if(session('error') || session('success'))
                    let sessionModal = $('#session-modal');
                    let sessionMessage = $('#session-message');
                    
                    @if(session('error'))
                        sessionMessage.text("{{ session('error') }}").addClass('text-red-600');
                    @elseif(session('success'))
                        sessionMessage.text("{{ session('success') }}").addClass('text-green-600');
                    @endif
                    
                    sessionModal.removeClass('hidden');

                    $('#session-close').on('click', function() {
                        closeSessionModal();
                    });
                @endif
            });

            function closeSessionModal() {
                const modal = document.getElementById('session-modal');
                if (modal) {
                    modal.style.opacity = '0';
                    modal.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => modal.remove(), 300);
                }
            }
        </script>
    </body>
</html>