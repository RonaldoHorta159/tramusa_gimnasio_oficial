<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id('id_cliente'); // Define el id como la clave primaria
            $table->string('nombre_cliente', 255)->nullable();
            $table->integer('dni_cliente')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('residencia', 255)->nullable();
            $table->string('tipo_membresia', 255)->nullable();
            $table->date('fecha_inicio_membresia')->nullable();
            $table->date('fecha_fin_membresia')->nullable();
            $table->integer('importe_membresia')->nullable();
            $table->timestamps(); // Agrega las columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
