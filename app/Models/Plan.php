<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
   use HasFactory;

    protected $table = 'plans';
    protected $primaryKey = 'courseyear_id_ref';
    public $incrementing = false;

    protected $fillable = [
        'courseyear_id_ref',
        'teaching_methods',
        'lesson_plan',
        'assessment_strategies',
        'rubrics',
        'grading_criteria',
        'grade_correction',
    ];

    protected $casts = [
        'teaching_methods' => 'array',
        'lesson_plan' => 'array',
        'assessment_strategies' => 'array',
        'rubrics' => 'array',
        'grading_criteria' => 'array',
    ];

    public function courseYear()
    {
        return $this->belongsTo(Courseyears::class, 'courseyear_id_ref', 'id');
    }
}
