document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = document.querySelectorAll('.toggle-pass');
    
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // ดึง ID ของ input จาก attribute data-target
            const targetId = this.getAttribute('data-target');
            const inp = document.getElementById(targetId);
            
            if (inp) {
                const isPwd = inp.type === 'password';
                inp.type = isPwd ? 'text' : 'password';
                this.textContent = isPwd ? 'ซ่อน' : 'แสดง';
            }
        });
    });

    // ตรวจสอบฟอร์ม
    const form = document.getElementById('registerForm');
    
    if (form) {
        form.addEventListener('submit', function (e) {
            const name = document.getElementById('name');
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const pass = document.getElementById('password');
            const confirm = document.getElementById('confirm');

            const nameErr = document.getElementById('nameErr');
            const usernameErr = document.getElementById('usernameErr');
            const emailErr = document.getElementById('emailErr');
            const passErr = document.getElementById('passErr');
            const confirmErr = document.getElementById('confirmErr');

            let valid = true;

            // รีเซ็ตการแสดงผล Error
            [nameErr, usernameErr, emailErr, passErr, confirmErr].forEach(el => {
                if(el) el.style.display = 'none';
            });

            // ตรวจสอบชื่อ
            if (!name.value.trim()) { 
                nameErr.style.display = 'block'; 
                nameErr.innerText = 'กรุณากรอกชื่อจริง';
                valid = false; 
            }
            
            // ตรวจสอบชื่อผู้ใช้
            if (!username.value.trim()) { 
                usernameErr.style.display = 'block'; 
                usernameErr.innerText = 'กรุณากรอกชื่อผู้ใช้';
                valid = false; 
            }

            // ตรวจสอบอีเมล
            if (!email.value.trim() || !email.value.includes('@')) { 
                emailErr.style.display = 'block'; 
                emailErr.innerText = 'กรุณากรอกอีเมลให้ถูกต้อง';
                valid = false; 
            }

            // ตรวจสอบรหัสผ่าน
            if (!pass.value || pass.value.length < 6) { 
                passErr.style.display = 'block'; 
                passErr.innerText = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
                valid = false; 
            }

            // ตรวจสอบยืนยันรหัสผ่าน
            if (pass.value !== confirm.value) { 
                confirmErr.style.display = 'block'; 
                valid = false; 
            }

            if (!valid) { 
                e.preventDefault(); 
            }
        });
    }
});