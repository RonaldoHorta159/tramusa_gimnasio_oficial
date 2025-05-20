<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class SunatController extends Controller
{
    //

    public function ValidarDni(Request $request)
    {
        $dni = $request->get('dni');
        $url = 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('SUNAT_TOKEN')
        ])->get($url);
        return response()->json([
            'status' => $response->status(),
            'data' => $response->json()
        ]);
    }

}
