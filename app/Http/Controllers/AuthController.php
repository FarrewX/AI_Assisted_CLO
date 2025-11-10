<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreAuthRequest;
use App\Http\Requests\UpdateAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|string', // ใช้ได้ทั้ง email หรือ username
            'password' => 'required|string|min:6',
        ]);

        // ตรวจสอบผู้ใช้จาก email หรือ username
        $user = User::where('email', $request->email)
                    ->orWhere('name', $request->email)
                    ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->has('remember'));
            return redirect()->intended('/')->with('success', 'เข้าสู่ระบบสำเร็จ');
        }

        return back()->with('error', 'อีเมล/ชื่อผู้ใช้ หรือ รหัสผ่านไม่ถูกต้อง')->withInput();
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = new User();

        // withTrashed() เพื่อค้นหา User ที่ถูกลบไปด้วย
        $lastUserMax = User::withTrashed()->select(DB::raw('MAX(CAST(user_id AS UNSIGNED)) as max_id'))->first();
        $nextId = $lastUserMax ? ($lastUserMax->max_id + 1) : 1;

        $user->user_id = str_pad($nextId, 6, '0', STR_PAD_LEFT);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = '2';
        
        if ($user->save()) {
            Auth::login($user);
            session(['debug_session_test' => 'Session ทำงานถูกต้อง']);
            return redirect()->route('home')->with('success', 'สมัครสมาชิกสำเร็จ! ยินดีต้อนรับ');
        }

        return redirect()->route('register')->with('error', 'สมัครสมาชิกไม่สำเร็จ! กรุณาลองใหม่อีกครั้ง.');
    }

    public function logout(Request $request)
    {
        Auth::logout(); // ลบ session ของ user

        $request->session()->invalidate(); // ยกเลิก session ทั้งหมด
        $request->session()->regenerateToken(); // สร้าง CSRF token ใหม่

        return redirect()->route('login')->with('success', 'ออกจากระบบเรียบร้อย');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAuthRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Auth $auth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auth $auth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAuthRequest $request, Auth $auth)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auth $auth)
    {
        //
    }
}
