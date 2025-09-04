<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="guide-container">
  <div class="guide-card">
    <h1>📌 วิธีการใช้งาน</h1>
    <ul>
      <li>เลือกประเภทผลลัพธ์ที่ต้องการสร้าง (มคอ.3 หรือ มคอ.5)</li>
      <li>เลือกรายวิชาที่ต้องการ</li>
      <li>กรอกคำอธิบายรายวิชาหรือเป้าหมายการสอน</li>
      <li>กดปุ่ม Generate เพื่อให้ระบบสร้างผลลัพธ์การเรียนรู้</li>
      <li>ตรวจสอบผลลัพธ์ในหน้าแสดงตัวอย่าง (Preview)</li>
      <li>สามารถกลับมาแก้ไข prompt หรือแก้ไขข้อมูลที่ AI Gen ออกมาได้</li>
    </ul>
  </div>
</div>


</body>
</html>
<style>
    /* Container จัดกึ่งกลางทั้งหน้า */
.guide-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh; /* เต็มจอแนวตั้ง */
  background-color: #f9fafb; /* สีพื้นหลังอ่อนๆ */
  padding: 20px;
}

/* Card */
.guide-card {
  background: #ffffff;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  max-width: 600px;
  width: 100%;
  text-align: center;
}

/* หัวข้อ */
.guide-card h1 {
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 24px;
}

/* รายการ */
.guide-card ul {
  list-style-type: disc;
  padding-left: 20px;
  text-align: left;
  font-size: 16px;
  font-weight: 500;
  line-height: 1.8;
}
.guide-card li {
  margin-bottom: 12px;
}
</style>