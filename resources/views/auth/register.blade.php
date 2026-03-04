<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register</title>
  
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth/register.js'])
  @endif

  <style>
    :root { --bg: #ffffffff; --card: #ffffffcc; --text: #000000ff; --muted: #9b9b9bff; --primary: #6366f1; --primary-hover: #C2E5F2; --ring: #035AA6; --danger: #ef4444; }
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body { margin: 0; color: var(--text); display: grid; place-items: center; padding: 2rem; }
    .card { width: 100%; max-width: 420px; background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02)); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; box-shadow: 0 20px 60px rgba(2,6,23,0.6); padding: 24px; position: relative; }
    .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
    .logo { width: 36px; height: 36px; border-radius: 10px; background: conic-gradient(from 180deg, #22d3ee, #8b5cf6, #22d3ee); box-shadow: 0 8px 30px rgba(139,92,246,0.35); }
    h1 { margin: 0; font-size: 1.25rem; letter-spacing: 0.2px; }
    p.sub { margin: 4px 0 20px; color: var(--muted); font-size: .95rem; }
    form { display: grid; gap: 14px; }
    label { font-size: .9rem; color: var(--muted); }
    .field { position: relative; }
    input[type="email"], input[type="text"], input[type="password"] { width: 100%; padding: 12px 44px 12px 12px; border-radius: 12px; border: 1px solid rgba(0, 0, 0, 0.15); background: rgba(255, 255, 255, 1); color: var(--text); outline: none; transition: border .2s, box-shadow .2s, transform .05s; }
    input:focus { border-color: var(--ring); box-shadow: 0 0 0 3px rgba(34,211,238,0.25); }
    .toggle-pass { position: absolute; right: 10px; top: 50%; border: 0; background: transparent; color: var(--muted); cursor: pointer; font-size: .9rem; }
    .btn { appearance: none; border: 0; cursor: pointer; padding: 12px 14px; border-radius: 12px; font-weight: 600; font-size: 1rem; background: linear-gradient(135deg, var(--primary), var(--primary-hover)); color: white; letter-spacing: .2px; box-shadow: 0 10px 30px rgba(79,70,229,0.35); transition: transform .05s ease, box-shadow .2s ease, filter .2s ease; }
    .btn:hover { filter: brightness(1.1); }
    .btn:active { transform: translateY(1px); }
    .muted-link { color: var(--muted); font-size: .9rem; text-decoration: none; }
    .muted-link:hover { text-decoration: underline; }
    .error { display:none; color: var(--danger); font-size: .9rem; margin-top: -12px; }
    @media (prefers-reduced-motion: no-preference) { .card { animation: floatIn .6s cubic-bezier(.2,.8,.2,1); } @keyframes floatIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0);} } }
  </style>
</head>
<body>
  <main class="card" aria-label="Register form">
    @if(session()->has('success'))
        <div class="alert alert-success" style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
            {{ session()->get('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-error" style="background-color: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
            {{ session('error') }}
        </div>
    @endif

    <div class="brand">
      <div class="logo" aria-hidden="true"></div>
      <div>
        <h1>สมัครสมาชิก</h1>
        <p class="sub">สร้างบัญชีใหม่สำหรับ ELO Generator</p>
      </div>
    </div>

    <form id="registerForm" action="{{ route('register.post') }}" method="POST" novalidate>
        @csrf
      <div class="field">
        <label for="name">ชื่อจริง</label>
        <input id="name" name="name" type="text" placeholder="yourname" required value="{{ old('name') }}" />
      </div>
      <div id="nameErr" class="error" @error('name') style="display:block;" @enderror>
          @error('name') {{ $message }} @else กรุณากรอกชื่อจริง @enderror
      </div>

      <div class="field">
        <label for="username">ชื่อผู้ใช้</label>
        <input id="username" name="username" type="text" placeholder="yourname" required value="{{ old('username') }}" />
      </div>
      <div id="usernameErr" class="error" @error('username') style="display:block;" @enderror>
          @error('username') {{ $message }} @else กรุณากรอกชื่อผู้ใช้ @enderror
      </div>

      <div class="field">
        <label for="email">อีเมล</label>
        <input id="email" name="email" type="email" placeholder="you@example.com" autocomplete="email" required value="{{ old('email') }}" />
      </div>
      <div id="emailErr" class="error" @error('email') style="display:block;" @enderror>
          @error('email') {{ $message }} @else กรุณากรอกอีเมลให้ถูกต้อง @enderror
      </div>

      <div class="field">
        <label for="password">รหัสผ่าน</label>
        <input id="password" name="password" type="password" placeholder="••••••••" autocomplete="new-password" required minlength="6" />
        <button type="button" class="toggle-pass" aria-label="แสดง/ซ่อนรหัสผ่าน" data-target="password">แสดง</button>
      </div>
      <div id="passErr" class="error" @error('password') style="display:block;" @enderror>
          @error('password') {{ $message }} @else รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร @enderror
      </div>

      <div class="field">
        <label for="confirm">ยืนยันรหัสผ่าน</label>
        <input id="confirm" name="password_confirmation" type="password" placeholder="••••••••" required minlength="6" />
        <button type="button" class="toggle-pass" aria-label="แสดง/ซ่อนรหัสผ่าน" data-target="confirm">แสดง</button>
      </div>
      <div id="confirmErr" class="error">รหัสผ่านไม่ตรงกัน</div>

      <button class="btn" style="background: linear-gradient(to right, #6CBAD9, #035AA6); cursor: pointer;" type="submit">สมัครสมาชิก</button>
      <p style="text-align:center; margin-top:10px;">
        <a href="login" class="muted-link">มีบัญชีแล้ว? เข้าสู่ระบบ</a>
      </p>
    </form>
  </main>
</body>
</html>