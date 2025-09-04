<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $primaryKey = 'course_id';
    protected $keyType = 'string';

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function prompts() {
        return $this->hasMany(Prompt::class, 'course_id', 'course_id');
    }

    public function status() {
        return $this->hasOne(Status::class, 'course_id', 'course_id');
    }
}