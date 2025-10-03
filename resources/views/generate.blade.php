<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AI Generated ELO</title>
</head>
<body>
<h1>AI Generated ELO</h1>

@foreach($Generates as $ai)
    <div style="margin-bottom:20px; padding:10px; border:1px solid #ccc;">
        <p><strong>Ref ID:</strong> {{ $ai->ref_id }}</p>
        <p><strong>AI Response:</strong></p>
        <pre>{{ json_encode(json_decode($ai->ai_text), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        <p><small>Created at: {{ $ai->created_at }}</small></p>
    </div>
@endforeach

</body>
</html>
