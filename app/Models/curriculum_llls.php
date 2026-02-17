<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class curriculum_llls extends Model
{
   use HasFactory;

    protected $primaryKey = 'curriculum_id_ref';
    public $incrementing = false;

    protected $fillable = ['curriculum_id_ref', 'num_LLL', 'name_LLL'];

    public function Curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id_ref', 'id');
    }
}
