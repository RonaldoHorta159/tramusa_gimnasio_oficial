@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Lista de todos los lockers </h1>
@stop

@section('content')



<div class="card">
    <div class="card-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#ModalLocker">Asignar Locker
            a cliente</button>
    </div>
    <div class="card-body">
        @php
            $heads = [
                ['label' => 'ID', 'width' => 5],
                ['label' => 'Numero de locker', 'width' => 10],
                ['label' => 'Estado', 'width' => 5],
                ['label' => 'Cliente', 'width' => 10],
                ['label' => 'Acciones', 'no-export' => true, 'width' => 5],
            ];

            $btnEdit = '<button class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit">
                                                                                                            <i class="fa fa-lg fa-fw fa-pen"></i>
                                                                                                            </button>';
            $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                                                                                            <i class="fa fa-lg fa-fw fa-trash"></i>
                                                                                                            </button>';
            $btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details">
                                                                                                            <i class="fa fa-lg fa-fw fa-eye"></i>
                                                                                                            </button>';
            $config = [
                'language' => ['url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'],
                // orden inicial (columna 1 = Número de locker)
                'order' => [[1, 'asc']],
                // decirle que columnas 0 (ID) y 1 (Número) son numéricas:
                'columnDefs' => [
                    ['targets' => [0, 1], 'type' => 'num']
                ],
            ];
        @endphp

        {{-- Minimal example / fill data using the component slot --}}
        <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
            @foreach($lockers as $locker)
                <tr>
                    <td>{{ $locker->id_locker }}</td>
                    <td>{{$locker->numero_locker}}</td>
                    <td>{{$locker->estado_locker}}</td>
                    <td>{{ $locker->cliente->nombre_cliente ?? 'No asignado' }}</td>
                    <td>
                        <form action="{{ route('locker.destroy', $locker->id_locker) }}" method="POST"
                            class="formEliminar d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow"
                                title="Liberar Locker">
                                <i class="fa fa-lg fa-fw fa-unlock-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </x-adminlte-datatable>
    </div>
    <!-- Modal clientes -->
    <div class="modal" id="ModalLocker">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Asignar Locker a Cliente</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('locker.store') }}" method="POST">
                    @csrf
                    <div class="card-body">


                        <!-- Selección de Cliente -->
                        <x-adminlte-select name="id_cliente" label="Seleccione cliente" label-class="text-lightblue"
                            required>
                            <x-slot name="prependSlot">
                                <div class="input-group-text bg-gradient-info">
                                    <i class="fas fa-user"></i>
                                </div>
                            </x-slot>
                            <option value="">-- Selecciona un cliente --</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}">{{ $cliente->nombre_cliente }}</option>
                            @endforeach
                        </x-adminlte-select>

                        <!-- Selección del Locker -->
                        <x-adminlte-select name="id_locker" label="Seleccione el locker" label-class="text-lightblue"
                            required>
                            <x-slot name="prependSlot">
                                <div class="input-group-text bg-gradient-info">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            </x-slot>
                            <option value="">-- Selecciona un locker --</option>
                            @foreach($lockers as $locker)
                                @if(strpos(strtolower($locker->estado_locker), 'ocupado') === false)
                                    <option value="{{ $locker->id_locker }}" data-numero-locker="{{ $locker->numero_locker }}">
                                        {{ $locker->numero_locker }}
                                    </option>
                                @endif
                            @endforeach
                        </x-adminlte-select>

                        <!-- Campo para la fecha de inicio -->
                        <x-adminlte-input-date name="fecha_inicio_locker" autocomplete="off" label="Fecha de inicio"
                            label-class="text-lightblue" :config="['format' => 'DD-MM-YYYY']"
                            value="{{ old('fecha_inicio_locker') }}" required />

                        <!-- Campo para la cantidad de meses -->
                        <x-adminlte-input type="number" name="cantidad_meses" label="Cantidad de meses"
                            label-class="text-lightblue" value="{{ old('cantidad_meses') }}" required />

                        <!-- Campo para la fecha de fin calculada -->
                        <x-adminlte-input-date name="fecha_fin_locker" autocomplete="off" label="Fecha de fin"
                            label-class="text-lightblue" :config="['format' => 'DD-MM-YYYY']"
                            value="{{ old('fecha_fin_locker') }}" readonly />

                        <x-adminlte-button class="btn btn-block btn-primary" type="submit" label="Guardar"
                            theme="primary" icon="fas fa-lg fa-save" />
                    </div>
                </form>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@stop

@section('css')
{{-- Add here extra stylesheets --}}
{{--
<link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lockerSelect = document.querySelector('select[name="id_locker"]');
        const numeroLockerInput = document.querySelector('input[name="numero_locker"]');

        if (lockerSelect && numeroLockerInput) {
            lockerSelect.addEventListener('change', function () {
                const selectedOption = lockerSelect.options[lockerSelect.selectedIndex];
                const numeroLocker = selectedOption.getAttribute('data-numero-locker');
                numeroLockerInput.value = numeroLocker || '';
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fechaInicioInput = document.querySelector('input[name="fecha_inicio_locker"]');
        const cantidadMesesInput = document.querySelector('input[name="cantidad_meses"]');
        const fechaFinInput = document.querySelector('input[name="fecha_fin_locker"]');

        if (fechaInicioInput && cantidadMesesInput && fechaFinInput) {
            cantidadMesesInput.addEventListener('input', function () {
                calcularFechaFin();
            });

            fechaInicioInput.addEventListener('change', function () {
                calcularFechaFin();
            });
        }

        function calcularFechaFin() {
            const fechaInicio = fechaInicioInput.value;
            const cantidadMeses = parseInt(cantidadMesesInput.value) || 0;

            if (fechaInicio && cantidadMeses > 0) {
                const fechaInicioDate = new Date(fechaInicio.split('-').reverse().join('-')); // Convertir formato DD-MM-YYYY a YYYY-MM-DD
                fechaInicioDate.setMonth(fechaInicioDate.getMonth() + cantidadMeses); // Añadir meses

                // Formatear la fecha final como DD-MM-YYYY
                const dia = String(fechaInicioDate.getDate()).padStart(2, '0');
                const mes = String(fechaInicioDate.getMonth() + 1).padStart(2, '0');
                const anio = fechaInicioDate.getFullYear();
                fechaFinInput.value = `${dia}-${mes}-${anio}`;
            }
        }
    });

    $(document).ready(function () {
        $('.formEliminar').submit(function (e) {
            e.preventDefault();  // evitamos el envío inmediato
            const form = this;
            Swal.fire({
                title: "¿Liberar locker?",
                text: "El locker quedará libre y sin cliente asignado.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, liberar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();   // aquí sí mandamos el DELETE
                }
            });
        });
    });
</script>
@stop