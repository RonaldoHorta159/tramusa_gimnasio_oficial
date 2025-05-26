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
        $validacion = $request->validate([
            'nombre_cliente' => 'string|max:75',
            'dni_cliente' => 'max:9',
            'fecha_nacimiento' => 'nullable|date_format:d-m-Y',
            'residencia' => 'string',
            'tipo_membresia' => 'string',
            'fecha_inicio_membresia' => 'nullable|date_format:d-m-Y',
            'fecha_fin_membresia' => 'nullable|date_format:d-m-Y',
            'importe_membresia' => 'numeric',
        ]);

        $cliente = new Client();
        $cliente->nombre_cliente = $validacion['nombre_cliente'];
        $cliente->dni_cliente = $validacion['dni_cliente'];
        $cliente->residencia = $validacion['residencia'];
        $cliente->tipo_membresia = $validacion['tipo_membresia'];
        $cliente->importe_membresia = $validacion['importe_membresia'];

        // Solo convierto si vino algo
        if (!empty($validacion['fecha_nacimiento'])) {
            $cliente->fecha_nacimiento =
                Carbon::createFromFormat('d-m-Y', $validacion['fecha_nacimiento'])
                    ->format('Y-m-d');
        }
        if (!empty($validacion['fecha_inicio_membresia'])) {
            $cliente->fecha_inicio_membresia =
                Carbon::createFromFormat('d-m-Y', $validacion['fecha_inicio_membresia'])
                    ->format('Y-m-d');
        }
        if (!empty($validacion['fecha_fin_membresia'])) {
            $cliente->fecha_fin_membresia =
                Carbon::createFromFormat('d-m-Y', $validacion['fecha_fin_membresia'])
                    ->format('Y-m-d');
        }

        $cliente->save();

        return back()->with('message', 'Cliente registrado correctamente');
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
            'nombre_cliente' => 'string|max:255',
            'fecha_nacimiento' => 'date_format:d-m-Y',
        ]);

        $cliente = Client::findOrFail($id);

        $updateData = [];
        if (isset($data['nombre_cliente'])) {
            $updateData['nombre_cliente'] = $data['nombre_cliente'];
        }
        if (isset($data['fecha_nacimiento'])) {
            $updateData['fecha_nacimiento'] = Carbon::createFromFormat('d-m-Y', $data['fecha_nacimiento'])->format('Y-m-d');
        }

        $cliente->update($updateData);

        return redirect()
            ->route('cliente.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 1) Buscar el cliente o lanzar 404 si no existe
        $cliente = Client::findOrFail($id);

        // 2) Eliminar (si usas SoftDeletes, será un “soft delete”)
        $cliente->delete();

        // 3) Redirigir con mensaje de éxito
        return redirect()
            ->route('cliente.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
    public function reporte()
    {
        $clientes = Client::all();
        return view('sistema.reporteCliente', compact('clientes'));
    }
}
