<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    use HasFactory;

    protected $fillable = ['ref_id','course_id','course_text'];

    public function courseYear()
    {
        return $this->belongsTo(Courseyears::class, 'ref_id', 'id');
    }
}
