<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courseyears extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'courseyears';
    protected $fillable = ['id','CC_id', 'user_id', 'year', 'term', 'TQF'];

    public function prompts()
    {
        return $this->hasOne(Prompt::class, 'id', 'courseyear_id_ref');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function status()
    {
        return $this->hasOne(Status::class, 'courseyear_id_ref', 'id');
    }

    public function curriculum_course()
    {
        return $this->belongsTo(Curriculum_course::class, 'CC_id', 'id');
    }

    public function course_learning_maps()
    {
        return $this->hasOne(course_learning_maps::class, 'courseyear_id_ref', 'id');
    }
}
