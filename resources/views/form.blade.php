<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ELO_Generate</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        
    @endif
</head>
<body>
  <div class="form-container">
    <h2 class="whitespace-nowrap font-bold" style="font-size:30px;">ELO_Generate</h2>
    <form action="/generate" method="POST">

      <div style="display: flex; gap: 20px; align-items: center;">
        <form style="display: flex; gap: 20px; align-items: center;">
          <div style="display: flex; align-items: center; gap: 5px;">
            <input type="radio" id="clo3" name="sec_clo" value="clo3" style="height: 15px; width: 15px; margin-top: 20px;">
            <label for="clo3">มคอ.3</label>&ensp;
          </div>
          <div style="display: flex; align-items: center; gap: 5px;">
            <input type="radio" id="clo5" name="sec_clo" value="clo5" style="height: 15px; width: 15px; margin-top: 20px;">
            <label for="clo5">มคอ.5</label>
          </div>
        </form>
      </div>

      <label for="course">เลือกรายวิชา :</label>
      <select id="course" name="course">
        <option value="" disabled selected>-- กรุณาเลือกรายวิชา --</option>
        <option value="CS101">CS101 - Introduction to Computer Science</option>
        <option value="CS102">CS102 - Programming Basics</option>
        <option value="CS201">CS201 - Data Structures</option>
        <option value="CS301">CS301 - Software Engineering</option>
        <option value="CS401">CS401 - AI Fundamentals</option>
      </select>

      <label for="prompt">รายละเอียดรายวิชา :</label>
      <textarea id="prompt" name="prompt" rows="4" placeholder="กรอกรายละเอียดรายวิชาที่ต้องการให้ AI สร้าง..."></textarea>
      <button type="submit" onclick="createPreview()" 
              class="inline-flex items-center justify-center whitespace-nowrap bg-blue-500
              hover:bg-blue-700 text-white font-bold transition duration-200 ease-in-out shadow-md hover:shadow-lg">
              Generate
      </button>
    </form>
    
    <div id="preview" style="margin-top:15px; padding:10px; background:#f9f9f9; border-radius:8px; border:1px solid #ddd; display:none;">
      <strong>ข้อความที่คุณกรอก:</strong>
      <p id="preview-text"></p>
    </div>
  </div>
</body>
</html>
<style>
    body {
      background: #f4f6f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-container {
      background: white;
      padding: 20px 30px;
      border-radius: 12px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
      width: 400px;
    }
    .form-container h2 {
      text-align: center;
      margin-bottom: 15px;
      color: #333;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 10px;
      margin-bottom: 5px;
    }
    input, textarea, select, button {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 15px;
      font-size: 14px;
    }
</style>
<script> 
function createPreview() {
  let prompt = document.getElementById("prompt").value;
  let sec_clo = document.querySelector('input[name="sec_clo"]:checked');
  let course = document.getElementById("course").value;

  if (prompt.trim() === "") {
    alert("กรุณากรอกข้อความก่อนกด Generate");
    return;
  }
  if (!course) {
    alert("กรุณาเลือกรายวิชา");
    return;
  }
  if (!sec_clo) {
    alert("กรุณาเลือก มคอ.3 หรือ มคอ.5");
    return;
  }

  let previewHtml = `
    <p><strong>Section CLO:</strong> ${sec_clo ? sec_clo.value : 'Not selected'}</p>
    <p><strong>Course:</strong> ${course}</p>
    <p><strong>รายละเอียด:</strong> ${prompt}</p>
  `;

  document.getElementById("preview").innerHTML = previewHtml;
  document.getElementById("preview").style.display = "block";
}
</script>