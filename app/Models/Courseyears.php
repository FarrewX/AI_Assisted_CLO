<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courseyears extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';

    protected $fillable = ['id', 'course_id', 'year'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'course_id');
    }

    public function prompts()
    {
        return $this->hasOne(Prompt::class, 'id', 'ref_id');
    }
}
