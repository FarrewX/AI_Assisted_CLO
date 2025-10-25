<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $primaryKey = 'ref_id';
    public $incrementing = false;

    protected $fillable = [
        'ref_id',
        'improvement',
        's4_agreement',
        'research',
        'research_subjects',
        'academic_service',
        'art_culture',
        's11_grade_correction',
    ];

    protected $casts = [
        'improvement' => 'array',
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'ref_id', 'ref_id');
    }
}
