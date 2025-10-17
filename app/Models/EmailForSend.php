<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailForSend extends Model
{
    protected $table = 'email_for_send';
    protected $fillable = ['mail_username', 'mail_password'];
}
