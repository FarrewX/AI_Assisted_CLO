<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plos extends Model
{
    /** @use HasFactory<\Database\Factories\PlosFactory> */
    use HasFactory;

    protected $primaryKey = 'plo';
    public $incrementing = true;

    protected $fillable = ['plo', 'description'];
}
