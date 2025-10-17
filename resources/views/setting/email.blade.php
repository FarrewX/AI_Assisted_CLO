<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ELO_Generator</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
    </head>
    <body class="bg-gray-50">
        @include('component.navbar')

        <div class="max-w-md mx-auto mt-20 p-6 bg-white rounded-xl shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 text-center">ตั้งค่า Gmail สำหรับส่งอีเมล</h2>

            @if(session('success'))
                <div class="p-3 mb-4 rounded-md bg-green-100 text-green-800 text-center">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('email.save') }}" method="POST" class="space-y-5 relative">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Email ผู้ส่ง :</label>
                    <input 
                        type="email" 
                        name="mail_username" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
                        value="{{ old('mail_username', $emailConfig->mail_username ?? '') }}">
                </div>

                <!-- Password -->
                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-gray-700 font-medium">Two Factor Password :</label>
                        <button 
                            type="button" 
                            id="helpButton"
                            class="text-gray-500 hover:text-gray-700 border border-gray-300 rounded-full w-6 h-6 flex items-center justify-center text-sm font-semibold"
                            title="วิธีการตั้งค่า">
                            ?
                        </button>
                    </div>

                    <input 
                        id="mail_password" 
                        type="password" 
                        name="mail_password" 
                        maxlength="20"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
                    >

                    <!-- ปุ่ม toggle -->
                    <button 
                        type="button" 
                        id="togglePassword"
                        title="แสดง / ซ่อนรหัสผ่าน"
                        class="absolute right-3 top-9 text-gray-500 hover:text-gray-700 text-sm">
                        แสดง
                    </button>

                    <!-- Tooltip -->
                    <div id="helpTooltip" 
                        class="hidden absolute right-0 mt-3 w-72 p-3 bg-gray-50 border border-gray-300 rounded shadow-lg text-sm text-gray-800 z-10">
                        <strong>วิธีสร้าง App Password (Google)</strong><br><br>
                        1️⃣ เข้า <a href="https://myaccount.google.com/security" target="_blank" class="text-blue-600 underline">บัญชี Google → Security</a><br>
                        2️⃣ เปิด 2-Step Verification<br>
                        3️⃣ ไปที่ <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-600 underline">App Passwords</a><br>
                        4️⃣ เลือก App = Mail, Device = Other → ตั้งชื่อเช่น ELO generator<br>
                        5️⃣ คัดลอก “รหัส 16 ตัว” มาวางในช่องนี้
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <button 
                    type="submit"
                    class="w-full bg-green-600 text-white px-5 py-2.5 rounded-md hover:bg-green-700 transition">
                    บันทึกค่า
                </button>
            </form>
        </div>

        <script>
            // Toggle password visibility
            const togglePassword = document.querySelector('#togglePassword');
            const passwordInput = document.querySelector('#mail_password');
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePassword.textContent = type === 'password' ? 'แสดง' : 'ซ่อน';
            });

            // Tooltip show/hide
            const helpButton = document.querySelector('#helpButton');
            const helpTooltip = document.querySelector('#helpTooltip');
            helpButton.addEventListener('click', () => {
                helpTooltip.classList.toggle('hidden');
            });
        </script>
    </body>
</html>
