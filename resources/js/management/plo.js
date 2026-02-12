import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
    
    // Helper Functions
    const toggleModal = (id, show) => {
        const el = document.getElementById(id);
        if (el) {
            if(show) {
                el.classList.remove('hidden');
                const content = el.querySelector('div'); 
                if(content) setTimeout(() => content.classList.remove('scale-95'), 10);
            } else {
                el.classList.add('hidden');
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
        if(iconContainer) iconContainer.classList.remove('bg-green-100', 'bg-red-100');

        popupCloseCallback = callback;

        if (isSuccess) {
            if(popupTitle) {
                popupTitle.textContent = "สำเร็จ";
                popupTitle.className = "text-lg font-bold text-green-600 mb-1";
            }
            
            if(iconContainer) iconContainer.classList.add('bg-green-100');
            if(iconSuccess) iconSuccess.classList.remove('hidden');

        } else {
            if(popupTitle) {
                popupTitle.textContent = "เกิดข้อผิดพลาด";
                popupTitle.className = "text-lg font-bold text-red-600 mb-1";
            }
            
            if(iconContainer) iconContainer.classList.add('bg-red-100');
            if(iconError) iconError.classList.remove('hidden');
        }

        toggleModal('popup-modal', true);
    };

    let confirmCallback = null;
    const showConfirm = (msg, cb, isDanger = false) => {
        const confirmMsg = document.getElementById('confirm-message');
        const confirmBtn = document.getElementById('confirm-ok');

        if(confirmMsg) confirmMsg.textContent = msg;
        confirmCallback = cb;

        if (confirmBtn) {
            if (isDanger) {
                confirmBtn.classList.remove('bg-blue-600', 'hover:bg-blue-500');
                confirmBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            } else {
                confirmBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                confirmBtn.classList.add('bg-blue-600', 'hover:bg-blue-500');
            }
        }

        toggleModal('confirm-modal', true);
    };

    // Setup Select2
    if (window.jQuery) {
        const $ = window.jQuery;
        $('#year_select').select2({
            placeholder: "-- เลือกปี พ.ศ. --",
            width: '100%',
            minimumResultsForSearch: Infinity 
        }).on('select2:select', function (e) {
            $(this).closest('form').submit();
        });
    }

    // Modal Close Buttons
    const closeButtons = [
        { id: 'confirm-cancel', modal: 'confirm-modal' },
        { id: 'add-plo-cancel', modal: 'add-plo-modal' },
        { id: 'edit-plo-cancel', modal: 'edit-plo-modal' }
    ];

    closeButtons.forEach(btn => {
        const el = document.getElementById(btn.id);
        if(el) el.addEventListener('click', () => toggleModal(btn.modal, false));
    });

    const popupCloseBtn = document.getElementById('popup-close');
    if(popupCloseBtn) {
        popupCloseBtn.addEventListener('click', () => {
            toggleModal('popup-modal', false);
            if(popupCloseCallback) popupCloseCallback();
        });
    }

    // Confirm Modal
    const confirmOkBtn = document.getElementById('confirm-ok');
    if(confirmOkBtn) {
        confirmOkBtn.addEventListener('click', () => {
            toggleModal('confirm-modal', false);
            if(confirmCallback) confirmCallback();
        });
    }

    // Main Logic (Add, Edit, Delete)
    const currentYear = document.body.getAttribute('data-current-year');

    // ADD PLO
    const addBtn = document.getElementById('add-plo-btn');
    const addSave = document.getElementById('add-plo-save');

    if(addBtn) {
        addBtn.addEventListener('click', () => toggleModal('add-plo-modal', true));
    }

    if(addSave) {
        addSave.addEventListener('click', () => {
            const ploInput = document.getElementById('new-plo');
            const descInput = document.getElementById('new-desc');
            const domainInput = document.getElementById('new-domain');
            const levelInput = document.getElementById('new-level');

            const ploNum = ploInput ? ploInput.value : '';
            const desc = descInput ? descInput.value.trim() : '';
            const domain = domainInput ? domainInput.value.trim() : '';
            const level = levelInput ? levelInput.value.trim() : '';

            if(!desc) { showPopup('กรุณากรอก Description'); return; }

            axios.post('/management/plos/create', {
                curriculum_year_ref: currentYear,
                plo: ploNum,
                description: desc,
                domain: domain,
                learning_level: level,
                _token: csrfToken
            })
            .then(res => {
                toggleModal('add-plo-modal', false);
                showPopup('บันทึกข้อมูลสำเร็จ', true, () => location.reload());
            })
            .catch(err => {
                console.error(err);
                showPopup('เกิดข้อผิดพลาด: ' + (err.response?.data?.message || err.message));
            });
        });
    }

    // EDIT & DELETE
    document.addEventListener('click', function(e) {
        // EDIT Button
        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            e.preventDefault();
            const tr = editBtn.closest('tr');
            if(!tr) return;

            const id = tr.dataset.id;
            const ploNum = tr.dataset.ploNum;
            const domain = tr.dataset.domain;
            const level = tr.dataset.level;
            
            const descEl = tr.querySelector('.desc-text');
            const desc = descEl ? descEl.innerText : tr.children[1].innerText.trim();

            const editIdInput = document.getElementById('edit-id');
            const editPloInput = document.getElementById('edit-plo-num');
            const editDescInput = document.getElementById('edit-desc');
            const editDomainInput = document.getElementById('edit-domain');
            const editLevelInput = document.getElementById('edit-level');

            if(editIdInput) editIdInput.value = id;
            if(editPloInput) editPloInput.value = ploNum;
            if(editDescInput) editDescInput.value = desc;
            if(editDomainInput) editDomainInput.value = domain;
            if(editLevelInput) editLevelInput.value = level;

            toggleModal('edit-plo-modal', true);
        }

        // DELETE Button
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            e.preventDefault();
            const tr = deleteBtn.closest('tr');
            const id = tr.dataset.id;
            const ploNum = tr.dataset.ploNum;

            showConfirm(`ต้องการลบ PLO ${ploNum} หรือไม่?`, () => {
                axios.delete(`/management/plos/delete/${id}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(res => {
                    showPopup('ลบข้อมูลสำเร็จ', true, () => location.reload());
                })
                .catch(err => {
                    console.error(err);
                    showPopup('เกิดข้อผิดพลาดในการลบ');
                });
            },true);
        }
    });

    // SAVE EDIT
    const editSave = document.getElementById('edit-plo-save');
    if(editSave) {
        editSave.addEventListener('click', () => {
            const id = document.getElementById('edit-id').value;
            const ploNum = document.getElementById('edit-plo-num').value;
            const desc = document.getElementById('edit-desc').value.trim();
            const domain = document.getElementById('edit-domain').value.trim();
            const level = document.getElementById('edit-level').value.trim();
            
            showConfirm('ต้องการบันทึกการแก้ไขหรือไม่?', () => {
                axios.post(`/management/plos/update/${id}`, { 
                    plo: ploNum,
                    description: desc,
                    domain: domain,
                    learning_level: level,
                    _token: csrfToken
                })
                .then(res => {
                    toggleModal('edit-plo-modal', false);
                    showPopup('แก้ไขข้อมูลสำเร็จ', true, () => location.reload());
                })
                .catch(err => {
                    console.error(err);
                    showPopup('เกิดข้อผิดพลาดในการแก้ไข: ' + (err.response?.data?.message || err.message));
                });
            }, false);
        });
    }
});