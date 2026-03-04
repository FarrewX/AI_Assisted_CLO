document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('course-search-input');
    const tableBody = document.querySelector('#courses-table tbody');

    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                if (row.id === 'empty-row') return;

                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Modal เพิ่ม/แก้ไข
    const btnAdd = document.getElementById('btn-add');
    const modal = document.getElementById('addcourse-modal');
    const form = document.getElementById('addcourse-form');
    const modalTitle = document.getElementById('modal-title');
    const methodField = document.getElementById('method-field');
    const btnCloseModal = document.getElementById('btn-close-modal');

    // ฟังก์ชันเปิด/ปิด Modal
    const toggleModal = (id, show) => {
        const el = document.getElementById(id);
        if (!el) return;
        
        if (show) {
            el.classList.remove('hidden');
            // Animation
            setTimeout(() => {
                el.classList.remove('opacity-0');
                const content = el.querySelector('.bg-white');
                if(content) content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        } else {
            el.classList.add('opacity-0');
            const content = el.querySelector('.bg-white');
            if(content) {
                content.classList.add('scale-95', 'opacity-0');
            }
            setTimeout(() => {
                el.classList.add('hidden');
            }, 300);
        }
    };

    if (btnAdd) {
        btnAdd.onclick = () => {
            // ดึงค่าจาก data-attribute ที่ฝังไว้ในปุ่ม
            const createUrl = btnAdd.getAttribute('data-route-create');
            const curriculumId = btnAdd.getAttribute('data-curriculum-id');

            form.action = createUrl;
            methodField.value = "POST";
            modalTitle.textContent = "เพิ่มรายวิชาใหม่";
            form.reset();
            
            // คืนค่า curriculum_id กลับไปใน hidden field
            const currInput = form.querySelector('input[name="curriculum_id"]');
            if (currInput) currInput.value = curriculumId;

            toggleModal('addcourse-modal', true);
        };
    }

    if (btnCloseModal) {
        btnCloseModal.onclick = () => toggleModal('addcourse-modal', false);
    }

    // ปุ่ม Edit / Delete
    document.addEventListener('click', (e) => {
        // ปุ่มแก้ไข
        const editBtn = e.target.closest('.btn-edit'); 
        
        if (editBtn) {
            const tr = editBtn.closest('tr'); // หาแถว (tr) ที่ปุ่มนี้อยู่
            const data = tr.dataset; // ดึงข้อมูลจาก data-attributes
            
            // ตั้งค่า Form Action
            form.action = `/management/addcourses/${data.id}`; 
            methodField.value = "PUT";
            modalTitle.textContent = `แก้ไขรายวิชา: ${data.code}`;
            
            // เติมข้อมูลลงฟอร์ม
            document.getElementById('input-code').value = data.code;
            document.getElementById('input-name-th').value = data.nameTh;
            document.getElementById('input-name-en').value = data.nameEn || '';
            document.getElementById('input-detail-th').value = data.detailTh || '';
            document.getElementById('input-detail-en').value = data.detailEn || '';
            document.getElementById('input-credit').value = data.credit || '';
            
            toggleModal('addcourse-modal', true);
        }

        // ปุ่มลบ
       const deleteBtn = e.target.closest('.btn-delete');
        
        if (deleteBtn) {
            const tr = deleteBtn.closest('tr');
            const deleteForm = document.getElementById('delete-form');
            const courseNameDisplay = document.getElementById('delete-course-name');
            
            if (deleteForm) {
                deleteForm.action = `/management/addcourses/${tr.dataset.id}`;
                
                if (courseNameDisplay) {
                    courseNameDisplay.textContent = `${tr.dataset.code} : ${tr.dataset.nameTh}`;
                }
                
                toggleModal('delete-modal', true);
            }
        }
    });

    // ปิด Modal ลบ
    const btnCloseDelete = document.getElementById('btn-close-delete');
    if (btnCloseDelete) {
        btnCloseDelete.onclick = () => toggleModal('delete-modal', false);
    }

    // ปิด delete-modal เมื่อคลิกพื้นหลัง
    const deleteModal = document.getElementById('delete-modal');
    if(deleteModal) {
        deleteModal.addEventListener('click', (e) => {
            if(e.target === deleteModal) {
                toggleModal('delete-modal', false);
            }
        });
    }

    // Modal ผลลัพธ์การ Import
    const importModal = document.getElementById('import-result-modal');
    if (importModal) {
        // เปิดเมื่อมี session
        setTimeout(() => {
            importModal.classList.remove('opacity-0');
            const content = importModal.querySelector('.bg-white');
            if(content) content.classList.remove('scale-95');
        }, 10);
        
        const closeButtons = importModal.querySelectorAll('button');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                importModal.classList.add('opacity-0');
                const content = importModal.querySelector('.bg-white');
                if(content) content.classList.add('scale-95');
                setTimeout(() => {
                    importModal.remove();
                }, 300);
            });
        });
    }

    // Popup Notification
    const popupNotification = document.getElementById('popup-notification');
    if (popupNotification) {
        // เปิดเมื่อมี session
        setTimeout(() => {
            popupNotification.classList.remove('opacity-0');
            const content = popupNotification.querySelector('.bg-white');
            if(content) content.classList.remove('scale-95');
        }, 10);
        
        const closeBtn = popupNotification.querySelector('button');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                popupNotification.classList.add('opacity-0');
                const content = popupNotification.querySelector('.bg-white');
                if(content) content.classList.add('scale-95');
                setTimeout(() => {
                    popupNotification.remove();
                }, 300);
            });
        }
    }
});