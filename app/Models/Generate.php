<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generate extends Model
{
    /** @use HasFactory<\Database\Factories\GenerateFactory> */
    use HasFactory;

    protected $table = 'generates';
    protected $fillable = ['courseyear_id_ref', 'ai_text'];

    public function prompts()
    {
        return $this->belongsToMany(Prompt::class, 'courseyear_id_ref', 'courseyear_id_ref');
    }
}
