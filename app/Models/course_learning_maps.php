<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course_learning_maps extends Model
{
   use HasFactory;

    protected $table = 'course_learning_maps';
    protected $primaryKey = 'courseyear_id_ref';
    public $incrementing = false;

    protected $fillable = ['courseyear_id_ref', 'course_accord'];
    public function courseYear()
    {
        return $this->belongsTo(Courseyears::class, 'courseyear_id_ref', 'id');
    }
}
