<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>สมัครสมาชิก | ELO Generator</title>
  
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600;700" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/register.js'])
  @else
  @endif

  <style>
      /* เก็บ Animation การลอยขึ้นมาให้ดูมีมิติเหมือนหน้า Login */
      @keyframes floatIn {
          from { opacity: 0; transform: translateY(15px); }
          to { opacity: 1; transform: translateY(0); }
      }
      .animate-float-in {
          animation: floatIn 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
      }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center py-10 px-4 font-sans antialiased text-slate-grey overflow-y-auto">
  <main class="w-full max-w-md bg-pure-white rounded-2xl shadow-[0_20px_60px_rgba(27,38,59,0.1)] border border-cloud-blue p-8 animate-float-in relative overflow-hidden" aria-label="Register form">
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
            <h1 class="text-2xl font-bold text-deep-navy m-0 leading-tight">สมัครสมาชิก</h1>
            <p class="text-slate-grey text-sm mt-1">สร้างบัญชีใหม่สำหรับ ELO Generator</p>
        </div>
    </div>

    <form id="registerForm" action="{{ route('register.post') }}" method="POST" novalidate class="space-y-4">
        @csrf
      <div>
          <label for="name" class="block text-sm font-semibold text-deep-navy mb-1.5">ชื่อจริง</label>
          <input id="name" name="name" type="text" placeholder="ชื่อ นามสกุล" required value="{{ old('name') }}" 
              class="w-full px-4 py-3 rounded-xl border border-cloud-blue bg-pure-white text-deep-navy focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all duration-200" />
          <div id="nameErr" class="hidden text-red-500 text-sm mt-1.5 flex items-center gap-1" @error('name') style="display:flex;" @enderror>
              <i class="fa-solid fa-circle-exclamation text-xs"></i> 
              @error('name') {{ $message }} @else กรุณากรอกชื่อจริง @enderror
          </div>
      </div>

      <div>
          <label for="username" class="block text-sm font-semibold text-deep-navy mb-1.5">ชื่อผู้ใช้ (Username)</label>
          <input id="username" name="username" type="text" placeholder="username" required value="{{ old('username') }}" 
              class="w-full px-4 py-3 rounded-xl border border-cloud-blue bg-pure-white text-deep-navy focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all duration-200" />
          <div id="usernameErr" class="hidden text-red-500 text-sm mt-1.5 flex items-center gap-1" @error('username') style="display:flex;" @enderror>
              <i class="fa-solid fa-circle-exclamation text-xs"></i> 
              @error('username') {{ $message }} @else กรุณากรอกชื่อผู้ใช้ @enderror
          </div>
      </div>

      <div>
          <label for="email" class="block text-sm font-semibold text-deep-navy mb-1.5">อีเมล</label>
          <input id="email" name="email" type="email" placeholder="you@example.com" autocomplete="email" required value="{{ old('email') }}" 
              class="w-full px-4 py-3 rounded-xl border border-cloud-blue bg-pure-white text-deep-navy focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all duration-200" />
          <div id="emailErr" class="hidden text-red-500 text-sm mt-1.5 flex items-center gap-1" @error('email') style="display:flex;" @enderror>
              <i class="fa-solid fa-circle-exclamation text-xs"></i> 
              @error('email') {{ $message }} @else กรุณากรอกอีเมลให้ถูกต้อง @enderror
          </div>
      </div>

      <div>
          <label for="password" class="block text-sm font-semibold text-deep-navy mb-1.5">รหัสผ่าน</label>
          <div class="relative">
              <input id="password" name="password" type="password" placeholder="••••••••" autocomplete="new-password" required minlength="6" 
                  class="w-full px-4 py-3 pr-16 rounded-xl border border-cloud-blue bg-pure-white text-deep-navy focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all duration-200" />
              <button type="button" class="toggle-pass absolute right-4 top-1/2 transform -translate-y-1/2 text-sm font-medium text-slate-grey hover:text-royal-blue transition-colors focus:outline-none" aria-label="แสดง/ซ่อนรหัสผ่าน" data-target="password">
                  แสดง
              </button>
          </div>
          <div id="passErr" class="hidden text-red-500 text-sm mt-1.5 flex items-center gap-1" @error('password') style="display:flex;" @enderror>
              <i class="fa-solid fa-circle-exclamation text-xs"></i> 
              @error('password') {{ $message }} @else รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร @enderror
          </div>
      </div>

      <div>
          <label for="confirm" class="block text-sm font-semibold text-deep-navy mb-1.5">ยืนยันรหัสผ่าน</label>
          <div class="relative">
              <input id="confirm" name="password_confirmation" type="password" placeholder="••••••••" required minlength="6" 
                  class="w-full px-4 py-3 pr-16 rounded-xl border border-cloud-blue bg-pure-white text-deep-navy focus:outline-none focus:border-royal-blue focus:ring-4 focus:ring-royal-blue/15 transition-all duration-200" />
              <button type="button" class="toggle-pass absolute right-4 top-1/2 transform -translate-y-1/2 text-sm font-medium text-slate-grey hover:text-royal-blue transition-colors focus:outline-none" aria-label="แสดง/ซ่อนรหัสผ่าน" data-target="confirm">
                  แสดง
              </button>
          </div>
          <div id="confirmErr" class="hidden text-red-500 text-sm mt-1.5 flex items-center gap-1">
              <i class="fa-solid fa-circle-exclamation text-xs"></i> รหัสผ่านไม่ตรงกัน
          </div>
      </div>

      <button class="w-full bg-royal-blue hover:bg-deep-navy text-pure-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-royal-blue/20 transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-royal-blue/30 mt-4" type="submit">
          สมัครสมาชิก
      </button>
        
      <p class="text-center mt-6">
          <a href="{{ route('login') }}" class="text-sm font-medium text-slate-grey hover:text-royal-blue transition-colors underline underline-offset-4">
              มีบัญชีแล้ว? เข้าสู่ระบบ
          </a>
      </p>
    </form>
  </main>
</body>
</html>