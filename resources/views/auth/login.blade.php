<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>เข้าสู่ระบบ | ELO Generator</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600;700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    @endif

    <style>
        /* เก็บ Animation การลอยขึ้นมาให้ดูมีมิติเหมือนโค้ดต้นฉบับ */
        @keyframes floatIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-float-in {
            animation: floatIn 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans antialiased text-slate-grey">

    <main class="w-full max-w-md bg-pure-white rounded-2xl shadow-[0_20px_60px_rgba(27,38,59,0.1)] border border-cloud-blue p-8 animate-float-in relative overflow-hidden" aria-label="Login form">
        
        <div class="absolute top-0 left-0 w-full h-1.5 bg-royal-blue"></div>

        @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-center text-sm font-medium flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-center text-sm font-medium flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 rounded-xl bg-royal-blue flex items-center justify-center text-pure-white shadow-lg shadow-royal-blue/30" aria-hidden="true">
                <i class="fa-solid fa-cube text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-deep-navy m-0 leading-tight">เข้าสู่ระบบ</h1>
                <p class="text-slate-grey text-sm mt-1">ยินดีต้อนรับสู่ ELO Generator</p>
            </div>
        </div>

        <form id="loginForm" action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-semibold text-deep-navy mb-1.5">อีเมลหรือชื่อผู้ใช้</label>
                <input id="email" name="email" type="text" placeholder="you@example.com" autocomplete="username" value="{{ old('email') }}" required 
                    class="w-full px-4 py-3 rounded-xl border border-cloud-blue bg-pure-white text-deep-navy focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all duration-200" />
                @error('email')
                    <div class="text-red-500 text-sm mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation text-xs"></i> {{ $message }}</div>
                @enderror
                <div id="emailErr" class="hidden text-red-500 text-sm mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation text-xs"></i> กรุณากรอกอีเมล</div>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-deep-navy mb-1.5">รหัสผ่าน</label>
                <div class="relative">
                    <input id="password" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required minlength="6" 
                        class="w-full px-4 py-3 pr-16 rounded-xl border border-cloud-blue bg-pure-white text-deep-navy focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all duration-200" />
                    <button type="button" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-sm font-medium text-slate-grey hover:text-royal-blue transition-colors focus:outline-none" onclick="togglePass()">
                        แสดง
                    </button>
                </div>
                @error('password')
                    <div class="text-red-500 text-sm mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation text-xs"></i> {{ $message }}</div>
                @enderror
                <div id="passErr" class="hidden text-red-500 text-sm mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation text-xs"></i> รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-cloud-blue text-royal-blue focus:ring-royal-blue/30 transition-colors cursor-pointer" /> 
                    <span class="text-sm text-slate-grey group-hover:text-deep-navy transition-colors">จดจำฉันไว้</span>
                </label>
                <a href="#" class="text-sm font-medium text-royal-blue hover:text-deep-navy transition-colors">ลืมรหัสผ่าน?</a>
            </div>

            <button class="w-full bg-royal-blue hover:bg-deep-navy text-pure-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-royal-blue/20 transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-royal-blue/30 mt-2" type="submit">
                เข้าสู่ระบบ
            </button>
        </form>
    </main>

    <script>
        function togglePass() {
            const inp = document.getElementById('password');
            const btn = document.querySelector('button[onclick="togglePass()"]');
            const isPwd = inp.type === 'password';
            inp.type = isPwd ? 'text' : 'password';
            btn.textContent = isPwd ? 'ซ่อน' : 'แสดง';
        }
    </script>
</body>
</html>