<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'user_id';
    protected $keyType = 'string';

    public function role() {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function courses() {
        return $this->hasMany(Course::class, 'user_id', 'user_id');
    }

    public function prompts() {
        return $this->hasMany(Prompt::class, 'user_id', 'user_id');
    }
}
