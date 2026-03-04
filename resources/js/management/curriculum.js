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
            const infoEl = document.getElementById('delete-curriculum-info');
            if (infoEl) {
                const year = tr.dataset.year || tr.getAttribute('data-year') || '';
                let text = '';
                if (year) text += `ปีหลักสูตร ${year}`;
                infoEl.textContent = text || '-';
            }

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
                const infoEl = document.getElementById('delete-curriculum-info');
                if (infoEl) infoEl.textContent = '';
            });
        }
    });

    // คลิกพื้นหลังเพื่อปิด Modal
    window.addEventListener('click', (e) => {
        if (e.target === modal) toggleModal('curriculum-modal', false);
        if (e.target === deleteModal) {
            toggleModal('delete-modal', false);
            const infoEl = document.getElementById('delete-curriculum-info');
            if (infoEl) infoEl.textContent = '';
        }
    });

    const subSettingsModal = document.getElementById('sub-settings-modal');
    const btnCloseSubSettings = document.getElementById('btn-close-sub-settings');
    
    // Badge HTML
    const badgeComplete = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">มีข้อมูลแล้ว</span>`;
    const badgeMissing = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">ยังไม่มีข้อมูล</span>`;

    // เปิด Modal Settings ย่อย
    document.querySelectorAll('.btn-sub-settings').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const year = this.getAttribute('data-year');
            const name = this.getAttribute('data-name');
            
            // ดึงสถานะ
            const hasPhilosophy = this.getAttribute('data-has-philosophy') === 'true';
            const hasPlo = this.getAttribute('data-has-plo') === 'true';
            const hasLlls = this.getAttribute('data-has-llls') === 'true';

            document.getElementById('sub-settings-title').innerText = name;

            document.getElementById('link-manage-philosophy').href = `/management/philosophy?year=${year}`;
            document.getElementById('link-manage-plo').href = `/management/plos?year=${year}`;
            document.getElementById('link-manage-llls').href = `/management/llls?year=${year}`;

            // อัปเดต Badge แจ้งเตือน
            document.getElementById('status-philosophy').innerHTML = hasPhilosophy ? badgeComplete : badgeMissing;
            document.getElementById('status-plo').innerHTML = hasPlo ? badgeComplete : badgeMissing;
            document.getElementById('status-llls').innerHTML = hasLlls ? badgeComplete : badgeMissing;

            subSettingsModal.classList.remove('hidden');
            setTimeout(() => {
                subSettingsModal.classList.remove('opacity-0');
                const content = subSettingsModal.querySelector('.bg-white');
                if(content) content.classList.remove('scale-95');
            }, 10);
        });
    });

    // ปิด Modal Settings ย่อย
    btnCloseSubSettings.addEventListener('click', () => {
        subSettingsModal.classList.add('opacity-0');
        const content = subSettingsModal.querySelector('.bg-white');
        if(content) content.classList.add('scale-95');
        setTimeout(() => {
            subSettingsModal.classList.add('hidden');
        }, 300);
    });

    // ปิดเมื่อคลิกพื้นที่พื้นหลัง
    subSettingsModal.addEventListener('click', (e) => {
        if (e.target === subSettingsModal) {
            subSettingsModal.classList.add('opacity-0');
            const content = subSettingsModal.querySelector('.bg-white');
            if(content) content.classList.add('scale-95');
            setTimeout(() => {
                subSettingsModal.classList.add('hidden');
            }, 300);
        }
    });
});