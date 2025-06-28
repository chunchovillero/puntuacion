<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToPilotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pilots', function (Blueprint $table) {
            // Agregar columna category_id con clave foránea
            $table->unsignedBigInteger('category_id')->nullable()->after('gender');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            
            // Eliminar columna experience_level (después de migrar datos si es necesario)
            $table->dropColumn('experience_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pilots', function (Blueprint $table) {
            // Remover clave foránea y columna
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            
            // Recrear columna experience_level
            $table->enum('experience_level', ['principiante', 'intermedio', 'avanzado'])->default('principiante');
        });
    }
}
