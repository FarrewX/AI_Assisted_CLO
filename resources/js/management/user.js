document.addEventListener('DOMContentLoaded', () => {
    console.log("User Management JS Loaded");

    // --- Helper: Modal Control ---
    const toggleModal = (modalId, show) => {
        const el = document.getElementById(modalId);
        if (!el) return;
        
        if (show) {
            el.classList.remove('hidden');
            el.classList.add('flex');
            setTimeout(() => {
                const content = el.querySelector('div[class*="transform"]');
                if(content) content.classList.replace('scale-95', 'scale-100');
            }, 10);
        } else {
            const content = el.querySelector('div[class*="transform"]');
            if(content) content.classList.replace('scale-100', 'scale-95');
            setTimeout(() => {
                el.classList.add('hidden');
                el.classList.remove('flex');
            }, 200);
        }
    };

    // --- 1. Edit Role Logic ---
    const editRoleModalId = 'edit-role-modal';
    const editForm = document.getElementById('edit-role-form');
    const deleteModalId = 'delete-user-modal';
    const deleteForm = document.getElementById('delete-user-form');
    const deleteNameSpan = document.getElementById('delete-user-name');
    const addModalId = 'add-user-modal';
    const restoreModalId = 'restore-user-modal';
    const restoreForm = document.getElementById('restore-user-form');
    const restoreNameSpan = document.getElementById('restore-user-name');
    
    document.body.addEventListener('click', (e) => {
        
        // ปุ่ม Edit Role
        const editBtn = e.target.closest('.btn-edit-role');
        if (editBtn) {
            const data = editBtn.dataset;
            console.log("Edit Clicked:", data);

            // 1.1 ใส่ข้อมูลพื้นฐาน
            document.getElementById('modal-user-name').textContent = data.name;
            document.getElementById('modal-user-email').textContent = data.email;
            
            // 1.2 🔥 แก้ไขส่วนแสดงรูปภาพ (Avatar Logic)
            const imgEl = document.getElementById('modal-user-img');
            const textEl = document.getElementById('modal-user-avatar');
            
            if (data.avatar) {
                // ถ้ามีรูป (URL ส่งมาจาก data-avatar)
                imgEl.src = data.avatar;
                imgEl.classList.remove('hidden'); // แสดงรูป
                textEl.classList.add('hidden');   // ซ่อนตัวอักษร
            } else {
                // ถ้าไม่มีรูป
                imgEl.src = '';
                imgEl.classList.add('hidden');    // ซ่อนรูป
                textEl.classList.remove('hidden');// แสดงตัวอักษร
                textEl.textContent = data.name.charAt(0).toUpperCase();
            }
            
            // 1.3 🔥 แก้ไขส่วนเลือก Role (ใช้ ID แทนการเทียบชื่อ)
            const roleSelect = document.getElementById('modal-role-select');
            if (roleSelect && data.roleId) {
                roleSelect.value = data.roleId; 
            }

            // 1.4 ตั้งค่า Action Form
            // ใช้ data.id (ซึ่งคือ user_id)
            if(editForm) editForm.action = `/management/users/${data.id}/update-role`; 
            
            toggleModal(editRoleModalId, true);
        }

        // ปุ่ม Close Modal
        if (e.target.closest('.btn-close-modal')) {
            toggleModal(editRoleModalId, false);
        }

        // ปิด Modal เมื่อคลิกพื้นหลัง
        if (e.target.id === editRoleModalId) {
            toggleModal(editRoleModalId, false);
        }

        // --- 2. Delete User Logic ---
        const deleteBtn = e.target.closest('.btn-delete-user');
        if (deleteBtn) {
            const userName = deleteBtn.dataset.name;
            const userId = deleteBtn.dataset.id;

            // 1. ใส่ชื่อคนที่จะลบใน Popup
            if (deleteNameSpan) deleteNameSpan.textContent = `"${userName}"`;

            // 2. กำหนด Action ให้ Form (ยิงไป Route destroy)
            if (deleteForm) deleteForm.action = `/management/users/${userId}`;

            // 3. เปิด Modal
            toggleModal(deleteModalId, true);
        }

        // ปุ่มปิด Modal ลบ (ปุ่มยกเลิก)
        if (e.target.closest('.btn-close-delete-modal')) {
            toggleModal(deleteModalId, false);
        }
        
        // ปิดเมื่อคลิกพื้นหลัง Modal ลบ
        if (e.target.id === deleteModalId) {
            toggleModal(deleteModalId, false);
        }

        // --- 3. Add User Logic ---
        const addUserBtn = document.getElementById('btn-add-user');
        if (addUserBtn) {
            addUserBtn.addEventListener('click', () => {
                toggleModal(addModalId, true);
            });
        }
    
        // ปิด Modal (ปุ่มกากบาท และปุ่มยกเลิก)
        if (e.target.closest('.btn-close-add-modal')) {
            toggleModal(addModalId, false);
        }
        
        // ปิดเมื่อคลิกพื้นหลัง
        if (e.target.id === addModalId) {
            toggleModal(addModalId, false);
        }

        // --- 4. Restore User Logic ---
        const restoreBtn = e.target.closest('.btn-restore-user');
        if (restoreBtn) {
            const userName = restoreBtn.dataset.name;
            const userId = restoreBtn.dataset.id;
    
            if (restoreNameSpan) restoreNameSpan.textContent = `"${userName}"`;
            
            // ตั้งค่า Action ของ Form ให้ยิงไป Route restore
            if (restoreForm) restoreForm.action = `/management/users/${userId}/restore`;
    
            toggleModal(restoreModalId, true);
        }
    });
});