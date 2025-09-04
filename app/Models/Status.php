<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = ['course_id','startprompt','generated','downloaded','success'];

    public function course() {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
