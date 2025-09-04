<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>ELO_Generator</title>

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
    @include('component.navbar')
    <h1 class="font-bold text-2xl text-center mb-40 mt-20">improve Productivity with an AI Generator</h1>
    <div class="button-container flex justify-center gap-10 mb-40">
      <button class="px-8 py-4 text-base font-bold border border-black rounded-full bg-white cursor-pointer transition-all duration-300 hover:bg-black hover:text-white">
        1-Click Generate
      </button>
      <button class="px-8 py-4 text-base font-bold border border-black rounded-full bg-white cursor-pointer transition-all duration-300 hover:bg-black hover:text-white">
        Free Downloads
      </button>
    </div>
</body>
</html>
