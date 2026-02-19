<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class curriculum_llls extends Model
{
   use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = ['curriculum_year_ref', 'num_LLL', 'name_LLL', 'check_LLL'];

    public function Curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_year_ref', 'curriculum_year');
    }
}
