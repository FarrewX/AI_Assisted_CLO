document.addEventListener('DOMContentLoaded', () => {
    const PAGE_DATA = window.PREVIEW_PAGE_DATA || {};

    const parseJsonSafe = (data) => {
        if (typeof data === 'object') return data;
        try { 
            return JSON.parse(String(data || '{}').replace(/```json/ig, '').replace(/```/g, '').trim()); 
        } catch(e) { 
            return {}; 
        }
    };

    // แปลงข้อมูลเตรียมไว้ใช้งาน
    const aiText = parseJsonSafe(PAGE_DATA.aiText);
    const tData = parseJsonSafe(PAGE_DATA.teachingMethods);
    let pData = parseJsonSafe(PAGE_DATA.planData);
    let aData = parseJsonSafe(PAGE_DATA.assessmentData);
    let rData = parseJsonSafe(PAGE_DATA.rubricsData);
    let refData = parseJsonSafe(PAGE_DATA.referencesData);
    
    if (!Array.isArray(pData)) pData = Object.values(pData);
    if (!Array.isArray(aData)) aData = Object.values(aData);
    if (!Array.isArray(rData)) rData = Object.values(rData);
    if (!Array.isArray(refData)) refData = Object.values(refData);

    // ========================================================
    // เรนเดอร์ 2.2 CLO
    // ========================================================
    let html22 = '<table class="w-full border-collapse border border-black text-sm bg-white"><tbody>';
    Object.keys(aiText).forEach(key => {
        let text = (aiText[key] && aiText[key].CLO) ? aiText[key].CLO : '';
        html22 += `<tr><td class="border border-black p-2 font-bold w-[15%] text-center bg-gray-100">${key.replace(/\s/g, '')}</td><td class="border border-black p-2">${text}</td></tr>`;
    });
    html22 += '</tbody></table>';
    const el22 = document.getElementById('preview-s22');
    if (el22) el22.innerHTML = Object.keys(aiText).length ? html22 : '<div class="text-gray-500 italic p-2 bg-gray-100 border rounded">ไม่มีข้อมูล CLO</div>';

    // ========================================================
    // เรนเดอร์ 5.2 Curriculum Mapping
    // ========================================================
    const cmData = parseJsonSafe(PAGE_DATA.curriculumMapData);
    let rowData52 = Array.isArray(cmData) ? (cmData[0] || {}) : cmData;
    let html52 = `<table class="min-w-full border-collapse border border-gray-400 text-sm text-center">
        <thead class="bg-yellow-100">
            <tr>
                <th rowspan="2" class="border border-gray-400 p-2 bg-white w-48">รายวิชา</th>
                <th colspan="7" class="border border-gray-400 p-1 text-xs">คุณธรรม จริยธรรม</th>
                <th colspan="8" class="border border-gray-400 p-1 text-xs">ความรู้</th>
                <th colspan="4" class="border border-gray-400 p-1 text-xs bg-white">ทักษะทางปัญญา</th>
                <th colspan="6" class="border border-gray-400 p-1 text-xs">ทักษะระหว่างบุคคลฯ</th>
                <th colspan="4" class="border border-gray-400 p-1 text-xs bg-white">ทักษะวิเคราะห์ฯ</th>
            </tr><tr>`;
    const cols52 = [7, 8, 4, 6, 4];
    cols52.forEach((count, i) => {
        let bg = (i === 2 || i === 4) ? 'bg-white' : 'bg-yellow-100';
        for(let j=1; j<=count; j++) { html52 += `<th class="border border-gray-400 p-1 font-normal ${bg}">${j}</th>`; }
    });
    html52 += `</tr></thead><tbody class="bg-white"><tr>
        <td class="border border-gray-400 p-2 text-left align-top">${PAGE_DATA.courseCode || ''} <br> ${PAGE_DATA.courseName || ''}</td>`;
    
    const stateSymbols = { '0': '&nbsp;', '1': '<span class="text-lg font-bold">●</span>', '2': '<span class="text-lg">○</span>' };
    for(let k=0; k < 29; k++) {
        let state = rowData52[String(k)] || 0;
        html52 += `<td class="border border-gray-400 p-1 align-middle">${stateSymbols[state] || '&nbsp;'}</td>`;
    }
    html52 += `</tr></tbody></table>`;
    const el52 = document.getElementById('preview-s52');
    if (el52) el52.innerHTML = Object.keys(rowData52).length ? html52 : '<div class="text-gray-500 italic p-2 bg-gray-100 border rounded">ไม่มีข้อมูล Curriculum Mapping</div>';

    // ========================================================
    // เรนเดอร์ 5.3 CLO-PLO Mapping
    // ========================================================
    const accordData = parseJsonSafe(PAGE_DATA.courseAccord);
    
    let cloDataPreview = [];
    if (Object.keys(aiText).length > 0) {
        Object.keys(aiText).forEach(key => {
            const details = aiText[key];
            if (details && typeof details === 'object' && details.CLO) {
                cloDataPreview.push({ code: key.replace(/\s/g, ''), description: details.CLO });
            }
        });
        cloDataPreview.sort((a, b) => (parseInt(a.code.replace('CLO', '')) || 0) - (parseInt(b.code.replace('CLO', '')) || 0));
    }

    const lllDataPreview = PAGE_DATA.lllData || [];
    const cloLllDataPreview = [...cloDataPreview, ...lllDataPreview];
    
    // นับจำนวน PLO เพื่อสร้างคอลัมน์ (สมมติว่าถ้าไม่มีให้ดึงจาก Database)
    let maxPloCount = PAGE_DATA.ploCount || 6; // แก้ไขจำนวน PLO สูงสุดได้ตามจริง

    // ฟังก์ชันช่วยสกัดตัวอักษร Domain
    function getDomainAbbrSafe(domainText) {
        if (!domainText) return '';
        const lower = String(domainText).toLowerCase();
        let types = [];
        if (lower.includes('knowledge')) types.push('K');
        if (lower.includes('skill')) types.push('S');
        if (lower.includes('application') || lower.includes('responsibility')) types.push('AR');
        return types.join(', ');
    }

    function getPloNumbersSafe(ploText) {
        if (!ploText) return [];
        const matches = String(ploText).match(/\d+/g);
        return matches ? matches.map(Number) : [];
    }

    let html53 = `<table class="w-full border-collapse border border-gray-400 text-sm mt-4">
                    <thead class="bg-blue-100 text-center font-bold">
                        <tr>
                            <th class="border border-gray-400 p-2 w-[8%] align-middle">รหัส</th>
                            <th class="border border-gray-400 p-2 w-[40%] align-middle text-left">คำอธิบาย CLOs/LLLs</th>`;
    
    // วาดหัวตารางคอลัมน์ PLO
    for(let c = 1; c <= maxPloCount; c++) {
        html53 += `<th class="border border-gray-400 p-2 align-middle">PLO${c}</th>`;
    }
    html53 += `</tr></thead><tbody class="bg-white">`;

    cloLllDataPreview.forEach((item, rowIndex) => {
        const itemCode = item.code ?? `Item ${rowIndex+1}`;
        const isLLL = itemCode.startsWith('LLL');

        let aiMappedPlos = [];
        let aiDomainAbbr = '';

        if (!isLLL) {
            const originalKey = itemCode.replace('CLO', 'CLO ');
            const aiDetails = aiText[originalKey] || aiText[itemCode] || {};
            let ploRaw = '';
            let domainRaw = '';
            
            for (const k in aiDetails) {
                const lowerK = k.toLowerCase();
                if (lowerK.includes('plo')) ploRaw = aiDetails[k];
                if (lowerK.includes('domain')) domainRaw = aiDetails[k];
            }
            aiMappedPlos = getPloNumbersSafe(ploRaw);
            aiDomainAbbr = getDomainAbbrSafe(domainRaw);
        }

        html53 += `<tr>
                    <td class="border border-gray-400 p-2 text-center font-bold align-top bg-gray-50">${itemCode}</td>
                    <td class="border border-gray-400 p-2 text-left align-top">${item.description ?? ''}</td>`;

        for (let colIndex = 0; colIndex < maxPloCount; colIndex++) {
            const ploDbIndex = colIndex + 1;
            let checkStatus = false;
            let levelValue = '';

            // ตรวจสอบจากข้อมูลที่เคยเซฟไว้
            if (accordData && accordData[itemCode] && accordData[itemCode][ploDbIndex]) {
                checkStatus = accordData[itemCode][ploDbIndex].check ?? false;
                levelValue = accordData[itemCode][ploDbIndex].level ?? '';
            } else {
                // Auto-fill ถ้ายังไม่ได้เซฟ
                if (!isLLL && aiMappedPlos.includes(ploDbIndex)) {
                    checkStatus = true;
                    levelValue = aiDomainAbbr;
                }
            }

            // เลือกการแสดงผล: ถ้าเป็น LLL แสดง ✔, ถ้าเป็น CLO แสดง Level (K, S, AR)
            let cellContent = '';
            if (checkStatus) {
                if (isLLL) {
                    cellContent = `<span class="font-bold text-lg text-blue-700">✔</span><div class="text-xs text-gray-500">${levelValue}</div>`;
                } else {
                    cellContent = `<span class="font-bold text-blue-700">${levelValue}</span>`;
                }
            }
            
            html53 += `<td class="border border-gray-400 p-2 text-center align-middle">${cellContent}</td>`;
        }
        html53 += `</tr>`;
    });

    html53 += `</tbody></table>`;
    
    const el53 = document.getElementById('preview-s53');
    if (el53) el53.innerHTML = cloLllDataPreview.length > 0 ? html53 : '<div class="text-gray-500 italic p-2 bg-gray-100 border rounded">ไม่มีข้อมูลความสอดคล้อง</div>';

    // ========================================================
    // เรนเดอร์ 6. Teaching Methods
    // ========================================================
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

    // ฟังก์ชันสร้างรายการ Checkbox แบบ Read-Only
    function renderCheckboxList(optionsObject, savedData, typeKey) {
        let html = '';
        let relevantData = null;

        if (Array.isArray(savedData)) {
            const relevantObject = savedData.find(obj => obj && typeof obj === 'object' && obj.hasOwnProperty(typeKey));
            if (relevantObject) relevantData = relevantObject[typeKey];
        }

        for (const key in optionsObject) {
            const category = optionsObject[key];
            html += `<div class="font-bold mt-2 text-blue-900">${category.label}</div>`;
            
            category.items.forEach(itemText => {
                let isChecked = false;
                if (relevantData) {
                    if (Array.isArray(relevantData)) {
                        isChecked = relevantData.includes(String(itemText).trim());
                    } else if (typeof relevantData === 'object' && relevantData[category.label] && Array.isArray(relevantData[category.label])) {
                        isChecked = relevantData[category.label].includes(String(itemText).trim());
                    }
                }

                // เลือกสัญลักษณ์ (ติ๊กถูก หรือ กล่องว่าง)
                const mark = isChecked ? '<span class="text-blue-600 font-bold text-lg">☑</span>' : '<span class="text-gray-400 text-lg">☐</span>';
                const indentClass = category.indent ? 'ml-4' : '';
                
                html += `<div class="flex items-start ${indentClass} mt-1 leading-tight">
                            <span>${mark} ${itemText}</span>
                         </div>`;
            });
        }
        return html;
    }

    let html6 = '<table class="w-full border-collapse border border-black text-sm text-left"><thead class="bg-blue-100"><tr><th class="border border-black p-2 w-[15%] text-center">CLO</th><th class="border border-black p-2 w-[40%]">วิธีการสอน (Active Learning)</th><th class="border border-black p-2 w-[45%]">การประเมินผล</th></tr></thead><tbody class="bg-white">';
    
    const sortedCloKeys = Object.keys(tData).sort((a, b) => (parseInt(a.replace(/\D/g, '')) || 0) - (parseInt(b.replace(/\D/g, '')) || 0));

    sortedCloKeys.forEach(cloKey => {
        let rowData = tData[cloKey] || [];
        html6 += `<tr>
                    <td class="border border-black p-2 text-center font-bold align-top bg-gray-50">${cloKey}</td>
                    <td class="border border-black p-2 align-top">${renderCheckboxList(data_s6_teachingOptions, rowData, 'วิธีการสอน')}</td>
                    <td class="border border-black p-2 align-top">${renderCheckboxList(data_s6_assessmentOptions, rowData, 'การประเมินผล')}</td>
                  </tr>`;
    });
    
    html6 += '</tbody></table>';
    const el6 = document.getElementById('preview-s6');
    if (el6) el6.innerHTML = sortedCloKeys.length ? html6 : '<div class="text-gray-500 italic p-2 bg-gray-100 border rounded">ไม่มีข้อมูลวิธีการสอน</div>';
    
    // ========================================================
    // เรนเดอร์ 7. Lesson Plan
    // ========================================================
    let html7 = '<table class="min-w-full border-collapse border border-black text-sm text-left"><thead class="bg-blue-100 text-center"><tr><th class="border border-black p-2 w-[5%]">สัปดาห์</th><th class="border border-black p-2 w-[15%]">หัวข้อ</th><th class="border border-black p-2 w-[20%]">วัตถุประสงค์</th><th class="border border-black p-2 w-[25%]">กิจกรรม</th><th class="border border-black p-2 w-[15%]">สื่อ</th><th class="border border-black p-2 w-[10%]">ประเมิน</th><th class="border border-black p-2 w-[10%]">CLO</th></tr></thead><tbody class="bg-white">';
    pData.forEach(r => {
        html7 += `<tr><td class="border border-black p-2 text-center align-top font-bold bg-gray-50">${r.week || ''}</td>
                  <td class="border border-black p-2 align-top whitespace-pre-wrap">${r.topic || ''}</td>
                  <td class="border border-black p-2 align-top whitespace-pre-wrap">${r.objective || ''}</td>
                  <td class="border border-black p-2 align-top whitespace-pre-wrap">${r.activity || ''}</td>
                  <td class="border border-black p-2 align-top whitespace-pre-wrap">${r.tool || ''}</td>
                  <td class="border border-black p-2 align-top whitespace-pre-wrap">${r.assessment || ''}</td>
                  <td class="border border-black p-2 align-top text-center">${r.clo || ''}</td></tr>`;
    });
    html7 += '</tbody></table>';
    const el7 = document.getElementById('preview-s7');
    if (el7) el7.innerHTML = pData.length ? html7 : '<div class="text-gray-500 italic p-2 bg-gray-100 border rounded">ไม่มีข้อมูลแผนการสอน</div>';

    // ========================================================
    // เรนเดอร์ 8.1 Assessment
    // ========================================================
    let html81 = '<table class="w-full border-collapse border border-black text-sm text-left"><thead class="bg-blue-100 text-center"><tr><th class="border border-black p-2 w-[25%]">วิธีการประเมิน</th><th class="border border-black p-2 w-[35%]">เครื่องมือ/รายละเอียด</th><th class="border border-black p-2 w-[10%]">สัดส่วน</th><th class="border border-black p-2 w-[30%]">ความสอดคล้อง CLO</th></tr></thead><tbody class="bg-white">';
    aData.forEach(r => {
        html81 += `<tr><td class="border border-black p-2 align-top whitespace-pre-wrap">${r.method || ''}</td>
                   <td class="border border-black p-2 align-top whitespace-pre-wrap">${r.tool || ''}</td>
                   <td class="border border-black p-2 text-center align-top font-bold">${r.percent ? r.percent + '%' : ''}</td>
                   <td class="border border-black p-2 align-top"><b>${r.clo || ''}</b><br><span class="text-gray-600">${r.clo_desc || ''}</span></td></tr>`;
    });
    html81 += '</tbody></table>';
    const el81 = document.getElementById('preview-s81');
    if (el81) el81.innerHTML = aData.length ? html81 : '<div class="text-gray-500 italic p-2 bg-gray-100 border rounded">ไม่มีข้อมูลกลยุทธ์การประเมิน</div>';

    // ========================================================
    // เรนเดอร์ 8.2 Rubrics
    // ========================================================
    let html82 = '';
    rData.forEach((rubric, idx) => {
        let letter = String.fromCharCode(0x0E01 + idx);
        html82 += `<div class="mb-6"><div class="font-bold mb-2 text-blue-900">${letter}. ${rubric.title || ''}</div>
                   <table class="w-full border-collapse border border-black text-sm text-left bg-white"><thead class="bg-gray-100 text-center"><tr><th class="border border-black p-2 w-20">ระดับ</th><th class="border border-black p-2">${rubric.header || 'คำอธิบายเกณฑ์'}</th></tr></thead><tbody>`;
        let rows = rubric.rows || {};
        [5,4,3,2,1,0].forEach(lvl => {
            let desc = rows[String(lvl)] || '';
            html82 += `<tr><td class="border border-black p-2 text-center font-bold bg-gray-50">${lvl}</td><td class="border border-black p-2 whitespace-pre-wrap">${desc}</td></tr>`;
        });
        html82 += '</tbody></table></div>';
    });
    const el82 = document.getElementById('preview-s82');
    if (el82) el82.innerHTML = rData.length ? html82 : '<div class="text-gray-500 italic p-2 bg-gray-100 border rounded">ไม่มีข้อมูลเกณฑ์รูบริค</div>';

    // ========================================================
    // เรนเดอร์ 9.1 References
    // ========================================================
    let html91 = '';
    refData.forEach(r => { if(r.trim() !== '') html91 += `<li class="mb-1">${r}</li>`; });
    const el91 = document.getElementById('preview-s91');
    if (el91) el91.innerHTML = html91 !== '' ? html91 : '<li class="text-gray-500 italic list-none -ml-8">ไม่มีข้อมูลสื่อการเรียนรู้</li>';
});