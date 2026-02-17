<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'course_code',
        'course_name_th',
        'course_name_en',
        'course_detail_th',
        'course_detail_en',
        'credit',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function curriculum_courses()
    {   
        return $this->hasMany(Curriculum_course::class, 'course_code', 'id');
    }
}