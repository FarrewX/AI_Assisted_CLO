<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course_evaluations extends Model
{
    use HasFactory;

    protected $table = 'course_evaluations';
    protected $primaryKey = 'courseyear_id_ref';
    public $incrementing = false;

    protected $fillable = ['courseyear_id_ref', 'feedback', 'improvement', 'agreement', 'curriculum_map_data'];

    protected $casts = [
        'curriculum_map_data' => 'array', // แปลง JSON เป็น Array อัตโนมัติ
    ];

    public function courseYear()
    {
        return $this->belongsTo(Courseyears::class, 'courseyear_id_ref', 'id');
    }

}
