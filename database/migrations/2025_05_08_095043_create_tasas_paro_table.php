<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tasas_paro', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('provincia_id');
            $table->foreign('provincia_id')->references('id')->on('provincias_codificadas')->onDelete('cascade');
            $table->year('anio');
            $table->decimal('valor', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['provincia_id', 'anio']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasas_paro');
    }
};
?>