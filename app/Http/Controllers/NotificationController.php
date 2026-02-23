<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courseyears;
use App\Models\EmailForSend;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class NotificationController extends Controller
{
    public function index()
    {
        // เพิ่ม .course เข้าไปที่ curriculum_course เพื่อดึงข้อมูลจากตาราง courses ต่อ
    $courses = Courseyears::with(['user', 'status', 'curriculum_course.course']) 
        ->whereNotNull('user_id')
        ->get();

    $notStarted = $courses->filter(function ($item) {
        $s = $item->status;
        return (!$s) || (!$s->startprompt || !$s->generated || !$s->success);
    });

    return view('notification', ['courses' => $notStarted]);
    }

    public function sendEmail(Request $request)
    {
        $request->validate(['subject' => 'required', 'message' => 'required']);

        // 1. ดึง Gmail จากตาราง EmailForSend
        $emailConfig = EmailForSend::latest()->first();
        if (!$emailConfig) return back()->with('error', 'กรุณาตั้งค่าอีเมลสาขาก่อน');

        // 2. ตั้งค่า SMTP Dynamic (ถอดรหัสผ่าน)
        config([
            'mail.mailers.smtp.username' => $emailConfig->mail_username,
            'mail.mailers.smtp.password' => Crypt::decryptString($emailConfig->mail_password),
            'mail.from.address' => $emailConfig->mail_username,
            'mail.from.name' => 'ระบบ AI ELO',
        ]);

        // 3. กรองข้อมูลผู้ที่จะส่ง (Logic เดียวกับ index)
        $targets = Courseyears::with(['user', 'status'])
            ->whereNotNull('user_id')
            ->get()
            ->filter(function ($item) {
                $s = $item->status;
                return (!$s) || (!$s->startprompt && !$s->generated && !$s->success);
            });

        // 4. ส่งอีเมล
        try {
            foreach ($targets as $item) {
                if ($item->user && $item->user->email) {
                    Mail::to($item->user->email)->send(
                        new NotificationMail($request->message, $request->subject)
                    );
                }
            }
            return back()->with('success', 'ส่งเมลหาอาจารย์ ' . $targets->count() . ' ท่านสำเร็จ!');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}