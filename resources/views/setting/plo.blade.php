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

    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2 w-18">PLO</th>
                <th class="border px-4 py-2">Description</th>
                <th class="border px-4 py-2 w-30">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plos as $plo)
            <tr data-id="{{ $plo->plo }}">
                <td class="px-4 py-2">
                    <input type="number" class="plo-input px-2 py-1 w-full" value="{{ $plo->plo }}" readonly>
                </td>
                <td class="px-4 py-2">
                    <textarea class="desc-input px-2 py-1 w-full resize-none" rows="1" style="overflow:hidden">{{ $plo->description }}</textarea>
                </td>
                <td class="px-4 py-2 text-center">
                    <button class="update-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">บันทึก</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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

document.querySelectorAll('.update-btn').forEach(button => {
    button.addEventListener('click', function() {
        const tr = this.closest('tr');
        const oldId = tr.dataset.id;
        const plo = tr.querySelector('.plo-input').value;
        const description = tr.querySelector('.desc-input').value;

        showConfirm('คุณต้องการบันทึกการเปลี่ยนแปลงนี้หรือไม่?', function() {
            axios.post('/plos/update/' + oldId, {
                plo: plo,
                description: description,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                showPopup(response.data.message);
                tr.dataset.id = plo;
            })
            .catch(error => {
                showPopup('เกิดข้อผิดพลาด');
                console.error(error);
            });
        });
    });
});
</script>
    </body>
</html>
