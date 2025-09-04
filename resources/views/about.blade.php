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
    <h1>improve Productivity with an AI Generator</h1>
    <div class="button-container">
    <button class="btn">1-Click Generate</button>
    <button class="btn">Free Downloads</button>
    </div>
</body>
</html>
<style>
    /* body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin: 0;
      padding: 100px 20px;
      background-color: #fff;
    } */

    h1 {
      font-size: 24px;
      font-weight: bold;
      margin-bottom: 40px;
      text-align: center
    }

    .button-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 15px 30px;
      font-size: 16px;
      font-weight: bold;
      border: 1px solid black;
      border-radius: 30px;
      background-color: white;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background-color: black;
      color: white;
    }


</style>