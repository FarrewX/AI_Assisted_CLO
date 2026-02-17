<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course_types extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $incrementing = false;
    protected $primaryKey = 'CC_id_ref';
    protected $keyType = 'int';

    protected $fillable = [
        'CC_id_ref',
        'specific',
        'core',
        'major_required',
        'major_elective',
        'free_elective',
        'prerequisites',
        'hours_theory',
        'hours_practice',
        'hours_self_study',
        'hours_field_trip',
    ];

    public function curriculum_courses()
    {   
        return $this->hasMany(Curriculum_course::class, 'CC_id_ref', 'id');
    }
}