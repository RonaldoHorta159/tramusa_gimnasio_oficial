@extends('adminlte::page')
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('title', 'Reportes de Clientes')

@section('content_header')
<h1>Reportes de Clientes</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form action="{{ route('reportes.clientes.filter') }}" method="POST" class="form-inline mb-4">
            @csrf
            <div class="form-group mr-2">
                <label for="desde" class="mr-1">Desde</label>
                <input type="date" name="desde" id="desde"
                    value="{{ old('desde', isset($desde) ? $desde->toDateString() : '') }}"
                    class="form-control @error('desde') is-invalid @enderror">
                @error('desde') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group mr-2">
                <label for="hasta" class="mr-1">Hasta</label>
                <input type="date" name="hasta" id="hasta"
                    value="{{ old('hasta', isset($hasta) ? $hasta->toDateString() : '') }}"
                    class="form-control @error('hasta') is-invalid @enderror">
                @error('hasta') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-filter"></i> Generar
            </button>
            @if(isset($clientes))
                <a href="{{ route('reportes.clientes.index') }}" class="btn btn-secondary ml-2">
                    <i class="fa fa-times"></i> Limpiar
                </a>
            @endif
        </form>
    </div>

    <div class="card-body">
        @isset($clientes)
            <p>Mostrando {{ $clientes->count() }} clientes de {{ $desde->format('d/m/Y') }} a {{ $hasta->format('d/m/Y') }}.
            </p>

            @php
                $config = [

                    'responsive' => true,
                    'lengthChange' => false,
                    'autoWidth' => false,
                ];
            @endphp

            <x-adminlte-datatable id="table-report" :heads="[
                'ID',
                'Nombre',
                'DNI',
                'Inicio Membresía',
                'Fin Membresía',
                'Estado',
                'Importe membresia',
            ]" :config="$config" striped hoverable with-buttons>
                @foreach($clientes as $c)
                    <tr>
                        <td>{{ $c->id_cliente }}</td>
                        <td>{{ $c->nombre_cliente }}</td>
                        <td>{{ $c->dni_cliente }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->fecha_inicio_membresia)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($c->fecha_fin_membresia)->format('d/m/Y') }}</td>
                        <td>{{ $c->estado }}</td>
                        <td>{{ $c->importe_membresia }}</td>
                    </tr>
                @endforeach
            </x-adminlte-datatable>
        @endisset
    </div>

</div>
@stop
@section('js')
<script>
    $(function () {
        $('#clientes-table').DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            dom: '<"row"<"col-sm-6"B><"col-sm-6"f>>rtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
        })
            .buttons()
            .container()
            .appendTo('#clientes-table_wrapper .col-sm-6:eq(0)');
    });
</script>
@stop