import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-philosophy');
    const btnSave = document.getElementById('btn-save');
    const popupModal = document.getElementById('popup-modal');
    const popupContent = document.getElementById('popup-content');
    const popupClose = document.getElementById('popup-close');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // หยุดการโหลดหน้าใหม่

            const originalBtnContent = btnSave.innerHTML;
            btnSave.disabled = true;
            btnSave.classList.add('cursor-not-allowed', 'opacity-80');
            btnSave.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                กำลังบันทึก...
            `;

            // เตรียมข้อมูลส่งไปที่ Controller
            const formData = {
                year: document.getElementById('current_year').value,
                mju_philosophy: document.getElementById('mju_philosophy').value,
                education_philosophy: document.getElementById('education_philosophy').value,
                curriculum_philosophy: document.getElementById('curriculum_philosophy').value,
            };

            const updateUrl = form.getAttribute('action');

            // ยิง AJAX request ไปบันทึกข้อมูล
            axios.post(updateUrl, formData, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // โชว์ Popup สำเร็จ
                popupModal.classList.remove('hidden');
                setTimeout(() => {
                    popupModal.classList.remove('opacity-0');
                    popupContent.classList.remove('scale-95', 'opacity-0');
                    popupContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล กรุณาลองใหม่อีกครั้ง');
            })
            .finally(() => {
                btnSave.disabled = false;
                btnSave.classList.remove('cursor-not-allowed', 'opacity-80');
                btnSave.innerHTML = originalBtnContent;
            });
        });
    }

    // ฟังก์ชันปิด Popup
    function closePopup() {
        popupModal.classList.add('opacity-0');
        popupContent.classList.remove('scale-100', 'opacity-100');
        popupContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            popupModal.classList.add('hidden');
        }, 300);
    }

    if(popupClose) popupClose.addEventListener('click', closePopup);
    if(popupModal) popupModal.addEventListener('click', (e) => {
        if(e.target === popupModal) closePopup();
    });
});