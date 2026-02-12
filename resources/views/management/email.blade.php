<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ตั้งค่าอีเมลแจ้งเตือน | ELO_Generator</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        @vite(['resources/js/app.js', 'resources/js/management/email.js'])
    </head>
    <body class="bg-gray-50 font-sans text-gray-900 antialiased">
        @include('component.navbar')

        <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 py-12 pt-24 sm:pt-28">
            
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden relative z-10">
                
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6 text-white text-center relative">
                    <div class="mx-auto bg-white/20 w-10 h-10 rounded-full flex items-center justify-center mb-3 backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight">ตั้งค่า Gmail Sender</h2>
                    <p class="text-blue-100 text-sm mt-1">กำหนดอีเมลสำหรับส่งการแจ้งเตือนในระบบ</p>
                </div>

                <div class="p-6">
                    <form action="{{ route('email.save') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 mt-1">Gmail Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input 
                                    type="email" 
                                    name="mail_username" 
                                    required
                                    placeholder="example@gmail.com"
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 placeholder-gray-400"
                                    value="{{ old('mail_username', $emailConfig->mail_username ?? '') }}">
                            </div>
                        </div>

                        <div class="relative" x-data="{ showTooltip: false }">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-gray-700">App Password</label>
                                <button type="button" 
                                    @click="showTooltip = !showTooltip"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    วิธีตั้งค่า
                                </button>
                            </div>

                            <div x-show="showTooltip" 
                                 @click.away="showTooltip = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="absolute right-0 bottom-full mb-2 w-72 bg-white border border-gray-200 rounded-xl shadow-2xl p-4 z-20 text-sm text-gray-600">
                                <div class="font-bold text-gray-800 mb-2 border-b pb-2">ขั้นตอนสร้างรหัสผ่านแอป</div>
                                <ol class="list-decimal pl-4 space-y-1.5 text-xs">
                                    <li>ไปที่ <a href="https://myaccount.google.com/security" target="_blank" class="text-blue-600 hover:underline font-medium">Google Security</a></li>
                                    <li>เปิดใช้งาน <strong>2-Step Verification</strong></li>
                                    <li>ไปที่เมนู <strong>App passwords</strong></li>
                                    <li>สร้างใหม่ตั้งชื่อว่า "ELO Generate"</li>
                                    <li>คัดลอกรหัสมาใส่ช่องนี้ (ไม่ต้องเว้นวรรค)</li>
                                </ol>
                                <div class="absolute bottom-[-6px] right-6 w-3 h-3 bg-white border-b border-r border-gray-200 transform rotate-45"></div>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input 
                                    id="mail_password" 
                                    type="password" 
                                    name="mail_password" 
                                    maxlength="19"
                                    placeholder="xxxx xxxx xxxx xxxx"
                                    class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 tracking-wider"
                                >
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 italic mb-2">* รหัสผ่านนี้ไม่ใช่รหัส Login Gmail ปกติ</p>

                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 flex gap-2 items-start text-xs text-amber-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>
                                    <strong>คำเตือน:</strong> กรุณาใส่รหัสผ่านให้ถูกต้อง ระบบไม่สามารถตรวจสอบความถูกต้องได้ หากใส่รหัสผิดจะไม่สามารถส่งอีเมลแจ้งเตือนได้
                                </span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full flex justify-center items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                บันทึกการตั้งค่า
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @if(session('success') || session('error'))
                <div id="popup-notification" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300">
                    
                    <div class="relative w-full max-w-sm p-6 mx-4 bg-white shadow-2xl rounded-2xl transform transition-all scale-100">
                        
                        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition focus:outline-none" onclick="document.getElementById('popup-notification').remove()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>

                        <div class="text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full mb-4 {{ session('error') ? 'bg-red-100' : 'bg-green-100' }}">
                                @if(session('error'))
                                    <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                @else
                                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-1">
                                {{ session('error') ? 'เกิดข้อผิดพลาด' : 'ทำรายการสำเร็จ' }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-6">
                                {{ session('success') ?? session('error') }}
                            </p>

                            <button onclick="document.getElementById('popup-notification').remove()" 
                                class="w-full inline-flex justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
                                {{ session('error') ? 'bg-red-600 hover:bg-red-500 focus-visible:outline-red-600' : 'bg-green-600 hover:bg-green-500 focus-visible:outline-green-600' }}">
                                ตกลง
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <script src="//unpkg.com/alpinejs" defer></script>
    </body>
</html>