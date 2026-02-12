document.addEventListener('DOMContentLoaded', () => {
    // Select2
    if (window.jQuery && window.jQuery().select2) {
        const $select = $('#curriculum_select');
        
        if ($select.length) {
            $select.select2({
                placeholder: "-- ค้นหาและเลือกหลักสูตร --",
                allowClear: true,
                width: '100%'
            }).on('select2:select', function (e) {
                $(this).closest('form').submit();
            });
        }
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
        if (el) el.classList.toggle('hidden', !show);
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
            
            if (deleteForm) {
                deleteForm.action = `/management/addcourses/${tr.dataset.id}`;
                toggleModal('delete-modal', true);
            }
        }
    });

    // ปิด Modal ลบ
    const btnCloseDelete = document.getElementById('btn-close-delete');
    if (btnCloseDelete) {
        btnCloseDelete.onclick = () => toggleModal('delete-modal', false);
    }

    // Modal ผลลัพธ์การ Import
    const importModal = document.getElementById('import-result-modal');
    if (importModal) {
        const closeButtons = importModal.querySelectorAll('button');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                importModal.remove();
            });
        });
    }
});