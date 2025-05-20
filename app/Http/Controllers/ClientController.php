<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Carbon\Carbon;
use function PHPUnit\Framework\returnArgument;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        // 1) Actualizar estados en la BD:
        // a) Marcar como 'finalizado' aquellos cuya membresía ya expiró
        Client::where('estado', 'activo')
            ->whereDate('fecha_fin_membresia', '<', Carbon::now())
            ->update(['estado' => 'finalizado']);

        // (Opcional) Si quieres que vuelvan a 'activo' al renovar:
        Client::where('estado', 'finalizado')
            ->whereDate('fecha_fin_membresia', '>=', Carbon::now())
            ->update(['estado' => 'activo']);

        // 2) Construir la consulta base (y aplicar filtros de fecha si los envías)
        $query = Client::query();

        // Solo obtener clientes activos
        $query->where('estado', 'activo'); // Filtra los clientes activos

        // Aplicar los filtros por fecha si están presentes
        if ($request->filled('desde') && $request->filled('hasta')) {
            $desde = Carbon::parse($request->input('desde'))->startOfDay();
            $hasta = Carbon::parse($request->input('hasta'))->endOfDay();
            $query->whereBetween('created_at', [$desde, $hasta]);
        }

        // 3) Obtener resultados
        $clientes = $query->orderBy('created_at', 'desc')->get();

        // 4) Devolver la vista con $clientes ya con su estado actualizado
        return view('sistema.listCliente', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sistema.addCliente');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validacion = $request->validate(
            [
                'nombre_cliente' => 'required|string|max:75',
                'dni_cliente' => 'required|max:9',
                'fecha_nacimiento' => 'required|date_format:d-m-Y',
                'residencia' => 'required | string',
                'tipo_membresia' => 'required|string',
                'fecha_inicio_membresia' => 'required|date_format:d-m-Y',
                'fecha_fin_membresia' => 'required|date_format:d-m-Y',
                'importe_membresia' => 'numeric',
            ]
        );

        $cliente = new Client();

        $cliente->nombre_cliente = $request->input('nombre_cliente');
        $cliente->dni_cliente = $request->input('dni_cliente');
        $cliente->fecha_nacimiento = $request->input('fecha_nacimiento');
        $cliente->residencia = $request->input('residencia');
        $cliente->tipo_membresia = $request->input('tipo_membresia');
        $cliente->fecha_inicio_membresia = $request->input('fecha_inicio_membresia');
        $cliente->fecha_fin_membresia = $request->input('fecha_fin_membresia');
        $cliente->importe_membresia = $request->input('importe_membresia');

        $cliente->fecha_nacimiento = Carbon::createFromFormat('d-m-Y', $request->input('fecha_nacimiento'))->format('Y-m-d');
        $cliente->fecha_inicio_membresia = Carbon::createFromFormat('d-m-Y', $request->input('fecha_inicio_membresia'))->format('Y-m-d');
        $cliente->fecha_fin_membresia = Carbon::createFromFormat('d-m-Y', $request->input('fecha_fin_membresia'))->format('Y-m-d');

        $cliente->save();


        return back()->with('message', 'ok');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date_format:d-m-Y',
        ]);

        $cliente = Client::findOrFail($id);

        // Convertir fecha a Y-m-d si tu campo es date
        $fecha = Carbon::createFromFormat('d-m-Y', $data['fecha_nacimiento'])
            ->format('Y-m-d');

        $cliente->update([
            'nombre_cliente' => $data['nombre_cliente'],
            'fecha_nacimiento' => $fecha,
        ]);

        return redirect()
            ->route('cliente.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $id;
    }
    public function reporte()
    {
        $clientes = Client::all();
        return view('sistema.reporteCliente', compact('clientes'));
    }
}
