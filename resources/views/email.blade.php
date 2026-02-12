<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่งแจ้งเตือน | ELO Generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased">
    @include('component.navbar')

    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8 mt-20">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                <i class="fa-solid fa-paper-plane text-orange-500 mr-2"></i> ระบบส่งการแจ้งเตือน
            </h1>
            <p class="mt-3 text-lg text-gray-500">
                ส่งอีเมลแจ้งเตือนถึงอาจารย์ที่ยังไม่เริ่มดำเนินการจัดทำ มคอ.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-r-md flex items-center shadow-sm animate-bounce">
                <i class="fa-solid fa-circle-check text-green-400 text-xl mr-3"></i>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-md flex items-center shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-red-400 text-xl mr-3"></i>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="p-8">
                <form action="{{ route('send.email') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-heading mr-1"></i> หัวข้ออีเมล
                        </label>
                        <select name="subject" id="subject" required
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out border">
                            <option value="แจ้งเตือน: กำหนดการจัดทำ มคอ. (AI ELO Generator)">แจ้งเตือน: กำหนดการจัดทำ มคอ. (AI ELO Generator)</option>
                            <option value="ประกาศ: ขอความอนุเคราะห์อาจารย์ดำเนินการในระบบ ELO">ประกาศ: ขอความอนุเคราะห์อาจารย์ดำเนินการในระบบ ELO</option>
                            <option value="ติดตาม: สถานะการใช้งานระบบ AI ELO">ติดตาม: สถานะการใช้งานระบบ AI ELO</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-envelope-open-text mr-1"></i> ข้อความแจ้งเตือน
                        </label>
                        <textarea name="message" id="message" rows="6" required
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 transition duration-150 ease-in-out border"
                            placeholder="พิมพ์ข้อความที่ต้องการส่งถึงอาจารย์ที่นี่...">ใกล้จะครบกำหนดส่ง มคอ. แล้ว อาจารย์ทุกท่านที่ยังไม่เริ่มดำเนินการ รบกวนรีบดำเนินการผ่านระบบ AI ELO Generator ด้วยครับ/ค่ะ หากอาจารย์ท่านใดทำแล้วทางเรากราบขออภัยด้วยครับ/ค่ะ</textarea>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('notification') }}" 
                           class="text-gray-500 hover:text-gray-700 font-medium transition duration-150 flex items-center">
                            <i class="fa-solid fa-arrow-left mr-2"></i> กลับหน้าตาราง
                        </a>
                        <button type="submit" 
                            onclick="return confirm('คุณแน่ใจหรือไม่ที่จะส่งอีเมลแจ้งเตือนถึงอาจารย์ทุกคนที่ยังไม่เริ่มดำเนินการ?')"
                            class="inline-flex items-center px-8 py-3 border border-transparent text-base font-bold rounded-full shadow-lg text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-200 ease-in-out transform hover:-translate-y-1">
                            ส่งการแจ้งเตือนทันที <i class="fa-solid fa-paper-plane ml-2 text-sm"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400 italic">
                    <i class="fa-solid fa-shield-halved mr-1"></i> ระบบจะส่งเมลโดยใช้ Gmail สาขาที่ตั้งค่าไว้ในฐานข้อมูล
                </span>
                <span class="text-xs text-orange-400 font-bold uppercase tracking-widest">
                    AI ELO System
                </span>
            </div>
        </div>
    </div>
</body>
</html>