<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ส่งอีเมลแจ้งเตือน</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    @include('component.navbar')

    <div class="flex items-center justify-center py-12 mt-40">
        <div class="w-full max-w-xl bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">📢 ฟอร์มส่งการแจ้งเตือนทางอีเมล</h2>

            @if(session('success'))
                <div class="p-3 mb-6 rounded-md bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('send.email') }}" method="POST" class="space-y-5">
                @csrf

                <!-- หัวข้อ -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1">หัวข้อ:</label>
                    <select name="subject" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400">
                        <option value="แจ้งเตือนกำหนดการส่ง มคอ.">แจ้งเตือนกำหนดการส่ง มคอ.</option>
                    </select>
                </div>

                <!-- ข้อความแจ้งเตือน -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1">ข้อความแจ้งเตือน:</label>
                    <textarea name="message" rows="5" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400"
                    >ใกล้จะครบกำหนดส่ง "มคอ.3" แล้ว อาจารย์ทุกท่านอย่าลืมส่งด้วยครับ/ค่ะ</textarea>
                </div>

                <!-- ปุ่มส่ง -->
                <div class="text-right">
                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-2.5 rounded-md hover:bg-green-700 transition">
                        ส่งการแจ้งเตือน
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
