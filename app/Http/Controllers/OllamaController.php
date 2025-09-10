<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OllamaController extends Controller
{
    public function generateText(Request $request)
    {
        $prompt = $request->input('prompt');

        $response = Http::post('http://localhost:11434/v1/completions', [
            'model' => 'llama3',
            'prompt' => $prompt,
        ]);
        
        $result = $response->json();

        return response()->json($result);
        
    }
}
