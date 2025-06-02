<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rentas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('municipio_id');
            $table->unsignedTinyInteger('provincia_id'); // <- correcto
            $table->year('anyo');
            $table->float('valor');
            $table->string('tipo');
            $table->timestamps();

            $table->foreign('municipio_id')
                ->references('id')
                ->on('municipios')
                ->onDelete('cascade');

            $table->foreign('provincia_id')
                ->references('id')
                ->on('provincias_codificadas')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rentas');
    }
};
