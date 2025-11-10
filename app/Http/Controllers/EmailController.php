<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\NotificationMail;
use App\Models\EmailForSend;
use Illuminate\Support\Facades\Crypt;

class EmailController extends Controller
{
    // แสดงฟอร์ม
    public function showForm()
    {
        return view('email');
    }

    public function index()
    {
        $emailConfig = EmailForSend::latest()->first();
        return view('management.email', compact('emailConfig'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'mail_username' => 'required|email',
            'mail_password' => 'required|string',
        ]);

        // insert ใหม่ทุกครั้ง
        EmailForSend::create([
            'mail_username' => $request->mail_username,
            'mail_password' => Crypt::encryptString(trim($request->mail_password)),
        ]);

        return back()->with('success', 'บันทึกค่า Gmail สำเร็จแล้ว!');
    }

    // ส่งอีเมล
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $subject = $request->input('subject');
        $message = $request->input('message');

        $courses = DB::table('courseyears as cy')
            ->join('users as u', 'cy.user_id', '=', 'u.user_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.ref_id')
            ->select(
                'u.email',
                'u.name',
                's.startprompt',
                's.generated',
                's.downloaded',
                's.success'
            )
            ->whereNotNull('u.email')
            ->get();

        // เอาเฉพาะที่ยังไม่ครบ 100%
        $filtered = $courses->filter(function ($item) {
            $stepCount = collect([
                $item->startprompt,
                $item->generated,
                $item->downloaded,
                $item->success
            ])->filter()->count();

            $progress = ($stepCount / 4) * 100;

            return $progress < 100;
        });

        // ส่งอีเมลให้แต่ละคน
        foreach ($filtered as $user) {
            Mail::to($user->email)->send(new NotificationMail($message, $subject));
        }

        return back()->with('success', '📧 ส่งอีเมลสำเร็จแล้ว!');
    }


}
