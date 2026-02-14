<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // หน้าแสดงรายการ User (Search & Filter)
    public function index(Request $request)
    {
        $query = User::withTrashed();

        // Logic การค้นหา
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Logic กรอง Role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10);
        $roles = Role::all(); // ถ้ามีตาราง roles

        return view('management.user', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูล
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. สร้าง User (ใช้ Transaction เพื่อความปลอดภัย)
        DB::transaction(function () use ($request) {
            $user = new User();

            // รันเลข user_id (ตาม logic เดิมของคุณ)
            $lastUser = User::withTrashed()
                            ->select(DB::raw('CAST(user_id AS UNSIGNED) as max_id'))
                            ->orderBy('max_id', 'desc')
                            ->lockForUpdate()
                            ->first();
            $nextId = $lastUser ? ($lastUser->max_id + 1) : 1;
            $user->user_id = str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // ใส่ข้อมูล
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role_id;
            $user->is_active = false;

            // จัดการรูปภาพ
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }

            $user->save();
        });

        return redirect()->back()->with('success', 'เพิ่มผู้ใช้งานสำเร็จ');
    }

    // ฟังก์ชันเปลี่ยน Role (Admin กดเปลี่ยน)
    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role_id = $request->role;
        $user->save();

        return redirect()->back()->with('success', 'เปลี่ยนบทบาทสำเร็จ');
    }

    // ฟังก์ชันลบ User (Admin กดลบคนอื่น)
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if (auth()->id() == $user->user_id) {
            return redirect()->back()->with('error', 'ไม่สามารถลบบัญชีตัวเองได้');
        }

        // สถานะเป็นก่อนลบ
        $user->is_active = false;
        $user->save();
        
        $user->delete();

        return redirect()->back()->with('success', 'ปิดการใช้งานผู้ใช้เรียบร้อย');
    }

    // ฟังก์ชันกู้คืน (Restore)
    public function restore($id)
    {
        // ค้นหาคนที่มีสถานะ Deleted ด้วย
        $user = User::withTrashed()->where('user_id', $id)->firstOrFail(); 
        
        $user->restore(); // กู้คืน Soft Delete
        
        // ปรับสถานะกลับเป็น Active
        $user->is_active = false; 
        $user->save();

        return redirect()->back()->with('success', 'เปิดการใช้งานผู้ใช้เรียบร้อย');
    }
}