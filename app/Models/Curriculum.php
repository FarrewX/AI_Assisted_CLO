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

    protected $fillable = ['curriculum_year', 'curriculum_name', 'faculty', 'major', 'campus'];

    public function curriculum_courses()
    {
        return $this->hasMany(Curriculum_course::class, 'curriculum_id', 'id');
    }

    public function plos()
    {
        return $this->hasMany(Plos::class, 'curriculum_year_ref', 'curriculum_year');
    }

    public function llls() {
        return $this->hasMany(curriculum_llls::class, 'curriculum_id_ref', 'id');
    }
}
