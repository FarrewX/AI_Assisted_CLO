<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        <div id="app">
            @include('component.navbar')
            <div class="container mx-auto px-4">
                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 80vh;">
                    <h1 class="whitespace-nowrap font-bold" style="font-size:60px;">Welcome</h1>
                    <a href="" data-slot="button" class="inline-flex items-center justify-center whitespace-nowrap bg-blue-600 hover:bg-blue-800 text-white font-bold" style="height:2rem;width:5.6000000000000005rem;font-size:0.76rem;border-radius:0.4rem">Get Started</a>
                </div>
            </div>
        </div>

        <!-- Additional Scripts -->
        @vite(['resources/js/app.js'])
    </body>
</html>
