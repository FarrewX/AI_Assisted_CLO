document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const urlCCId = params.get('CC_id'); 
    
    const courseSelect = document.getElementById('course');

    if (courseSelect && urlCCId) {
        const option = Array.from(courseSelect.options).find(opt => 
            opt.getAttribute('data-cc-id') === urlCCId
        );

        if (option) {
            option.selected = true;
            loadPromptFromOption(option);
        }
    }

    // Attach event listeners that don't depend on global scope
    document.getElementById('course').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        loadPromptFromOption(selectedOption);
    });

    document.getElementById('popup-close').onclick = function() {
        document.getElementById('popup-modal').classList.add('hidden');
    };

    // Checkbox Logic
    document.getElementById('numClo').addEventListener('change', updatePloCheckboxLimit);
    updatePloCheckboxLimit();
    document.querySelectorAll('.selectPlo').forEach(cb => {
        cb.addEventListener('change', updatePloCheckboxLimit);
    });
});

// Helper to get CSRF Token
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

window.loadPromptFromOption = function(option) {
    const savedPrompt = option.getAttribute('data-coursetext');
    const defaultDetail = option.getAttribute('data-detail');
    
    if (savedPrompt && savedPrompt !== 'null' && savedPrompt.trim() !== '') {
        document.getElementById('prompt').value = savedPrompt;
    } else {
        document.getElementById('prompt').value = defaultDetail || '';
    }

    const CCid = option.getAttribute('data-cc-id');
    const year = option.getAttribute('data-year');
    const term = option.getAttribute('data-term');
    const TQF = option.getAttribute('data-TQF');
    fetchPrompt(CCid, year, term, TQF); 
}

window.fetchPrompt = function(CCid, year, term, TQF) {
    if (!CCid) return;

    fetch('/getprompt', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken() // Changed from Blade syntax
        },
        body: JSON.stringify({ CC_id: CCid, year, term, TQF })
    })
    .then(res => {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
    })
    .then(data => {
        if(data && data.prompt) {
            document.getElementById('prompt').value = data.prompt;
        }
    })
    .catch(err => console.error("Error fetching prompt:", err));
}

window.showPopup = function(message) {
    document.getElementById('popup-message').textContent = message;
    document.getElementById('popup-modal').classList.remove('hidden');
}

window.openPreview = function() {
    let prompt = document.getElementById("prompt").value.trim();
    let courseSelect = document.getElementById("course");
    let numClo = document.getElementById("numClo").value;
    let ploChecked = Array.from(document.querySelectorAll('.selectPlo:checked'));
    let ploLabels = ploChecked.map(cb => {
        return cb.parentElement.querySelector('span').textContent.trim();
    });

    if (!courseSelect.value) { showPopup("กรุณาเลือกรายวิชา"); return; }
    if (!prompt) { showPopup("กรุณากรอกรายละเอียดรายวิชา"); return; }
    if (!numClo) { showPopup("กรุณาเลือกจำนวน CLO ที่ต้องการ"); return; }
    if (ploChecked.length === 0) { showPopup("กรุณาเลือกอย่างน้อย 1 PLO"); return; }

    document.getElementById("previewModal").classList.remove("hidden");
    
    let selectedOption = courseSelect.options[courseSelect.selectedIndex];
    let selectedText = selectedOption.text.trim();

    const escapeHtml = (text) => {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    };

    let content = `
        <div class="grid grid-cols-1 gap-2">
            <p><span class="font-semibold">รายวิชา:</span> ${selectedText}</p>
            <p><span class="font-semibold">รายละเอียด:</span> <br>
               <span class="text-gray-600 bg-gray-50 p-2 block rounded mt-1 border whitespace-pre-wrap">${escapeHtml(prompt)}</span>
            </p>
            <div class="flex gap-4">
                <p><span class="font-semibold">จำนวน CLO:</span> ${numClo}</p>
                <p><span class="font-semibold">PLO ที่เลือก:</span> ${ploLabels.join(', ')}</p>
            </div>
        </div>
    `;

    document.getElementById("previewContent").innerHTML = content;
}

window.closePreview = function() { 
    document.getElementById("previewModal").classList.add("hidden"); 
}

window.closeOnBackground = function(event) { 
    if (event.target.id === "previewModal") closePreview(); 
}

window.submitForm = function() {
    closePreview(); 

    let courseSelect = document.getElementById("course");
    let prompt = document.getElementById("prompt").value.trim();
    let numClo = document.getElementById("numClo").value;
    
    let ploChecked = Array.from(document.querySelectorAll('.selectPlo:checked'));
    let ploLabels = ploChecked.map(cb => cb.parentElement.querySelector('span').textContent.trim());

    let selectedOption = courseSelect.options[courseSelect.selectedIndex];
    
    let CCid = selectedOption.getAttribute('data-cc-id'); 
    let coursePk = courseSelect.value;
    let courseCode = selectedOption.getAttribute('data-coursecode');
    let coursename = selectedOption.getAttribute('data-coursename');
    let year = selectedOption.getAttribute('data-year');
    let term = selectedOption.getAttribute('data-term');
    let TQF  = selectedOption.getAttribute('data-TQF');

    if (!CCid) {
        alert("กรุณาเลือกรายวิชาให้ถูกต้อง");
        return;
    }

    showLoadingPopup();
    
    // Save Prompt
    fetch('/saveprompt', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ 
            CC_id: CCid,
            course_pk: coursePk,
            year, term, TQF, prompt 
        })
    })
    .then(async res => {
        const responseData = await res.text(); 
        
        if (!res.ok) {
            console.error("Server Error Detail:", responseData);
            try {
                const jsonError = JSON.parse(responseData);
                if (jsonError.message) throw new Error(jsonError.message);
            } catch (e) {
            }
            throw new Error(`Save Failed (${res.status}): ${responseData.substring(0, 100)}...`);
        }

        return JSON.parse(responseData);
    })
    .then(data => {
        // Generate AI
        return fetch('/generate', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ 
                courseId: coursePk, 
                courseCode, 
                prompt, 
                coursename, 
                numClo, 
                ploLabels, 
                year, 
                term, 
                TQF 
            })
        });
    })
    .then(res => res.json())
    .then(data => { 
        let generatedText = data.response?.choices?.[0]?.text || '';
        let prompt_string = data.prompt_string || '';
        console.log("Debug prompt_string:", prompt_string);
        console.log("AI generated text:", generatedText);

        let jsonData = cleanAndParseAIResponse(generatedText, numClo);

        let aiResponseToSave = '';
        if (jsonData) {
            aiResponseToSave = JSON.stringify(jsonData, null, 2);
        } else {
            aiResponseToSave = generatedText
                .replace(/^[\s\S]*?```json/i, '')
                .replace(/^[\s\S]*?json/i, '')
                .replace(/```[\s\S]*$/, '')
                .trim();
        }

        // Save AI Response
        return fetch('/generate/save', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                CC_id: CCid,
                course_code: coursePk,
                year, term, TQF,
                ai_response: aiResponseToSave
            })
        })
    })
    .then(res => {
         const contentType = res.headers.get('content-type') || '';
         if (!res.ok) throw new Error(`HTTP ${res.status}`);
         
         if (contentType.includes('application/json')) {
            return res.json();
         } else {
            return res.text().then(text => {
              console.warn('Server returned HTML:', text.slice(0, 100));
              return { message: 'HTML response' };
            });
         }
    })
    .then(saveData => {
        if (saveData.redirect) {
            // window.location.href = saveData.redirect;
        } else {
            alert('บันทึกสำเร็จ แต่ไม่มี Redirect URL');
            hideLoadingPopup();
        }
    })
    .catch(err => {
        console.error(err);
        alert("เกิดข้อผิดพลาด: " + err.message);
        hideLoadingPopup();
    });
}

function cleanAndParseAIResponse(generatedText, numClo) {
    let jsonString = generatedText;
    let jsonData = null;

    try {
        // 1. ตัด Markdown โค้ดบล็อกออกก่อน (เผื่อ AI ใส่ ```json มาให้)
        jsonString = jsonString.replace(/```json/ig, '').replace(/```/g, '').trim();

        // 2. ค้นหาตำแหน่งเริ่มต้นและสิ้นสุดของ JSON ({...} หรือ [...])
        const firstCurly = jsonString.indexOf('{');
        const firstSquare = jsonString.indexOf('[');
        const lastCurly = jsonString.lastIndexOf('}');
        const lastSquare = jsonString.lastIndexOf(']');

        let firstIndex = -1;
        let lastIndex = -1;

        // หาจุดเริ่มต้นที่แท้จริง (เจอปีกกาแบบไหนก่อน)
        if (firstCurly !== -1 && firstSquare !== -1) {
            firstIndex = Math.min(firstCurly, firstSquare);
        } else {
            firstIndex = Math.max(firstCurly, firstSquare);
        }

        // หาจุดสิ้นสุดที่แท้จริง
        if (lastCurly !== -1 && lastSquare !== -1) {
            lastIndex = Math.max(lastCurly, lastSquare);
        } else {
            lastIndex = Math.max(lastCurly, lastSquare);
        }

        // 3. ตัดเอาเฉพาะเนื้อหาข้างในปีกกามา
        if (firstIndex !== -1 && lastIndex !== -1 && lastIndex > firstIndex) {
            jsonString = jsonString.substring(firstIndex, lastIndex + 1);
        }

        // ทำความสะอาด Quote เล็กน้อย (เปลี่ยน Quote โค้ง เป็น Quote ตรง)
        jsonString = jsonString
            .replace(/["“”]/g, '"')
            .replace(/['‘’]/g, "'")
            .trim();

        // 4. แปลงเป็น JSON
        jsonData = JSON5.parse(jsonString);

        // 5. จัดระเบียบข้อมูลหลังแปลงเป็น Object/Array สำเร็จแล้ว
        if (jsonData) {
            // ถ้าระบบส่งมาเป็น Array [{...}, {...}] ให้แปลงเป็นรูปแบบ {"CLO 1": {...}}
            if (Array.isArray(jsonData)) {
                let formattedData = {};
                jsonData.forEach((item, index) => {
                    formattedData[`CLO ${index + 1}`] = item;
                });
                jsonData = formattedData;
            }

            // จัดการเปลี่ยนชื่อ Key Learning's Level ตามที่คุณเขียนไว้
            for (const key in jsonData) {
                if (jsonData[key] && jsonData[key]["Learning’s Level"]) {
                    jsonData[key]["Learning's Level"] = jsonData[key]["Learning’s Level"];
                    delete jsonData[key]["Learning’s Level"];
                }
            }
            console.log("Parsed JSON Successfully:", jsonData);
        }

        return jsonData;

    } catch (e) {
        console.error("JSON5 parse error:", e.message);
        console.error("Raw string that failed:", jsonString);
        return null; // ส่งกลับเป็น null เพื่อให้ระบบดักจับและแสดง Error ที่หน้าจอได้
    }
}

window.showLoadingPopup = function() { document.getElementById('loadingPopup').classList.remove('hidden'); }
window.hideLoadingPopup = function() { document.getElementById('loadingPopup').classList.add('hidden'); }

window.updatePloCheckboxLimit = function() {
    const max = parseInt(document.getElementById('numClo').value, 10) || 1;
    const checkboxes = document.querySelectorAll('.selectPlo');
    let checkedCount = 0;
    checkboxes.forEach(cb => { if (cb.checked) checkedCount++; });
    
    if (checkedCount > max) {
        let count = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                count++;
                if (count > max) cb.checked = false;
            }
        });
    }
    checkboxes.forEach(cb => {
        if (!cb.checked && document.querySelectorAll('.selectPlo:checked').length >= max) {
            cb.disabled = true;
            cb.parentElement.classList.add('opacity-50');
        } else {
            cb.disabled = false;
            cb.parentElement.classList.remove('opacity-50');
        }
    });
}