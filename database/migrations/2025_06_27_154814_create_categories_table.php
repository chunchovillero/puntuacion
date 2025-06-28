<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Ej: "Escuela Varones 6-"
            $table->string('type', 50); // escuela, novicios
            $table->string('gender', 20)->nullable(); // varones, mujeres, null para novicios
            $table->integer('age_min')->nullable(); // Edad mínima
            $table->integer('age_max')->nullable(); // Edad máxima (null para "6-")
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'gender']);
            $table->index(['age_min', 'age_max']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
