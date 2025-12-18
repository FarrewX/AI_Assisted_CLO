<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ELO_Generator</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    </head>
    <body>
        @include('component.navbar')

        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4">จัดการ Courses</h1>
            
            <!-- เลือกคอร์ส -->
            <form method="GET" action="{{ route('courses') }}">
                @csrf
                <select id="course" name="course_id" class="border px-3 py-2 rounded w-1/2" onchange="this.form.submit()">
                    <option value="">-- เลือกคอร์ส --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->course_id }}" 
                            {{ request('course_id') == $course->course_id ? 'selected' : '' }}>
                            {{ $course->course_name_th }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if(isset($professor))
                <!-- ตารางอาจารย์ -->
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
                                <td class="border px-4 py-2">{{ $t->user->name }}</td>
                                <td class="border px-4 py-2">
                                    <span class="year-display">{{ $t->year }}</span>
                                </td>
                                <td class="border px-4 py-2">
                                    <span class="term-display">{{ $t->term }}</span>
                                </td>
                                <td class="border px-4 py-2">
                                    <span class="TQF-display">{{ $t->TQF }}</span>
                                </td>
                                <td class="border px-4 py-2 text-center space-y-2 space-x-2">
                                    <button type="button" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded edit-btn"
                                        data-id="{{ $t->id }}"
                                        data-name="{{ $t->user->name }}"
                                        data-user-id="{{ $t->user->user_id }}"
                                        data-year="{{ $t->year }}"
                                        data-term="{{ $t->term }}"
                                        data-TQF="{{ $t->TQF }}"
                                        data-update-url="{{ route('professor.update', [$courseId, $t->id]) }}"
                                    >แก้ไข</button>

                                    <!-- ปุ่มลบ -->
                                    <form method="POST" action="{{ route('professor.destroy', [$courseId, $t->id]) }}" class="inline-block ml-1 delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded delete-btn"
                                            data-url="{{ route('professor.destroy', [$courseId, $t->id]) }}">
                                            ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Popup Modal สำหรับ session messages -->
                <div id="session-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
                    <div class="bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
                        <div id="session-message" class="mb-4 font-bold text-center"></div>
                        <button id="session-close" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">ปิด</button>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div id="delete-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
                    <div class="bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
                        <div class="mb-4 font-bold">คุณแน่ใจหรือไม่ว่าต้องการลบอาจารย์นี้?</div>
                        <div class="flex justify-center gap-2">
                            <button id="delete-confirm" class="bg-red-500 text-white px-4 py-2 rounded">ลบ</button>
                            <button id="delete-cancel" class="bg-gray-400 text-white px-4 py-2 rounded">ยกเลิก</button>
                        </div>
                    </div>
                </div>

                <!-- เพิ่มอาจารย์ใหม่ -->
                <div class="mt-4">
                    <h2 class="font-semibold mb-2">เพิ่มอาจารย์ในคอร์ส</h2>
                    <form method="POST" action="{{ route('professor.store', $courseId) }}">
                        @csrf
                        <select name="user_id" class="border px-2 py-1 rounded">
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="year" class="border px-2 py-1 rounded w-24" placeholder="ปี" value="{{ date('Y') }}">
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

                <!-- Edit Year Modal -->
                <div id="edit-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
                    <div class="bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
                        <form id="edit-form" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <div class="font-bold mb-2">แก้ไขข้อมูลอาจารย์</div>
                                <select name="user_id" id="edit-user-input" class="border px-2 py-1 rounded w-full mb-2">
                                    @foreach($users as $user)
                                        <option value="{{ $user->user_id }}">{{ $user->name }}</option>
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
            @endif
        </div>

        <!-- jQuery + Select2 -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <script>
        $(document).ready(function() {
            // เปิดใช้งานค้นหาด้วย select2
            $('#course').select2({
                placeholder: "-- เลือกคอร์ส --",
                allowClear: true,
                width: 'resolve'
            }).on('select2:select', function (e) {
                // เมื่อเลือกคอร์ส ให้ submit form ทันที
                $(this).closest('form').submit();
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // ================== Edit modal ==================
            document.querySelectorAll('.edit-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const modal = document.getElementById('edit-modal');
                    const form = document.getElementById('edit-form');
                    const name = btn.getAttribute('data-name');
                    const year = btn.getAttribute('data-year');
                    const term = btn.getAttribute('data-term');
                    const TQF = btn.getAttribute('data-TQF');
                    const url = btn.getAttribute('data-update-url');
                    const userId = btn.getAttribute('data-user-id');

                    document.getElementById('edit-year-input').value = year;
                    document.getElementById('edit-term-input').value = term;
                    document.getElementById('edit-TQF-input').value = TQF;
                    form.action = url;

                    let userSelect = document.getElementById('edit-user-input');
                    if (userId) {
                        userSelect.value = userId;
                    } else {
                        for (let i = 0; i < userSelect.options.length; i++) {
                            if (userSelect.options[i].text === name) {
                                userSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }

                    modal.classList.remove('hidden');
                });
            });

            // ================== Edit modal duplicate check ==================
            $('#edit-form').on('submit', function(e) {
                var userId = $('#edit-user-input').val();
                var year = $('#edit-year-input').val();
                var term = $('#edit-term-input').val();
                var TQF = $('#edit-TQF-input').val();
                var currentId = null;

                // หา id ของแถวที่กำลังแก้ไข (จาก form action)
                var actionUrl = $(this).attr('action');
                var match = actionUrl.match(/professor\/(\d+)$/);
                if (match) {
                    currentId = match[1];
                }

                var isDuplicate = false;

                $('table tbody tr').each(function() {
                    var rowUser = $(this).find('td:first').text().trim();
                    var rowYear = $(this).find('.year-display').text().trim();
                    var rowTerm = $(this).find('.term-display').text().trim();
                    var rowTQF  = $(this).find('.TQF-display').text().trim();
                    var editBtn = $(this).find('.edit-btn');
                    var rowId   = editBtn.data('id') ? editBtn.data('id').toString() : '';

                    // เงื่อนไขซ้ำ: same user + same year + same term + same TQF
                    if (
                        rowUser === $('#edit-user-input option:selected').text().trim() &&
                        rowYear === year &&
                        rowTerm === term &&
                        rowTQF === TQF &&
                        rowId !== currentId
                    ) {
                        isDuplicate = true;
                        return false; // break
                    }
                });

                if (isDuplicate) {
                    $('#duplicate-modal').removeClass('hidden');
                    e.preventDefault();
                    return false;
                }

                $('#edit-modal').addClass('hidden');
            });

            // ปุ่ม cancel ปิด modal
            document.getElementById('edit-cancel').onclick = function() {
                document.getElementById('edit-modal').classList.add('hidden');
            };

            // ================== Delete modal ==================
            let deleteModal = document.getElementById('delete-modal');
            let deleteConfirmBtn = document.getElementById('delete-confirm');
            let deleteCancelBtn = document.getElementById('delete-cancel');
            let currentDeleteForm = null;

            document.querySelectorAll('.delete-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    currentDeleteForm = btn.closest('form'); // เก็บ form ปัจจุบัน
                    deleteModal.classList.remove('hidden');
                });
            });

            // กดลบจริง
            deleteConfirmBtn.addEventListener('click', function() {
                if (currentDeleteForm) {
                    currentDeleteForm.submit();
                }
            });

            // กดยกเลิก
            deleteCancelBtn.addEventListener('click', function() {
                deleteModal.classList.add('hidden');
                currentDeleteForm = null;
            });

            // ================== Session popup ==================
            @if(session('error') || session('success'))
                let sessionModal = document.getElementById('session-modal');
                let sessionMessage = document.getElementById('session-message');
                @if(session('error'))
                    sessionMessage.textContent = "{{ session('error') }}";
                    sessionMessage.classList.add('text-red-600');
                @elseif(session('success'))
                    sessionMessage.textContent = "{{ session('success') }}";
                    sessionMessage.classList.add('text-green-600');
                @endif
                sessionModal.classList.remove('hidden');

                // ปุ่มปิด popup
                document.getElementById('session-close').onclick = function() {
                    sessionModal.classList.add('hidden');
                };
            @endif
        });
        </script>
    </body>
</html>
