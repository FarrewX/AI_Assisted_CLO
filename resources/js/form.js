document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const urlCCId = params.get('CC_id'); 
    const urlYear = params.get('year');
    const urlTerm = params.get('term');
    const urlTQF = params.get('TQF');
    
    const courseSelect = document.getElementById('course');

    if (courseSelect && urlCCId) {
        const option = Array.from(courseSelect.options).find(opt => {
            let isMatch = opt.getAttribute('data-cc-id') === urlCCId;
            
            if (urlYear) isMatch = isMatch && opt.getAttribute('data-year') === urlYear;
            if (urlTerm) isMatch = isMatch && opt.getAttribute('data-term') === urlTerm;
            if (urlTQF)  isMatch = isMatch && opt.getAttribute('data-TQF') === urlTQF;
            
            return isMatch;
        });

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
        if (!res.ok) throw new Error(`Save Failed (${res.status})`);
        return JSON.parse(responseData);
    })
    .then(data => {
        const attemptAIGeneration = (retriesLeft) => {
            console.log(`[AI] กำลังเจนข้อมูล... (เหลือโควต้าให้พังได้อีก ${retriesLeft} ครั้ง)`);
            
            // ยิงไปขอ AI
            return fetch('/generate', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ 
                    courseId: coursePk, courseCode, prompt, coursename, numClo, ploLabels, year, term, TQF 
                })
            })
            .then(res => res.json())
            .then(data => { 
                let generatedText = data.response?.choices?.[0]?.text || '';
                
                // โยนไปให้ฟังก์ชันทำความสะอาดตรวจสอบ
                let jsonData = cleanAndParseAIResponse(generatedText, numClo);

                if (!jsonData) {
                    if (retriesLeft > 0) {
                        console.warn("⚠️ ข้อมูลพัง! ระบบกำลังแอบสั่ง AI เจนใหม่อัตโนมัติ...");
                        // เรียกตัวเองเพื่อยิง API ใหม่ทันที (ลดโควต้าลง 1)
                        return attemptAIGeneration(retriesLeft - 1); 
                    } else {
                        // ถ้าพังครบ 3 รอบ ให้ยอมแพ้แล้วฟ้อง Error
                        throw new Error("AI สร้างข้อมูลพังเกินเยียวยา กรุณากดปุ่มสร้างใหม่อีกครั้ง");
                    }
                }

                // ถ้ารอดมาได้ แปลว่าข้อมูลสมบูรณ์! จับแปลงเป็น String เตรียมเซฟ
                let aiResponseToSave = JSON.stringify(jsonData, null, 2);

                // ส่งไป Save
                return fetch('/generate/save', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        CC_id: CCid, course_code: coursePk, year, term, TQF, ai_response: aiResponseToSave
                    })
                });
            });
        };

        // สั่งเจน AI โดยให้โควต้าลองแก้ตัวใหม่ได้ 3 ครั้ง!
        return attemptAIGeneration(3);
    })
    .then(res => {
         const contentType = res.headers.get('content-type') || '';
         if (!res.ok) throw new Error(`HTTP ${res.status}`);
         
         if (contentType.includes('application/json')) {
            return res.json();
         } else {
            return res.text().then(text => { return { message: 'HTML response' }; });
         }
    })
    .then(saveData => {
        if (saveData.redirect) {
            window.location.href = saveData.redirect;
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
    if (!generatedText || typeof generatedText !== 'string') return null;

    let jsonData = null;
    let jsonString = generatedText;

    // console.log("[AI Parser] เริ่มต้นทำความสะอาดข้อความจาก AI...");

    try {
        // ตัดขยะรอบนอกและ Markdown ทิ้งให้หมด
        jsonString = jsonString
            .replace(/```json/ig, '')
            .replace(/```/g, '')
            .trim();

        // พยายาม Parse ด้วยวิธีปกติก่อน (กรณี AI ส่งมาเป๊ะ)
        // ถ้าใช้ JSON5 ต้องเช็คก่อนว่ามีการโหลดไลบรารีมาหรือเปล่า ถ้าไม่มีใช้ JSON ปกติ
        if (typeof JSON5 !== 'undefined') {
            jsonData = JSON5.parse(jsonString);
        } else {
            jsonData = JSON.parse(jsonString);
        }
        
        // console.log("[AI Parser] แปลง JSON สำเร็จด้วยวิธีปกติ:", jsonData);

    } catch (e) {
        console.warn("⚠️ [AI Parser] แปลง JSON ไม่สำเร็จ! ระบบกำลังใช้วิธีสกัดข้อมูล (Regex Extractor)...");
        
        // สกัดข้อมูลเหมือนที่ทำใน editdoc
        let finalData = {};
        
        // ค้นหา Block ที่มีคำว่า CLO
        const blocks = jsonString.match(/\{\s*"CLO"[\s\S]*?\}/g) || jsonString.match(/\{\s*CLO[\s\S]*?\}/g);
        
        if (blocks) {
            blocks.forEach((block, index) => {
                const cloKey = `CLO ${index + 1}`;
                
                // ฟังก์ชันย่อยสำหรับสกัดค่า
                const extractValue = (keyword) => {
                    const regex = new RegExp(`"?${keyword}"?\\s*:\\s*"?([^\\n\\}]+)"?`, 'i');
                    const match = block.match(regex);
                    if (match) {
                        let val = match[1].trim();
                        // ลบลูกน้ำหรือฟันหนูที่เกินมาด้านหลัง
                        if (val.endsWith(',')) val = val.slice(0, -1).trim();
                        if (val.endsWith('"') || val.endsWith("'")) val = val.slice(0, -1).trim();
                        return val;
                    }
                    return '';
                };

                // ดึงคำอธิบาย CLO
                const cloMatch = block.match(/"?CLO"?\s*:\s*"([^"]+)"/i) || block.match(/"?CLO"?\s*:\s*([^,\n}]+)/i);
                
                finalData[cloKey] = {
                    "CLO": cloMatch ? cloMatch[1].trim() : '',
                    "PLO ต่อ ร้องรับ": extractValue('PLO ที่รองรับ') || extractValue('PLO ต่อ ร้องรับ') || extractValue('PLO'),
                    "Domain": extractValue('Domain'),
                    "Learning's Level": extractValue('Learning[’\']s Level') || extractValue('Learning')
                };
            });
            
            jsonData = finalData;
            // console.log("🛠️ [AI Parser] สกัดข้อมูลสำเร็จ:", jsonData);
        } else {
            console.error("❌ [AI Parser] ข้อมูลพังเกินเยียวยา ไม่พบ Block CLO");
            return null;
        }
    }

    // จัดระเบียบขั้นสุดท้าย
    if (jsonData) {
        let formattedData = {};
        
        // กรณี AI ส่งมาเป็น Array [{...}, {...}] ให้แปลงเป็น {"CLO 1": {...}, ...}
        if (Array.isArray(jsonData)) {
            jsonData.forEach((item, index) => {
                formattedData[`CLO ${index + 1}`] = item;
            });
            jsonData = formattedData;
        }

        // วนลูปแก้ชื่อ Key ที่อาจจะมีปัญหาเรื่องเครื่องหมายขีด (Quote) ให้เป็นมาตรฐาน
        for (const key in jsonData) {
            let itemDetails = jsonData[key];
            
            // ถ้า AI พิมพ์ "Learning’s Level" (แบบ Quote โค้ง)
            if (itemDetails && itemDetails["Learning’s Level"]) {
                itemDetails["Learning's Level"] = itemDetails["Learning’s Level"];
                delete itemDetails["Learning’s Level"];
            }
            // ถ้า AI ขี้เกียจพิมพ์ พิมพ์มาแค่ "Learning"
            else if (itemDetails && itemDetails["Learning"] && !itemDetails["Learning's Level"]) {
                 itemDetails["Learning's Level"] = itemDetails["Learning"];
                 delete itemDetails["Learning"];
            }
            
            // กรณีชื่อ PLO ผิดเพี้ยน
            if (itemDetails && itemDetails["PLO ที่รองรับ"]) {
                itemDetails["PLO ต่อ ร้องรับ"] = itemDetails["PLO ที่รองรับ"];
                delete itemDetails["PLO ที่รองรับ"];
            } else if (itemDetails && itemDetails["PLO"] && !itemDetails["PLO ต่อ ร้องรับ"]) {
                itemDetails["PLO ต่อ ร้องรับ"] = itemDetails["PLO"];
                delete itemDetails["PLO"];
            }
        }
    }

    return Object.keys(jsonData).length > 0 ? jsonData : null;
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