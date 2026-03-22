<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    @endif
  <title>{{ config('app.name', 'AI-Assisted CLO') }} | Login</title>
  <style>
    :root { --bg: #ffffffff; --card: #ffffffcc; --text: #000000ff; --muted: #9b9b9bff; --primary: #6366f1; --primary-hover: #C2E5F2; --ring: #035AA6; --danger: #ef4444; }

    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      margin: 0;
      color: var(--text);
      display: grid;
      place-items: center;
      padding: 2rem;
    }

    .card {
      width: 100%;
      max-width: 420px;
      background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(2,6,23,0.6);
      padding: 24px;
      position: relative;
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 10px;
    }
    .logo {
      width: 36px; height: 36px; border-radius: 10px;
      background: conic-gradient(from 180deg, #22d3ee, #8b5cf6, #22d3ee);
      box-shadow: 0 8px 30px rgba(139,92,246,0.35);
    }
    h1 {
      margin: 0; font-size: 1.25rem; letter-spacing: 0.2px;
    }
    p.sub {
      margin: 4px 0 20px; color: var(--muted); font-size: .95rem;
    }

    form { display: grid; gap: 14px; }
    label { font-size: .9rem; color: var(--muted); }

    .field { position: relative; }
    input[type="email"], input[type="text"], input[type="password"] {
      width: 100%;
      padding: 12px 44px 12px 12px;
      border-radius: 12px;
      border: 1px solid rgba(0, 0, 0, 0.15);
      background: rgba(255, 255, 255, 1);
      color: var(--text);
      outline: none;
      transition: border .2s, box-shadow .2s, transform .05s;
    }
    input:focus {
      border-color: var(--ring);
      box-shadow: 0 0 0 3px rgba(34,211,238,0.25);
    }

    .toggle-pass {
      position: absolute; right: 10px; top: 50%;
      border: 0; background: transparent; color: var(--muted); cursor: pointer; font-size: .9rem;
    }

    .row { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .row label.inline { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .row input[type="checkbox"] { width: 16px; height: 16px; }

    .btn {
      appearance: none; border: 0; cursor: pointer;
      padding: 12px 14px; border-radius: 12px; font-weight: 600; font-size: 1rem;
      background: linear-gradient(135deg, var(--primary), var(--primary-hover));
      color: white; letter-spacing: .2px;
      box-shadow: 0 10px 30px rgba(79,70,229,0.35);
      transition: transform .05s ease, box-shadow .2s ease, filter .2s ease;
    }
    .btn:hover { filter: brightness(1.1); box-shadow: 0 10px 30px rgba(79,70,229,0.35); }
    .btn:active { transform: translateY(1px); }

    .muted-link { color: var(--muted); font-size: .9rem; text-decoration: none; }
    .muted-link:hover { text-decoration: underline; }

    .error { display:none; color: var(--danger); font-size: .9rem; margin-top: -12px; }

    @media (prefers-reduced-motion: no-preference) {
      .card { animation: floatIn .6s cubic-bezier(.2,.8,.2,1); }
      @keyframes floatIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0);} }
    }
  </style>
</head>
<body>
  <main class="card" aria-label="Login form">
    @if(session()->has('success'))
        <div class="alert alert-success" style="background-color: #d1fae5; color: #065f46; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
            {{ session('success') }}
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
        <h1>เข้าสู่ระบบ</h1>
        <p class="sub">ยินดีต้อนรับสู่ {{ config('app.name', 'AI-Assisted CLO') }}</p>
      </div>
    </div>

    <form id="loginForm" action="{{ route('login.post') }}" method="POST">
    @csrf
    <div class="field">
      <label for="email">อีเมลหรือชื่อผู้ใช้</label>
      <input id="email" name="email" type="text" placeholder="you@example.com" autocomplete="username" value="{{ old('email') }}" required />
      @error('email')
        <div class="error" style="display:block; color: red; margin-top: 5px; font-size: 0.9em;">{{ $message }}</div>
      @enderror
      <div id="emailErr" class="error" style="display:none; color:red; font-size: 0.9em; margin-top: 5px;">กรุณากรอกอีเมล</div>
    </div>

    <div class="field">
      <label for="password">รหัสผ่าน</label>
      <div style="position: relative;">
          <input id="password" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required minlength="6" style="width: 100%; padding-right: 50px;" />
          <button type="button" class="toggle-pass" onclick="togglePass()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666;">แสดง</button>
      </div>
      @error('password')
        <div class="error" style="display:block; color: red; margin-top: 5px; font-size: 0.9em;">{{ $message }}</div>
      @enderror
      <div id="passErr" class="error" style="display:none; color:red; font-size: 0.9em; margin-top: 5px;">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</div>
    </div>

    <div class="row">
      <label class="inline"><input type="checkbox" name="remember" /> จดจำฉันไว้</label>
      <a href="#" class="muted-link">ลืมรหัสผ่าน?</a>
    </div>

    <button class="btn" style="background: linear-gradient(to right, #6CBAD9, #035AA6); cursor: pointer;" type="submit">เข้าสู่ระบบ</button>
  </form>
  </main>

  <script>
    function togglePass() {
      const inp = document.getElementById('password');
      const btn = document.querySelector('.toggle-pass');
      const isPwd = inp.type === 'password';
      inp.type = isPwd ? 'text' : 'password';
      btn.textContent = isPwd ? 'ซ่อน' : 'แสดง';
    }
  </script>
</body>
</html>