<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('component.editprofile');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],

            // ✅ แก้ไขส่วน Email:
            'email' => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')
            ],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('avatar')) {
            // ลบรูปเก่า
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // อัปรูปใหม่
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }
}