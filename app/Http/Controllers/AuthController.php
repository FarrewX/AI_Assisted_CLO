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
        if (Auth::check()) {
            return redirect()->route('home');
        }
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
                    ->orWhere('username', $request->email)
                    ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->has('remember'));

            // อัปเดตสถานะ Active และเวลา Login ล่าสุด
            $user->is_active = true;
            $user->last_login_at = now();
            $user->save();

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
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($request) {
            $user = new User();

            $lastUser = User::withTrashed()
                            ->select(DB::raw('CAST(user_id AS UNSIGNED) as max_id'))
                            ->orderBy('max_id', 'desc')
                            ->lockForUpdate()
                            ->first();

            $nextId = $lastUser ? ($lastUser->max_id + 1) : 1;

            $user->user_id = str_pad($nextId, 6, '0', STR_PAD_LEFT);
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = '2';
            
            // กำหนดสถานะ Active ทันทีที่สมัคร
            $user->is_active = true;
            $user->last_login_at = now();
            
            $user->save();

            Auth::login($user); 
        });

        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'สมัครสมาชิกสำเร็จ! ยินดีต้อนรับ');
    }

    public function logout(Request $request)
    {
        // อัปเดตสถานะเป็น Inactive ก่อน Logout
        if (Auth::check()) {
            $user = Auth::user();
            $user->is_active = false;
            $user->save();
        }

        Auth::logout(); // ลบ session ของ user

        $request->session()->invalidate(); // ยกเลิก session ทั้งหมด
        $request->session()->regenerateToken(); // สร้าง CSRF token ใหม่

        return redirect()->route('login')->with('success', 'ออกจากระบบเรียบร้อย');
    }
}
