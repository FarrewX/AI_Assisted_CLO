<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $primaryKey = 'ref_id';
    public $incrementing = false;

    protected $fillable = [
        'ref_id',
        'improvement',
        'agreement',
        'research',
        'research_subjects',
        'academic_service',
        'art_culture',
    ];

    protected $casts = [
        'research' => 'array',
    ];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'ref_id', 'ref_id');
    }
}
