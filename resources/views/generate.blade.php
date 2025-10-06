<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>AI Generated ELO</title>
</head>
<body>
<h1>AI Generated ELO</h1>

@if($Generates->isEmpty())
    <p>No AI generated data found.</p>
@else
    @foreach($Generates as $ai)
        <div style="margin-bottom:20px; padding:10px; border:1px solid #ccc;">
            <p><strong>Ref ID:</strong> {{ $ai->ref_id }}</p>
            <p><strong>AI Response:</strong></p>
            <pre>{{ $ai->ai_text }}</pre>
            <p><small>Created at: {{ $ai->created_at }}</small></p>
        </div>
    @endforeach
@endif

</body>
</html>
