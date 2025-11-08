<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;
    protected $table = 'curricula';
    protected $primaryKey = 'ref_id';
    public $incrementing = false;
    protected $fillable = [
        'ref_id',
        'curriculum_name',
        'faculty',
        'major',
        'campus',
        'credits',
        'curriculum_year',
        'is_specific',
        'is_core',
        'is_major_required',
        'is_major_elective',
        'is_free_elective',
        'prerequisites',
        'hours_theory',
        'hours_practice',
        'hours_self_study',
        'hours_field_trip',
        'outcome_statement',
        'curriculum_map_data',
        'course_accord',
    ];

    /**
     * แปลงคอลัมน์ JSON เป็น Array อัตโนมัติ
     */
    protected $casts = [
        'outcome_statement' => 'array',
        'curriculum_map_data' => 'array',
        'course_accord' => 'array',
        'is_specific' => 'boolean',
        'is_core' => 'boolean',
        'is_major_required' => 'boolean',
        'is_major_elective' => 'boolean',
        'is_free_elective' => 'boolean',
    ];

    public function courseyears()
    {
        return $this->belongsTo(Courseyears::class, 'ref_id', 'id');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'ref_id', 'ref_id');
    }

    public function plan()
    {
        return $this->hasOne(Plan::class, 'ref_id', 'ref_id');
    }
}
