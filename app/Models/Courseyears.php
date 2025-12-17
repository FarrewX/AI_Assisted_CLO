<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courseyears extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'courseyears';
    protected $fillable = ['id','CC_id', 'user_id', 'year', 'term', 'TQF'];

    public function prompts()
    {
        return $this->hasOne(Prompt::class, 'id', 'ref_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'ref_id');
    }

    public function curriculum_course()
    {
        return $this->belongsTo(Curriculum_course::class, 'CC_id', 'id');
    }
}
