<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generate extends Model
{
    /** @use HasFactory<\Database\Factories\GenerateFactory> */
    use HasFactory;

    protected $table = 'generates';
    protected $fillable = ['ref_id', 'ai_text'];

    public function prompts()
    {
        return $this->belongsToMany(Prompt::class, 'ref_id', 'ref_id');
    }
}
