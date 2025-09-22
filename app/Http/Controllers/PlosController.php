<?php

namespace App\Http\Controllers;

use App\Models\Plos;
use App\Http\Requests\StorePlosRequest;
use App\Http\Requests\UpdatePlosRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PlosController extends Controller
{
    public function plos()
    {
        $plos = DB::table('plos')
            ->select('plo', 'description')
            ->get();
        
        return $plos;
    }
}
