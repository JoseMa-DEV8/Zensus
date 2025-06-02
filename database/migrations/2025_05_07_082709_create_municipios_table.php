<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->id();

            // 👇 Clave foránea correctamente definida
            $table->unsignedTinyInteger('provincia_id');
            $table->foreign('provincia_id')->references('id')->on('provincias_codificadas')->onDelete('cascade');

            $table->string('cop', 2); // Código provincia (ej: '01')
            $table->string('com', 3); // Código municipio (ej: '003')
            $table->string('nombre');

            $table->timestamps();
            $table->unique(['cop', 'com']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('municipios');
    }
};
