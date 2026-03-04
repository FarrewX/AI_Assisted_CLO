<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่งแจ้งเตือน | ELO Generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/email.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="font-sans antialiased">
    @include('component.navbar')

    <div class="max-w-4xl mx-auto py-2 px-4 sm:px-6 lg:px-8 mt-20">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold text-deep-navy sm:text-4xl">
                <i class="fa-solid fa-paper-plane text-royal-blue mr-2"></i> ระบบส่งการแจ้งเตือน
            </h1>
            <p class="mt-3 text-lg text-slate-grey">
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

        <div class="bg-pure-white shadow-sm rounded-2xl overflow-hidden border border-cloud-blue">
            <div class="p-8">
                <form id="emailForm" action="{{ route('send.email') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="subject" class="block text-sm font-semibold text-deep-navy mb-2">
                            <i class="fa-solid fa-heading text-royal-blue mr-1"></i> หัวข้ออีเมล
                        </label>
                        <select name="subject" id="subject" required
                            class="block w-full px-4 py-3 rounded-lg border-cloud-blue text-slate-grey bg-pure-white shadow-sm focus:ring-royal-blue/30 focus:border-royal-blue transition duration-150 ease-in-out border">
                            <option value="" disabled selected hidden>เลือกหัวข้อการแจ้งเตือน</option>
                            <option value="แจ้งเตือน: กำหนดการจัดทำ มคอ. (AI ELO Generator)">แจ้งเตือน: กำหนดการจัดทำ มคอ. (AI ELO Generator)</option>
                            <option value="ประกาศ: ขอความอนุเคราะห์อาจารย์ดำเนินการในระบบ ELO">ประกาศ: ขอความอนุเคราะห์อาจารย์ดำเนินการในระบบ ELO</option>
                            <option value="ติดตาม: สถานะการใช้งานระบบ AI ELO">ติดตาม: สถานะการใช้งานระบบ AI ELO</option>
                            <option value="other">อื่นๆ (ระบุหัวข้อเอง)</option>
                        </select>
                        <input type="text" id="custom_subject" placeholder="โปรดระบุหัวข้ออีเมลที่ต้องการ..."
                            class="hidden mt-3 w-full px-4 py-3 rounded-lg border-cloud-blue text-slate-grey bg-pure-white shadow-sm focus:ring-royal-blue/30 focus:border-royal-blue transition duration-150 ease-in-out border">
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-deep-navy mb-2">
                            <i class="fa-solid fa-envelope-open-text text-royal-blue mr-1"></i> ข้อความแจ้งเตือน
                        </label>
                        <textarea name="message" id="message" rows="7" required
                            class="block w-full px-4 py-3 rounded-lg border-cloud-blue text-slate-grey bg-pure-white shadow-sm focus:ring-royal-blue/30 focus:border-royal-blue transition duration-150 ease-in-out border"
                            placeholder="พิมพ์ข้อความที่ต้องการส่งถึงอาจารย์ที่นี่..."></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('notification') }}" 
                           class="text-slate-grey hover:text-royal-blue font-medium transition duration-150 flex items-center">
                            <i class="fa-solid fa-arrow-left mr-2"></i> กลับหน้าตาราง
                        </a>
                        
                        <button type="button" 
                            onclick="loadPreview()"
                            class="inline-flex items-center px-8 py-3 border border-transparent text-base font-bold rounded-full shadow-md text-pure-white bg-royal-blue hover:bg-deep-navy focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-royal-blue transition duration-200 ease-in-out transform hover:-translate-y-1">
                            ส่งการแจ้งเตือนทันที <i class="fa-solid fa-paper-plane ml-2 text-sm"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="px-8 py-4 bg-cloud-blue/20 border-t border-cloud-blue flex items-center justify-between">
                <span class="text-xs text-slate-grey italic">
                    <i class="fa-solid fa-circle-info mr-1"></i> <span class="underline">ระบบจะส่งเมลโดยใช้ Gmail สาขาที่ตั้งค่าไว้ในฐานข้อมูล</span>
                </span>
                <span class="text-xs text-royal-blue font-bold uppercase tracking-widest">
                    AI ELO GENERATOR
                </span>
            </div>
        </div>
    </div>

    <div id="previewModal" class="fixed inset-0 bg-deep-navy/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-pure-white w-full max-w-lg rounded-2xl shadow-2xl border border-cloud-blue p-8 mx-4 transform scale-100 transition-all">
            <h3 class="text-2xl font-bold mb-4 text-deep-navy border-b border-cloud-blue pb-4">
                <i class="fa-solid fa-users text-royal-blue mr-2"></i>ยืนยันรายชื่อผู้รับอีเมล
            </h3>
            
            <div id="previewContent" class="text-base text-slate-grey leading-relaxed min-h-[100px] max-h-64 overflow-y-auto pr-2">
                <p class="text-center text-slate-grey/70 mt-4">กำลังโหลดรายชื่อ...</p>
            </div>

            <div class="flex justify-end gap-4 mt-8 pt-4 border-t border-cloud-blue">
                <button type="button" onclick="closePreview()" class="px-5 py-2.5 bg-cloud-blue/50 hover:bg-cloud-blue text-deep-navy font-medium rounded-xl transition-colors">
                    ยกเลิก
                </button>
                <button type="button" id="confirmSubmitBtn" onclick="submitForm()" class="px-5 py-2.5 bg-royal-blue hover:bg-deep-navy text-pure-white font-semibold rounded-xl shadow-sm transition-all flex items-center">
                    ยืนยันและส่งอีเมล <i class="fa-solid fa-paper-plane ml-2"></i>
                </button>
            </div>
        </div>
    </div>
</body>
</html>