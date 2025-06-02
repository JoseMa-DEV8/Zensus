<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('demografias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('municipio_id')->constrained('municipios')->onDelete('cascade');
            $table->enum('sexo', ['hombres', 'mujeres', 'total']);
            $table->enum('tipo', ['total', 'españoles', 'extranjeros']);
            $table->year('anio');
            $table->unsignedBigInteger('valor')->nullable();

            $table->timestamps();

            $table->unique(['municipio_id', 'sexo', 'tipo', 'anio']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('demografias');
    }
};
?>