<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EmailForSend extends Model
{
    protected $table = 'email_for_send'; // ตรวจสอบชื่อให้ตรงกับ DB
    protected $fillable = ['mail_username', 'mail_password'];

    /**
     * ฟังก์ชันช่วยถอดรหัสผ่านอัตโนมัติเมื่อเรียกใช้ (Optional)
     */
    public function getDecryptedPasswordAttribute()
    {
        try {
            return Crypt::decryptString($this->mail_password);
        } catch (\Exception $e) {
            return null;
        }
    }
}