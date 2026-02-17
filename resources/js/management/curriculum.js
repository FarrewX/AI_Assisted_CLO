document.addEventListener('DOMContentLoaded', () => {
    // Select Elements
    const btnAdd = document.getElementById('btn-add');
    const modal = document.getElementById('curriculum-modal');
    const deleteModal = document.getElementById('delete-modal');
    const form = document.getElementById('curriculum-form');
    const deleteForm = document.getElementById('delete-form');
    const modalTitle = document.getElementById('modal-title');
    const methodField = document.getElementById('method-field');
    const toast = document.getElementById('toast-notification');

    // Modal (Animation เล็กน้อย)
    const toggleModal = (modalId, show) => {
        const el = document.getElementById(modalId);
        if (!el) return;

        const content = el.querySelector('div');

        if (show) {
            el.classList.remove('hidden');
            setTimeout(() => {
                if(content) content.classList.replace('scale-95', 'scale-100');
            }, 10);
        } else {
            if(content) content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => {
                el.classList.add('hidden');
            }, 200);
        }
    };

    // ปุ่มเพิ่มหลักสูตร
    if (btnAdd) {
        btnAdd.addEventListener('click', () => {
            const storeUrl = btnAdd.getAttribute('data-route-store');
            form.action = storeUrl;
            methodField.value = "POST";
            modalTitle.textContent = "เพิ่มหลักสูตรใหม่";
            form.reset();
            toggleModal('curriculum-modal', true);
        });
    }

    // Event Delegation (สำหรับปุ่ม Edit / Delete ในตาราง)
    document.addEventListener('click', (e) => {
        // ปุ่ม Edit
        const editBtn = e.target.closest('.btn-edit');
        if (editBtn) {
            const tr = editBtn.closest('tr');
            const data = tr.dataset;

            form.action = `/management/curriculum/${data.id}`;
            methodField.value = "PUT";
            modalTitle.textContent = `แก้ไขหลักสูตร: ${data.name}`;
            
            document.getElementById('input-year').value = data.year;
            document.getElementById('input-name').value = data.name;
            document.getElementById('input-faculty').value = data.faculty;
            document.getElementById('input-major').value = data.major;
            document.getElementById('input-campus').value = data.campus;

            toggleModal('curriculum-modal', true);
        }

        // ปุ่ม Delete
        const deleteBtn = e.target.closest('.btn-delete');
        if (deleteBtn) {
            const tr = deleteBtn.closest('tr');
            deleteForm.action = `/management/curriculum/${tr.dataset.id}`;
            toggleModal('delete-modal', true);
        }
    });

    // ปุ่มปิด Modal ต่างๆ
    const closeButtons = [
        document.getElementById('btn-close-modal'),
        document.getElementById('btn-close-x'),
        document.getElementById('btn-close-delete')
    ];

    closeButtons.forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                toggleModal('curriculum-modal', false);
                toggleModal('delete-modal', false);
            });
        }
    });

    // คลิกพื้นหลังเพื่อปิด Modal
    window.addEventListener('click', (e) => {
        if (e.target === modal) toggleModal('curriculum-modal', false);
        if (e.target === deleteModal) toggleModal('delete-modal', false);
    });
});