<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course_resources extends Model
{
    use HasFactory;

    protected $table = 'course_resources';
    protected $primaryKey = 'courseyear_id_ref';
    public $incrementing = false;

    protected $fillable = ['courseyear_id_ref', 'research', 'research_subjects', 'academic_service', 'art_culture'];
    
    public function courseYear()
    {
        return $this->belongsTo(Courseyears::class, 'courseyear_id_ref', 'id');
    }
}
