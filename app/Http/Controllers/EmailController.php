<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;

class EmailController extends Controller
{
    // แสดงฟอร์ม
    public function showForm()
    {
        return view('email');
    }

    // ส่งอีเมล
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $email = $request->input('email');
        $subject = $request->input('subject');
        $message = $request->input('message');

        // ส่งอีเมลผ่าน Mailable
        Mail::to($email)->send(new NotificationMail($message, $subject));

        return back()->with('success', '📧 ส่งอีเมลสำเร็จแล้ว!');
    }
}
