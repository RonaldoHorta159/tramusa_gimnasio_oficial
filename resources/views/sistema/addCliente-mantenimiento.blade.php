@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Agregar nuevos clientes</h1>
@stop

@section('content')
<p>Aquí podrás agregar nuevos clientes para el sistema</p>
<div class="card">
    <div class="card-body">
        <form action="{{ route('cliente.store') }}" method="POST">
            @csrf
            {{-- With prepend slot --}}
            <x-adminlte-input type="text" name="nombre_cliente" label="Nombre del cliente"
                placeholder="Ingrese el nombre del cliente nuevo" label-class="text-lightblue"
                value="{{ old('nombre_cliente') }}">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-user text-lightblue"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
            <x-adminlte-input type="number" name="dni_cliente" label="DNI del cliente"
                placeholder="Ingrese el dni del cliente nuevo" label-class="text-lightblue"
                value="{{ old('dni_cliente') }}">
                <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fa fa-id-card text-lightblue"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
            <x-adminlte-input-date name="fecha_nacimiento" label="Fecha de Nacimiento" placeholder="Seleccione la fecha"
                label-class="text-lightblue" :config="['format' => 'DD-MM-YYYY']" value="{{ old('fecha_nacimiento') }}">
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
            <x-adminlte-input-date name="fecha_inicio_membresia" label="Fecha de inicio de la membresía"
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
            <x-adminlte-button class="btn-flat mt-2 mb-3" type="submit" label="Guardar" theme="primary"
                icon="fas fa-lg fa-save" />
        </form>
    </div>
</div>

@stop

@section('css')
{{-- Add here extra stylesheets --}}
@stop
@section('js')
<script>
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
</script>
@stop