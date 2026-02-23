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

        // 1. ดึง Config ล่าสุดจาก DB เพื่อตั้งค่า SMTP
        $emailConfig = EmailForSend::latest()->first();
        if ($emailConfig) {
            config([
                'mail.mailers.smtp.username' => $emailConfig->mail_username,
                'mail.mailers.smtp.password' => Crypt::decryptString($emailConfig->mail_password),
                'mail.from.address' => $emailConfig->mail_username,
                'mail.from.name' => 'ระบบ AI ELO',
            ]);
        }

        // 2. Query ข้อมูลอาจารย์และสถานะ
        $courses = DB::table('courseyears as cy')
            ->join('users as u', 'cy.user_id', '=', 'u.user_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.courseyear_id_ref')
            ->select(
                'u.email',
                'u.name',
                's.startprompt',
                's.generated',
                's.success'
            )
            ->whereNotNull('u.email')
            ->get();

        // 3. กรองเอาเฉพาะคนที่ยังไม่เสร็จ และ "เอาเฉพาะอีเมลที่ไม่ซ้ำกัน"
        $filtered = $courses->filter(function ($item) {
            $steps = [
                $item->startprompt,
                $item->generated,
                $item->success
            ];
            
            // นับว่าทำไปแล้วกี่ step
            $completedSteps = collect($steps)->filter()->count();

            // ถ้าทำไม่ครบ 3 steps ให้ส่งเมลหา
            return $completedSteps < 3;
        })->unique('email');

        // 4. ส่งอีเมลให้แต่ละคน (Unique Email)
        try {
            foreach ($filtered as $user) {
                Mail::to($user->email)->send(new NotificationMail($request->message, $request->subject));
            }
            return back()->with('success', '📧 ส่งอีเมลแจ้งเตือนอาจารย์จำนวน ' . $filtered->count() . ' ท่านสำเร็จแล้ว! (ท่านละ 1 ฉบับ)');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาดในการส่ง: ' . $e->getMessage());
        }
    }

    // ดึงข้อมูลมาโชว์ใน Popup
    public function previewRecipients()
    {
        $courses = DB::table('courseyears as cy')
            ->join('users as u', 'cy.user_id', '=', 'u.user_id')
            ->leftJoin('statuses as s', 'cy.id', '=', 's.courseyear_id_ref')
            ->select('u.email', 'u.name', 's.startprompt', 's.generated', 's.success')
            ->whereNotNull('u.email')
            ->get();

        $filtered = $courses->filter(function ($item) {
            $steps = [$item->startprompt, $item->generated, $item->success];
            return collect($steps)->filter()->count() < 3;
        })->unique('email')->values();

        return response()->json([
            'count' => $filtered->count(),
            'recipients' => $filtered
        ]);
    }

}
