import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    if (window.jQuery) {
        const $ = window.jQuery;
        
        // Select2 สำหรับตัวกรองหลักสูตร
        $('#curriculum').select2({
            placeholder: "-- เลือกหลักสูตร --",
            width: '100%',
            minimumResultsForSearch: Infinity 
        }).on('select2:select', function (e) {
            $(this).closest('form').submit();
        });

        // Select2 สำหรับตัวกรองรายวิชา
        $('#course').select2({
            placeholder: "-- เลือกรายวิชา --",
            allowClear: true,
            width: '100%'
        }).on('select2:select', function (e) {
            $(this).closest('form').submit();
        });
    }

    const toggleModal = (modalId, show) => {
        const el = document.getElementById(modalId);
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

    // Edit Button
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.getAttribute('data-user-id');
            const year = btn.getAttribute('data-year');
            const term = btn.getAttribute('data-term');
            const tqf = btn.getAttribute('data-tqf');
            const url = btn.getAttribute('data-update-url');

            // ใส่ค่าลง Input
            const yearInput = document.getElementById('edit-year-input');
            const termInput = document.getElementById('edit-term-input');
            const tqfInput = document.getElementById('edit-TQF-input');
            const userInput = document.getElementById('edit-user-input');
            const form = document.getElementById('edit-form');

            if(yearInput) yearInput.value = year;
            if(termInput) termInput.value = term;
            if(tqfInput) tqfInput.value = tqf;
            if(userInput) userInput.value = userId;
            
            if(form) form.action = url;

            toggleModal('edit-modal', true);
        });
    });

    const editCancel = document.getElementById('edit-cancel');
    if(editCancel) {
        editCancel.addEventListener('click', () => toggleModal('edit-modal', false));
    }

    // Delete Button
    let currentDeleteForm = null;
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // หา Form ที่หุ้มปุ่มนี้อยู่
            currentDeleteForm = btn.closest('form');

            const infoEl = document.getElementById('delete-course-name');
            if (infoEl) {
                const name = btn.getAttribute('data-name') || '';
                const year = btn.getAttribute('data-year') || '';
                const term = btn.getAttribute('data-term') || '';
                let text = '';
                if(name) text += `อาจารย์: ${name}`;
                if(year) text += text ? `, ปี ${year}` : `ปี ${year}`;
                if(term) text += text ? `, ภาคเรียน ${term}` : `ภาคเรียน ${term}`;
                infoEl.textContent = text;
            }

            toggleModal('delete-modal', true);
        });
    });

    const deleteConfirm = document.getElementById('delete-confirm');
    if(deleteConfirm) {
        deleteConfirm.addEventListener('click', () => {
            if(currentDeleteForm) currentDeleteForm.submit();
        });
    }

    const deleteCancel = document.getElementById('delete-cancel');
    if(deleteCancel) {
        deleteCancel.addEventListener('click', () => {
            toggleModal('delete-modal', false);
            currentDeleteForm = null;
            const infoEl = document.getElementById('delete-course-name');
            if(infoEl) infoEl.textContent = '';
        });
    }

    // Session Modal & Error Modal Close Logic
    window.closeSessionModal = function() {
        const modals = ['session-modal', 'error-modal'];
        modals.forEach(id => {
            const modal = document.getElementById(id);
            if(modal) {
                modal.classList.add('opacity-0');
                const content = modal.querySelector('.bg-white');
                if(content) content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => modal.remove(), 300);
            }
        });
    };

    // ปิด Modal เมื่อคลิกพื้นหลัง
    window.addEventListener('click', (e) => {
        const modals = ['edit-modal', 'delete-modal', 'session-modal', 'error-modal'];
        modals.forEach(id => {
            const modal = document.getElementById(id);
            if(e.target === modal) {
                if(id === 'session-modal' || id === 'error-modal') {
                    closeSessionModal();
                } else {
                    toggleModal(id, false);
                    if(id === 'delete-modal') {
                        const infoEl = document.getElementById('delete-course-name');
                        if(infoEl) infoEl.textContent = '';
                        currentDeleteForm = null;
                    }
                }
            }
        });
    });
});