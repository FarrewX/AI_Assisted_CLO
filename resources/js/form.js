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
    .then(res => res.json())
    .then(data => {
        if(data.prompt) {
            document.getElementById('prompt').value = data.prompt;
        }
    })
    .catch(err => console.error(err));
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

    let content = `
        <div class="grid grid-cols-1 gap-2">
            <p><span class="font-semibold">รายวิชา:</span> ${selectedText}</p>
            <p><span class="font-semibold">รายละเอียด:</span> <br><span class="text-gray-600 bg-gray-50 p-2 block rounded mt-1 border">${prompt}</span></p>
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
    .then(res => {
        if (!res.ok) throw new Error('Failed to save prompt');
        return res.json();
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
        console.log("AI generated text:", generatedText);

        // Regex Logic (Keep exact logic from original)
        const jsonMatch = generatedText.match(/```json([\s\S]*?)```/i) || generatedText.match(/\{[\s\S]*\}/);
        let jsonData = null;

        if (jsonMatch) {
            let jsonString = jsonMatch[1] ? jsonMatch[1].trim() : jsonMatch[0].trim();
            let cloCounter = 0;
            jsonString = jsonString.replace(/^```json/i, '').replace(/```$/, '').trim();

            // Extensive regex cleanup (Same as original)
            jsonString = jsonString
                .replace(/["“”]/g, '"')
                .replace(/['‘’]/g, "'")
                .replace(/\n\s*/g, ' ')
                .replace(/\s{2,}/g, ' ')
                .replace(/[“”]/g, '"')
                .replace(/[‘’]/g, "'")
                .replace(/\r?\n/g, ' ')
                .replace(/:\s*([A-Za-zก-๙0-9_]+)\s*([,}\]])/g, ': "$1"$2')
                .replace(/,\s*(?=[}\]])/g, '')
                .replace(/"([^"]*?)'([^"]*?)"/g, (m, g1, g2) => `"${g1}\\'"${g2}"`)
                .replace(/:\s*"([^"]*?)'([^"]*?)"/g, (m, g1, g2) => `: "${g1}\\'${g2}"`)
                .replace(/\\'"+s/g, "'s")
                .replace(/\\'"/g, "'")
                .replace(/"\\'s/g, "'s")
                .replace(/}\s*,\s*{\s*"/g, (match) => {
                    cloCounter++;
                    if (cloCounter <= numClo) {
                        return `}, "CLO ${cloCounter}": { "`;
                    }
                    return match;
                })
                .replace(/"Assessment Method"\s*:\s*([^,\]}]+)/g, (m, g1) => `"Assessment Method": "${g1.trim().replace(/[)\s]+$/g, '')}"`)
                .replace(/\)\s*,/g, ',')
                .replace(/\)\s*}/g, '}')
                .replace(/""([^"]+?)""/g, '"$1"')
                .replace(/"(\s*)"เหตุผล":/g, '", "เหตุผล":')
                .replace(/,\s*}/g, '}')
                .trim();

            if (!jsonString.startsWith('{')) jsonString = '{' + jsonString;
            if (!jsonString.endsWith('}')) jsonString = jsonString + '}';
            
            // Clean specific keys
            try {
                // Pre-parsing cleanup loop (simulation of logic, easier after parse usually but followed structure)
                // Note: Logic here is slightly tricky without object, doing parse first
                jsonData = JSON5.parse(jsonString);
                
                for (const key in jsonData) {
                    if (jsonData[key]["Learning’s Level"]) {
                        jsonData[key]["Learning's Level"] = jsonData[key]["Learning’s Level"];
                        delete jsonData[key]["Learning’s Level"];
                    }
                }
                console.log("Parsed JSON5:", jsonData);
            } catch (e) {
                console.error("JSON5 parse error:", e.message);
                jsonData = null;
            }
        }

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
                course_id: coursePk,
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