<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ส่งอีเมลแจ้งเตือน</title>
    <style>
        body { font-family: Tahoma, sans-serif; background: #f9fafb; padding: 30px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 6px; }
        button { background: #16a34a; color: white; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background: #15803d; }
        .alert { padding: 12px; margin-bottom: 15px; border-radius: 6px; }
        .alert-success { background: #dcfce7; color: #166534; }
    </style>
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
