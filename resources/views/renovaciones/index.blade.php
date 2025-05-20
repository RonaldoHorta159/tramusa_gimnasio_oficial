@extends('adminlte::page')

@section('title', 'Renovaciones')

@section('content_header')
<h1>Renovaciones de Membresía</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <p>Clientes con membresía expirada (por mes):</p>

        @php
            $heads = [
                'ID',
                'Nombre',
                'DNI',
                'Fin Membresía',
                ['label' => 'Acciones', 'no-export' => true]
            ];
            $config = [
                'responsive' => true,
                'lengthChange' => false,
                'autoWidth' => false,
            ];
        @endphp

        <x-adminlte-datatable id="renovaciones-table" :heads="$heads" :config="$config" striped hoverable>
            @foreach($clients as $c)
                <tr>
                    <td>{{ $c->id_cliente }}</td>
                    <td>{{ $c->nombre_cliente }}</td>
                    <td>{{ $c->dni_cliente }}</td>
                    <td>{{ \Carbon\Carbon::parse($c->fecha_fin_membresia)->format('d/m/Y') }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#renewModal"
                            data-id="{{ $c->id_cliente }}" data-name="{{ $c->nombre_cliente }}">
                            Renovar
                        </button>
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>

<!-- Modal de Renovación -->
<div class="modal fade" id="renewModal" tabindex="-1" role="dialog" aria-labelledby="renewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form id="renewForm" method="POST" action="{{ route('renovaciones.renew') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renewModalLabel">Renovar Membresía</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="client_id" id="modal_client_id">

                    <div class="form-group">
                        <label>Cliente</label>
                        <input type="text" id="modal_client_name" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="cantidad_meses">Cantidad de Meses</label>
                        <input type="number" name="cantidad_meses" id="cantidad_meses" class="form-control" min="1"
                            value="1" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control"
                            value="{{ \Carbon\Carbon::now()->toDateString() }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Renovar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    // Al abrir el modal, cargamos datos del cliente
    $('#renewModal').on('show.bs.modal', function (e) {
        var btn = $(e.relatedTarget);
        var id = btn.data('id');
        var name = btn.data('name');
        $('#modal_client_id').val(id);
        $('#modal_client_name').val(name);
        // Opcional: fijar fecha_inicio en hoy
        $('#fecha_inicio').val(new Date().toISOString().substr(0, 10));
    });

</script>
@stop