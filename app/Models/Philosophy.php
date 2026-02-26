<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Philosophy extends Model
{
    use HasFactory;

    protected $table = 'philosophy';
    
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['id', 'curriculum_year_ref', 'mju_philosophy', 'education_philosophy', 'curriculum_philosophy'];

    public function Curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_year_ref', 'curriculum_year');
    }
}
