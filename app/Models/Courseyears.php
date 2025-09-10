<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courseyears extends Model
{
    use HasFactory;
    
    protected $fillable = ['course_id', 'year'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function prompts()
    {
        return $this->hasMany(Prompt::class, 'course_id', 'course_id');
    }
}
