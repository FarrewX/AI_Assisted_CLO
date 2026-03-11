window.loadPreview = async function() {
    const form = document.getElementById('emailForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const modal = document.getElementById('previewModal');
    const content = document.getElementById('previewContent');
    const submitBtn = document.getElementById('confirmSubmitBtn');
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<div class="flex justify-center my-8"><i class="fa-solid fa-spinner fa-spin text-orange-500 text-3xl"></i></div>';
    submitBtn.style.display = 'none'; 

    try {
        let response = await fetch('/email/preview-recipients');
        let data = await response.json();

        if (data.count === 0) {
            content.innerHTML = '<div class="text-center py-4"><i class="fa-solid fa-circle-check text-green-500 text-5xl mb-3"></i><p class="text-lg text-green-600 font-bold">ไม่มีเป้าหมายในการส่งอีเมล</p><p class="text-gray-500 mt-1">อาจารย์ทุกคนดำเนินการครบทุกขั้นตอนแล้ว</p></div>';
            return;
        }

        submitBtn.style.display = 'flex';
        
        let html = `<p class="font-medium mb-3 text-gray-800">พบอาจารย์ที่เข้าเกณฑ์จำนวน <span class="text-orange-600 font-bold text-xl">${data.count}</span> ท่าน ดังนี้:</p>`;
        html += `<ul class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">`;
        
        data.recipients.forEach(user => {
            html += `
                <li class="flex items-start gap-3 text-sm">
                    <i class="fa-solid fa-user-circle text-gray-400 mt-0.5 text-lg"></i>
                    <div>
                        <div class="font-bold text-gray-800">${user.name || 'ไม่ระบุชื่อ'}</div>
                        <div class="text-gray-500">${user.email}</div>
                    </div>
                </li>`;
        });
        
        html += `</ul>`;
        content.innerHTML = html;

    } catch (error) {
        content.innerHTML = '<div class="text-center text-red-500 py-4"><i class="fa-solid fa-triangle-exclamation text-4xl mb-3"></i><p class="font-bold">เกิดข้อผิดพลาดในการดึงข้อมูล</p><p class="text-sm">กรุณาตรวจสอบการเชื่อมต่อ หรือลองใหม่อีกครั้ง</p></div>';
    }
}

window.closePreview = function() {
    const modal = document.getElementById('previewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.submitForm = function() {
    const btn = document.getElementById('confirmSubmitBtn');
    btn.innerHTML = 'กำลังส่ง... <i class="fa-solid fa-spinner fa-spin ml-2"></i>';
    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');

    document.getElementById('emailForm').submit();
}

const appName = window.APP_CONFIG.name;
const emailTemplates = {
    "แจ้งเตือน: กำหนดการจัดทำ มคอ.3": `เรียน อาจารย์ผู้รับผิดชอบรายวิชาทุกท่าน\n\nเนื่องด้วยใกล้ถึงกำหนดการส่ง มคอ. ประจำภาคการศึกษา ทางฝ่ายวิชาการจึงขอแจ้งเตือนให้อาจารย์ดำเนินการจัดทำรายละเอียดของรายวิชาผ่านระบบ ${appName} ให้แล้วเสร็จตามกำหนดเวลา เพื่อให้การบริหารจัดการหลักสูตรเป็นไปอย่างมีประสิทธิภาพ\n\nขอขอบคุณในความร่วมมืออย่างยิ่ง`,

    [`ประกาศ: ขอความอนุเคราะห์อาจารย์ดำเนินการในระบบ ${appName}`]: `เรียน อาจารย์ผู้สอนและผู้รับผิดชอบรายวิชาทุกท่าน\n\nทางหลักสูตรขอความอนุเคราะห์อาจารย์ทุกท่าน เข้าใช้งานระบบ ${appName} เพื่อดำเนินการเตรียมความพร้อมและจัดทำ มคอ. สำหรับภาคการศึกษานี้ \n\nระบบ AI จะช่วยอำนวยความสะดวกในการจัดทำเอกสารของท่าน หากท่านมีข้อสงสัยหรือพบปัญหาในการใช้งานระบบ สามารถติดต่อฝ่ายวิชาการได้ทันที\n\nขอแสดงความนับถือ`,

    [`ติดตาม: สถานะการใช้งานระบบ ${appName}`]: `เรียน อาจารย์ผู้รับผิดชอบรายวิชา\n\nจากการตรวจสอบในระบบ ${appName} พบว่ายังมีบางรายวิชาที่ยังไม่ได้เริ่มดำเนินการ หรืออยู่ระหว่างการจัดทำ ทางเราจึงขออนุญาตติดตามความคืบหน้า และรบกวนอาจารย์ช่วยดำเนินการในระบบให้เสร็จสิ้นภายในระยะเวลาที่กำหนด\n\nขอขอบคุณครับ/ค่ะ`
};

document.addEventListener('DOMContentLoaded', function() {
    const subjectSelect = document.getElementById('subject');
    const customSubjectInput = document.getElementById('custom_subject');
    const messageTextarea = document.getElementById('message');

    if (subjectSelect && customSubjectInput && messageTextarea) {
        subjectSelect.addEventListener('change', function() {
            const selectedSubject = this.value;
            
            if (selectedSubject === 'other') {
                // ถ้าเลือก "อื่นๆ": แสดงช่องกรอกหัวข้อ และบังคับให้ต้องกรอก (required)
                customSubjectInput.classList.remove('hidden');
                customSubjectInput.classList.add('block');
                customSubjectInput.setAttribute('required', 'true');
                
                // สลับ name="subject" มาไว้ที่ช่องกรอกเอง เพื่อให้ส่งไป Backend ได้ถูกต้อง
                customSubjectInput.setAttribute('name', 'subject');
                subjectSelect.removeAttribute('name');
                
                // เคลียร์กล่องข้อความให้ว่าง เพื่อให้พิมพ์เอง
                messageTextarea.value = '';
                messageTextarea.placeholder = 'กรุณาพิมพ์ข้อความแจ้งเตือนที่ต้องการส่งถึงอาจารย์...';
                
            } else {
                // ถ้าเลือกหัวข้อมาตรฐาน: ซ่อนช่องกรอกหัวข้อ และยกเลิกบังคับกรอก
                customSubjectInput.classList.remove('block');
                customSubjectInput.classList.add('hidden');
                customSubjectInput.removeAttribute('required');
                
                // คืน name="subject" กลับมาที่ Dropdown
                customSubjectInput.removeAttribute('name');
                subjectSelect.setAttribute('name', 'subject');
                
                // ดึงข้อความ Template มาใส่
                if (emailTemplates[selectedSubject]) {
                    messageTextarea.value = emailTemplates[selectedSubject];
                }
            }
        });
    }
});