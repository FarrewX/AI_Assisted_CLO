document.addEventListener('DOMContentLoaded', () => {
    // ดึงข้อมูลจาก Global Variable ที่ประกาศไว้ใน Blade
    const PAGE_DATA = window.pageData || {};

    // สำหรับ Popup (SweetAlert2)
    const AppAlert = (message, iconType = 'warning') => {
        return new Promise((resolve) => {
            Swal.fire({
                title: 'แจ้งเตือน',
                text: message,
                icon: iconType,
                didOpen: (modal) => {
                    const popup = modal.closest('.swal2-container');
                    const defaultActions = popup.querySelector('.swal2-actions');
                    
                    if (defaultActions) {
                        defaultActions.innerHTML = '';
                        
                        // Create OK button
                        const okBtn = document.createElement('button');
                        okBtn.className = 'px-5 py-2.5 bg-[#035AA6] hover:bg-[#6CBAD9] text-white font-medium rounded-xl shadow-md transition-colors';
                        okBtn.textContent = 'ตกลง';
                        okBtn.addEventListener('click', () => {
                            Swal.close();
                            resolve({ isConfirmed: true });
                        });
                        
                        defaultActions.style.display = 'flex';
                        defaultActions.style.justifyContent = 'center';
                        defaultActions.appendChild(okBtn);
                    }
                },
                showConfirmButton: false
            });
        });
    };

    const AppConfirm = (message) => {
        return new Promise((resolve) => {
            Swal.fire({
                title: 'ยืนยันการดำเนินการ',
                text: message,
                icon: 'warning',
                didOpen: (modal) => {
                    const popup = modal.closest('.swal2-container');
                    const defaultActions = popup.querySelector('.swal2-actions');
                    
                    if (defaultActions) {
                        defaultActions.innerHTML = '';
                        
                        // Create cancel button
                        const cancelBtn = document.createElement('button');
                        cancelBtn.id = 'edit-plo-cancel';
                        cancelBtn.className = 'px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-xl transition-colors';
                        cancelBtn.textContent = 'ยกเลิก';
                        cancelBtn.addEventListener('click', () => {
                            Swal.close();
                            resolve({ isConfirmed: false });
                        });
                        
                        // Create save button
                        const saveBtn = document.createElement('button');
                        saveBtn.id = 'edit-plo-save';
                        saveBtn.className = 'px-5 py-2.5 bg-[#035AA6] hover:bg-[#6CBAD9] text-white font-medium rounded-xl shadow-md transition-colors flex items-center gap-2';
                        saveBtn.innerHTML = 'บันทึกการแก้ไข';
                        saveBtn.addEventListener('click', () => {
                            Swal.close();
                            resolve({ isConfirmed: true });
                        });
                        
                        defaultActions.style.display = 'flex';
                        defaultActions.style.gap = '10px';
                        defaultActions.style.justifyContent = 'center';
                        defaultActions.appendChild(saveBtn);
                        defaultActions.appendChild(cancelBtn);
                    }
                },
                showCancelButton: false,
                confirmButtonText: ''
            });
        });
    };

    async function saveData(fieldName, value, targetElement = null) {
        const saveUrl = '/savedataedit';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const urlParams = new URLSearchParams(window.location.search);
        
        // แก้ไขให้ดึงค่า CC_id (ให้ตรงกับ URL parameter ที่คุณใช้)
        const CC_id = urlParams.get('CC_id'); 
        const year = urlParams.get('year');
        const term = urlParams.get('term');
        const tqf = urlParams.get('TQF');
        
        // Find the element for feedback
        let element = document.querySelector(`[data-field="${CSS.escape(fieldName)}"]`);
        if (!element) element = document.querySelector(`[name="${CSS.escape(fieldName)}"]`);
        if (!element) element = document.querySelector(`[id="${CSS.escape(fieldName)}"]`);

        if (!element && fieldName.startsWith('curriculum_map_r')) {
            const matches = fieldName.match(/curriculum_map_r(\d+)_c(\d+)/);
            if (matches) element = document.querySelector(`#curriculum-mapping-table .score-cell[data-row="${matches[1]}"][data-col="${matches[2]}"]`);
        }

        // Special handling for checkboxes inside a cell for Section 6
        if (fieldName.startsWith('s6_r') && fieldName.includes('_cb')) {
            element = document.querySelector(`input[name="${CSS.escape(fieldName)}"]`);
        }
        // Special handling for triggers
        if (!element && fieldName === 'section6_data') element = document.getElementById('cloTable_S6');
        if (!element && fieldName === 'section7_data') element = document.getElementById('planTable');
        if (!element && fieldName === 'section8_1_data') element = document.getElementById('assessmentTable');
        if (!element && fieldName === 'section8_2_data') element = document.getElementById('rubric-container');
        if (!element && fieldName === 'section9_1_data') element = document.getElementById('referenceList');
        if (!element && fieldName.startsWith('plo')) element = document.getElementById('plosTableBody');
        
        // console.log('Saving:', { field: fieldName, value: value, CC_id: CC_id });
        
        // Feedback Logic
        let originalColor = '';
        let feedbackElement = null;
        
        if (targetElement) {
             feedbackElement = targetElement.closest('td') || targetElement.closest('li') || targetElement.closest('.rubric-section');
             if (!feedbackElement) feedbackElement = targetElement;
        }

        if (!feedbackElement) feedbackElement = element;
        const formContainer = document.getElementById('tqf-form-container') || document.querySelector('.w-\\[210mm\\]'); 
        if (!feedbackElement && formContainer) feedbackElement = formContainer; 

        if (feedbackElement) {
            originalColor = feedbackElement.style.backgroundColor;
            feedbackElement.style.transition = 'background-color 0.1s ease';
            feedbackElement.style.backgroundColor = '#fffbdd';
        }

        try {
            const response = await fetch(saveUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    CC_id: CC_id,
                    year: year, 
                    term: term, 
                    TQF: tqf,
                    field: fieldName,
                    value: typeof value === 'object' ? JSON.stringify(value) : value
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error('Server error response:', errorData);
                throw new Error(`Server error: ${response.status}`);
            }

            const result = await response.json();
            // console.log('Save successful:', result);

            if (feedbackElement) {
                feedbackElement.style.backgroundColor = '#d4edda'; 
                setTimeout(() => {
                     if(feedbackElement) {
                        feedbackElement.style.backgroundColor = originalColor;
                        feedbackElement.style.transition = '';
                     }
                }, 1000);
            }

        } catch (error) {
            console.error('Save failed:', error);
            if (feedbackElement) {
                feedbackElement.style.backgroundColor = '#f8d7da';
                setTimeout(() => {
                     if(feedbackElement) {
                         feedbackElement.style.backgroundColor = originalColor;
                         feedbackElement.style.transition = '';
                     }
                }, 2000);
            }
        }
    }

    function getFieldName(element) {
        // ดักจับช่องแก้ไขเนื้อหา CLO (หมวด 2.2)
        if (element.classList.contains('clo-description')) {
            const row = element.closest('.clo-item-row');
            if (row) {
                const keyInput = row.querySelector('.clo-key');
                if (keyInput) {
                    return `cloLll_desc_${keyInput.value.trim()}`;
                }
            }
        }

        const section6Container = element.closest('#section-6-container');
        if (section6Container) return 'section6_data_trigger';

        const section7Container = element.closest('#section-7-container');
        if (section7Container) {
            if(element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') return 'section7_data_trigger';
        }

        const section8Container = element.closest('#section-8-container');
        if (section8Container) {
            if (element.closest('#assessmentTable')) {
                if(element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') return 'section8_1_data_trigger';
            }
            if (element.closest('#rubric-container')) {
                if (element.hasAttribute('contenteditable') || element.tagName === 'INPUT' || element.tagName === 'SELECT' || element.tagName === 'TEXTAREA') return 'section8_2_data_trigger';
            }
        }

        const section9Container = element.closest('#section-9-container');
        if (section9Container) {
            if (element.closest('#referenceList') && (element.tagName === 'INPUT' || element.classList.contains('remove-btn'))) return 'section9_1_data_trigger';
            if (element.tagName === 'TEXTAREA' && element.name && !element.closest('#referenceList')) return element.name;
        }

        if (element.dataset.field) return element.dataset.field;
        if (element.name) return element.name;
        if (element.id) return element.id;

        const cell = element.closest('td, th');
        const row = element.closest('tr');
        const table = element.closest('table');
        if (!table || !row || !cell) return null;

        const tableId = table.id || 'unknown_table';
        const parentElement = row.parentElement;
        const rowIndex = Array.from(parentElement.children).indexOf(row);
        const cellIndex = Array.from(row.children).indexOf(cell);
        const query = element.tagName;
        const elementsInCell = Array.from(cell.querySelectorAll(query + ', [contenteditable="true"]'));
        const elementIndex = elementsInCell.length > 1 ? elementsInCell.indexOf(element) : 0;

        if (tableId === 'curriculum-mapping-table' && element.classList.contains('score-cell')) {
            return `curriculum_map_r${element.dataset.row}_c${element.dataset.col}`;
        }
        if (tableId === 'ploTable' || tableId === 'plosTableBody') {
            const codeCell = row.cells[0];
            const code = codeCell ? codeCell.textContent.trim() : null;
            if (!code) return null;

            if (element.tagName === 'SPAN' && element.hasAttribute('contenteditable')) {
                return `cloLll_desc_${code}`;
            } else {
                const mapColIndex = cellIndex - 2;
                if (mapColIndex >= 0) {
                    const inputType = element.type === 'checkbox' ? 'check' : 'level';
                    return `plo_map_${code}_c${mapColIndex}_${inputType}`;
                }
            }
            if(element.name && element.name.startsWith('plo')) return element.name;
        }
        
        if (tableId && tableId.startsWith('rubricTable_') && element.hasAttribute('contenteditable')) { if(element.dataset.field) return element.dataset.field; }
        if (element.name && element.name.startsWith('reference_') && element.tagName === 'INPUT') { return element.name; }
        if (tableId === 'gradeTable' && element.hasAttribute('contenteditable')) { if(element.dataset.field) return element.dataset.field; }

        console.warn("Using fallback naming for element:", element);
        return `${tableId}_r${rowIndex}_c${cellIndex}_e${elementIndex}`;
    }

    function handleFormChange(event) {
        const target = event.target;
        const nodeName = target.nodeName;
        let fieldName = getFieldName(target);

        if (!fieldName) { console.warn("Could not determine field name on change:", target); return; }

        if (fieldName === 'section9_1_data_trigger') { return; }

        let value;
        if (nodeName === 'INPUT') {
            value = (target.type === 'checkbox') ? target.checked : target.value;
        } else if (nodeName === 'TEXTAREA' || nodeName === 'SELECT') {
            value = target.value;
        } else { return; }
        saveData(fieldName, value, target);
    }
    
    function handleFormBlur(event) {
        const target = event.target;
        if (target instanceof HTMLElement && target.isContentEditable) {
            let fieldName = getFieldName(target);
            if (!fieldName) { console.warn("Could not determine field name on blur:", target); return; }

            if (fieldName === 'section9_1_data_trigger') { saveData('section9_1_data', getSection9_1Data(), target); return; }

            const value = target.textContent.trim();
            saveData(fieldName, value, target);
        }
        if (target.tagName === 'INPUT' && target.closest('#referenceList')) {
            saveData('section9_1_data', getSection9_1Data(), target);
        }
        if (target.tagName === 'TEXTAREA' && target.closest('#section-9-container') && !target.closest('#referenceList') && target.name) {
            saveData(target.name, target.value, target);
        }
    }

    // Bind Global Events
    const formContainer = document.querySelector('.w-\\[210mm\\]');
    if (formContainer) {
        formContainer.addEventListener('change', handleFormChange);
        formContainer.addEventListener('blur', handleFormBlur, true);
    }

    window.SHARED_CLO_DATA = {};

    try {
        let rawText = PAGE_DATA.aiText;
        
        // กรณีที่ Blade แปลงมาเป็น Object ให้แล้ว
        if (typeof rawText === 'object') {
            window.SHARED_CLO_DATA = rawText;
        } 
        // กรณีเป็นก้อนตัวหนังสือจาก Database
        else if (typeof rawText === 'string') {
            // ตัด Markdown (```json) และช่องว่างที่มองไม่เห็นทิ้งไปก่อน
            rawText = rawText.replace(/```json/ig, '').replace(/```/g, '').trim();
            
            if (rawText !== '') {
                window.SHARED_CLO_DATA = JSON.parse(rawText);
            }
        }

        // ถ้าแปลงแล้วหลุดมาเป็นค่าประหลาด ให้บังคับเป็น Object
        if (!window.SHARED_CLO_DATA || typeof window.SHARED_CLO_DATA !== 'object') {
            window.SHARED_CLO_DATA = {};
        }
        
        // ถ้ารอดมาถึงตรงนี้ แสดงว่า JSON สมบูรณ์ร้อยเปอร์เซ็นต์
        // if (Object.keys(window.SHARED_CLO_DATA).length > 0) {
        //     console.log("✅ โหลดข้อมูล CLO สำเร็จ:", window.SHARED_CLO_DATA);
        // }

    } catch (e) {
        console.warn("⚠️ พบ JSON ใน Database ผิดรูปแบบ!");
        
        // ถ้า JSON พังจนแปลงไม่ได้ ให้สกัดเฉพาะคำว่า CLO ออกมา
        let finalData = {};
        const rawString = String(PAGE_DATA.aiText || '');
        
        // ค้นหา Block ที่มีคำอธิบาย CLO อยู่ข้างใน
        const blocks = rawString.match(/\{\s*"CLO"[\s\S]*?\}/g) || rawString.match(/\{\s*CLO[\s\S]*?\}/g);
        
        if (blocks) {
            blocks.forEach((block, index) => {
                const cloKey = `CLO ${index + 1}`;
                // ดึงข้อความในช่อง CLO ออกมา
                const cloMatch = block.match(/"?CLO"?\s*:\s*"([^"]+)"/i) || block.match(/"?CLO"?\s*:\s*([^,\n}]+)/i);
                if (cloMatch) {
                    finalData[cloKey] = { "CLO": cloMatch[1].trim() };
                }
            });
            window.SHARED_CLO_DATA = finalData;
            // console.log("🛠️ ซ่อมแซมข้อมูลสำเร็จ! กู้คืนมาได้:", window.SHARED_CLO_DATA);
        } else {
            console.error("❌ ข้อมูลพังเกินเยียวยา สกัดไม่ได้:", rawString);
            window.SHARED_CLO_DATA = {};
        }
    }

    // --- Section 2.2 ---
    function renderSection2_2() {
        const container = document.getElementById('clo-input-container');
        if (!container) return;

        container.innerHTML = ''; 
        
        // ถ้าเป็น null ให้มองเป็น Object ว่างๆ {}
        const safeData = window.SHARED_CLO_DATA || {};
        const cloKeys = Object.keys(safeData);

        if (cloKeys.length === 0) {
            container.innerHTML = `<div class="text-sm text-red-500 italic p-3 bg-red-50 rounded">⚠️ ไม่มีข้อมูล CLO</div>`;
            return;
        }

        cloKeys.sort((a, b) => (parseInt(a.replace(/\D/g, '')) || 0) - (parseInt(b.replace(/\D/g, '')) || 0));

        cloKeys.forEach(key => {
            const details = safeData[key];
            const row = document.createElement('div');
            row.className = 'flex gap-2 clo-item-row mb-2';
            
            // 🌟 เพิ่มปุ่ม "ลบ" เข้าไปในแถว
            row.innerHTML = `
                <input type="text" class="w-20 p-2 text-sm font-bold border rounded bg-gray-100 text-center clo-key" value="${key.replace(/\s/g, '')}" readonly>
                <input type="text" 
                    class="flex-1 p-2 text-sm border rounded focus:ring-1 focus:ring-blue-500 clo-description" 
                    data-original-key="${key}"
                    name="ignore_auto"
                    value="${details.CLO || ''}">
                <button type="button" onclick="deleteCLO('${key}')" class="px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors text-sm shadow-sm">
                    ลบ
                </button>
            `;
            container.appendChild(row);
        });
    }

    // เพิ่ม CLO ใหม่
    window.addNewCLO = function() {
        if (!window.SHARED_CLO_DATA) window.SHARED_CLO_DATA = {};
        
        // หาเลข CLO สูงสุดเพื่อรันเลขถัดไป
        let maxNum = 0;
        for (let key in window.SHARED_CLO_DATA) {
            let num = parseInt(key.replace(/\D/g, ''));
            if (!isNaN(num) && num > maxNum) maxNum = num;
        }
        let nextNum = maxNum + 1;
        let newKey = "CLO " + nextNum;

        window.SHARED_CLO_DATA[newKey] = { CLO: "" };
        
        renderSection2_2();

        if (typeof saveData === 'function') {
            saveData('ai_text', JSON.stringify(window.SHARED_CLO_DATA));
        }
    };

    // ลบ CLO
    window.deleteCLO = function(cloKey) {
        if (!confirm(`คุณแน่ใจหรือไม่ว่าต้องการลบ [${cloKey}] ?\n\n*หมายเหตุ: ข้อมูลในตารางอื่นๆ ที่ผูกกับ CLO นี้อาจได้รับผลกระทบ`)) {
            return;
        }

        // ลบ Key ออกจาก Object หลัก
        delete window.SHARED_CLO_DATA[cloKey];
        
        renderSection2_2();

        if (typeof saveData === 'function') {
            saveData('ai_text', JSON.stringify(window.SHARED_CLO_DATA));
        }
    };

    // เรียกใช้งานครั้งแรกตอนโหลดหน้าเว็บ
    renderSection2_2();

    // จัดการ Event Listener สำหรับการพิมพ์แก้ไขข้อความ
    const container = document.getElementById('clo-input-container');
    if (container) {
        // เมื่อมีการพิมพ์และคลิกออกนอกกล่อง (change/blur)
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('clo-description')) {
                const cloKey = e.target.getAttribute('data-original-key');
                const newValue = e.target.value;

                // อัปเดตข้อความใหม่เข้าไปในตัวแปร JSON ส่วนกลาง
                if (window.SHARED_CLO_DATA[cloKey]) {
                    window.SHARED_CLO_DATA[cloKey].CLO = newValue;
                }

                // แพ็คข้อมูลทั้งหมดกลับเป็น JSON String
                const updatedJSON = JSON.stringify(window.SHARED_CLO_DATA);

                // สั่งเซฟข้อมูลทั้งก้อนลงฐานข้อมูล (คอลัมน์ ai_text)
                if (typeof saveData === 'function') {
                    // console.log(`💾 กำลังบันทึกการแก้ไขของ ${cloKey}...`);
                    saveData('ai_text', updatedJSON, e.target);
                }
            }
        });
    }

    // --- ปุ่มดึงข้อมูล หมวด 4 ---
    const fetchPrevAgreementBtn = document.getElementById('fetchPrevAgreementBtn');
    if (fetchPrevAgreementBtn) {
        fetchPrevAgreementBtn.addEventListener('click', async () => {
            // ดึงพารามิเตอร์จาก URL
            const urlParams = new URLSearchParams(window.location.search);
            const CC_id = urlParams.get('CC_id'); 
            const year = urlParams.get('year');
            const term = urlParams.get('term');

            if (!CC_id || !year || !term) {
                AppAlert('ข้อมูล URL ไม่ครบถ้วน ไม่สามารถดึงข้อมูลได้');
                return;
            }

            // ยืนยันก่อนดึงหากมีข้อมูลเดิมพิมพ์ค้างไว้อยู่
            const textarea = document.querySelector('textarea[name="agreement"]');
            if (textarea && textarea.value.trim() !== '') {
                const conf = await AppConfirm('คุณมีข้อความเดิมอยู่แล้ว การดึงข้อมูลใหม่จะลบข้อความเดิมทิ้ง ต้องการทำต่อหรือไม่?');
                if (!conf.isConfirmed) return;
            }

            try {
                // เปลี่ยนข้อความปุ่มระหว่างโหลด
                const originalText = fetchPrevAgreementBtn.innerHTML;
                fetchPrevAgreementBtn.innerHTML = 'กำลังค้นหา...';
                fetchPrevAgreementBtn.disabled = true;

                const response = await fetch(`/get-previous-agreement?CC_id=${CC_id}&year=${year}&term=${term}`);
                const result = await response.json();

                if (result.success) {
                    if (textarea) {
                        textarea.value = result.data; // เติมข้อความลงกล่อง
                        
                        // สั่ง Auto-save ทันทีที่ดึงข้อมูลเสร็จ
                        if (typeof saveData === 'function') {
                            saveData('agreement', result.data, textarea);
                        }
                    }
                } else {
                    AppAlert(result.message || 'ไม่พบข้อมูลเก่า', 'info');
                }
                
                // คืนค่าปุ่มเดิม
                fetchPrevAgreementBtn.innerHTML = originalText;
                fetchPrevAgreementBtn.disabled = false;

            } catch (error) {
                console.error('Error fetching previous agreement:', error);
                AppAlert('เกิดข้อผิดพลาดในการดึงข้อมูลจากเซิร์ฟเวอร์', 'error');
                fetchPrevAgreementBtn.innerText = 'ดึงข้อมูลเก่า';
                fetchPrevAgreementBtn.disabled = false;
            }
        });
    }

    // --- Section 5.1 Logic ---
    try {
        const addRowBtn = document.getElementById('addPloRowBtn');
        const tableBody = document.getElementById('plosTableBody');

        if (addRowBtn && tableBody) {
            addRowBtn.addEventListener('click', function() {
                let currentRowIndex = parseInt(tableBody.dataset.maxPlo, 10);
                let newRowIndex = currentRowIndex + 1;
                tableBody.dataset.maxPlo = newRowIndex;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td class="text-center border border-black p-2.5 align-top">${newRowIndex}</td>
                    <td class="border border-black p-2.5 align-top">
                        <textarea name="plo${newRowIndex}_outcome" rows="3" class="w-full border border-gray-300 rounded p-1"></textarea>
                    </td>
                    <td class="text-center border border-black p-2.5 align-top">
                        <input type="checkbox" name="plo${newRowIndex}_specific" class="scale-125" value="1">
                    </td>
                    <td class="text-center border border-black p-2.5 align-top">
                        <input type="checkbox" name="plo${newRowIndex}_generic" class="scale-125" value="1">
                    </td>
                    <td class="text-center border border-black p-2.5 align-top">
                        <select name="plo${newRowIndex}_level" class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">--เลือก--</option>
                            <option value="R">R</option> <option value="U">U</option>
                            <option value="AP">AP</option> <option value="AN">AN</option>
                            <option value="E">E</option> <option value="C">C</option>
                        </select>
                    </td>
                    <td class="text-center border border-black p-2.5 align-top">
                        <select name="plo${newRowIndex}_type" class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">--เลือก--</option>
                            <option value="K">K</option> <option value="S">S</option>
                            <option value="AR">AR</option> <option value="Rs">Rs</option>
                        </select>
                    </td>
                `;
                tableBody.appendChild(newRow);
                const firstInput = newRow.querySelector('textarea[name^="plo"]');
                if(firstInput) saveData(firstInput.name, firstInput.value);
            });
        }
    } catch(e) { console.error("S5.1 Error", e); }

    // --- Section 5.2 Logic ---
    try {
        const curriculumMapData = PAGE_DATA.curriculumMapData || [];
        const stateSymbols_s5_2 = { '0': '&nbsp;', '1': '<span class="text-xl font-bold">●</span>', '2': '<span class="text-xl">○</span>' };
        const mappingTable = document.getElementById('curriculum-mapping-table');

        // ปุ่มดึงข้อมูล 5.2
        const fetchPrevMapBtn = document.getElementById('fetchPrevMapBtn');
        if (fetchPrevMapBtn) {
            fetchPrevMapBtn.addEventListener('click', async () => {
                const urlParams = new URLSearchParams(window.location.search);
                const CC_id = urlParams.get('CC_id'); 
                const year = urlParams.get('year');
                const term = urlParams.get('term');

                const conf = await AppConfirm('ข้อมูลเดิมจะถูกแทนที่ด้วยข้อมูลจุดจากเทอมก่อนหน้า ต้องการทำต่อหรือไม่?');
                if (!conf.isConfirmed) return;

                try {
                    const originalText = fetchPrevMapBtn.innerHTML;
                    fetchPrevMapBtn.innerHTML = 'กำลังโหลด...';
                    fetchPrevMapBtn.disabled = true;

                    const response = await fetch(`/get-previous-curriculum-map?CC_id=${CC_id}&year=${year}&term=${term}`);
                    const result = await response.json();

                    if (result.success && result.data && result.data.length > 0) {
                        const rowData = result.data[0] || {}; 
                        
                        // วาดจุดลงในตาราง HTML ปัจจุบัน
                        const cells = mappingTable.querySelectorAll('.score-cell');
                        cells.forEach(cell => {
                            const colIndex = cell.dataset.col;
                            const state = rowData[colIndex] ?? 0;
                            cell.dataset.state = state;
                            cell.innerHTML = stateSymbols_s5_2[state] || '&nbsp;';
                        });

                        // อัปเดตข้อมูลบนหน้าจอ
                        PAGE_DATA.curriculumMapData = result.data; 

                        // สั่ง Save ข้อมูลทั้งตารางลง Database ทันที
                        saveData('curriculum_map_data_all', result.data, mappingTable);

                    } else {
                        AppAlert(result.message || 'ไม่พบข้อมูลเก่า', 'info');
                    }

                    fetchPrevMapBtn.innerHTML = originalText;
                    fetchPrevMapBtn.disabled = false;

                } catch (error) {
                    console.error('Error fetching previous map:', error);
                    AppAlert('เกิดข้อผิดพลาดในการดึงข้อมูลจากเซิร์ฟเวอร์', 'error');
                    fetchPrevMapBtn.innerText = 'ดึงข้อมูลเก่า';
                    fetchPrevMapBtn.disabled = false;
                }
            });
        }

        if (mappingTable && Array.isArray(curriculumMapData) && curriculumMapData.length > 0 && typeof curriculumMapData[0] === 'object') {
            const rowData = curriculumMapData[0] || {};
            const cells = mappingTable.querySelectorAll('.score-cell');
            cells.forEach(cell => {
                const rowIndex = parseInt(cell.dataset.row); 
                const colIndex = parseInt(cell.dataset.col); 
                const state = rowData[String(colIndex)] ?? 0;
                cell.dataset.state = state;
                cell.innerHTML = stateSymbols_s5_2[state] || '&nbsp;';

                cell.addEventListener('click', () => {
                    let currentState = parseInt(cell.dataset.state || '0');
                    let nextState = (currentState + 1) % 3;
                    cell.dataset.state = nextState;
                    cell.innerHTML = stateSymbols_s5_2[nextState];
                    saveData(`curriculum_map_r${rowIndex}_c${colIndex}`, nextState); 
                });
            });
        } else {
             const cells = mappingTable ? mappingTable.querySelectorAll('.score-cell') : [];
             cells.forEach(cell => {
                 cell.dataset.state = '0';
                 cell.innerHTML = stateSymbols_s5_2['0'];
                 cell.addEventListener('click', () => {
                     let currentState = parseInt(cell.dataset.state || '0');
                     let nextState = (currentState + 1) % 3;
                     cell.dataset.state = nextState;
                     cell.innerHTML = stateSymbols_s5_2[nextState];
                     saveData(`curriculum_map_r${cell.dataset.row}_c${cell.dataset.col}`, nextState);
                 });
             });
        }
    } catch (e) { console.error("S5.2 Error", e); }

    // --- Section 5.3 Logic ---
    try {
        // แปลงตัวย่อเป็นชื่อเต็ม
        function getFullLevelName(abbr, domainStr = '') {
            if (!abbr) return '';
            const cleanAbbr = abbr.trim().toLowerCase();
            
            const map = {
                'r': 'Remember', 'u': 'Understand', 'ap': 'Apply',
                'an': 'Analyz', 'e': 'Evaluate', 'c': 'Create',
                'pe': 'Perception', 'se': 'Set', 'gr': 'Guided Response',
                'me': 'Mechanism', 'cor': 'Complex Over Response', 'ad': 'Adaptation',
                'rp': 'Receiving Phenomena', 'rs': 'Responding to Phenomena',
                'va': 'Valuing', 'iv': 'Internalizes Values'
            };
            
            if (map[cleanAbbr]) return map[cleanAbbr];
            
            if (cleanAbbr === 'or') {
                const lowerDomain = String(domainStr).toLowerCase();
                if (lowerDomain.includes('skill')) return 'Origination';
                if (lowerDomain.includes('ethic') || lowerDomain.includes('character')) return 'Organization';
                return 'Origination'; 
            }
            return abbr; 
        }

        // ดึงเฉพาะตัวเลขออกจากข้อความ PLO
        function getPloNumbers(ploText) {
            if (!ploText) return [];
            const matches = String(ploText).match(/\d+/g);
            return matches ? matches.map(Number) : [];
        }

        function renderSection5_3() {
            const aiTextData = window.SHARED_CLO_DATA || {};
            let cloData = [];

            if (Object.keys(aiTextData).length > 0) {
                Object.keys(aiTextData).forEach(key => {
                    const details = aiTextData[key];
                    if (details && typeof details === 'object' && details.CLO) {
                        cloData.push({ code: key.replace(/\s/g, ''), description: details.CLO });
                    }
                });
                cloData.sort((a, b) => (parseInt(a.code.replace('CLO', '')) || 0) - (parseInt(b.code.replace('CLO', '')) || 0));
            }

            const lllData = PAGE_DATA.lllData || [];
            const cloLllData = [...cloData, ...lllData];
            const ploCount = PAGE_DATA.ploCount || 0;
            const table = document.querySelector("#ploTable");
            const tbody = table ? table.querySelector("tbody") : null;
            
            // ดึงค่า Level จากหัวตารางมาเก็บไว้เป็น Array
            const ploHeaders = table ? Array.from(table.querySelectorAll("thead th")).slice(2) : [];
            const ploLevelsFromHeader = ploHeaders.map(th => {
                const match = th.textContent.match(/\((.*?)\)/);
                return match ? match[1].trim() : '';
            });

            if (tbody) {
                tbody.innerHTML = '';
                cloLllData.forEach((item, rowIndex) => {
                    const tr = document.createElement("tr");
                    const itemCode = item.code ?? `Item ${rowIndex+1}`;
                    const isLLL = itemCode.startsWith('LLL');

                    // ดึงข้อมูล AI ประจำแถว
                    let aiMappedPlos = [];
                    if (!isLLL) {
                        const originalKey = itemCode.replace('CLO', 'CLO ');
                        const aiDetails = aiTextData[originalKey] || aiTextData[itemCode] || {};
                        let ploRaw = '';
                        
                        for (const k in aiDetails) {
                            const lowerK = k.toLowerCase();
                            if (lowerK.includes('plo')) ploRaw = aiDetails[k];
                        }
                        aiMappedPlos = getPloNumbers(ploRaw);
                    }

                    tr.innerHTML = `
                        <td class="border border-gray-400 p-2 text-center font-bold" readonly>${itemCode}</td>
                        <td class="border border-gray-400 p-2 text-left">
                           <span class="inline-block w-full ${isLLL ? 'text-gray-800' : ''} p-1 rounded"
                                data-field="cloLll_desc_${itemCode}">${item.description ?? ''}</span>
                        </td>`;

                    for (let colIndex = 0; colIndex < ploCount; colIndex++) {
                        const td = document.createElement("td");
                        td.className = "border border-gray-400 p-2 text-center";
                        const ploDbIndex = colIndex + 1;
                        
                        let savedCellData = { check: false, level: '' };
                        const targetPloLevel = ploLevelsFromHeader[colIndex] || '';

                        if (isLLL) {
                            const mappedPlos = item.mapped_plos || [];
                            if (mappedPlos.includes(ploDbIndex)) {
                                savedCellData.check = true;
                                savedCellData.level = targetPloLevel;
                            }
                        } else {
                        // สำหรับ CLO ดึงจาก AI
                            if (aiMappedPlos.includes(ploDbIndex)) {
                                savedCellData.check = true;
                                savedCellData.level = targetPloLevel;
                            }
                        }

                        if (isLLL) {
                        // โชว์ติ๊กถูกสำหรับ LLL
                            if (savedCellData.check) {
                                td.innerHTML = `<span class="font-bold text-lg">✔</span>`;
                            } else {
                                td.innerHTML = `<span class="text-gray-200"></span>`;
                            }
                        } else {
                        // สร้าง Checkbox สำหรับแก้ไข CLO
                            const checkbox = document.createElement("input");
                            checkbox.type = "checkbox";
                            checkbox.className = `mr-1.5 scale-125 plo-map-checkbox cursor-pointer`;
                            checkbox.name = `plo_map_${itemCode}_c${colIndex}_check`; 
                            checkbox.checked = savedCellData.check;
                            
                            const hiddenLevelInput = document.createElement("input");
                            hiddenLevelInput.type = "hidden";
                            hiddenLevelInput.name = `plo_map_${itemCode}_c${colIndex}_level`;
                            hiddenLevelInput.value = savedCellData.check ? targetPloLevel : '';

                            const levelDisplay = document.createElement("div");
                            levelDisplay.className = "text-xs mt-1 text-gray-600 font-medium h-4"; 
                            levelDisplay.textContent = savedCellData.check ? targetPloLevel : '';

                        // เมื่อคนคลิกเปลี่ยนค่า Checkbox
                            checkbox.addEventListener('change', function() {
                                if(this.checked) {
                                    levelDisplay.textContent = targetPloLevel;
                                    hiddenLevelInput.value = targetPloLevel;
                                } else {
                                    levelDisplay.textContent = '';
                                    hiddenLevelInput.value = '';
                                }

                            // เซฟลง JSON
                                if (!isLLL && window.SHARED_CLO_DATA) {
                                    let targetKey = Object.keys(window.SHARED_CLO_DATA).find(k => k.replace(/\s/g, '') === itemCode);
                                    
                                    if (targetKey) {
                                        const rowCheckboxes = tr.querySelectorAll('input[type="checkbox"]:checked');
                                        let selectedPlos = [];
                                        let selectedLevels = [];
                                        
                                        const domainKey = window.SHARED_CLO_DATA[targetKey].hasOwnProperty('Domain') ? 'Domain' : 'Domain';
                                        const domainStr = window.SHARED_CLO_DATA[targetKey][domainKey] || '';

                                        rowCheckboxes.forEach(cb => {
                                            const match = cb.name.match(/_c(\d+)_check/);
                                            if (match) {
                                                const colIdx = parseInt(match[1]);
                                                const ploNum = colIdx + 1;
                                                selectedPlos.push(`PLO${ploNum}`);
                                                
                                                const lvlAbbr = ploLevelsFromHeader[colIdx] || '';
                                                if (lvlAbbr) {
                                                    lvlAbbr.split(',').forEach(abbr => {
                                                        const fullLvl = getFullLevelName(abbr, domainStr);
                                                        if (fullLvl && !selectedLevels.includes(fullLvl)) {
                                                            selectedLevels.push(fullLvl);
                                                        }
                                                    });
                                                }
                                            }
                                        });

                                        const ploKey = window.SHARED_CLO_DATA[targetKey].hasOwnProperty('PLO') ? 'PLO' : 'PLO ต่อ ร้องรับ';
                                        const levelKey = window.SHARED_CLO_DATA[targetKey].hasOwnProperty('Learning Level') ? 'Learning Level' : "Learning's Level";

                                        window.SHARED_CLO_DATA[targetKey][ploKey] = selectedPlos.length > 0 ? selectedPlos.join(', ') : '-';
                                        window.SHARED_CLO_DATA[targetKey][levelKey] = selectedLevels.length > 0 ? selectedLevels.join(', ') : '-';

                                        if (typeof saveData === 'function') {
                                            saveData('ai_text', JSON.stringify(window.SHARED_CLO_DATA), this);
                                        }
                                    }
                                }
                            });

                            td.appendChild(checkbox);
                            td.appendChild(levelDisplay);
                            td.appendChild(hiddenLevelInput);
                        }
                        
                        tr.appendChild(td);
                    }
                    tbody.appendChild(tr);
                });
            }
        }

        // วาดตาราง 5.3 ตอนโหลดหน้าเว็บครั้งแรก
        renderSection5_3();

        // ซิงค์การอัปเดตตาราง 5.3 เมื่อมีการแก้ไขข้อความ CLO ในหมวด 2.2
        const cloInputContainer = document.getElementById('clo-input-container');
        if (cloInputContainer) {
            cloInputContainer.addEventListener('change', (e) => {
                if (e.target.classList.contains('clo-description')) {
                    // หน่วงเวลารอให้หมวด 2.2 อัปเดตตัวเองให้เสร็จก่อน
                    setTimeout(() => {
                        renderSection5_3();
                    }, 100);
                }
            });
        }

    } catch (s5Error) { console.error("S5.3 Error", s5Error); }

    // --- Section 6 Logic ---
    try {
        const data_s6_teachingOptions = { 
            onSite: { 
                label: "On-site โดยมีการเรียนการสอนแบบ", 
                items: ["บรรยาย (Lecture)","ฝึกปฏิบัติ (Laboratory Model)","เรียนรู้จากการลงมือทำ (Learning by Doing)","การเรียนรู้โดยใช้กิจกรรมเป็นฐาน (Activity-based Learning)",
                    "การเรียนรู้โดยใช้วิจัยเป็นฐาน (Research-based Learning)","ถามตอบสะท้อนคิด (Refractive Learning)","นำเสนออภิปรายกลุ่ม (Discussion Group)",
                    "เรียนรู้จากการสืบเสาะหาความรู้ (Inquiry-based Learning)","การเรียนรู้แบบร่วมมือร่วมใจ (Cooperative Learning)","การเรียนรู้แบบร่วมมือ (Collaborative Learning)",
                    "การเรียนรู้โดยใช้โครงการเป็นฐาน (Project-based Learning: PBL)","การเรียนรู้โดยใช้ปัญหาเป็นฐาน (Problem-based Learning)","วิเคราะห์โจทย์ปัญหา (Problem Solving)",
                    "เรียนรู้การตัดสินใจ (Decision Making)","ศึกษาค้นคว้าจากกรณีศึกษา (Case Study)","เรียนรู้ผ่านการเกม/การเล่น (Game/Play Learning)","ศึกษาดูงาน (Field Trips)",
                    "อื่น ๆ....................................." ], 
                indent: true 
            }, 
            online: { 
                label: "Online โดยมีการเรียนการสอนแบบ", 
                items: ["บรรยาย (Lecture)"], 
                indent: true 
            }
        };
        const data_s6_assessmentOptions = { 
            exam: { 
                label: "คะแนนจากแบบทดสอบสัมฤทธิ์ผล (ข้อสอบ)", 
                items: ["ทดสอบย่อย (Quiz)","ทดสอบกลางภาค (Midterm)","ทดสอบปลายภาค (Final)" ] 
            }, 
            performance: { 
                label: "คะแนนจากผลงานที่ได้รับมอบหมาย (Performance) โดยใช้เกณฑ์ Rubric Score การทำงานกลุ่มและเดี่ยว", 
                items: ["การทำงานเป็นทีม (Team Work)","โปรแกรม ซอฟต์แวร์","ผลงาน ชิ้นงาน","รายงาน (Report)","การนำเสนอ (Presentation)",
                    "แฟ้มสะสมงาน (Portfolio)","รายงานการศึกษาด้วยตนเอง (Self-Study Report)" ] 
            }, 
            behavior: { 
                label: "คะแนนจากผลการพฤติกรรม", 
                items: ["ความรับผิดชอบ การมีส่วนร่วม","ประเมินผลการงานที่ส่ง" ] 
            } 
        };

        // ดึงข้อมูลจัดรูปแบบใหม่ (แยกย่อยตามหมวดหมู่)
        function getSection6Data() {
            const sectionData = {};
            const tableBody = document.getElementById('cloTableBody_S6');
            if (!tableBody) return sectionData;

            const rows = tableBody.querySelectorAll('tr.clo-row');

            rows.forEach((row, rowIndex) => {
                const cloCell = row.querySelector('.clo-cell-s6');
                // ดึง Key ให้ตัดข้อความภาษาไทยทิ้ง เอาแค่ "CLO1" ไว้เป็น Key สำหรับเซฟ
                let fullCloText = cloCell ? cloCell.textContent.trim() : `CLO${rowIndex + 1}`;
                let cloKeyMatch = fullCloText.match(/CLO\s*\d+/i);
                let cloKey = cloKeyMatch ? cloKeyMatch[0].replace(/\s/g, '') : `CLO${rowIndex + 1}`;

                // สร้าง Object เก็บหมวดย่อยของการสอน
                const teachingMethods = {};
                row.querySelectorAll('.teaching-cell-s6 input[type="checkbox"]:checked').forEach(cb => {
                    const category = cb.getAttribute('data-category');
                    const val = cb.value;
                    if (!teachingMethods[category]) teachingMethods[category] = [];
                    teachingMethods[category].push(val);
                });

                // สร้าง Object เก็บหมวดย่อยของการประเมิน
                const assessmentMethods = {};
                row.querySelectorAll('.assessment-cell-s6 input[type="checkbox"]:checked').forEach(cb => {
                    const category = cb.getAttribute('data-category');
                    const val = cb.value;
                    if (!assessmentMethods[category]) assessmentMethods[category] = [];
                    assessmentMethods[category].push(val);
                });

                sectionData[cloKey] = [
                    { "วิธีการสอน": teachingMethods },
                    { "การประเมินผล": assessmentMethods }
                ];
            });
            // console.log("Prepared Section 6 Data (Nested):", sectionData);
            return sectionData;
        }

        function initializeCloTable_Section6() {
            const tableBody = document.getElementById('cloTableBody_S6');
            const template = document.getElementById('cloRowTemplate_S6');

            let section6Data = {};

            try {
                const jsonDataString = PAGE_DATA.teachingMethods || null;
                if (typeof jsonDataString === 'string' && jsonDataString.length > 0 && jsonDataString !== 'null') {
                    section6Data = parseAIJSON(jsonDataString);
                } else if (typeof jsonDataString === 'object' && jsonDataString !== null) {
                    section6Data = jsonDataString;
                }
                if (typeof section6Data !== 'object' || section6Data === null) section6Data = {};
            } catch (parseError) {
                console.error("Error parsing section 6 JSON data:", parseError);
                section6Data = {};
            }

            let cloKeysFromAI = Object.keys(window.SHARED_CLO_DATA).map(key => key.replace(/\s/g, '').toUpperCase());
            cloKeysFromAI.sort((a, b) => (parseInt(a.replace(/\D/g, '')) || 0) - (parseInt(b.replace(/\D/g, '')) || 0));

            // สร้าง Checkbox รองรับการอ่านทั้ง Format เก่าและใหม่
            function _s6_createCheckboxHtml(optionsObject, type, rowData) {
                let html = '';
                const typeKey = (type === 'teach') ? 'วิธีการสอน' : 'การประเมินผล';
                let relevantData = null;

                // ดึงก้อนข้อมูล (วิธีการสอน หรือ การประเมินผล) ของ CLO นั้นๆ
                if (rowData && Array.isArray(rowData)) {
                    const relevantObject = rowData.find(obj => obj && typeof obj === 'object' && obj.hasOwnProperty(typeKey));
                    if (relevantObject) relevantData = relevantObject[typeKey];
                }

                for (const key in optionsObject) {
                    const category = optionsObject[key];
                    const catLabel = category.label; 
                    
                    html += `<div class="font-semibold mt-2">${catLabel}</div>`;
                    
                    category.items.forEach(itemText => {
                        const indentClass = category.indent ? 'ml-4' : '';
                        let isChecked = false;
                        
                        if (relevantData) {
                            if (Array.isArray(relevantData)) {
                                isChecked = relevantData.includes(String(itemText).trim());
                            } else if (typeof relevantData === 'object' && relevantData[catLabel] && Array.isArray(relevantData[catLabel])) {
                                isChecked = relevantData[catLabel].includes(String(itemText).trim());
                            }
                        }

                        html += `
                            <label class="flex items-start ${indentClass} hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" class="mt-1 scale-125 mr-1.5 s6-checkbox" data-category="${catLabel}" value="${itemText}" ${isChecked ? 'checked' : ''}>
                                <span class="ml-2">${itemText}</span>
                            </label>
                        `;
                    });
                }
                return html;
            }

            function _s6_addNewCloRow(cloKey = null, rowData = null, rowIndex = -1) {
                if (rowIndex === -1) rowIndex = tableBody.children.length;
                if (!cloKey) cloKey = `CLO${rowIndex + 1}`;

                const newRow = template.content.cloneNode(true);
                const cloRowElement = newRow.querySelector('tr');
                if (!cloRowElement) return;

                const cloCell = newRow.querySelector('.clo-cell-s6');
                const teachingCell = newRow.querySelector('.teaching-cell-s6');
                const assessmentCell = newRow.querySelector('.assessment-cell-s6');

                if (cloCell.hasAttribute('data-field')) cloCell.removeAttribute('data-field');

                // ดึงข้อความอธิบาย (Description) ของ CLO
                let cloDescription = '';
                // หาคีย์ "CLO 1" หรือ "CLO1"
                const originalKey = Object.keys(window.SHARED_CLO_DATA).find(k => k.replace(/\s/g, '').toUpperCase() === cloKey);
                if (originalKey && window.SHARED_CLO_DATA[originalKey].CLO) {
                    cloDescription = window.SHARED_CLO_DATA[originalKey].CLO;
                }

                // จับข้อความมารวมกัน (ถ้ามี Description ก็ให้ใส่เคาะวรรคต่อท้าย)
                if (cloDescription) {
                    cloCell.innerHTML = `<span class="font-bold">${cloKey}</span> ${cloDescription}`;
                } else {
                    cloCell.innerHTML = `<span class="font-bold">${cloKey}</span>`;
                }

                teachingCell.innerHTML = _s6_createCheckboxHtml(data_s6_teachingOptions, 'teach', rowData);
                assessmentCell.innerHTML = _s6_createCheckboxHtml(data_s6_assessmentOptions, 'assess', rowData);

                tableBody.appendChild(newRow);
            }

            tableBody.innerHTML = '';
            
            if (cloKeysFromAI.length > 0) {
                cloKeysFromAI.forEach((cloKey, index) => { 
                    const savedData = section6Data[cloKey] || null; 
                    _s6_addNewCloRow(cloKey, savedData, index);
                });
            } else {
                const loadedCloKeys = Object.keys(section6Data);
                if (loadedCloKeys.length > 0) {
                    loadedCloKeys.sort((a, b) => {
                            const numA = parseInt(a.replace('CLO', '')) || 0;
                            const numB = parseInt(b.replace('CLO', '')) || 0;
                            return numA - numB;
                    });
                    loadedCloKeys.forEach((key, index) => {
                        _s6_addNewCloRow(key, section6Data[key], index);
                    });
                } else {
                    _s6_addNewCloRow(null, null, 0); 
                }
            }
            
            tableBody.addEventListener('change', (event) => {
                if (event.target.type === 'checkbox') {
                    const section6JSON = getSection6Data();
                    saveData('section6_data', section6JSON, event.target);
                }
            });
        }
        
        initializeCloTable_Section6();

        // อัปเดตตัวเองอัตโนมัติ ถ้ามีการพิมพ์แก้ไขใน 2.2
        document.getElementById('clo-input-container')?.addEventListener('change', (e) => {
            if (e.target.classList.contains('clo-description')) {
                setTimeout(() => {
                    const currentS6Data = getSection6Data();
                    PAGE_DATA.teachingMethods = currentS6Data;
                    initializeCloTable_Section6();
                }, 100);
            }
        });

    } catch (e) {
        console.error("Error initializing Section 6 (CLO Table):", e);
    }

    // --- Section 7 Logic ---
    try {
        function getSection7Data() {
            const planData = [];
            const tableBody = document.querySelector("#planTable tbody");
            if (!tableBody) return planData;

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                const weekNumber = index + 1;
                const rowData = {
                    week: weekNumber,
                    topic: row.querySelector(`textarea[name="plan_topic_${weekNumber}"]`)?.value || '',
                    objective: row.querySelector(`textarea[name="plan_objective_${weekNumber}"]`)?.value || '',
                    activity: row.querySelector(`textarea[name="plan_activity_${weekNumber}"]`)?.value || '',
                    tool: row.querySelector(`textarea[name="plan_tool_${weekNumber}"]`)?.value || '',
                    assessment: row.querySelector(`textarea[name="plan_assessment_${weekNumber}"]`)?.value || '',
                    clo: row.querySelector(`select[name="plan_clo_${weekNumber}"]`)?.value || ''
                };
                planData.push(rowData);
            });
            // console.log("Prepared Section 7 Data:", planData);
            return planData;
        }

        function createCloOptions(cloKeys, selectedClo) {
            let optionsHtml = '<option value="">--เลือก--</option>';
            
            if (!Array.isArray(cloKeys) || cloKeys.length === 0) {
                console.warn("S7: No CLO keys from ai_text, using default fallback.");
                optionsHtml += '<option value="CLO1" ' + (selectedClo === 'CLO1' ? 'selected' : '') + '>CLO 1</option>';
                optionsHtml += '<option value="CLO2" ' + (selectedClo === 'CLO2' ? 'selected' : '') + '>CLO 2</option>';
                optionsHtml += '<option value="CLO3" ' + (selectedClo === 'CLO3' ? 'selected' : '') + '>CLO 3</option>';
            } else {
                cloKeys.forEach(cloKey => { 
                    const isSelected = (selectedClo === cloKey) ? 'selected' : '';
                    const cleanKey = cloKey.replace(/(\d+)$/, ' $1'); 
                    optionsHtml += `<option value="${cloKey}" ${isSelected}>${cleanKey}</option>`; 
                });
            }
            return optionsHtml;
        }

        function addTableLesson(forceInputCount = false) {
            const tbody = document.querySelector("#planTable tbody");
            if (!tbody) { console.warn("Table body for lesson plan (#planTable tbody) not found."); return; }
            tbody.innerHTML = "";
            const weekCountInput = document.getElementById("weekCount");
            
            const loadedPlanData = PAGE_DATA.planData || [];
            let lastWeekWithData = 0;
            if (Array.isArray(loadedPlanData) && loadedPlanData.length > 0) {
                for (let i = loadedPlanData.length - 1; i >= 0; i--) {
                    const weekData = loadedPlanData[i] || {};
                    const isEmpty = !(weekData.topic || weekData.objective || weekData.activity || weekData.tool || weekData.assessment || weekData.clo);
                    if (!isEmpty) {
                        lastWeekWithData = parseInt(weekData.week || 0);
                        break; 
                    }
                }
            }
            
            const minWeekFromData = lastWeekWithData > 0 ? lastWeekWithData : 0; 
            const inputWeekCount = (weekCountInput && !isNaN(parseInt(weekCountInput.value))) ? parseInt(weekCountInput.value) : 10; 

            let weekCount;
            if(forceInputCount) {
                weekCount = Math.max(inputWeekCount, minWeekFromData); 
            } else {
                weekCount = Math.max(inputWeekCount, minWeekFromData);
            }
            
            if (weekCount > 20) weekCount = 20;
            if (weekCount < 1) weekCount = 1;
            if (weekCountInput && parseInt(weekCountInput.value) !== weekCount) { 
                weekCountInput.value = weekCount;
            }

            let cloKeysFromAI = Object.keys(window.SHARED_CLO_DATA).map(key => key.replace(/\s/g, '').toUpperCase());
            cloKeysFromAI.sort((a, b) => (parseInt(a.replace(/\D/g, '')) || 0) - (parseInt(b.replace(/\D/g, '')) || 0));

            for (let i = 1; i <= weekCount; i++) {
                const weekData = loadedPlanData.find(item => parseInt(item.week) === i) || {};
                const row = document.createElement("tr");
                const cloOptions = createCloOptions(cloKeysFromAI, weekData.clo || '');

                row.innerHTML = `
                    <td class="border border-black p-2.5 align-top text-center">${i}</td>
                    <td class="border border-black p-1 align-top">
                        <textarea name="plan_topic_${i}" placeholder="ใส่หัวข้อสัปดาห์ ${i}" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.topic || ''}</textarea>
                    </td>
                    <td class="border border-black p-1 align-top">
                        <textarea name="plan_objective_${i}" placeholder="ใส่วัตถุประสงค์" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.objective || ''}</textarea>
                    </td>
                    <td class="border border-black p-1 align-top">
                        <textarea name="plan_activity_${i}" placeholder="ใส่กิจกรรมการเรียนรู้" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.activity || ''}</textarea>
                    </td>
                    <td class="border border-black p-1 align-top">
                        <textarea name="plan_tool_${i}" placeholder="ใส่สื่อ/เครื่องมือ" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.tool || ''}</textarea>
                    </td>
                    <td class="border border-black p-1 align-top">
                        <textarea name="plan_assessment_${i}" placeholder="ใส่การประเมินผล" class="w-full h-16 border border-gray-300 rounded p-1">${weekData.assessment || ''}</textarea>
                    </td>
                    <td class="border border-black p-2.5 align-top text-center">
                        <select name="plan_clo_${i}" class="border rounded px-2 py-1 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                            ${cloOptions}
                        </select>
                    </td>
                `;
                tbody.appendChild(row);
            }
        }

        const getValueFlexible = (obj, possibleKeys) => {
            if (!obj || typeof obj !== 'object') return '';
            const lowerObj = Object.keys(obj).reduce((acc, key) => {
                acc[key.toLowerCase()] = obj[key];
                return acc;
            }, {});
            
            for (let key of possibleKeys) {
                if (lowerObj[key.toLowerCase()] !== undefined && lowerObj[key.toLowerCase()] !== null) {
                    return lowerObj[key.toLowerCase()];
                }
            }
            return '';
        };

        function generateTableLesson(dataToRender = null) {
            const tbody = document.querySelector("#planTable tbody");
            if (!tbody) return;
            tbody.innerHTML = "";
            const weekCountInput = document.getElementById("weekCount");
            
            // เลือก Data
            let loadedPlanData = [];
            if (dataToRender && Array.isArray(dataToRender)) {
                loadedPlanData = dataToRender;
            } else {
                let rawPlanData = PAGE_DATA.planData || [];
                if (typeof rawPlanData === 'string') {
                    try { rawPlanData = JSON.parse(rawPlanData); } catch (e) { rawPlanData = []; }
                }
                if (rawPlanData && typeof rawPlanData === 'object' && !Array.isArray(rawPlanData)) {
                    let foundArray = Object.values(rawPlanData).find(val => Array.isArray(val));
                    rawPlanData = foundArray ? foundArray : Object.values(rawPlanData);
                }
                loadedPlanData = Array.isArray(rawPlanData) ? rawPlanData : [];
            }

            // คำนวณจำนวนสัปดาห์
            let lastWeekWithData = 0;
            if (loadedPlanData.length > 0) {
                for (let i = loadedPlanData.length - 1; i >= 0; i--) {
                    const weekData = loadedPlanData[i] || {};
                    if (getValueFlexible(weekData, ['topic', 'หัวข้อ']) || getValueFlexible(weekData, ['objective', 'วัตถุประสงค์'])) {
                        lastWeekWithData = parseInt(getValueFlexible(weekData, ['week', 'สัปดาห์', 'สัปดาห์ที่'])) || 0;
                        break; 
                    }
                }
            }
            
            const minWeekFromData = lastWeekWithData > 0 ? lastWeekWithData : 0; 
            const inputWeekCount = (weekCountInput && !isNaN(parseInt(weekCountInput.value))) ? parseInt(weekCountInput.value) : 10; 

            let weekCount = dataToRender ? dataToRender.length : Math.max(inputWeekCount, minWeekFromData);
            if (weekCount > 20) weekCount = 20;
            if (weekCount < 1) weekCount = 1;
            if (weekCountInput && parseInt(weekCountInput.value) !== weekCount) { 
                weekCountInput.value = weekCount;
            }

            let cloKeysFromAI = Object.keys(window.SHARED_CLO_DATA || {}).map(key => key.replace(/\s/g, '').toUpperCase());
            cloKeysFromAI.sort((a, b) => (parseInt(a.replace(/\D/g, '')) || 0) - (parseInt(b.replace(/\D/g, '')) || 0));

            // วาดตารางตามข้อมูล
            for (let i = 1; i <= weekCount; i++) {
                const weekData = loadedPlanData.find(item => parseInt(getValueFlexible(item, ['week', 'สัปดาห์', 'สัปดาห์ที่'])) === i) || {};

                const valTopic = getValueFlexible(weekData, ['topic', 'หัวข้อ', 'เนื้อหา']);
                const valObjective = getValueFlexible(weekData, ['objective', 'วัตถุประสงค์']);
                const valActivity = getValueFlexible(weekData, ['activity', 'กิจกรรม', 'วิธีการสอน']);
                const valTool = getValueFlexible(weekData, ['tool', 'สื่อ', 'อุปกรณ์']);
                const valAssessment = getValueFlexible(weekData, ['assessment', 'ประเมิน', 'การประเมินผล']);
                const valClo = getValueFlexible(weekData, ['clo', 'clos']);

                const row = document.createElement("tr");
                const cloOptions = createCloOptions(cloKeysFromAI, valClo);

                row.innerHTML = `
                    <td class="border border-black p-2.5 align-top text-center font-bold">${i}</td>
                    <td class="border border-black p-1 align-top"><textarea name="plan_topic_${i}" class="w-full h-24 border border-gray-300 rounded p-1 text-sm">${valTopic}</textarea></td>
                    <td class="border border-black p-1 align-top"><textarea name="plan_objective_${i}" class="w-full h-24 border border-gray-300 rounded p-1 text-sm">${valObjective}</textarea></td>
                    <td class="border border-black p-1 align-top"><textarea name="plan_activity_${i}" class="w-full h-24 border border-gray-300 rounded p-1 text-sm">${valActivity}</textarea></td>
                    <td class="border border-black p-1 align-top"><textarea name="plan_tool_${i}" class="w-full h-24 border border-gray-300 rounded p-1 text-sm">${valTool}</textarea></td>
                    <td class="border border-black p-1 align-top"><textarea name="plan_assessment_${i}" class="w-full h-24 border border-gray-300 rounded p-1 text-sm">${valAssessment}</textarea></td>
                    <td class="border border-black p-2.5 align-top text-center"><select name="plan_clo_${i}" class="w-full border rounded px-1 py-1 text-sm bg-white">${cloOptions}</select></td>
                `;
                tbody.appendChild(row);
            }
        }
        
        // วาดตารางครั้งแรกตอนโหลดหน้าเว็บ
        generateTableLesson();

        const addlessonBtn = document.getElementById('addTableLesson');
        if (addlessonBtn) {
            addlessonBtn.addEventListener('click', (event) => {
                PAGE_DATA.planData = getSection7Data(); 
                generateTableLesson();
                const section7JSON = getSection7Data();
                saveData('section7_data', section7JSON, event.target);
            });
        }

        const lessonBtn = document.getElementById('generateLessonTableBtn');
        if (lessonBtn) {
            lessonBtn.addEventListener('click', async (event) => {
                const weekCount = document.getElementById("weekCount").value || 10;
                const conf = await AppConfirm(`ต้องการให้ AI สร้างแผนการสอน ${weekCount} สัปดาห์อัตโนมัติหรือไม่?\n(ข้อมูลในตารางปัจจุบันจะถูกแทนที่)`);
                if (!conf.isConfirmed) return;

                const originalText = lessonBtn.innerHTML;

                const loadingOverlay = document.createElement('div');
                loadingOverlay.id = 'ai-loading-overlay';
                loadingOverlay.innerHTML = `
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-80 flex flex-col items-center justify-center z-[9999] backdrop-blur-sm transition-opacity">
                        <i class="fa-solid fa-robot fa-bounce text-orange-500 text-6xl mb-6"></i>
                        <h2 class="text-white text-2xl font-bold tracking-wider mb-2">กำลังให้ AI วิเคราะห์และสร้างแผนการสอน...</h2>
                        <p class="text-gray-300 text-lg">กำลังสร้างเนื้อหา ${weekCount} สัปดาห์ กรุณารอสักครู่ ☕</p>
                        <div class="mt-6 flex gap-3">
                            <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: -0.3s"></div>
                            <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: -0.15s"></div>
                            <div class="w-3 h-3 bg-orange-500 rounded-full animate-bounce" style="animation-delay: -0.0s"></div>
                        </div>
                    </div>
                `;
                document.body.appendChild(loadingOverlay);
                
                try {
                    lessonBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>กำลังให้ AI คิด...';
                    lessonBtn.disabled = true;

                    // ดึงข้อมูลวิธีการสอนหมวด 6
                    const s6Data = {};
                    const cloKeysData = Object.keys(window.SHARED_CLO_DATA || {});
                    
                    // ถ้าดึงจาก DOM ไม่ได้ ให้ลองจำลองค่าเริ่มต้น
                    cloKeysData.forEach(key => {
                        const cleanKey = key.replace(/\s/g, '');
                        // ลองดึงจาก DOM
                        const row = document.querySelector(`#cloTableBody_S6 tr[data-code="${cleanKey}"]`);
                        let teach = [];
                        let assess = [];
                        
                        if (row) {
                            teach = Array.from(row.querySelectorAll('.teaching-cell-s6 input[type="checkbox"]:checked')).map(cb => cb.value);
                            assess = Array.from(row.querySelectorAll('.assessment-cell-s6 input[type="checkbox"]:checked')).map(cb => cb.value);
                        }
                        
                        // ถ้าไม่มีข้อมูลเลย ให้ยัดข้อความเปล่าๆ
                        s6Data[cleanKey] = { 
                            "วิธีการสอน": teach.length > 0 ? teach : ["ให้ AI แนะนำ"], 
                            "การประเมินผล": assess.length > 0 ? assess : ["ให้ AI แนะนำ"]
                        };
                    });

                    const payload = {
                        CC_id: new URLSearchParams(window.location.search).get('CC_id'),
                        year: new URLSearchParams(window.location.search).get('year'),
                        term: new URLSearchParams(window.location.search).get('term'),
                        weekCount: parseInt(weekCount),
                        cloData: window.SHARED_CLO_DATA || {},
                        section6Data: s6Data
                    };

                    const response = await fetch('/generate-lesson-plan-ai', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();
                    
                    // console.log("Prompt Debug:", result.prompt_debug || 'No Prompt'); 
                    console.log("AI Raw Output:", result.raw_output || 'No Raw Output');  

                    // ถ้า Backend ตอบว่า success = false ให้โยน Error เลย
                    if (!result.success) {
                        throw new Error(result.message || 'AI เกิดข้อผิดพลาดในการสร้างเนื้อหา');
                    }

                    // ถ้าผ่านเงื่อนไข success ค่อยมาเช็ก Data
                    let finalData = [];
                    if (result.data) {
                        if (typeof result.data === 'string') {
                            let cleanedStr = result.data.replace(/```json/gi, '').replace(/```/g, '').trim();
                            try {
                                finalData = JSON.parse(cleanedStr);
                            } catch(e) {
                                throw new Error("ไม่สามารถอ่านข้อมูล JSON จาก AI ได้");
                            }
                        } else if (Array.isArray(result.data)) {
                            finalData = result.data;
                        }

                        if (!Array.isArray(finalData) || finalData.length === 0) {
                            throw new Error("AI ส่งตารางเปล่ากลับมา (ไม่มีข้อมูล)");
                        }

                        PAGE_DATA.planData = finalData; 
                        generateTableLesson(finalData); 
                        
                        const section7JSON = getSection7Data();
                        saveData('section7_data', section7JSON, document.getElementById('planTable'));
                        AppAlert('AI สร้างแผนการสอนสำเร็จ!', 'success');
                    } else {
                        throw new Error('ไม่พบข้อมูล (Data) ในการตอบกลับจาก AI');
                    }

                } catch (error) {
                    console.error("AI Lesson Plan Error:", error);
                    AppAlert(error.message, 'error');
                } finally {
                    const overlayToRemove = document.getElementById('ai-loading-overlay');
                    if (overlayToRemove) overlayToRemove.remove();

                    lessonBtn.innerHTML = originalText;
                    lessonBtn.disabled = false;
                }
            });
        }

        // ปุ่มดึงข้อมูล หมวด 7
        const fetchPrevLessonPlanBtn = document.getElementById('fetchPrevLessonPlanBtn');
        if (fetchPrevLessonPlanBtn) {
            fetchPrevLessonPlanBtn.addEventListener('click', async () => {
                const urlParams = new URLSearchParams(window.location.search);
                const CC_id = urlParams.get('CC_id'); 
                const year = urlParams.get('year');
                const term = urlParams.get('term');

                const conf = await AppConfirm('ข้อมูลตารางแผนการสอนเดิมจะถูกแทนที่ด้วยข้อมูลจากเทอมก่อนหน้า ต้องการทำต่อหรือไม่?');
                if (!conf.isConfirmed) return;

                try {
                    const originalText = fetchPrevLessonPlanBtn.innerHTML;
                    fetchPrevLessonPlanBtn.innerHTML = 'กำลังโหลด...';
                    fetchPrevLessonPlanBtn.disabled = true;

                    const response = await fetch(`/get-previous-lesson-plan?CC_id=${CC_id}&year=${year}&term=${term}`);
                    const result = await response.json();

                    if (result.success && result.data && result.data.length > 0) {
                        
                        // อัปเดตความจำในระบบด้วยข้อมูลเก่าที่ได้มา
                        PAGE_DATA.planData = result.data; 

                        // สั่งวาดตารางใหม่จากข้อมูลความจำ
                        addTableLesson(true);

                        // สั่ง Save ข้อมูลทั้งตารางลง Database ทันที
                        const section7JSON = getSection7Data();
                        saveData('section7_data', section7JSON, document.getElementById('planTable'));

                    } else {
                        AppAlert(result.message || 'ไม่พบข้อมูลเก่า', 'info');
                    }

                    fetchPrevLessonPlanBtn.innerHTML = originalText;
                    fetchPrevLessonPlanBtn.disabled = false;

                } catch (error) {
                    console.error('Error fetching previous lesson plan:', error);
                    AppAlert('เกิดข้อผิดพลาดในการดึงข้อมูลจากเซิร์ฟเวอร์', 'error');
                    fetchPrevLessonPlanBtn.innerText = 'ดึงข้อมูลเก่า';
                    fetchPrevLessonPlanBtn.disabled = false;
                }
            });
        }

        // ดักจับและ Auto-save อัตโนมัติเวลาพิมพ์เสร็จหรือเลือก Dropdown
        const planTableBody = document.querySelector("#planTable tbody");
        if (planTableBody) {
            // เมื่อมีการเลือก Dropdown (CLO)
            planTableBody.addEventListener('change', (event) => {
                if (event.target.tagName === 'SELECT' || event.target.tagName === 'INPUT') {
                    const section7JSON = getSection7Data();
                    PAGE_DATA.planData = section7JSON;
                    saveData('section7_data', section7JSON, event.target);
                }
            });

            // เมื่อพิมพ์ช่อง Textarea เสร็จแล้วกดคลิกที่อื่น (Blur)
            planTableBody.addEventListener('blur', (event) => {
                if (event.target.tagName === 'TEXTAREA') {
                    const section7JSON = getSection7Data();
                    PAGE_DATA.planData = section7JSON;
                    saveData('section7_data', section7JSON, event.target);
                }
            }, true);
        }

    } catch (e) {
        console.error("Error initializing Section 7 (Lesson Plan):", e);
    }
    
    // --- Section 8.1 Logic ---
    try {
        function getSection8_1Data() {
            const assessmentData = [];
            const tableBody = document.querySelector("#assessmentTable tbody");
            if (!tableBody) return assessmentData;

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                const rowData = {
                    method: row.querySelector(`textarea[name="assess_method_${index}"]`)?.value || '',
                    tool: row.querySelector(`textarea[name="assess_tool_${index}"]`)?.value || '',
                    percent: row.querySelector(`input[name="assess_percent_${index}"]`)?.value || '',
                    clo: row.querySelector(`select[name="assess_clo_${index}"]`)?.value || '',
                    clo_desc: row.querySelector(`textarea[name="assess_clo_desc_${index}"]`)?.value || ''
                };
                assessmentData.push(rowData);
            });
            // console.log("Prepared Section 8.1 Data:", assessmentData);
            return assessmentData;
        }

        function createCloOptions_S8(cloKeys, selectedClo) {
            let optionsHtml = '<option value="">--เลือก--</option>';
            if (!Array.isArray(cloKeys) || cloKeys.length === 0) {
                optionsHtml += '<option value="CLO1" ' + (selectedClo === 'CLO1' ? 'selected' : '') + '>CLO 1</option>';
                optionsHtml += '<option value="CLO2" ' + (selectedClo === 'CLO2' ? 'selected' : '') + '>CLO 2</option>';
                optionsHtml += '<option value="CLO3" ' + (selectedClo === 'CLO3' ? 'selected' : '') + '>CLO 3</option>';
            } else {
                cloKeys.forEach(cloKey => { 
                    const isSelected = (selectedClo === cloKey) ? 'selected' : '';
                    const cleanKey = cloKey.replace(/(\d+)$/, ' $1'); 
                    optionsHtml += `<option value="${cloKey}" ${isSelected}>${cleanKey}</option>`; 
                });
            }
            return optionsHtml;
        }

        function generateTableAssessment(forceInputCount = false) {
            const tbody = document.querySelector("#assessmentTable tbody");
            if(!tbody) return;
            tbody.innerHTML = "";
            const assessmentCountInput = document.getElementById("AssessmentCount");
            
            const loadedAssessmentData = PAGE_DATA.assessmentData || [];
            let lastItemWithData = 0;
            if (Array.isArray(loadedAssessmentData) && loadedAssessmentData.length > 0) {
                for (let i = loadedAssessmentData.length - 1; i >= 0; i--) {
                    const itemData = loadedAssessmentData[i] || {};
                    const isEmpty = !(itemData.method || itemData.tool || itemData.percent || itemData.clo || itemData.clo_desc);
                    if (!isEmpty) {
                        lastItemWithData = i + 1; 
                        break; 
                    }
                }
            }
            
            const minItemFromData = lastItemWithData > 0 ? lastItemWithData : 0; 
            const inputCount = (assessmentCountInput && !isNaN(parseInt(assessmentCountInput.value))) ? parseInt(assessmentCountInput.value) : 3; 

            let assessmentCount = Math.max(inputCount, minItemFromData);
            if (assessmentCount > 20) assessmentCount = 20;
            if (assessmentCount < 1) assessmentCount = 1;
            if (assessmentCountInput) assessmentCountInput.value = assessmentCount; 

            let cloKeysFromAI = [];
            try {
                const aiTextJson = PAGE_DATA.aiText || '{}';
                let aiTextData = {};
                if (typeof aiTextJson === 'string' && aiTextJson.trim() !== '') {
                    aiTextData = JSON.parse(aiTextJson);
                } else if (typeof aiTextJson === 'object' && aiTextJson !== null) {
                    aiTextData = aiTextJson;
                }
                
                if (Object.keys(aiTextData).length > 0) {
                    Object.keys(aiTextData).forEach(key => {
                        const details = aiTextData[key];
                        if (details && typeof details === 'object' && details.CLO) {
                            cloKeysFromAI.push(key.replace(/\s/g, '')); 
                        }
                    });
                    cloKeysFromAI.sort((a, b) => (parseInt(a.replace('CLO', '')) || 0) - (parseInt(b.replace('CLO', '')) || 0));
                }
            } catch (e) { }

            for (let i = 0; i < assessmentCount; i++) {
                const rowData = loadedAssessmentData[i] || {};
                const row = document.createElement("tr");
                const cloOptions = createCloOptions_S8(cloKeysFromAI, rowData.clo || '');
                
                row.innerHTML = `
                    <td class="border border-black p-1 align-top"><textarea name="assess_method_${i}" placeholder="เช่น แบบฝึกหัด, Quiz, สอบกลางภาค" class="w-full h-16 border border-gray-300 rounded p-1">${rowData.method || ''}</textarea></td>
                    <td class="border border-black p-1 align-top"><textarea name="assess_tool_${i}" placeholder="เช่น Lab, การนำเสนอ, โครงการ, รายงาน" class="w-full h-16 border border-gray-300 rounded p-1">${rowData.tool || ''}</textarea></td>
                    <td class="border border-black p-1 align-top"><input type="number" name="assess_percent_${i}" min="0" max="100" placeholder="%" class="w-full text-center border border-gray-300 rounded p-1" value="${rowData.percent || ''}"></td>
                    <td class="border border-black p-1 align-top">
                        <select name="assess_clo_${i}" class="w-full mb-1 p-1 border rounded">${cloOptions}</select>
                        <textarea name="assess_clo_desc_${i}" placeholder="ระบุรายละเอียดความสอดคล้อง" class="w-full h-12 border border-gray-300 rounded p-1">${rowData.clo_desc || ''}</textarea>
                    </td>
                `;
                tbody.appendChild(row);
            }
        }

        const assessmentBtn = document.getElementById('generateAssessmentTableBtn');
        if(assessmentBtn) {
            assessmentBtn.addEventListener('click', (event) => {
                // อัปเดตความจำก่อนสร้างตารางใหม่
                PAGE_DATA.assessmentData = getSection8_1Data();
                generateTableAssessment(true);
                const section8_1JSON = getSection8_1Data();
                saveData('section8_1_data', section8_1JSON, event.target);
            });
        }
        generateTableAssessment(false);

        // เพิ่ม Auto-save สำหรับหมวด 8.1
        const assessmentTableBody = document.querySelector("#assessmentTable tbody");
        if (assessmentTableBody) {
            assessmentTableBody.addEventListener('change', (event) => {
                if (event.target.tagName === 'SELECT' || event.target.tagName === 'INPUT') {
                    const section8_1JSON = getSection8_1Data();
                    PAGE_DATA.assessmentData = section8_1JSON; 
                    saveData('section8_1_data', section8_1JSON, event.target);
                }
            });
            assessmentTableBody.addEventListener('blur', (event) => {
                if (event.target.tagName === 'TEXTAREA') {
                    const section8_1JSON = getSection8_1Data();
                    PAGE_DATA.assessmentData = section8_1JSON; 
                    saveData('section8_1_data', section8_1JSON, event.target);
                }
            }, true);
        }

        const fetchPrevAssessmentBtn = document.getElementById('fetchPrevAssessmentBtn');
        if (fetchPrevAssessmentBtn) {
            fetchPrevAssessmentBtn.addEventListener('click', async () => {
                const urlParams = new URLSearchParams(window.location.search);
                const CC_id = urlParams.get('CC_id'); 
                const year = urlParams.get('year');
                const term = urlParams.get('term');

                const conf = await AppConfirm('ข้อมูลตารางกลยุทธ์การประเมินเดิมจะถูกแทนที่ด้วยข้อมูลจากเทอมก่อนหน้า ต้องการทำต่อหรือไม่?');
                if (!conf.isConfirmed) return;

                try {
                    const originalText = fetchPrevAssessmentBtn.innerHTML;
                    fetchPrevAssessmentBtn.innerHTML = 'กำลังโหลด...';
                    fetchPrevAssessmentBtn.disabled = true;

                    const response = await fetch(`/get-previous-assessment-data?CC_id=${CC_id}&year=${year}&term=${term}`);
                    const result = await response.json();

                    if (result.success && result.data && result.data.length > 0) {
                        
                        // อัปเดตความจำในระบบด้วยข้อมูลเก่าที่ได้มา
                        PAGE_DATA.assessmentData = result.data; 

                        // สั่งวาดตารางใหม่จากข้อมูลความจำ
                        generateTableAssessment(true);

                        // สั่ง Save ข้อมูลทั้งตารางลง Database ทันที
                        const section8_1JSON = getSection8_1Data();
                        saveData('section8_1_data', section8_1JSON, document.getElementById('assessmentTable'));

                    } else {
                        AppAlert(result.message || 'ไม่พบข้อมูลเก่า', 'info');
                    }

                    fetchPrevAssessmentBtn.innerHTML = originalText;
                    fetchPrevAssessmentBtn.disabled = false;

                } catch (error) {
                    console.error('Error fetching previous assessment data:', error);
                    AppAlert('เกิดข้อผิดพลาดในการดึงข้อมูลจากเซิร์ฟเวอร์', 'error');
                    fetchPrevAssessmentBtn.innerText = 'ดึงข้อมูลเก่า';
                    fetchPrevAssessmentBtn.disabled = false;
                }
            });
        }

    } catch (e) { console.error("Error initializing Section 8.1:", e); }

    // --- Section 8.2 Logic ---
    try {
        function getSection8_2Data() {
            const rubricsData = [];
            const rubricContainer = document.getElementById('rubric-container');
            if (!rubricContainer) return rubricsData;

            const sections = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)');
            sections.forEach((section, rubricIndex) => {
                const titleElement = section.querySelector('.rubric-title');
                const headerElement = section.querySelector('.rubric-header');
                const tableBody = section.querySelector('.rubric-tbody');
                const rubric = {
                    title: titleElement ? titleElement.textContent.trim().replace(/^[ก-ฮ]\.\s*/, '') : '',
                    header: headerElement ? headerElement.textContent.trim() : '',
                    rows: []
                };

                if (tableBody) {
                    let descriptions = new Array(6).fill(''); 
                    tableBody.querySelectorAll('tr').forEach((row) => {
                        const levelCell = row.querySelector('.level-cell');
                        const descCell = row.querySelector('.description-cell');
                        if (levelCell && descCell) {
                            const level = parseInt(levelCell.textContent.trim(), 10);
                            const description = descCell.textContent.trim();
                            if (level >= 0 && level <= 5) { descriptions[level] = description; }
                        }
                    });
                    rubric.rows = descriptions;
                }
                rubricsData.push(rubric);
            });
            return rubricsData;
        }

        const rubricContainer = document.getElementById('rubric-container');
        const addRubricBtn = document.getElementById('add-rubric-btn');
        const rubricTemplate = document.getElementById('rubric-template');
        const loadedRubricsData = PAGE_DATA.rubricsData || [];
        const rubricLevels = [5, 4, 3, 2, 1, 0];

        function createRubricRow(rubricIndex, rowIndex, level, description) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td class="level-cell border border-gray-400 bg-gray-100 p-1 text-center align-top font-semibold" data-field="rubric_${rubricIndex}_r${rowIndex}_level">${level}</td>
            <td class="description-cell border border-gray-400 p-1 align-top hover:bg-yellow-50" contenteditable="true" data-field="rubric_${rubricIndex}_r${rowIndex}_desc">${description || (level === 0 ? '' : '') }</td>
        `;
            return tr;
        }

        function addOrLoadRubric(rubricData = null, rubricIndex = -1) {
            if (!rubricTemplate) return;
            if (rubricIndex === -1) rubricIndex = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)').length;

            const newRubric = rubricTemplate.cloneNode(true);
            newRubric.classList.remove('hidden'); newRubric.removeAttribute('id');
            const titleElement = newRubric.querySelector('.rubric-title');
            const headerElement = newRubric.querySelector('.rubric-header');
            const tableBody = newRubric.querySelector('.rubric-tbody');

            const letter = String.fromCharCode(0x0E01 + rubricIndex);
            titleElement.textContent = rubricData?.title ? `${letter}. ${rubricData.title}` : `${letter}. [หัวข้อเกณฑ์ใหม่]`;
            headerElement.textContent = rubricData?.header || `คำอธิบายเกณฑ์ฯ`;

            let rowsData = rubricData?.rows || {};
            if (Array.isArray(rowsData) && (rowsData.length === 0 || typeof rowsData[0] === 'string')) {
                const tempRows = {};
                rowsData.forEach((desc, lvl) => { tempRows[String(lvl)] = desc; });
                rowsData = tempRows;
            } else if (Array.isArray(rowsData)) {
                const tempRows = {}; 
                rowsData.forEach(r => { if(r && r.level !== undefined) tempRows[r.level] = r.description ?? ''; });
                rowsData = tempRows;
            } else if (typeof rowsData !== 'object' || rowsData === null) { 
                rowsData = {}; 
            }

            rubricLevels.forEach((level, rowIndex) => { 
                const currentDescription = rowsData[String(level)] ?? '';
                const row = createRubricRow(rubricIndex, rowIndex, level, currentDescription);
                tableBody.appendChild(row);
            });
            rubricContainer.appendChild(newRubric);
        }

        rubricContainer.innerHTML = '';
        if (Array.isArray(loadedRubricsData) && loadedRubricsData.length > 0) {
            loadedRubricsData.forEach((rubric, index) => addOrLoadRubric(rubric, index));
        } else {
            addOrLoadRubric({ title: 'การประเมินการปฏิบัติงาน (Performance)' }, 0);
        }
        updateRubricLetters();

        if (addRubricBtn) {
            addRubricBtn.addEventListener('click', () => { 
                addOrLoadRubric(); 
                updateRubricLetters(); 
                const d = getSection8_2Data(); 
                PAGE_DATA.rubricsData = d;
                saveData('section8_2_data', d); 
            });
        }
        
        rubricContainer.addEventListener('click', async (event) => {
            if (event.target.classList.contains('delete-rubric-btn')) {
                const totalRubrics = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)').length;
                if (totalRubrics <= 1) { 
                    await AppAlert("อย่างน้อยต้องมี 1 หัวข้อ"); 
                    return; 
                }
                const rubricToRemove = event.target.closest('.rubric-section');
                if (rubricToRemove) {
                    const conf = await AppConfirm('ต้องการลบหัวข้อนี้?');
                    if (conf.isConfirmed) {
                        rubricToRemove.remove(); 
                        updateRubricLetters(); 
                        const d = getSection8_2Data(); 
                        PAGE_DATA.rubricsData = d;
                        saveData('section8_2_data', d);
                    }
                }
            }
        });

        // เพิ่ม Auto-save สำหรับหมวด 8.2 (แก้ไขข้อความ)
        rubricContainer.addEventListener('blur', (event) => {
            if (event.target.hasAttribute('contenteditable')) {
                const section8_2JSON = getSection8_2Data();
                PAGE_DATA.rubricsData = section8_2JSON;
                saveData('section8_2_data', section8_2JSON, event.target);
            }
        }, true);

        function updateRubricLetters() {
            const allRubrics = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)');
            allRubrics.forEach((rubric, index) => {
                const letter = String.fromCharCode(0x0E01 + index);
                const titleElement = rubric.querySelector('.rubric-title');
                const headerElement = rubric.querySelector('.rubric-header');
                if (titleElement) { let txt = titleElement.textContent.trim().replace(/^[ก-ฮ]\.\s*/, ''); titleElement.textContent = `${letter}. ${txt}`; }
            });
        }

        // ปุ่มดึงข้อมูลและบันทึกอัตโนมัติ หมวด 8.2 
        const fetchPrevRubricsBtn = document.getElementById('fetchPrevRubricsBtn');
        if (fetchPrevRubricsBtn) {
            fetchPrevRubricsBtn.addEventListener('click', async () => {
                const urlParams = new URLSearchParams(window.location.search);
                const CC_id = urlParams.get('CC_id'); 
                const year = urlParams.get('year');
                const term = urlParams.get('term');

                const conf = await AppConfirm('ข้อมูลตารางรูบริคเดิมจะถูกลบทิ้งและแทนที่ด้วยข้อมูลจากเทอมก่อนหน้า ต้องการทำต่อหรือไม่?');
                if (!conf.isConfirmed) return;

                try {
                    const originalText = fetchPrevRubricsBtn.innerHTML;
                    fetchPrevRubricsBtn.innerHTML = 'กำลังโหลด...';
                    fetchPrevRubricsBtn.disabled = true;

                    const response = await fetch(`/get-previous-rubrics-data?CC_id=${CC_id}&year=${year}&term=${term}`);
                    const result = await response.json();

                    if (result.success && result.data && result.data.length > 0) {
                        
                        // อัปเดตความจำ
                        PAGE_DATA.rubricsData = result.data;

                        // ล้างตารางเดิมออก
                        const container = document.getElementById('rubric-container');
                        container.innerHTML = ''; 

                        // วาดตารางใหม่จากข้อมูลที่ดึงมา
                        result.data.forEach((rubric, index) => addOrLoadRubric(rubric, index));
                        updateRubricLetters();

                        // สั่ง Save ข้อมูลลง Database ทันที
                        const section8_2JSON = getSection8_2Data();
                        saveData('section8_2_data', section8_2JSON, container);

                    } else {
                        AppAlert(result.message || 'ไม่พบข้อมูลเก่า', 'info');
                    }

                    fetchPrevRubricsBtn.innerHTML = originalText;
                    fetchPrevRubricsBtn.disabled = false;

                } catch (error) {
                    console.error('Error fetching previous rubrics data:', error);
                    AppAlert('เกิดข้อผิดพลาดในการดึงข้อมูลจากเซิร์ฟเวอร์', 'error');
                    fetchPrevRubricsBtn.innerText = 'ดึงข้อมูลเก่า';
                    fetchPrevRubricsBtn.disabled = false;
                }
            });
        }

    } catch (e) { console.error("Error initializing Section 8.2 (Rubrics):", e); }

    // Section 9.1
    try {
        // Function to gather Section 9.1 data
        function getSection9_1Data() {
            const referencesData = [];
            const referenceList = document.getElementById('referenceList');
            if (!referenceList) return referencesData;
            referenceList.querySelectorAll('li input[type="text"]').forEach(input => {
                if (input.value.trim()) {
                    referencesData.push(input.value.trim());
                }
            });
            // console.log("Prepared Section 9.1 Data:", referencesData);
            return referencesData;
        }

        const referenceList = document.getElementById('referenceList');
        const addReferenceBtn = document.getElementById('addReferenceItemBtn');
        const loadedReferencesData = PAGE_DATA.referencesData || [];

        function createReferenceItem(value = '', index = -1) {
            if (index === -1) index = referenceList.children.length;
            const li = document.createElement('li');
            li.className = "mb-2.5 relative";
            const escapedValue = String(value || '').replace(/"/g, '&quot;');
            li.innerHTML = `
            <input type="text" name="reference_${index}" class="w-4/5 p-2 border border-gray-300 rounded-md text-[15px]" placeholder="พิมพ์รายการใหม่..." value="${escapedValue}">
            <button class="remove-btn bg-red-500 text-white border-none rounded-md px-2.5 py-1.5 ml-2.5 cursor-pointer text-[13px] hover:bg-red-600">ลบ</button>
        `;
            referenceList.appendChild(li);
        }

        referenceList.innerHTML = '';
        if (Array.isArray(loadedReferencesData) && loadedReferencesData.length > 0) {
            loadedReferencesData.forEach((refText, index) => createReferenceItem(refText, index));
        } else {
            createReferenceItem('', 0);
        }

        // Add/Delete Buttons
        if (addReferenceBtn) {
            addReferenceBtn.addEventListener('click', () => { createReferenceItem(); const d = getSection9_1Data(); saveData('section9_1_data', d); }); // Save after adding
        }
        if (referenceList) {
            referenceList.addEventListener('click', async (event) => {
                if (event.target.classList.contains('remove-btn')) {
                    const itemCount = referenceList.children.length;
                    if (itemCount > 1) {
                        event.target.parentElement.remove();
                        // Re-index names AND save
                        referenceList.querySelectorAll('li input').forEach((input, index) => { input.name = `reference_${index}`; });
                        const d = getSection9_1Data(); saveData('section9_1_data', d); 
                    } else { 
                        await AppAlert("อย่างน้อยต้องมี 1 รายการ"); 
                    }
                }
            });
            referenceList.addEventListener('change', (event) => {
                if(event.target.tagName === 'INPUT'){ const d = getSection9_1Data(); saveData('section9_1_data', d); }
            });
            referenceList.addEventListener('blur', (event) => {
                if(event.target.tagName === 'INPUT'){ const d = getSection9_1Data(); saveData('section9_1_data', d); }
            }, true);
        }
    } catch (e) { console.error("Error initializing Section 9.1:", e); }

    // Section 10
    // ปุ่มดึงข้อมูลและบันทึกอัตโนมัติ หมวด 10
    const fetchPrevGradingBtn = document.getElementById('fetchPrevGradingBtn');
    if (fetchPrevGradingBtn) {
        fetchPrevGradingBtn.addEventListener('click', async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const CC_id = urlParams.get('CC_id'); 
            const year = urlParams.get('year');
            const term = urlParams.get('term');

            const conf = await AppConfirm('ข้อมูลเกณฑ์การประเมินเดิมจะถูกแทนที่ด้วยข้อมูลจากเทอมก่อนหน้า ต้องการทำต่อหรือไม่?');
            if (!conf.isConfirmed) return;

            try {
                const originalText = fetchPrevGradingBtn.innerHTML;
                fetchPrevGradingBtn.innerHTML = 'กำลังโหลด...';
                fetchPrevGradingBtn.disabled = true;

                const response = await fetch(`/get-previous-grading-criteria?CC_id=${CC_id}&year=${year}&term=${term}`);
                const result = await response.json();

                if (result.success && result.data) {
                    const gradingData = result.data;
                    
                    // นำข้อมูลไปหยอดใส่ตารางหน้าเว็บ
                    const grades = ['A', 'Bp', 'B', 'Cp', 'C', 'Dp', 'D', 'F'];
                    grades.forEach(grade => {
                        const levelCell = document.querySelector(`[data-field="grade_${grade}_level"]`);
                        const criteriaCell = document.querySelector(`[data-field="grade_${grade}_criteria"]`);
                        
                        if (levelCell && gradingData[`grade_${grade}_level`] !== undefined) {
                            levelCell.textContent = gradingData[`grade_${grade}_level`];
                        }
                        if (criteriaCell && gradingData[`grade_${grade}_criteria`] !== undefined) {
                            criteriaCell.textContent = gradingData[`grade_${grade}_criteria`];
                        }
                    });

                    // สั่ง Save ข้อมูลแบบรวดเดียวลงตาราง plans ทันที
                    saveData('grading_criteria_all', gradingData, document.getElementById('gradeTable'));

                } else {
                    AppAlert(result.message || 'ไม่พบข้อมูลเก่า', 'info');
                }

                fetchPrevGradingBtn.innerHTML = originalText;
                fetchPrevGradingBtn.disabled = false;

            } catch (error) {
                console.error('Error fetching previous grading criteria:', error);
                AppAlert('เกิดข้อผิดพลาดในการดึงข้อมูลจากเซิร์ฟเวอร์', 'error');
                fetchPrevGradingBtn.innerText = 'ดึงข้อมูลเก่า';
                fetchPrevGradingBtn.disabled = false;
            }
        });
    }
});

window.openPreviewModal = function() {
    // บังคับให้ช่องที่กำลังพิมพ์อยู่เอาเคอร์เซอร์ออก(เพื่อ Auto-save)
    if (document.activeElement && document.activeElement !== document.body) {
        document.activeElement.blur();
    }

    const modal = document.getElementById('previewModal');
    const iframe = document.getElementById('previewIframe');
    const loading = document.getElementById('previewLoading');
    
    if(modal) modal.classList.remove('hidden');
    
    // ดึงพารามิเตอร์ทั้งหมดจาก URL ของหน้าเว็บปัจจุบัน
    const urlParams = new URLSearchParams(window.location.search);
    const CC_id = urlParams.get('CC_id') || '';
    const year = urlParams.get('year') || '';
    const term = urlParams.get('term') || '';
    const TQF = urlParams.get('TQF') || '';
    
    // หน่วงเวลาเพื่อให้ Auto-save ทำงานส่งไปหา Backend ทัน
    setTimeout(() => {
        if(iframe) {
            iframe.src = `/preview-docx?CC_id=${CC_id}&year=${year}&term=${term}&TQF=${TQF}`;
        }
        
        if(iframe && loading) {
            iframe.onload = function() {
                loading.classList.add('hidden');
            };
        }
    }, 150);
};

window.closePreviewModal = function() {
    const modal = document.getElementById('previewModal');
    const iframe = document.getElementById('previewIframe');
    const loading = document.getElementById('previewLoading');
    
    if(modal) modal.classList.add('hidden');
    
    setTimeout(() => {
        if(iframe) iframe.src = "";
        if(loading) loading.classList.remove('hidden');
    }, 300);
};