<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provincias_codificadas', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary(); // 1-52
            $table->string('codigo', 2)->unique();         // '01', '02'...
            $table->string('nombre');
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
        Schema::dropIfExists('provincias_codificadas');
    }
};
