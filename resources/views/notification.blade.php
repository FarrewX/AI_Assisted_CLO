<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    @include('component.navbar')    
    <!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายการวิชา</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
    <br>
    
  <div class="container">
    <button class="notify-btn" onclick="location.href='message'">ส่งแจ้งเตือน</button>
    
    <table>
      <thead>
        <tr>
          <th>รหัสวิชา</th>
          <th>รายชื่อวิชา</th>
          <th>อาจารย์รายวิชา</th>
          <th>สถานะงาน</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>00000001</td>
          <td>การออกแบบอัลกอริทึมและการเขียนโปรแกรมเชิงขั้นตอน...</td>
          <td>อาจารย์เอ</td>
          <td>ยังไม่เสร็จ</td>
        </tr>
        <tr>
          <td>00000002</td>
          <td>หลักการและเทคนิคการพัฒนาโปรแกรมคอมพิวเตอร์...</td>
          <td>อาจารย์บี</td>
          <td>ยังไม่เริ่ม</td>
        </tr>
      </tbody>
    </table>
  </div>

</body>
</html>

</body>
</html>
<style>
    body {
  font-family: 'Tahoma', sans-serif;
  background-color: #f9f9f9;
  margin: 0;
  padding: 20px;
}

.container {
  width: 90%;
  max-width: 1000px;
  margin: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
  margin-top: 20px;
}

th, td {
  padding: 12px 15px;
  border: 1px solid #ccc;
  text-align: left;
  vertical-align: top;
}

th {
  background-color: #f2f2f2;
}

.notify-btn {
  float: right;
  background-color: orange;
  color: white;
  border: none;
  padding: 10px 20px;
  font-weight: bold;
  cursor: pointer;
  margin-bottom: 10px;
}

.notify-btn:hover {
  background-color: darkorange;
}

.email-btn {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  font-weight: bold;
  cursor: pointer;
}

.email-btn:hover {
  background-color: #45a049;
}

textarea {
  width: 100%;
  padding: 10px;
  font-size: 16px;
}

</style>