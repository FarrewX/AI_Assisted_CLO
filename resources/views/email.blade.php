<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ส่งอีเมลแจ้งเตือน</title>
</head>
<body>
    <div class="container">
        <h2>📢 ฟอร์มส่งการแจ้งเตือนทางอีเมล</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('send.email') }}" method="POST">
            @csrf
            <label>อีเมลผู้รับ:</label>
            <input type="email" name="email" placeholder="recipient@example.com" required>

            <label>หัวข้อ:</label>
            <input type="text" name="subject" placeholder="กรอกหัวข้ออีเมล" required>

            <label>ข้อความแจ้งเตือน:</label>
            <textarea name="message" rows="5" placeholder="กรอกข้อความแจ้งเตือน..." required></textarea>

            <button type="submit">ส่งการแจ้งเตือน</button>
        </form>
    </div>
</body>
</html>
