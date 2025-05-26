@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>LISTA DE CLIENTES ACTIVOS</h1>
@stop

@section('content')
<p>Aqui veras la lista de todos los clientes</p>
<div class="card">
    <div class="card-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#ModalCliente">
            Agregar cliente nuevo
        </button>
    </div>
    <div class="card-body">
        @php
            $heads = [
                ['label' => 'ID', 'width' => 5],
                ['label' => 'Nombre', 'width' => 10],
                ['label' => 'Residencia', 'width' => 10],
                ['label' => 'Estado', 'width' => 5],
                ['label' => 'Acciones', 'width' => 10],
            ];


            $btnEdit = '';
            /*$btnDelete = '<button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                                                                                <i class="fa fa-lg fa-fw fa-trash"></i>
                                                                                                </button>';
            $btnDetails = '<button class="btn btn-xs btn-default text-teal mx-1 shadow" data-toggle="modal" data-target="#ModalDetalles" title="Details">
                                                                                                <i class="fa fa-lg fa-fw fa-eye"></i>
                                                                                                </button>';*/
            $config = [
                'language' => [
                    'url' => '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                ],
                'columns' => [
                    null,    // ID
                    null,    // Nombre
                    null,    // Residencia
                    null,    // Estado
                    ['orderable' => false, 'searchable' => false],  // Acciones
                ],
            ];
        @endphp

        {{-- resources/views/sistema/listCliente.blade.php --}}

        {{-- Incluye SweetAlert2 en tu layout principal, si no lo tienes ya --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- Minimal example / fill data using the component slot --}}
        <x-adminlte-datatable id="table1" :heads="$heads" :config="$config">
            @foreach($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->id_cliente }}</td>
                    <td>{{ $cliente->nombre_cliente }}</td>
                    <td>{{ $cliente->residencia }}</td>
                    <td>{{ $cliente->estado }}</td>
                    <td class="d-flex">
                        <!-- Botón Editar -->
                        <button class="btn btn-warning btn-sm mr-2" data-toggle="modal" data-target="#ModalEditar"
                            data-id="{{ $cliente->id_cliente }}" data-nombre="{{ $cliente->nombre_cliente }}"
                            data-fecha_nacimiento="{{ \Carbon\Carbon::parse($cliente->fecha_nacimiento)->format('d-m-Y') }}">
                            <i class="fas fa-edit"></i> Editar
                        </button>

                        <!-- Botón Detalles -->
                        <button class="btn btn-info btn-sm mr-2" data-toggle="modal"
                            data-target="#ModalDetalles-{{ $cliente->id_cliente }}">
                            <i class="fas fa-eye"></i> Detalles
                        </button>

                        <!-- Botón Eliminar -->
                        <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $cliente->id_cliente }}">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>

                        <!-- Formulario oculto para DELETE -->
                        <form id="form-delete-{{ $cliente->id_cliente }}"
                            action="{{ route('cliente.destroy', $cliente->id_cliente) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>

                <!-- Modal Detalles específico para este cliente -->
                <div class="modal fade" id="ModalDetalles-{{ $cliente->id_cliente }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-body">
                                {{-- Este es el widget AdminLTE nativo --}}
                                <x-adminlte-profile-widget name="{{ $cliente->nombre_cliente }}"
                                    desc="{{ Str::title(str_replace('_', ' ', $cliente->tipo_membresia)) }}" theme="teal"
                                    img="https://picsum.photos/id/{{ $cliente->id_cliente }}/100">

                                    <x-adminlte-profile-col-item title="ID" text="{{ $cliente->id_cliente }}" url="#" />
                                    <x-adminlte-profile-col-item title="DNI" text="{{ $cliente->dni_cliente }}" url="#" />
                                    <x-adminlte-profile-col-item title="Nacimiento" text="{{ $cliente->fecha_nacimiento }}"
                                        url="#" />
                                    <x-adminlte-profile-col-item title="Residencia" text="{{ $cliente->residencia }}"
                                        url="#" />
                                    <x-adminlte-profile-col-item title="Tipo Membresía"
                                        text="{{ Str::title(str_replace('_', ' ', $cliente->tipo_membresia)) }}" url="#" />
                                    <x-adminlte-profile-col-item title="Inicio Membresía"
                                        text="{{ $cliente->fecha_inicio_membresia }}" url="#" />
                                    <x-adminlte-profile-col-item title="Fin Membresía"
                                        text="{{ $cliente->fecha_fin_membresia }}" url="#" />
                                    <x-adminlte-profile-col-item title="Importe (S/)"
                                        text="{{ $cliente->importe_membresia }}" url="#" />
                                    <x-adminlte-profile-col-item title="Estado" text="{{ ucfirst($cliente->estado) }}"
                                        url="#" />

                                </x-adminlte-profile-widget>
                            </div>

                            <div class="modal-footer justify-content-end">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                    Cerrar
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </x-adminlte-datatable>
    </div>
</div>
<!-- Único Modal Detalles -->
<div class="modal fade" id="ModalDetalles" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body">
                <x-adminlte-profile-widget id="perfilWidget" name="" {{-- se rellenará en JS --}} desc="" theme="teal"
                    img="https://via.placeholder.com/100">

                    {{-- Cada item con su propio id para inyectar texto --}}
                    <x-adminlte-profile-col-item title="ID" id="item_id" text="" />
                    <x-adminlte-profile-col-item title="DNI" id="item_dni" text="" />
                    <x-adminlte-profile-col-item title="Nacimiento" id="item_fecha_nac" text="" />
                    <x-adminlte-profile-col-item title="Residencia" id="item_residencia" text="" />
                    <x-adminlte-profile-col-item title="Tipo Membresía" id="item_tipo" text="" />
                    <x-adminlte-profile-col-item title="Inicio Membresía" id="item_inicio" text="" />
                    <x-adminlte-profile-col-item title="Fin Membresía" id="item_fin" text="" />
                    <x-adminlte-profile-col-item title="Importe (S/)" id="item_importe" text="" />
                    <x-adminlte-profile-col-item title="Estado" id="item_estado" text="" />
                    <x-adminlte-profile-col-item title="Creado" id="item_created_at" text="" />
                    <x-adminlte-profile-col-item title="Actualizado" id="item_updated_at" text="" />

                </x-adminlte-profile-widget>
            </div>

            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal Editar -->
<!-- Modal Editar -->
<div class="modal fade" id="ModalEditar" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditar" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">Editar Cliente</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_cliente" id="edit_id">

                    <x-adminlte-input id="edit_nombre" name="nombre_cliente" label="Nombre"
                        placeholder="Nombre del cliente" />

                    <x-adminlte-input-date id="edit_fecha_nacimiento" name="fecha_nacimiento"
                        label="Fecha de Nacimiento" placeholder="DD-MM-YYYY" :config="['format' => 'DD-MM-YYYY']" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal clientes -->
<div class="modal" id="ModalCliente">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Agregar los datos del nuevo cliente</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form role="form" action="{{ route('cliente.store') }}" method="POST">
                    @csrf
                    {{-- With prepend slot --}}
                    <x-adminlte-input type="number" name="dni_cliente" label="DNI del cliente"
                        placeholder="Ingrese el dni del cliente nuevo" label-class="text-lightblue"
                        value="{{ old('dni_cliente') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text" onclick="Get_dni()">
                                <i class="fa fa-search text-lightblue icon-hover"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    <x-adminlte-input type="text" id="nombre_cliente" name="nombre_cliente" label="Nombre del cliente"
                        placeholder="Ingrese el nombre del cliente nuevo" label-class="text-lightblue"
                        value="{{ old('nombre_cliente') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-user text-lightblue"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    <x-adminlte-input-date name="fecha_nacimiento" autocomplete="off" label="Fecha de Nacimiento"
                        placeholder="Seleccione la fecha" label-class="text-lightblue" :config="['format' => 'DD-MM-YYYY']" value="{{ old('fecha_nacimiento') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text ">
                                <i class="fa fa-calendar-alt text-lightblue"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                    {{-- With prepend slot, lg size, and label --}}
                    <x-adminlte-select name="residencia" label="Residencia" label-class="text-lightblue"
                        value="{{ old('residencia') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradien ">
                                <i class="fa fa-home text-lightblue"></i>
                            </div>
                        </x-slot>
                        <option value="residente"> Residente</option>
                        <option value="nacional"> Nacional</option>
                    </x-adminlte-select>
                    {{-- With prepend slot, lg size, and label --}}
                    <x-adminlte-select id="tipo_membresia" name="tipo_membresia" label="Tipo de membresia"
                        label-class="text-lightblue" value="{{ old('tipo_membresia') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradien ">
                                <i class="fas fa-gem text-lightblue"></i>
                            </div>
                        </x-slot>
                        <option value="por_dia"> Por día</option>
                        <option value="por_mes"> Por meses</option>
                    </x-adminlte-select>
                    <div id="cantidad_meses_container" style="display: none;">
                        <x-adminlte-input type="number" name="cantidad_meses" label="Cantidad de meses"
                            placeholder="Ingrese la cantidad de meses para la membresía" label-class="text-lightblue"
                            value="{{ old('cantidad_meses') }}">
                            <x-slot name="prependSlot">
                                <div class="input-group-text">
                                    <i class="fa fa-hashtag text-lightblue"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input>
                    </div>
                    <x-adminlte-input-date name="fecha_inicio_membresia" autocomplete="off"
                        label="Fecha de inicio de la membresía"
                        placeholder="Seleccione la fecha de inicio de la membresía" label-class="text-lightblue"
                        :config="['format' => 'DD-MM-YYYY']" value="{{ old('fecha_inicio_membresia') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text ">
                                <i class="fa fa-calendar-alt text-lightblue"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                    <x-adminlte-input-date name="fecha_fin_membresia" label="Fecha de finalización de la membresía"
                        placeholder="Seleccione la fecha de fin de la membresía" label-class="text-lightblue"
                        :config="['format' => 'DD-MM-YYYY']" value="{{ old('fecha_fin_membresia') }}">
                        <x-slot name="prependSlot">
                            <div class="input-group-text ">
                                <i class="fa fa-calendar-alt text-lightblue"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input-date>
                    <x-adminlte-input type="number" name="importe_membresia" label="Importe de la membresía (soles)"
                        placeholder="Aquí se verá el importe de la membresía" label-class="text-lightblue"
                        value="{{ old('importe_membresia') }}" disabled>
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fa fa-id-card text-lightblue"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                    <x-adminlte-button class="btn btn-block btn-primary" type="submit" label="Guardar" theme="primary"
                        icon="fas fa-lg fa-save" />
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

@stop

@push('css')
    <style>
        .icon-hover {
            transition: transform 0.2s ease-in-out;
        }

        .icon-hover:hover {
            transform: scale(1.2);
        }

        /* 1) Definimos los keyframes */
        @keyframes zoomIn {
            0% {
                transform: scale(0.7);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes zoomOut {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            100% {
                transform: scale(0.7);
                opacity: 0;
            }
        }

        /* 2) Aplicamos la animación al .modal-dialog */
        .modal.fade .modal-dialog {
            animation-duration: 0.3s;
            animation-fill-mode: both;
            /* para que el origen del zoom sea el centro */
            transform-origin: center center;
        }

        /* 3) Cuando aparece (tiene .show), uso zoomIn */
        .modal.fade.show .modal-dialog {
            animation-name: zoomIn;
        }

        /* 4) Cuando desaparece (pierde .show), uso zoomOut */
        .modal.fade:not(.show) .modal-dialog {
            animation-name: zoomOut;
        }
    </style>
@endpush

@section('js')
<script>
    async function Get_dni() {
        var dni = document.getElementById("dni_cliente").value;
        if (dni.length < 8) {
            alert("El DNI debe tener al menos 8 dígitos.");
            return;
        }
        var nombre = document.getElementById("nombre_cliente"); // Cambiado para obtener el elemento DOM
        const respuesta = await fetch("/api/validar_dni?dni=" + dni, {
            method: "GET",
        });
        const data = await respuesta.json();
        nombre.value = data.data.nombreCompleto; // Asignar el valor al campo
    }
    $(document).ready(function () {
        function toggleCantidadMeses() {
            if ($('#tipo_membresia').val() === 'por_mes') {
                $('#cantidad_meses_container').show();
            } else {
                $('#cantidad_meses_container').hide();
            }
        }

        function calcularImporte() {
            var tipoMembresia = $('#tipo_membresia').val();
            var tipoResidencia = $('#residencia').val();
            var cantidadMeses = parseInt($('#cantidad_meses').val()) || 1;
            var importeBase = 0;

            if (tipoMembresia === 'por_dia') {
                importeBase = 10;
            } else if (tipoMembresia === 'por_mes') {
                importeBase = 50 * cantidadMeses;
            }

            if (tipoMembresia === 'por_mes' && tipoResidencia === 'nacional') {
                importeBase *= 2;
            }

            $('#importe_membresia').val(importeBase);
        }

        function formatDate(date) {
            var day = String(date.getDate()).padStart(2, '0');
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var year = date.getFullYear();
            return day + '-' + month + '-' + year;
        }

        function calcularFechaCulminacion() {
            var tipoMembresia = $('#tipo_membresia').val();
            var fechaInicio = $('#fecha_inicio_membresia').val();
            var cantidadMeses = parseInt($('#cantidad_meses').val()) || 0;

            if (!fechaInicio) {
                $('#fecha_fin_membresia').val('');
                return;
            }

            var fechaInicioParts = fechaInicio.split('-');
            var fechaInicioDate = new Date(fechaInicioParts[2], fechaInicioParts[1] - 1, fechaInicioParts[0]);

            if (tipoMembresia === 'por_dia') {
                // Mismo día que la fecha de inicio
                $('#fecha_fin_membresia').val(fechaInicio);
            } else if (tipoMembresia === 'por_mes') {
                // Calcular fecha de culminación: inicio + (31 días * cantidad de meses)
                var diasTotales = 30 * cantidadMeses;
                var fechaCulminacion = new Date(fechaInicioDate);
                fechaCulminacion.setDate(fechaInicioDate.getDate() + diasTotales);
                // Formatear la fecha a DD-MM-YYYY
                var formattedDate = formatDate(fechaCulminacion);
                $('#fecha_fin_membresia').val(formattedDate);
            }
        }

        // Vincular eventos
        $('#tipo_membresia').on('change', function () {
            toggleCantidadMeses();
            calcularImporte();
            calcularFechaCulminacion();
        });
        $('#cantidad_meses').on('input', function () {
            calcularImporte();
            calcularFechaCulminacion();
        });
        $('#fecha_inicio_membresia').on('change.datetimepicker', calcularFechaCulminacion);
        $('#residencia').on('change', calcularImporte);

        // Ejecutar al cargar la página
        toggleCantidadMeses();
        calcularImporte();
        calcularFechaCulminacion();
    });
    $('form').on('submit', function () {
        $('#importe_membresia').prop('disabled', false);
    });

    $('#ModalEditar').on('show.bs.modal', function (event) {
        const btn = $(event.relatedTarget);
        const modal = $(this);

        const id = btn.data('id');
        const nombre = btn.data('nombre');
        const fecha = btn.data('fecha_nacimiento');

        modal.find('#edit_id').val(id);
        modal.find('#edit_nombre').val(nombre);
        modal.find('#edit_fecha_nacimiento').val(fecha);

        // Ajustar la acción del form
        const baseUrl = "{{ url('client') }}";
        modal.find('#formEditar').attr('action', `${baseUrl}/${id}`);
    });


    $('#formEditar').submit(function () {
        Swal.fire({
            title: "¡Éxito!",
            icon: "success",
            draggable: true
        });
        // y aquí no evitas el envío, el formulario se envía normalmente
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción no se puede deshacer!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`form-delete-${id}`).submit();
                    }
                });
            });
        });
    });


</script>
@stop