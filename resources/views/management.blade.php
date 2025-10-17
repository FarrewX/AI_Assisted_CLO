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
        <div class="bg-white flex flex-col items-center gap-5" style="margin-top: 100px;">
            <h1 class="text-2xl font-bold mb-6">การจัดการ</h1>
            <div class="flex flex-wrap gap-4 justify-center w-full max-w-3xl">
                <a href="{{ url('/management/plos') }}" 
                    class="px-6 py-3 bg-sky-300 text-black font-bold rounded-lg shadow hover:bg-sky-400 flex-1 min-w-[160px] max-w-[calc(20%-1rem)] text-center">
                        จัดการ PLOs
                </a>
                <a href="{{ url('/management/courses') }}" 
                    class="px-6 py-3 bg-sky-300 text-black font-bold rounded-lg shadow hover:bg-sky-400 flex-1 min-w-[160px] max-w-[calc(20%-1rem)] text-center">
                        จัดการ course
                </a>
                <a href="{{ url('/management/email') }}"
                    class="px-6 py-3 bg-sky-300 text-black font-bold rounded-lg shadow hover:bg-sky-400 flex-1 min-w-[160px] max-w-[calc(20%-1rem)] text-center">
                        จัดการการส่ง Email
                </a>
                <a href="{{ url('/management/users') }}" 
                    class="px-6 py-3 bg-sky-300 text-black font-bold rounded-lg shadow hover:bg-sky-400 flex-1 min-w-[160px] max-w-[calc(20%-1rem)] text-center">
                        จัดการ ผู้ใช้
                </a>
            </div>
        </div>
    </body>
</html>
