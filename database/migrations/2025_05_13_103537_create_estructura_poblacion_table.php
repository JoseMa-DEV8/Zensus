<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estructura_poblacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipio_id')->constrained()->onDelete('cascade');
            $table->year('anio');
            $table->string('grupo_edad');
            $table->enum('sexo', ['Hombre', 'Mujer', 'Total']);
            $table->integer('poblacion');
            $table->timestamps();

            $table->unique(['municipio_id', 'anio', 'grupo_edad', 'sexo'], 'uq_municipio_anio_edad_sexo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estructura_poblacion');
    }
};
