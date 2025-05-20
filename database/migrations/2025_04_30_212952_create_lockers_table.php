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
        Schema::create('lockers', function (Blueprint $table) {
            $table->id('id_locker'); // Define el id como la clave primaria
            $table->unsignedBigInteger('id_cliente')->nullable(); // Asegura que el campo sea del tipo correcto
            $table->foreign('id_cliente')->references('id_cliente')->on('clients')->onDelete('cascade'); // Relación con la tabla clients
            $table->integer('numero_locker')->nullable();
            $table->string('estado_locker', 255)->nullable();
            $table->date('fecha_inicio_locker')->nullable();
            $table->date('fecha_fin_locker')->nullable();
            $table->integer('precio_locker')->nullable();
            $table->timestamps(); // Agrega las columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lockers');
    }
};
