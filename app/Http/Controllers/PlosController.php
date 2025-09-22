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

    public function index()
    {
        $plos = Plos::all();
        return view('setting.plo', compact('plos'));
    }

    public function update(Request $request, $id)
    {
        $plo = Plos::find($id);
        if (!$plo) {
            return response()->json(['message' => 'ไม่พบ PLO นี้'], 404);
        }

        $plo->plo = $request->plo;
        $plo->description = $request->description;
        $plo->save();

        return response()->json(['message' => 'อัพเดทสำเร็จ']);
    }
   
}
