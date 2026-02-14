<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';
    protected $primaryKey = 'ref_id';
    public $incrementing = false;

    protected $fillable = ['ref_id', 'startprompt', 'generated', 'success'];
    
    public function course() {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }
}
