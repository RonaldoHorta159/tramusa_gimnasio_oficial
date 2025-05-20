<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Carbon\Carbon;

class RenewalController extends Controller
{
    /** Muestra clientes expirados de tipo “por_mes” */
    public function index()
    {
        // Aseguramos estados actualizados: marcar como 'finalizado' aquellos cuya membresía ya expiró
        Client::where('estado', 'activo')
            ->whereDate('fecha_fin_membresia', '<', Carbon::now()) // Compara la fecha de fin de membresía con la fecha actual
            ->update(['estado' => 'finalizado']);

        // Filtra los clientes cuyo tipo de membresía es 'por_mes' y cuyo estado es 'finalizado'
        $clients = Client::where('estado', 'finalizado')
            ->where('tipo_membresia', 'por_mes') // Solo los clientes con membresía 'por_mes'
            ->get();

        // Retorna la vista con los clientes filtrados
        return view('renovaciones.index', compact('clients'));
    }

    /** Procesa la renovación enviada por el modal */
    public function renew(Request $request)
    {
        // 1) Validación
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id_cliente',
            'cantidad_meses' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
        ]);

        // 2) Busca el cliente usando el client_id
        $client = Client::find($data['client_id']);

        // 3) Calcula fechas e importe
        $start = Carbon::parse($data['fecha_inicio'])->startOfDay();
        $days = 30 * $data['cantidad_meses'];
        $end = (clone $start)->addDays($days);
        $importe = 50 * $data['cantidad_meses'];
        if ($client->residencia === 'nacional') {
            $importe *= 2;
        }

        // 4) Actualiza sólo los campos que sí existen en la tabla
        $client->update([
            'fecha_inicio_membresia' => $start,
            'fecha_fin_membresia' => $end,
            'importe_membresia' => $importe,
            'estado' => 'activo',
        ]);

        return redirect()
            ->route('renovaciones.index')
            ->with('success', 'Membresía renovada correctamente.');
    }
}
