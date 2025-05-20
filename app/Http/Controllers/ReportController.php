<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Carbon\Carbon;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    //
    /** Muestra el formulario vacío */
    public function index()
    {
        return view('reportes.index');
    }

    /** Valida fechas, filtra y devuelve la misma vista con datos */
    public function filter(Request $request)
    {
        $data = $request->validate([
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        $desde = Carbon::parse($data['desde'])->startOfDay();
        $hasta = Carbon::parse($data['hasta'])->endOfDay();

        $clientes = Client::whereBetween('created_at', [$desde, $hasta])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reportes.index', compact('clientes', 'desde', 'hasta'));
    }

}
