<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; font-size: 16px; color: #000; line-height: 1.6;">
    {{-- ตัวแปรชื่อ content ต้องตรงกับที่ส่งมาจาก Mailable --}}
    {!! nl2br(e($content)) !!}
</body>
</html>