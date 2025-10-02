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
    </head>
    <body>
        @include('component.navbar')

        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4">จัดการ PLOs</h1>

            <button id="add-plo-btn" class="mb-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">เพิ่ม PLO</button>

            <table class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2 w-18">PLO</th>
                        <th class="border px-4 py-2">Description</th>
                        <th class="border px-4 py-2 w-35">Domain</th>
                        <th class="border px-4 py-2 w-40">Learning Level</th>
                        <th class="border px-4 py-2 w-40">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plos as $plo)
                    <tr data-id="{{ $plo->plo }}" data-domain="{{ $plo->domain }}" data-level="{{ $plo->learning_level }}">
                        <td class="px-4 py-2">
                            <input type="number" class="plo-input px-2 py-1 w-full" value="{{ $plo->plo }}" readonly>
                        </td>
                        <td class="px-4 py-2">
                            <textarea class="desc-input px-2 py-1 w-full resize-none" rows="1" style="overflow:hidden" readonly>{{ $plo->description }}</textarea>
                        </td>
                        <td class="px-4 py-2">
                            <textarea class="desc-input px-2 py-1 w-full resize-none" rows="1" style="overflow:hidden" readonly>{{ $plo->domain }}</textarea>
                        </td>
                        <td class="px-4 py-2">
                            <textarea class="desc-input px-2 py-1 w-full resize-none" rows="1" style="overflow:hidden" readonly>{{ $plo->learning_level }}</textarea>
                        </td>
                        <td class="px-4 py-2 text-center space-y-5 space-x-5">
                            <button class="edit-btn bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">แก้ไข</button>
                            <button class="delete-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">ลบ</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Add PLO Modal -->
        @php
            $nextPloNumber = ($plos->max('plo') ?? 0) + 1;
        @endphp
        <div id="add-plo-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
            <div class="bg-white rounded shadow-lg p-6 min-w-[400px] max-w-lg text-center">
                <h2 class="text-lg font-bold mb-4">เพิ่ม PLO</h2>
                <div class="mb-2 text-left">
                    <label class="block mb-1">PLO</label>
                    <input id="new-plo" type="number" class="border px-2 py-1 w-full rounded bg-gray-100" min="1" readonly>
                </div>
                <div class="mb-4 text-left">
                    <label class="block mb-1">Description</label>
                    <textarea id="new-desc" class="border px-2 py-1 w-full rounded resize-none" rows="7"></textarea>
                </div>
                <div class="mb-4 text-left">
                    <label class="block mb-1">Domain</label>
                    <textarea id="new-domain" class="border px-2 py-1 w-full rounded resize-none" rows="2"></textarea>
                </div>
                <div class="mb-4 text-left">
                    <label class="block mb-1">Learning Level</label>
                    <textarea id="new-level" class="border px-2 py-1 w-full rounded resize-none" rows="2"></textarea>
                </div>
                <button id="add-plo-save" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">บันทึก</button>
                <button id="add-plo-cancel" class="bg-red-400 hover:bg-red-500 text-white px-4 py-2 rounded">ยกเลิก</button>
            </div>
        </div>

        <!-- Edit PLO Modal -->
        <div id="edit-plo-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
            <div class="bg-white rounded shadow-lg p-6 min-w-[400px] max-w-lg text-center">
                <h2 class="text-lg font-bold mb-4">แก้ไข PLO</h2>
                <div class="mb-2 text-left">
                    <label class="block mb-1">PLO</label>
                    <input id="edit-plo" type="number" class="border px-2 py-1 w-full rounded bg-gray-100" readonly>
                </div>
                <div class="mb-4 text-left">
                    <label class="block mb-1">Description</label>
                    <textarea id="edit-desc" class="border px-2 py-1 w-full rounded resize-none" rows="7"></textarea>
                </div>
                <div class="mb-4 text-left">
                    <label class="block mb-1">Domain</label>
                    <textarea id="edit-domain" class="border px-2 py-1 w-full rounded resize-none" rows="2"></textarea>
                </div>
                <div class="mb-4 text-left">
                    <label class="block mb-1">Learning Level</label>
                    <textarea id="edit-level" class="border px-2 py-1 w-full rounded resize-none" rows="2"></textarea>
                </div>
                <button id="edit-plo-save" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mr-2">บันทึก</button>
                <button id="edit-plo-cancel" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">ยกเลิก</button>
            </div>
        </div>

        <!-- Modal Popup -->
        <div id="popup-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
            <div class="bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
                <div id="popup-message" class="mb-4"></div>
                <button id="popup-close" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">ปิด</button>
            </div>
        </div>

        <!-- Confirm Modal -->
        <div id="confirm-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
            <div class="bg-white rounded shadow-lg p-6 min-w-[250px] max-w-xs text-center">
                <div id="confirm-message" class="mb-4">คุณต้องการบันทึกการเปลี่ยนแปลงนี้หรือไม่?</div>
                <button id="confirm-ok" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mr-2">ตกลง</button>
                <button id="confirm-cancel" class="bg-red-400 hover:bg-red-500 text-white px-4 py-2 rounded">ยกเลิก</button>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script>
        // ปรับ textarea ให้ขยายตามข้อความ
        document.querySelectorAll('.desc-input').forEach(textarea => {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });

        function showPopup(message) {
            document.getElementById('popup-message').textContent = message;
            document.getElementById('popup-modal').classList.remove('hidden');
        }
        document.getElementById('popup-close').onclick = function() {
            document.getElementById('popup-modal').classList.add('hidden');
        };

        // Custom confirm modal
        let confirmCallback = null;
        function showConfirm(message, onOk) {
            document.getElementById('confirm-message').textContent = message;
            document.getElementById('confirm-modal').classList.remove('hidden');
            confirmCallback = onOk;
        }
        document.getElementById('confirm-ok').onclick = function() {
            document.getElementById('confirm-modal').classList.add('hidden');
            if (typeof confirmCallback === 'function') confirmCallback();
        };
        document.getElementById('confirm-cancel').onclick = function() {
            document.getElementById('confirm-modal').classList.add('hidden');
            confirmCallback = null;
        };

        // แก้ไข PLO
        let currentEditTr = null;
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                currentEditTr = this.closest('tr');
                const plo = currentEditTr.querySelector('.plo-input').value;
                const desc = currentEditTr.querySelector('.desc-input').value;
                document.getElementById('edit-plo').value = plo;
                document.getElementById('edit-desc').value = desc;
                document.getElementById('edit-domain').value = currentEditTr.dataset.domain || '';
                document.getElementById('edit-level').value = currentEditTr.dataset.level || '';
                document.getElementById('edit-plo-modal').classList.remove('hidden');
            });
        });
        document.getElementById('edit-plo-cancel').onclick = function() {
            document.getElementById('edit-plo-modal').classList.add('hidden');
        };

        // บันทึกการแก้ไข PLO
        document.getElementById('edit-plo-save').onclick = function() {
            const plo = document.getElementById('edit-plo').value.trim();
            const desc = document.getElementById('edit-desc').value.trim();
            const domain = document.getElementById('edit-domain').value.trim();
            const level = document.getElementById('edit-level').value.trim();
            showConfirm('คุณต้องการบันทึกการเปลี่ยนแปลงนี้หรือไม่?', function() {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/plos/update/' + plo, true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            try {
                                var data = JSON.parse(xhr.responseText);
                                showPopup(data.message);
                                if (currentEditTr) {
                                    currentEditTr.querySelector('.desc-input').value = desc;
                                    currentEditTr.dataset.domain = domain;
                                    currentEditTr.dataset.level = level;
                                }
                                document.getElementById('edit-plo-modal').classList.add('hidden');
                                setTimeout(() => location.reload(), 1000);
                            } catch (e) {
                                showPopup('เกิดข้อผิดพลาด');
                            }
                        } else {
                            showPopup('เกิดข้อผิดพลาด');
                        }
                    }
                };
                xhr.send(JSON.stringify({
                    plo: plo,
                    description: desc,
                    domain: domain,
                    learning_level: level
                }));
            });
        };

        // ลบ PLO
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tr = this.closest('tr');
                const plo = tr.dataset.id; // หรือ tr.querySelector('.plo-input').value;

                showConfirm('คุณต้องการลบ PLO นี้หรือไม่?', function() {
                    var xhr = new XMLHttpRequest();
                    xhr.open('DELETE', '/plos/delete/' + plo, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                showPopup('ลบ PLO สำเร็จ');
                                tr.remove();
                            } else {
                                showPopup('เกิดข้อผิดพลาด');
                            }
                        }
                    };
                    xhr.send();
                });
            });
        });

        // Add PLO Modal logic
        const nextPloNumber = {{ $nextPloNumber }};
        document.getElementById('add-plo-btn').onclick = function() {
            document.getElementById('new-plo').value = nextPloNumber;
            document.getElementById('new-desc').value = '';
            document.getElementById('new-domain').value = '';
            document.getElementById('new-level').value = '';
            document.getElementById('add-plo-modal').classList.remove('hidden');
        };
        document.getElementById('add-plo-cancel').onclick = function() {
            document.getElementById('add-plo-modal').classList.add('hidden');
        };
        document.getElementById('add-plo-save').onclick = function() {
            const plo = document.getElementById('new-plo').value.trim();
            const desc = document.getElementById('new-desc').value.trim();
            const domain = document.getElementById('new-domain').value.trim();
            const level = document.getElementById('new-level').value.trim();
            if (!plo || !desc) {
                showPopup('กรุณากรอกข้อมูลให้ครบถ้วน');
                return;
            }
            axios.post('/plos/create', {
                plo: plo,
                description: desc,
                domain: domain,
                learning_level: level,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                showPopup(response.data.message || 'เพิ่ม PLO สำเร็จ');
                document.getElementById('add-plo-modal').classList.add('hidden');
                setTimeout(() => location.reload(), 1000);
            })
            .catch(error => {
                showPopup('เกิดข้อผิดพลาด');
                console.error(error);
            });
        };
        </script>
    </body>
</html>
