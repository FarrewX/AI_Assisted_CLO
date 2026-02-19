<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plos extends Model
{
    /** @use HasFactory<\Database\Factories\PlosFactory> */
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['id', 'curriculum_year_ref', 'plo', 'description', 'domain', 'learning_level', 'specific_lo'];

    public function Curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_year_ref', 'curriculum_year');
    }
}
