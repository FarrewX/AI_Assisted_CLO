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
                            {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if(isset($teachers))
                <!-- ตารางอาจารย์ -->
                <table class="min-w-full border border-gray-300 mb-6 mt-4">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2 w-300">อาจารย์</th>
                            <th class="border px-4 py-2 w-30">ปีการสอน</th>
                            <th class="border px-4 py-2 w-28">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $t)
                            <tr>
                                <td class="border px-4 py-2">{{ $t->user->name }}</td>
                                <td class="border px-4 py-2">
                                    <span class="year-display">{{ $t->year }}</span>
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <button type="button" class="bg-yellow-500 text-white px-2 py-1 rounded edit-btn"
                                        data-id="{{ $t->id }}"
                                        data-name="{{ $t->user->name }}"
                                        data-user-id="{{ $t->user->user_id }}"
                                        data-year="{{ $t->year }}"
                                        data-update-url="{{ route('teachers.update', [$courseId, $t->id]) }}"
                                    >แก้ไข</button>
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

                <!-- เพิ่มอาจารย์ใหม่ -->
                <div class="mt-4">
                    <h2 class="font-semibold mb-2">เพิ่มอาจารย์ในคอร์ส</h2>
                    <form method="POST" action="{{ route('teachers.store', $courseId) }}">
                        @csrf
                        <select name="user_id" class="border px-2 py-1 rounded">
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="year" class="border px-2 py-1 rounded w-24" placeholder="ปี" value="{{ date('Y') }}">
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
                                <input type="number" name="year" id="edit-year-input" class="border px-2 py-1 rounded w-24" required>
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
                    const url = btn.getAttribute('data-update-url');
                    const userId = btn.getAttribute('data-user-id');

                    document.getElementById('edit-year-input').value = year;
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
                var isDuplicate = false;
                var isYearConflict = false;
                var currentRowUser = $('#edit-user-input option:selected').text().trim();
                var currentId = null;

                // หา id ของแถวที่กำลังแก้ไข (จาก form action)
                var actionUrl = $(this).attr('action');
                var match = actionUrl.match(/teachers\/update\/(\d+)$/);
                if (match) {
                    currentId = match[1];
                }

                $('table tbody tr').each(function() {
                    var rowUser = $(this).find('td:first').text().trim();
                    var rowYear = $(this).find('.year-display').text().trim();
                    var editBtn = $(this).find('.edit-btn');
                    var rowId = editBtn.data('id') ? editBtn.data('id').toString() : '';

                    //user+year ซ้ำ
                    if (
                        rowUser === currentRowUser &&
                        rowYear === year &&
                        rowId !== currentId
                    ) {
                        isDuplicate = true;
                        return false; // break
                    }

                    //ปีซ้ำ แต่คนละ user
                    if (
                        rowUser !== currentRowUser &&
                        rowYear === year &&
                        rowId !== currentId
                    ) {
                        isYearConflict = true;
                        return false;
                    }
                });

                if (isDuplicate) {
                    $('#duplicate-modal').removeClass('hidden');
                    e.preventDefault();
                    return false;
                }

                if (isYearConflict) {
                    $('#year-duplicate-modal').removeClass('hidden');
                    e.preventDefault();
                    return false;
                }

                $('#edit-modal').addClass('hidden');
            });

            // ปุ่ม cancel ปิด modal
            document.getElementById('edit-cancel').onclick = function() {
                document.getElementById('edit-modal').classList.add('hidden');
            };

            // ================== Duplicate check ตอน create ==================
            $('form[action*="teachers.store"]').on('submit', function(e) {
                var $form = $(this);
                var userId = $form.find('select[name="user_id"]').val();
                var year = $form.find('input[name="year"]').val();
                var isDuplicate = false;

                $('table tbody tr').each(function() {
                    var rowUser = $(this).find('td:first').text().trim();
                    var rowYear = $(this).find('.year-display').text().trim();
                    var selectedUser = $form.find('select[name="user_id"] option:selected').text().trim();
                    if (rowUser === selectedUser && rowYear === year) {
                        isDuplicate = true;
                        return false;
                    }
                });

                if (isDuplicate) {
                    $('#duplicate-modal').removeClass('hidden');
                    e.preventDefault();
                    return false;
                }
            });

            $('#duplicate-close').on('click', function() {
                $('#duplicate-modal').addClass('hidden');
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
