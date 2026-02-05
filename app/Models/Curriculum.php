<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curriculum extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'curriculum';
    
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['curriculum_year', 'curriculum_name', 'faculty', 'major'];

    public function curriculum_courses()
    {
        return $this->hasMany(Curriculum_course::class, 'id', 'curriculum_id');
    }

    public function plos()
    {
        return $this->hasMany(Plos::class, 'curriculum_year_ref', 'curriculum_year');
    }
}
