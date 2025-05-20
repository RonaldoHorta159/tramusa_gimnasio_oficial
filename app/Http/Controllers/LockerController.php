<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Locker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LockerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1) Actualizar estados relacionados con los lockers
        // Actualizar lockers cuya fecha_fin_locker ha vencido
        Locker::where('estado_locker', 'ocupado')
            ->whereDate('fecha_fin_locker', '<', Carbon::now())
            ->update([
                'id_cliente' => null,
                'estado_locker' => 'libre',
                'fecha_inicio_locker' => null,
                'fecha_fin_locker' => null
            ]);

        // Liberar lockers asignados a clientes cuya membresía ha finalizado
        Locker::whereIn('id_cliente', function ($query) {
            $query->select('id_cliente')
                ->from('clients')
                ->where('estado', 'finalizado');
        })
            ->update([
                'id_cliente' => null,
                'estado_locker' => 'libre',
                'fecha_inicio_locker' => null,
                'fecha_fin_locker' => null
            ]);

        // 2) Construir la consulta base para los lockers
        $query = Locker::query();

        // Filtrar por estado si se proporciona en la solicitud (ejemplo: ?estado=libre)
        if ($request->filled('estado')) {
            $query->where('estado_locker', $request->input('estado'));
        }

        // Aplicar filtros por fecha si están presentes
        if ($request->filled('desde') && $request->filled('hasta')) {
            $desde = Carbon::parse($request->input('desde'))->startOfDay();
            $hasta = Carbon::parse($request->input('hasta'))->endOfDay();
            $query->whereBetween('created_at', [$desde, $hasta]);
        }

        // Ordenar los lockers por número en orden ascendente
        $lockers = $query->orderBy('numero_locker', 'asc')->get();

        // 3) Obtener los clientes activos sin locker asignado
        $clientes = Client::where('estado', 'activo')
            ->whereDoesntHave('locker')
            ->get();

        // 4) Devolver la vista con los datos
        return view('sistema.listLocker', compact('lockers', 'clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar que los datos estén presentes
        $request->validate([
            'id_cliente' => 'required|exists:clients,id_cliente',
            'id_locker' => 'required|exists:lockers,id_locker', // Asegúrate de que el locker exista
            'fecha_inicio_locker' => 'required|date_format:d-m-Y', // Validar formato de fecha
            'cantidad_meses' => 'required|numeric|min:1', // Asegúrate de que se ingrese la cantidad de meses
        ]);

        // Buscar el locker seleccionado
        $locker = Locker::find($request->id_locker);

        if ($locker) {
            // Asignamos el cliente al locker
            $locker->id_cliente = $request->id_cliente; // Aquí asignamos el id_cliente
            $locker->estado_locker = 'ocupado'; // Marcamos el locker como "ocupado"

            // Asignamos las fechas de inicio y fin al locker
            $fechaInicio = Carbon::createFromFormat('d-m-Y', $request->fecha_inicio_locker);
            $locker->fecha_inicio_locker = $fechaInicio->format('Y-m-d'); // Convertir a formato Y-m-d

            // Calcular la fecha de fin
            $fechaFin = $fechaInicio->copy()->addMonths($request->cantidad_meses); // Sumar los meses
            $locker->fecha_fin_locker = $fechaFin->format('Y-m-d'); // Convertir a formato Y-m-d

            // Guardamos los cambios en el locker
            $locker->save();

            // Actualizamos el cliente para añadirle el importe del locker
            $cliente = Client::find($request->id_cliente);
            if ($cliente) {
                // Incrementamos el importe_membresia en 15 soles
                $cliente->importe_membresia += 15;
                $cliente->save(); // Guardamos el cliente con el nuevo importe
            }
        }

        // Redirigir después de la asignación
        return redirect()->route('sistema.listLocker')->with('success', 'Locker asignado correctamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Locker $locker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Locker $locker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Locker $locker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $locker = Locker::findOrFail($id);
        // Desasignar cliente y actualizar estado
        $locker->id_cliente = null;
        $locker->estado_locker = 'libre';
        $locker->save();

        return redirect()
            ->route('locker.index')
            ->with('success', 'Locker liberado correctamente.');
    }
}
