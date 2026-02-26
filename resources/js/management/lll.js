import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    axios.defaults.headers.common['Accept'] = 'application/json';
    
    // ==========================================
    // 1. Helper Functions (ฟังก์ชันช่วยเหลือ)
    // ==========================================
    const toggleModal = (id, show) => {
        const el = document.getElementById(id);
        if (el) {
            if(show) {
                el.classList.remove('hidden');
                const content = el.querySelector('div'); 
                if(content) setTimeout(() => {
                    el.classList.remove('opacity-0');
                    content.classList.remove('scale-95');
                }, 10);
            } else {
                el.classList.add('opacity-0');
                const content = el.querySelector('div');
                if(content) content.classList.add('scale-95');
                setTimeout(() => el.classList.add('hidden'), 300);
            }
        }
    };

    let popupCloseCallback = null;
    const showPopup = (msg, isSuccess = false, callback = null) => {
        const popupMsg = document.getElementById('popup-message');
        const popupTitle = document.getElementById('popup-title');
        const iconContainer = document.getElementById('popup-icon-container');
        const iconSuccess = document.getElementById('icon-success');
        const iconError = document.getElementById('icon-error');

        if(popupMsg) popupMsg.textContent = msg;
        if(iconSuccess) iconSuccess.classList.add('hidden');
        if(iconError) iconError.classList.add('hidden');
        if(iconContainer) iconContainer.className = 'mx-auto flex h-20 w-20 items-center justify-center rounded-full mb-5 transition-colors duration-300';

        popupCloseCallback = callback;

        if (isSuccess) {
            if(popupTitle) popupTitle.textContent = "สำเร็จ!";
            if(iconContainer) iconContainer.classList.add('bg-green-100');
            if(iconSuccess) iconSuccess.classList.remove('hidden');
        } else {
            if(popupTitle) popupTitle.textContent = "เกิดข้อผิดพลาด";
            if(iconContainer) iconContainer.classList.add('bg-red-100');
            if(iconError) iconError.classList.remove('hidden');
        }

        toggleModal('popup-modal', true);
    };

    let confirmCallback = null;
    const showConfirmDelete = (msg, cb) => {
        const confirmMsg = document.getElementById('confirm-message');
        const confirmBtn = document.getElementById('confirm-ok');

        if(confirmMsg) confirmMsg.textContent = msg;
        confirmCallback = cb;

        if (confirmBtn) {
            confirmBtn.className = 'flex-1 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-3 text-sm font-bold text-white shadow-md transition-all duration-200';
            confirmBtn.innerHTML = 'ใช่, ลบเลย';
        }

        toggleModal('confirm-modal', true);
    };

    // ==========================================
    // 2. Setup Plugins & Close Buttons
    // ==========================================
    const closeButtons = [
        { id: 'confirm-cancel', modal: 'confirm-modal' },
        { id: 'add-lll-cancel', modal: 'add-lll-modal' },
        { id: 'edit-lll-cancel', modal: 'edit-lll-modal' }
    ];

    closeButtons.forEach(btn => {
        const el = document.getElementById(btn.id);
        if(el) el.addEventListener('click', (e) => {
            e.preventDefault();
            toggleModal(btn.modal, false);
        });
    });

    const popupCloseBtn = document.getElementById('popup-close');
    if(popupCloseBtn) {
        popupCloseBtn.addEventListener('click', () => {
            toggleModal('popup-modal', false);
            if(popupCloseCallback) popupCloseCallback();
        });
    }

    const confirmOkBtn = document.getElementById('confirm-ok');
    if(confirmOkBtn) {
        confirmOkBtn.addEventListener('click', () => {
            toggleModal('confirm-modal', false);
            if(confirmCallback) confirmCallback();
        });
    }

    // ==========================================
    // 3. Main Logic (Add, Edit, Delete)
    // ==========================================
    const currentYear = document.body.getAttribute('data-current-year');

    const addBtn = document.getElementById('add-lll-btn');
    const addSave = document.getElementById('add-lll-save');

    if(addBtn) addBtn.addEventListener('click', () => toggleModal('add-lll-modal', true));

    if(addSave) {
        addSave.addEventListener('click', function() {
            const lllNum = document.getElementById('new-lll')?.value || '';
            const desc = document.getElementById('new-desc')?.value.trim() || '';

            const checkedPlos = Array.from(document.querySelectorAll('input[name="add_check_plo[]"]:checked')).map(cb => cb.value);

            if(!desc) { showPopup('กรุณากรอกชื่อทักษะ'); return; }

            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = 'กำลังบันทึก...';

            axios.post('/management/llls', {
                curriculum_year_ref: currentYear, 
                num_LLL: lllNum,
                name_LLL: desc,
                check_LLL: checkedPlos
            })
            .then(res => {
                toggleModal('add-lll-modal', false);
                showPopup('เพิ่มข้อมูล LLL สำเร็จเรียบร้อย', true, () => location.reload());
            })
            .catch(err => {
                console.error(err);
                showPopup('เกิดข้อผิดพลาด: ' + (err.response?.data?.message || err.message));
            })
            .finally(() => {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = originalText;
            });
        });
    }

    // Modal แก้ไข หรือ ยืนยันการลบ
    document.addEventListener('click', function(e) {
        
        // --- ส่วนของการแก้ไข (EDIT) ---
        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            e.preventDefault();
            const tr = editBtn.closest('tr');
            if(!tr) return;

            document.getElementById('edit-id').value = tr.dataset.id;
            document.getElementById('edit-lll-num').value = tr.dataset.lllNum;
            document.getElementById('edit-desc').value = tr.dataset.desc;

            const checkLllString = tr.dataset.checkLll || ''; 
            const checkArray = checkLllString.split(',').map(item => item.trim());

            // ล้าง Checkbox เก่าออกให้หมดก่อน
            document.querySelectorAll('input[name="edit_check_plo[]"]').forEach(cb => cb.checked = false);
            
            // ติ๊ก Checkbox ให้ตรงกับข้อมูลในฐานข้อมูล
            document.querySelectorAll('input[name="edit_check_plo[]"]').forEach(cb => {
                if(checkArray.includes(cb.value)) {
                    cb.checked = true;
                }
            });

            toggleModal('edit-lll-modal', true);
        }

        // --- ส่วนของการลบ (DELETE) ---
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            e.preventDefault();
            const tr = deleteBtn.closest('tr');
            const id = tr.dataset.id;
            const lllNum = tr.dataset.lllNum;

            showConfirmDelete(`คุณแน่ใจหรือไม่ที่จะลบ LLL ${lllNum} ?`, () => {
                const iconBtn = deleteBtn.innerHTML;
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '...';

                axios.delete(`/management/llls/${id}`)
                .then(res => {
                    showPopup('ลบข้อมูล LLL สำเร็จ', true, () => location.reload());
                })
                .catch(err => {
                    console.error(err);
                    showPopup('เกิดข้อผิดพลาดในการลบข้อมูล');
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = iconBtn;
                });
            }); 
        }
    });

    // ทึกการแก้ไข (SAVE EDIT)
    const editSave = document.getElementById('edit-lll-save');
    if(editSave) {
        editSave.addEventListener('click', function() {
            const id = document.getElementById('edit-id').value;
            const lllNum = document.getElementById('edit-lll-num').value;
            const desc = document.getElementById('edit-desc').value.trim();

            const checkedPlos = Array.from(document.querySelectorAll('input[name="edit_check_plo[]"]:checked')).map(cb => cb.value);
            
            if(!desc) { showPopup('กรุณากรอกชื่อทักษะ'); return; }
            
            const btn = editSave;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = 'กำลังบันทึก...';

            // ส่ง Payload ให้ตรงกับคอลัมน์ Database
            axios.put(`/management/llls/${id}`, { 
                num_LLL: lllNum,
                name_LLL: desc,
                check_LLL: checkedPlos
            })
            .then(res => {
                toggleModal('edit-lll-modal', false);
                showPopup('อัปเดตข้อมูลสำเร็จเรียบร้อย', true, () => location.reload());
            })
            .catch(err => {
                console.error(err);
                showPopup('เกิดข้อผิดพลาดในการอัปเดต: ' + (err.response?.data?.message || err.message));
            })
            .finally(() => {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = originalText;
            });
        });
    }
});