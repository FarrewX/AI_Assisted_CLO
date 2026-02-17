<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';
    protected $primaryKey = 'courseyear_id_ref';
    public $incrementing = false;

    protected $fillable = ['courseyear_id_ref', 'startprompt', 'generated', 'success'];
    
    public function course() {
        return $this->belongsTo(Course::class, 'course_code', 'course_code');
    }
}
