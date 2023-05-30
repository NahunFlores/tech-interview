<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion',255)->nullable(false);
            $table->string('prioridad',80)->nullable(false);
            $table->date('fecha_limite')->nullable(false);
            $table->unsignedBigInteger('responsable')->nullable();
            $table->foreign('responsable')->references('id')->on('users');
            $table->enum('estado',['Pendiente',
                                    'En progreso',
                                    'Completada',
                                    'En revisión',
                                    'Aplazada',
                                    'Bloqueada',
                                    'Cancelada',
                                    'Rechazada'])->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tareas');
    }
}
