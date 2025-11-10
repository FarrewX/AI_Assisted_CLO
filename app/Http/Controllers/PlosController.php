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
        return view('management.plo', compact('plos'));
    }

    public function update(Request $request, $id)
    {
        $plo = Plos::find($id);
        if (!$plo) {
            return response()->json(['message' => 'ไม่พบ PLO นี้'], 404);
        }

        $plo->plo = $request->plo;
        $plo->description = $request->description;
        $plo->domain = $request->domain;
        $plo->learning_level = $request->learning_level;
        $plo->save();

        return response()->json(['message' => 'อัพเดทสำเร็จ']);
    }

    public function create(Request $request)
    {
        $request->validate([
            'plo' => 'required|integer|unique:plos,plo',
            'description' => 'required|string'
        ]);

        $plo = new Plos();
        $plo->plo = $request->plo;
        $plo->description = $request->description;
        $plo->domain = $request->domain;
        $plo->learning_level = $request->learning_level;
        $plo->save();

        return response()->json(['message' => 'เพิ่ม PLO สำเร็จ']);
    }

    public function destroy($id)
    {
        $plo = Plos::find($id);
        if (!$plo) {
            return response()->json(['message' => 'ไม่พบ PLO นี้'], 404);
        }
        $plo->delete();
        return response()->json(['message' => 'ลบ PLO สำเร็จ']);
    }
}
