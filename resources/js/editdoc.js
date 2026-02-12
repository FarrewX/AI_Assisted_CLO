document.addEventListener('DOMContentLoaded', () => {
    // ดึงข้อมูลจาก Global Variable ที่ประกาศไว้ใน Blade
    const PAGE_DATA = window.pageData || {};

    async function saveData(fieldName, value, targetElement = null) {
        const saveUrl = '/savedataedit';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const urlParams = new URLSearchParams(window.location.search);
        
        // ใช้ค่าจาก URL หรือถ้าไม่มีให้ใช้ค่าเริ่มต้น (ป้องกัน Error)
        const courseId = urlParams.get('course_id');
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
        
        console.log('Saving:', { field: fieldName, value: value, course_id: courseId });
        
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
                    course_id: courseId, year: year, term: term, TQF: tqf,
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
            console.log('Save successful:', result);

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

        if (fieldName === 'section6_data_trigger') { saveData('section6_data', getSection6Data(), target); return; }
        if (fieldName === 'section7_data_trigger') { saveData('section7_data', getSection7Data(), target); return; }
        if (fieldName === 'section8_1_data_trigger') { saveData('section8_1_data', getSection8_1Data(), target); return; }
        if (fieldName === 'section8_2_data_trigger') { saveData('section8_2_data', getSection8_2Data(), target); return; }
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

            if (fieldName === 'section6_data_trigger') {
                if (target.classList.contains('clo-cell-s6')) saveData('section6_data', getSection6Data(), target);
                return;
            }
            if (fieldName === 'section7_data_trigger') return;
            if (fieldName === 'section8_1_data_trigger') return;
            if (fieldName === 'section8_2_data_trigger') { saveData('section8_2_data', getSection8_2Data(), target); return; }
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
        const aiTextJson = PAGE_DATA.aiText || '{}';
        let aiTextData = {};
        let cloData = [];

        try {
            if (typeof aiTextJson === 'string' && aiTextJson.trim() !== '') {
                aiTextData = JSON.parse(aiTextJson);
            } else if (typeof aiTextJson === 'object' && aiTextJson !== null) {
                aiTextData = aiTextJson;
            }
        } catch (err) {}

        if (Object.keys(aiTextData).length > 0) {
            Object.keys(aiTextData).forEach(key => {
                const details = aiTextData[key];
                if (details && typeof details === 'object' && details.CLO) {
                    cloData.push({ code: key.replace(/\s/g, ''), description: details.CLO });
                }
            });
            cloData.sort((a, b) => (parseInt(a.code.replace('CLO', '')) || 0) - (parseInt(b.code.replace('CLO', '')) || 0));
        }

        const lllData = [
            { code: "LLL1", description: "Creativity ความคิดสร้างสรรค์" },
            { code: "LLL2", description: "Problem Solving การแก้ปัญหา" },
            { code: "LLL3", description: "Critical Thinking การคิดเชิงวิพากษ์" },
            { code: "LLL4", description: "Leadership การเป็นผู้นำ" },
            { code: "LLL5", description: "Communication การสื่อสาร" },
            { code: "LLL6", description: "Collaboration การประสานงาน" },
            { code: "LLL7", description: "Information Management การจัดการข้อมูล " },
            { code: "LLL8", description: "Adaptability การปรับตัว" },
            { code: "LLL9", description: "Curiosity ความอยากรู้อยากเห็น" },
            { code: "LLL10", description: "Reflection การสะท้อนทักษะความรู้" }
        ];
        
        const cloLllData = [...cloData, ...lllData];
        const ploCount = PAGE_DATA.ploCount || 0;
        const levelOptions = ["", "R", "U", "AP", "AN", "E", "C"];
        const courseAccordData = PAGE_DATA.courseAccord || [];
        const tbody = document.querySelector("#ploTable tbody");

        if (tbody) {
            tbody.innerHTML = '';
            cloLllData.forEach((item, rowIndex) => {
                const tr = document.createElement("tr");
                const itemCode = item.code ?? `Item ${rowIndex+1}`;
                tr.innerHTML = `
                    <td class="border border-gray-400 p-2 text-center font-bold">${itemCode}</td>
                    <td class="border border-gray-400 p-2 text-left">
                       <span class="inline-block w-full hover:bg-yellow-50 focus:outline-none focus:ring-1 focus:ring-blue-500 p-1 rounded"
                            contenteditable="true" data-field="cloLll_desc_${itemCode}">${item.description ?? ''}</span>
                    </td>`;

                for (let colIndex = 0; colIndex < ploCount; colIndex++) {
                    const td = document.createElement("td");
                    td.className = "border border-gray-400 p-2 text-center";
                    const ploDbIndex = colIndex + 1;
                    let savedCellData = { check: false, level: '' };

                    if (courseAccordData && courseAccordData[itemCode] !== undefined) {
                        const rowData = courseAccordData[itemCode];
                        if (rowData && rowData[ploDbIndex]) {
                             savedCellData = { check: rowData[ploDbIndex].check ?? false, level: rowData[ploDbIndex].level ?? '' };
                        }
                    }

                    const checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.className = "mr-1.5 scale-125 plo-map-checkbox";
                    checkbox.name = `plo_map_${itemCode}_c${colIndex}_check`; 
                    checkbox.checked = savedCellData.check;

                    const select = document.createElement("select");
                    select.className = "w-[70px] p-1 border rounded plo-map-level";
                    select.name = `plo_map_${itemCode}_c${colIndex}_level`;
                    levelOptions.forEach(opt => {
                        const op = document.createElement("option");
                        op.value = opt;
                        op.textContent = opt;
                        if (savedCellData.level === opt) op.selected = true;
                        select.appendChild(op);
                    });

                    td.appendChild(checkbox);
                    td.appendChild(select);
                    tr.appendChild(td);
                }
                tbody.appendChild(tr);
            });
        }
    } catch (s5Error) { console.error("S5.3 Error", s5Error); }

    // --- Section 6 Logic ---
    try {
        const data_s6_teachingOptions = { onSite: { label: "On-site โดยมีการเรียนการสอนแบบ", items: ["บรรยาย (Lecture)","ฝึกปฏิบัติ (Laboratory Model)","เรียนรู้จากการลงมือทำ (Learning by Doing)","การเรียนรู้โดยใช้กิจกรรมเป็นฐาน (Activity-based Learning)","การเรียนรู้โดยใช้วิจัยเป็นฐาน (Research-based Learning)","ถามตอบสะท้อนคิด (Refractive Learning)","นำเสนออภิปรายกลุ่ม (Discussion Group)","เรียนรู้จากการสืบเสาะหาความรู้ (Inquiry-based Learning)","การเรียนรู้แบบร่วมมือร่วมใจ (Cooperative Learning)","การเรียนรู้แบบร่วมมือ (Collaborative Learning)","การเรียนรู้โดยใช้โครงการเป็นฐาน (Project-based Learning: PBL)","การเรียนรู้โดยใช้ปัญหาเป็นฐาน (Problem-based Learning)","วิเคราะห์โจทย์ปัญหา (Problem Solving)","เรียนรู้การตัดสินใจ (Decision Making)","ศึกษาค้นคว้าจากกรณีศึกษา (Case Study)","เรียนรู้ผ่านการเกม/การเล่น (Game/Play Learning)","ศึกษาดูงาน (Field Trips)","อื่น ๆ....................................." ], indent: true }, online: { label: "Online โดยมีการเรียนการสอนแบบ", items: ["บรรยาย (Lecture)"], indent: true } };
        const data_s6_assessmentOptions = { exam: { label: "คะแนนจากแบบทดสอบสัมฤทธิ์ผล (ข้อสอบ)", items: ["ทดสอบย่อย (Quiz)","ทดสอบกลางภาค (Midterm)","ทดสอบปลายภาค (Final)" ] }, performance: { label: "คะแนนจากผลงานที่ได้รับมอบหมาย (Performance) โดยใช้เกณฑ์ Rubric Score การทำงานกลุ่มและเดี่ยว", items: ["การทำงานเป็นทีม (Team Work)","โปรแกรม ซอฟต์แวร์","ผลงาน ชิ้นงาน","รายงาน (Report)","การนำเสนอ (Presentation)","แฟ้มสะสมงาน (Portfolio)","รายงานการศึกษาด้วยตนเอง (Self-Study Report)" ] }, behavior: { label: "คะแนนจากผลการพฤติกรรม", items: ["ความรับผิดชอบ การมีส่วนร่วม","ประเมินผลการงานที่ส่ง" ] } };

        function getSection6Data() {
            const sectionData = {};
            const tableBody = document.getElementById('cloTableBody_S6');
            if (!tableBody) return sectionData;

            const rows = tableBody.querySelectorAll('tr.clo-row');

            rows.forEach((row, rowIndex) => {
                const cloCell = row.querySelector('.clo-cell-s6');
                let cloKey = cloCell ? cloCell.textContent.trim().replace(/ \[คลิกเพื่อแก้ไข\]$/, '').trim().replace(/\s/g, '') : `CLO${rowIndex + 1}`;
                if (!cloKey) cloKey = `CLO${rowIndex + 1}`;

                const teachingMethods = [];
                row.querySelectorAll('.teaching-cell-s6 input[type="checkbox"]:checked').forEach(cb => {
                    const labelSpan = cb.closest('label')?.querySelector('span');
                    if (labelSpan) teachingMethods.push(labelSpan.textContent.trim());
                });

                const assessmentMethods = [];
                row.querySelectorAll('.assessment-cell-s6 input[type="checkbox"]:checked').forEach(cb => {
                    const labelSpan = cb.closest('label')?.querySelector('span');
                    if (labelSpan) assessmentMethods.push(labelSpan.textContent.trim());
                });

                sectionData[cloKey] = [
                    { "วิธีการสอน": teachingMethods },
                    { "การประเมินผล": assessmentMethods }
                ];
            });
            console.log("Prepared Section 6 Data:", sectionData);
            return sectionData;
        }

        function initializeCloTable_Section6() {
            const tableBody = document.getElementById('cloTableBody_S6');
            const addBtn = document.getElementById('addCloBtn_S6');
            const template = document.getElementById('cloRowTemplate_S6');
            if (!tableBody || !addBtn || !template) {
                console.warn("Section 6 elements not found.");
                return;
            }

            let section6Data = {};
            try {
                const jsonDataString = PAGE_DATA.teachingMethods || null;
                if (typeof jsonDataString === 'string' && jsonDataString.length > 0 && jsonDataString !== 'null') {
                    section6Data = JSON.parse(jsonDataString);
                } else if (typeof jsonDataString === 'object' && jsonDataString !== null) {
                    section6Data = jsonDataString;
                }
                if (typeof section6Data !== 'object' || section6Data === null) section6Data = {};
                console.log("Loaded Section 6 Data:", section6Data);
            } catch (parseError) {
                console.error("Error parsing section 6 JSON data:", parseError, @json($data->teaching_methods ?? null));
                section6Data = {};
            }

            let cloKeysFromAI = [];
            try {
                const aiTextJson = @json($data->ai_text ?? '{}');
                let aiTextData = {};
                
                if (typeof aiTextJson === 'string' && aiTextJson.trim() !== '') {
                    aiTextData = JSON.parse(aiTextJson);
                } else if (typeof aiTextJson === 'object' && aiTextData !== null) {
                    aiTextData = aiTextJson;
                }
                
                if (Object.keys(aiTextData).length > 0) {
                    Object.keys(aiTextData).forEach(key => {
                        const details = aiTextData[key];
                        if (details && typeof details === 'object' && details.CLO) {
                            // Normalize key ("CLO 1" -> "CLO1")
                            cloKeysFromAI.push(key.replace(/\s/g, ''));
                        }
                    });
                    
                    // Sort keys numerically
                    cloKeysFromAI.sort((a, b) => {
                        const numA = parseInt(a.replace('CLO', '')) || 0;
                        const numB = parseInt(b.replace('CLO', '')) || 0;
                        return numA - numB;
                    });
                }
                console.log("Loaded CLO Keys from ai_text for S6:", cloKeysFromAI);
            } catch (e) {
                console.error("Error parsing ai_text for S6:", e, @json($data->ai_text ?? '{}'));
            }

            // rowData (e.g., [ {"วิธีการสอน":...}, {"การประเมินผล":...} ])
            function _s6_createCheckboxHtml(optionsObject, type, rowData) {
                let html = '';
                let checkedItems = [];
                const cloEntry = rowData;

                if(cloEntry && Array.isArray(cloEntry)) {
                    const typeKey = (type === 'teach') ? 'วิธีการสอน' : 'การประเมินผล';
                    const relevantObject = cloEntry.find(obj => obj && typeof obj === 'object' && obj.hasOwnProperty(typeKey));
                    if(relevantObject && Array.isArray(relevantObject[typeKey])) {
                        checkedItems = relevantObject[typeKey].map(item => String(item).trim());
                    }
                }

                for (const key in optionsObject) {
                    const category = optionsObject[key];
                    html += `<div class="font-semibold mt-2">${category.label}</div>`;
                    category.items.forEach(itemText => {
                        const indentClass = category.indent ? 'ml-4' : '';
                        const isChecked = checkedItems.includes(String(itemText).trim());
                        html += `
                            <label class="flex items-start ${indentClass} hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" class="mt-1 scale-125 mr-1.5 s6-checkbox" ${isChecked ? 'checked' : ''}>
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

                cloCell.textContent = cloKey;

                teachingCell.innerHTML = _s6_createCheckboxHtml(data_s6_teachingOptions, 'teach', rowData);
                assessmentCell.innerHTML = _s6_createCheckboxHtml(data_s6_assessmentOptions, 'assess', rowData);

                tableBody.appendChild(newRow);
            }

            tableBody.innerHTML = '';
            
            if (cloKeysFromAI.length > 0) {
                cloKeysFromAI.forEach((cloKey, index) => { // cloKey is "CLO1"
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

            addBtn.addEventListener('click', (event) => {
                _s6_addNewCloRow();
                const section6JSON = getSection6Data();
                saveData('section6_data', section6JSON, event.target);
            });
        }
        initializeCloTable_Section6();
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
                const weekNumber = index + 1; // Get week number from row index
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
            console.log("Prepared Section 7 Data:", planData);
            return planData;
        }

        function createCloOptions(cloKeys, selectedClo) {
            let optionsHtml = '<option value="">--เลือก--</option>';
            
            // ตรวจสอบว่า cloKeys เป็น Array และมีข้อมูล
            if (!Array.isArray(cloKeys) || cloKeys.length === 0) {
                console.warn("S7: No CLO keys from ai_text, using default fallback.");
                // Fallback ถ้า ai_text ว่าง
                optionsHtml += '<option value="CLO1" ' + (selectedClo === 'CLO1' ? 'selected' : '') + '>CLO 1</option>';
                optionsHtml += '<option value="CLO2" ' + (selectedClo === 'CLO2' ? 'selected' : '') + '>CLO 2</option>';
                optionsHtml += '<option value="CLO3" ' + (selectedClo === 'CLO3' ? 'selected' : '') + '>CLO 3</option>';
            } else {
                // วน Loop สร้าง <option> จาก Keys ที่ได้
                cloKeys.forEach(cloKey => { // cloKey is "CLO1"
                    const isSelected = (selectedClo === cloKey) ? 'selected' : '';
                    // "CLO1" -> "CLO 1" (สำหรับแสดงผล)
                    const cleanKey = cloKey.replace(/(\d+)$/, ' $1'); 
                    optionsHtml += `<option value="${cloKey}" ${isSelected}>${cleanKey}</option>`; 
                });
            }
            return optionsHtml;
        }

        function generateTableLesson(forceInputCount = false) {
            const tbody = document.querySelector("#planTable tbody");
            if (!tbody) { console.warn("Table body for lesson plan (#planTable tbody) not found."); return; }
            tbody.innerHTML = "";
            const weekCountInput = document.getElementById("weekCount");
            
            // Load data
            const loadedPlanData = PAGE_DATA.planData || [];

            // Find the last week that actually has data
            let lastWeekWithData = 0;
            if (Array.isArray(loadedPlanData) && loadedPlanData.length > 0) {
                // Loop backwards from the end
                for (let i = loadedPlanData.length - 1; i >= 0; i--) {
                    const weekData = loadedPlanData[i] || {};
                    // Check if all fields (except week) are empty
                    const isEmpty = !(weekData.topic || weekData.objective || weekData.activity || weekData.tool || weekData.assessment || weekData.clo);
                    
                    if (!isEmpty) {
                        lastWeekWithData = parseInt(weekData.week || 0);
                        break; // Found the last non-empty week
                    }
                }
            }
            
            // Determine minimum required weeks (based on data)
            const minWeekFromData = lastWeekWithData > 0 ? lastWeekWithData : 0; // e.g., 6 (or 0 if empty)

            // Get count from input field
            const inputWeekCount = (weekCountInput && !isNaN(parseInt(weekCountInput.value))) ? parseInt(weekCountInput.value) : 10; // Default 10

            // Determine final weekCount
            let weekCount;
            if(forceInputCount) {
                // If button clicked (Req 3: Cannot go below data)
                weekCount = Math.max(inputWeekCount, minWeekFromData); 
            } else {
                // On initial load (Req 2: Start at input default OR data max, whichever is higher)
                weekCount = Math.max(inputWeekCount, minWeekFromData);
            }
            
            // Enforce limits
            if (weekCount > 20) weekCount = 20;
            if (weekCount < 1) weekCount = 1;
            if (weekCountInput && parseInt(weekCountInput.value) !== weekCount) { // sync input
                weekCountInput.value = weekCount;
            }

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
                            cloKeysFromAI.push(key.replace(/\s/g, '')); // "CLO 1" -> "CLO1"
                        }
                    });
                    cloKeysFromAI.sort((a, b) => {
                        const numA = parseInt(a.replace('CLO', '')) || 0;
                        const numB = parseInt(b.replace('CLO', '')) || 0;
                        return numA - numB;
                    });
                }
            } catch (e) {
                console.error("Error parsing ai_text for S7:", e, PAGE_DATA.aiText || '{}');
            }

            for (let i = 1; i <= weekCount; i++) {
                const weekData = loadedPlanData.find(item => parseInt(item.week) === i) || {};
                const row = document.createElement("tr");

                // Call the helper function
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
        const lessonBtn = document.getElementById('generateLessonTableBtn');
        if (lessonBtn) {
            lessonBtn.addEventListener('click', (event) => {
                generateTableLesson(true);
                const section7JSON = getSection7Data();
                saveData('section7_data', section7JSON, event.target);
            });
        } else {
            console.warn("Button to generate lesson table (#generateLessonTableBtn) not found.");
        }
        generateTableLesson(false);
    } catch (e) {
        console.error("Error initializing Section 7 (Lesson Plan):", e);
    }
    
    // Section 8.1
    try {
        function getSection8_1Data() {
            const assessmentData = [];
            const tableBody = document.querySelector("#assessmentTable tbody");
            if (!tableBody) return assessmentData;

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                // Find inputs by index-based name
                const rowData = {
                    method: row.querySelector(`textarea[name="assess_method_${index}"]`)?.value || '',
                    tool: row.querySelector(`textarea[name="assess_tool_${index}"]`)?.value || '',
                    percent: row.querySelector(`input[name="assess_percent_${index}"]`)?.value || '',
                    clo: row.querySelector(`select[name="assess_clo_${index}"]`)?.value || '',
                    clo_desc: row.querySelector(`textarea[name="assess_clo_desc_${index}"]`)?.value || ''
                };
                assessmentData.push(rowData);
            });
            console.log("Prepared Section 8.1 Data:", assessmentData);
            return assessmentData;
        }

        function createCloOptions_S8(cloKeys, selectedClo) {
            let optionsHtml = '<option value="">--เลือก--</option>';
            
            if (!Array.isArray(cloKeys) || cloKeys.length === 0) {
                console.warn("S8: No CLO keys from ai_text, using default fallback.");
                // Fallback ถ้า ai_text ว่าง
                optionsHtml += '<option value="CLO1" ' + (selectedClo === 'CLO1' ? 'selected' : '') + '>CLO 1</option>';
                optionsHtml += '<option value="CLO2" ' + (selectedClo === 'CLO2' ? 'selected' : '') + '>CLO 2</option>';
                optionsHtml += '<option value="CLO3" ' + (selectedClo === 'CLO3' ? 'selected' : '') + '>CLO 3</option>';
            } else {
                // วน Loop สร้าง <option> จาก Keys ที่ได้
                cloKeys.forEach(cloKey => { // cloKey is "CLO1"
                    const isSelected = (selectedClo === cloKey) ? 'selected' : '';
                    // "CLO1" -> "CLO 1" (สำหรับแสดงผล)
                    const cleanKey = cloKey.replace(/(\d+)$/, ' $1'); 
                    optionsHtml += `<option value="${cloKey}" ${isSelected}>${cleanKey}</option>`; 
                });
            }
            return optionsHtml;
        }

        function generateTableAssessment(forceInputCount = false) {
            const tbody = document.querySelector("#assessmentTable tbody");
            if(!tbody) { console.warn("Assessment table body not found."); return; }
            tbody.innerHTML = "";
            const assessmentCountInput = document.getElementById("AssessmentCount");
            
            // Load data *first*
            const loadedAssessmentData = PAGE_DATA.assessmentData || [];

            // Find the last item that actually has data
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
            
            // Determine minimum required items
            const minItemFromData = lastItemWithData > 0 ? lastItemWithData : 0; 
            
            // Get count from input field
            const inputCount = (assessmentCountInput && !isNaN(parseInt(assessmentCountInput.value))) ? parseInt(assessmentCountInput.value) : 3; // Default 3

            // Determine final itemCount
            let assessmentCount;
            if (forceInputCount) {
                    // If button clicked (Req 3: Cannot go below data)
                    assessmentCount = Math.max(inputCount, minItemFromData);
            } else {
                    // On initial load (Req 2: Start at input default OR data max)
                    assessmentCount = Math.max(inputCount, minItemFromData);
            }

            // Enforce limits
            if (assessmentCount > 20) assessmentCount = 20;
            if (assessmentCount < 1) assessmentCount = 1;
            if (assessmentCountInput) assessmentCountInput.value = assessmentCount; // Sync input

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
                            cloKeysFromAI.push(key.replace(/\s/g, '')); // "CLO 1" -> "CLO1"
                        }
                    });
                    cloKeysFromAI.sort((a, b) => {
                        const numA = parseInt(a.replace('CLO', '')) || 0;
                        const numB = parseInt(b.replace('CLO', '')) || 0;
                        return numA - numB;
                    });
                }
            } catch (e) {
                console.error("Error parsing ai_text for S8:", e, PAGE_DATA.aiText || '{}');
            }

            for (let i = 0; i < assessmentCount; i++) {
                    const rowData = loadedAssessmentData[i] || {};
                    const row = document.createElement("tr");
                    
                    // Call the helper function
                    const cloOptions = createCloOptions_S8(cloKeysFromAI, rowData.clo || '');
                    
                    row.innerHTML = `
                    <td class="border border-black p-1 align-top"><textarea name="assess_method_${i}" placeholder="เช่น แบบฝึกหัด, Quiz, สอบกลางภาค" class="w-full h-16 border border-gray-300 rounded p-1">${rowData.method || ''}</textarea></td>
                    <td class="border border-black p-1 align-top"><textarea name="assess_tool_${i}" placeholder="เช่น Lab, การนำเสนอ, โครงการ, รายงาน" class="w-full h-16 border border-gray-300 rounded p-1">${rowData.tool || ''}</textarea></td>
                    <td class="border border-black p-1 align-top"><input type="number" name="assess_percent_${i}" min="0" max="100" placeholder="%" class="w-full text-center border border-gray-300 rounded p-1" value="${rowData.percent || ''}"></td>
                    <td class="border border-black p-1 align-top">
                        <select name="assess_clo_${i}" class="w-full mb-1 p-1 border rounded">
                            ${cloOptions}
                        </select>
                        <textarea name="assess_clo_desc_${i}" placeholder="ระบุรายละเอียดความสอดคล้อง" class="w-full h-12 border border-gray-300 rounded p-1">${rowData.clo_desc || ''}</textarea>
                    </td>
                `;
                tbody.appendChild(row);
            }
            }
        const assessmentBtn = document.getElementById('generateAssessmentTableBtn');
        if(assessmentBtn) {
            assessmentBtn.addEventListener('click', () => {
                generateTableAssessment(true);
                const section8_1JSON = getSection8_1Data();
                saveData('section8_1_data', section8_1JSON);
            });
            generateTableAssessment(false);
        } else {
            console.warn("Generate assessment table button not found.");
        }
    } catch (e) {
        console.error("Error initializing Section 8.1 (Assessment Strategy):", e);
    }

    // Section 8.2
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
                    // Initialize rows as an Array
                    rows: []
                };

                if (tableBody) {
                    // Create a temporary array to hold descriptions, indexed by level
                    let descriptions = new Array(6).fill(''); // Array[6] (for levels 0-5)
                    
                    tableBody.querySelectorAll('tr').forEach((row, rowIndex) => {
                        const levelCell = row.querySelector('.level-cell');
                        const descCell = row.querySelector('.description-cell');
                        if (levelCell && descCell) {
                            const level = parseInt(levelCell.textContent.trim(), 10);
                            const description = descCell.textContent.trim();
                            if (level >= 0 && level <= 5) {
                                descriptions[level] = description; 
                            }
                        }
                    });
                    // Assign the sequential array (index 0 = level 0, index 5 = level 5)
                    rubric.rows = descriptions;
                }
                rubricsData.push(rubric);
            });
            console.log("Prepared Section 8.2 Data:", rubricsData);
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
            <td class="level-cell border border-gray-400 p-1 text-center align-top font-semibold" contenteditable="true" data-field="rubric_${rubricIndex}_r${rowIndex}_level">${level}</td>
            <td class="description-cell border border-gray-400 p-1 align-top hover:bg-yellow-50" contenteditable="true" data-field="rubric_${rubricIndex}_r${rowIndex}_desc">${description || (level === 0 ? 'ไม่ส่งผลงาน' : '[ใส่คำอธิบาย]') }</td>
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
            const table = newRubric.querySelector('.rubric-table');
            table.id = `rubricTable_${rubricIndex}`;

            const letter = String.fromCharCode(0x0E01 + rubricIndex);
            titleElement.textContent = rubricData?.title ? `${letter}. ${rubricData.title}` : `${letter}. [หัวข้อเกณฑ์ใหม่]`;
            titleElement.dataset.field = `rubric_${rubricIndex}_title`;
            headerElement.textContent = rubricData?.header || `คำอธิบายเกณฑ์ฯ ${letter}`;
            headerElement.dataset.field = `rubric_${rubricIndex}_header`;

            tableBody.innerHTML = '';
            let rowsData = rubricData?.rows || {};
            
            // ตรวจสอบว่าเป็น Format เก่า (Array of strings) หรือไม่
            if (Array.isArray(rowsData) && (rowsData.length === 0 || typeof rowsData[0] === 'string')) {
                // นี่คือ Format เก่า: ["Desc 0", "Desc 1", ..., "Desc 5"]
                const tempRows = {};
                // แปลงให้เป็น Format ใหม่ (Object): { "0": "Desc 0", "1": "Desc 1", ... }
                rowsData.forEach((description, level) => {
                    tempRows[String(level)] = description;
                });
                rowsData = tempRows; // ตอนนี้ rowsData เป็น Object แล้ว
            
            } 
            // ตรวจสอบว่าเป็น Format เก่ามากๆ (Array of Objects) หรือไม่
            else if (Array.isArray(rowsData)) {
                const tempRows = {}; 
                rowsData.forEach(r => { if(r && r.level !== undefined) tempRows[r.level] = r.description ?? ''; });
                rowsData = tempRows;
            } 
            // ถ้าไม่ใช่ทั้งสองแบบ หรือเป็น null ให้ใช้ Object ว่าง
            else if (typeof rowsData !== 'object' || rowsData === null) { 
                rowsData = {}; // ทำให้เป็น Object ว่าง
            }


            rubricLevels.forEach((level, rowIndex) => { 
                const currentDescription = rowsData[String(level)] ?? '';
                const row = createRubricRow(rubricIndex, rowIndex, level, currentDescription);
                tableBody.appendChild(row);
            });
            rubricContainer.appendChild(newRubric);
        }

        // Load existing rubrics
        rubricContainer.innerHTML = '';
        if (Array.isArray(loadedRubricsData) && loadedRubricsData.length > 0) {
            loadedRubricsData.forEach((rubric, index) => addOrLoadRubric(rubric, index));
        } else {
            addOrLoadRubric({ title: 'การประเมินการปฏิบัติงาน (Performance)' }, 0);
        }
        updateRubricLetters();

        // Add/Delete Buttons
        if (addRubricBtn) {
            addRubricBtn.addEventListener('click', () => { addOrLoadRubric(); updateRubricLetters(); const d = getSection8_2Data(); saveData('section8_2_data', d); });
        }
        rubricContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('delete-rubric-btn')) {
                const totalRubrics = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)').length;
                if (totalRubrics <= 1) { alert("อย่างน้อยต้องมี 1 หัวข้อ"); return; }
                const rubricToRemove = event.target.closest('.rubric-section');
                if (rubricToRemove && confirm('ต้องการลบหัวข้อนี้?')) {
                    rubricToRemove.remove(); updateRubricLetters(); const d = getSection8_2Data(); saveData('section8_2_data', d);
                }
            }
        });

        function updateRubricLetters() {
            const allRubrics = rubricContainer.querySelectorAll('.rubric-section:not(.hidden)');
            allRubrics.forEach((rubric, index) => {
                const letter = String.fromCharCode(0x0E01 + index);
                const titleElement = rubric.querySelector('.rubric-title');
                const headerElement = rubric.querySelector('.rubric-header');
                if (titleElement) { let txt = titleElement.textContent.trim().replace(/^[ก-ฮ]\.\s*/, ''); titleElement.textContent = `${letter}. ${txt}`; titleElement.dataset.field = `rubric_${index}_title`; }
                if (headerElement) { headerElement.dataset.field = `rubric_${index}_header`; }
                rubric.querySelectorAll('.rubric-tbody tr').forEach((row, rowIndex) => {
                    const levelCell = row.querySelector('.level-cell'); const descCell = row.querySelector('.description-cell');
                    if(levelCell) levelCell.dataset.field = `rubric_${index}_r${rowIndex}_level`;
                    if(descCell) descCell.dataset.field = `rubric_${index}_r${rowIndex}_desc`;
                });
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
            console.log("Prepared Section 9.1 Data:", referencesData);
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
            referenceList.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-btn')) {
                    const itemCount = referenceList.children.length;
                    if (itemCount > 1) {
                        event.target.parentElement.remove();
                        // Re-index names AND save
                        referenceList.querySelectorAll('li input').forEach((input, index) => { input.name = `reference_${index}`; });
                        const d = getSection9_1Data(); saveData('section9_1_data', d); // Save after deleting
                    } else { alert("อย่างน้อยต้องมี 1 รายการ"); }
                }
            });
            referenceList.addEventListener('change', (event) => {
                if(event.target.tagName === 'INPUT'){ const d = getSection9_1Data(); saveData('section9_1_data', d); }
            });
            referenceList.addEventListener('blur', (event) => {
                if(event.target.tagName === 'INPUT'){ const d = getSection9_1Data(); saveData('section9_1_data', d); }
            }, true);
        }
    } catch (e) { console.error("Error initializing Section 9.1 (Reference List):", e); }
});