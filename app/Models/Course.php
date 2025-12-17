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

    protected $fillable = [
        'id',
        'course_id',
        'course_name_th',
        'course_name_en',
        'course_detail_th',
        'course_detail_en',
    ];

    public function curriculum_courses()
        {
            return $this->hasMany(Curriculum_course::class, 'course_id', 'course_id');
        }
}