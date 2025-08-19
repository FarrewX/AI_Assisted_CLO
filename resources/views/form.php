<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="form-container">
    <h2>Generative AI Form</h2>
    <form action="/generate" method="POST">
      <!-- Prompt -->
      <label for="prompt">ข้อความสำหรับ AI:</label>
      <textarea id="prompt" name="prompt" rows="4" placeholder="กรอกข้อความที่ต้องการให้ AI สร้าง..."></textarea>

      <!-- Create button under prompt -->
      <button type="button" class="btn-create" onclick="createPreview()">Create</button>
      
      <!-- Model Selection -->
      <label for="model">เลือกโมเดล:</label>
      <select id="model" name="model">
        <option value="gpt">Chat GPT</option>
        <option value="image">Azura</option>
        <option value="image">Auto Mode</option>
      </select>
      
      <button type="submit">Generate</button>
    </form>

    <!-- Preview Output -->
    <div id="preview" style="margin-top:15px; padding:10px; background:#f9f9f9; border-radius:8px; border:1px solid #ddd; display:none;">
      <strong>ข้อความที่คุณกรอก:</strong>
      <p id="preview-text"></p>
    </div>
  </div>
</body>
</html>
<style>
    body {
      font-family: Arial, sans-serif;
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
    button {
      background-color: #4a90e2;
      color: white;
      border: none;
      cursor: pointer;
      font-weight: bold;
    }
    button:hover {
      background-color: #357abd;
    }
    .btn-create {
      background-color: #28a745;
    }
    .btn-create:hover {
        background-color: #1e7e34;
    }
</style>
<script>
     function createPreview() {
      let prompt = document.getElementById("prompt").value;
      if (prompt.trim() === "") {
        alert("กรุณากรอกข้อความก่อนกด Create");
        return;
      }
      document.getElementById("preview-text").innerText = prompt;
      document.getElementById("preview").style.display = "block";
    }
</script>