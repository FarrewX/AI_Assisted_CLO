<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
   use HasFactory;

    protected $table = 'plans';
    protected $primaryKey = 'ref_id';
    public $incrementing = false;

    protected $fillable = [
        'ref_id',
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

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'ref_id', 'ref_id');
    }
}
