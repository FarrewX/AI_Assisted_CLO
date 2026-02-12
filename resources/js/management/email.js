document.addEventListener('DOMContentLoaded', () => {
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#mail_password');

    // เช็คว่าในหน้านี้มีปุ่ม togglePassword ไหม (ถ้าไม่มีก็ไม่ทำงาน)
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            this.classList.toggle('text-blue-600');
        });
    }
});