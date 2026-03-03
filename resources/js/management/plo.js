import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
    
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
            if(popupTitle) {
                popupTitle.textContent = "สำเร็จ!";
            }
            if(iconContainer) iconContainer.classList.add('bg-blue-100');
            if(iconSuccess) iconSuccess.classList.remove('hidden');
        } else {
            if(popupTitle) {
                popupTitle.textContent = "เกิดข้อผิดพลาด";
            }
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

        // บังคับให้ปุ่มเป็นสีแดงเสมอ (เพราะใช้เฉพาะกับการลบ)
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
        { id: 'add-plo-cancel', modal: 'add-plo-modal' },
        { id: 'edit-plo-cancel', modal: 'edit-plo-modal' }
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

    // เพิ่ม PLO (ADD)
    const addBtn = document.getElementById('add-plo-btn');
    const addSave = document.getElementById('add-plo-save');

    if(addBtn) addBtn.addEventListener('click', () => toggleModal('add-plo-modal', true));

    if(addSave) {
        addSave.addEventListener('click', function() {
            const ploNum = document.getElementById('new-plo')?.value || '';
            const desc = document.getElementById('new-desc')?.value.trim() || '';
            const dom1Value = document.getElementById('new-domain1').value;
            const dom2Value = document.getElementById('new-domain2').value;
            const level = document.getElementById('new-level')?.value.trim() || '';
            
            const loTypeNode = document.querySelector('input[name="new_lo_type"]:checked');
            const specificLo = loTypeNode ? (loTypeNode.value === '1' ? 1 : 0) : 1;

            if(!desc) { showPopup('กรุณากรอกคำอธิบาย (Description)'); return; }

            let domainArray = [];
            if (dom1Value) domainArray.push(dom1Value);
            if (dom2Value && dom2Value !== dom1Value) domainArray.push(dom2Value);

            const finalDomainString = domainArray.join(', ');

            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = 'กำลังบันทึก...';

            axios.post('/management/plos/create', {
                curriculum_year_ref: currentYear,
                plo: ploNum,
                description: desc,
                domain: finalDomainString,
                learning_level: level,
                specific_lo: specificLo,
                _token: csrfToken
            })
            .then(res => {
                toggleModal('add-plo-modal', false);
                showPopup('เพิ่มข้อมูล PLO สำเร็จเรียบร้อย', true, () => location.reload());
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
            document.getElementById('edit-plo-num').value = tr.dataset.ploNum;
            document.getElementById('edit-desc').value = tr.dataset.desc;

            const dbDomain = tr.dataset.domain || ''; 
            const dbLevel = tr.dataset.level || '';   
            
            // แตกข้อความ Domain แยกเป็น 2 ช่อง
            const domainArray = dbDomain ? dbDomain.split(',').map(d => d.trim()) : [];
            const domain1Value = domainArray[0] || '';
            const domain2Value = domainArray[1] || '';
            
            document.getElementById('edit-domain1').value = domain1Value;
            document.getElementById('edit-domain2').value = domain2Value;

            // Specific/Generic LO
            if (tr.dataset.specificLo === '1') {
                document.getElementById('edit-specific-lo').checked = true;
            } else {
                document.getElementById('edit-generic-lo').checked = true;
            }

            // อัปเดตตัวเลือก Level ทันที แล้วยัดค่าเก่าเข้าไป
            if (typeof updateEditLevels === 'function') {
                updateEditLevels();
                setTimeout(() => {
                    document.getElementById('edit-level').value = dbLevel;
                }, 10);
            }

            toggleModal('edit-plo-modal', true);
            return;
        }

        // --- ส่วนของการลบ (DELETE) ---
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            e.preventDefault();
            const tr = deleteBtn.closest('tr');
            if(!tr) return;
            
            const id = tr.dataset.id;
            const ploNum = tr.dataset.ploNum;

            showConfirmDelete(`คุณแน่ใจหรือไม่ที่จะลบ PLO ${ploNum} ?`, () => {
                const iconBtn = deleteBtn.innerHTML;
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '...';

                axios.delete(`/management/plos/delete/${id}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(res => {
                    showPopup('ลบข้อมูล PLO สำเร็จ', true, () => location.reload());
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

    // บันทึกการแก้ไข (SAVE EDIT)
    const editSave = document.getElementById('edit-plo-save');
    if(editSave) {
        editSave.addEventListener('click', function() {
            const id = document.getElementById('edit-id').value;
            const ploNum = document.getElementById('edit-plo-num').value;
            const desc = document.getElementById('edit-desc').value.trim();
            const dom1 = document.getElementById('edit-domain1').value;
            const dom2 = document.getElementById('edit-domain2').value;
            const level = document.getElementById('edit-level').value.trim();
            
            const loTypeNode = document.querySelector('input[name="edit_lo_type"]:checked');
            const specificLo = loTypeNode ? (loTypeNode.value === '1' ? 1 : 0) : 1;
            
            if(!desc) { showPopup('กรุณากรอกคำอธิบาย (Description)'); return; }

            // นำมารวมกันคั่นด้วยลูกน้ำ
            let finalDomains = [];
            if (dom1) finalDomains.push(dom1);
            if (dom2 && dom2 !== dom1) finalDomains.push(dom2);

            const domainToSave = finalDomains.join(', ');
            
            const btn = editSave;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = 'กำลังบันทึก...';

            axios.post(`/management/plos/update/${id}`, { 
                plo: ploNum,
                description: desc,
                domain: domainToSave,
                learning_level: level,
                specific_lo: specificLo,
                _token: csrfToken
            })
            .then(res => {
                toggleModal('edit-plo-modal', false);
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

    const domainLevels = {
        "Knowledge": ["Remember", "Understand", "Apply", "Analyz", "Evaluate", "Create"],
        "Application and Responsibility": ["Remember", "Understand", "Apply", "Analyz", "Evaluate", "Create"],
        "Skill": ["Perception", "Set", "Guided Response", "Mechanism", "Complex Over Response", "Adaptation", "Origination"],
        "Ethics": ["Receiving Phenomena", "Responding to Phenomena", "Valuing", "Organization", "Internalizes Values"],
        "Character": ["Receiving Phenomena", "Responding to Phenomena", "Valuing", "Organization", "Internalizes Values"],
    };

    function setupMultiDomainLevelSync(prefix) {
        const dom1 = document.getElementById(`${prefix}-domain1`);
        const dom2 = document.getElementById(`${prefix}-domain2`);
        const levelSelect = document.getElementById(`${prefix}-level`);

        if (!dom1 || !dom2 || !levelSelect) return;

        const updateLevels = () => {
            const val1 = dom1.value;
            const val2 = dom2.value;
            
            // รวบรวม Level จาก Domain ที่ถูกเลือก
            let availableLevels = [];
            if (val1 && domainLevels[val1]) availableLevels.push(...domainLevels[val1]);
            if (val2 && val2 !== val1 && domainLevels[val2]) availableLevels.push(...domainLevels[val2]);

            // ล้างตัวเลือกเก่า
            levelSelect.innerHTML = '<option value="" hidden>-- เลือก Level --</option>';

            if (availableLevels.length > 0) {
                levelSelect.disabled = false;
                levelSelect.classList.remove('bg-gray-100', 'cursor-not-allowed', 'text-gray-500');
                levelSelect.classList.add('bg-gray-50', 'cursor-pointer', 'text-gray-900');

                // นำ Array มาสร้างเป็น <option>
                availableLevels.forEach(level => {
                    const option = document.createElement('option');
                    option.value = level;
                    option.textContent = level;
                    levelSelect.appendChild(option);
                });
            } else {
                // ล็อคการใช้งานถ้าไม่มี Level
                levelSelect.disabled = true;
                levelSelect.innerHTML = '<option value="">-- ไม่มี Level หรือยังไม่ได้เลือก Domain --</option>';
                levelSelect.classList.add('bg-gray-100', 'cursor-not-allowed', 'text-gray-500');
                levelSelect.classList.remove('bg-gray-50', 'cursor-pointer', 'text-gray-900');
            }
        };

        dom1.addEventListener('change', updateLevels);
        dom2.addEventListener('change', updateLevels);

        return updateLevels;
    }

    setupMultiDomainLevelSync('new');
    const updateEditLevels = setupMultiDomainLevelSync('edit');
});