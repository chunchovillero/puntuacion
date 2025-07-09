<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('race_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matchday_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // ej: "Serie A", "Serie B", etc.
            $table->integer('series_number'); // 1, 2, 3, etc. para ordenar
            $table->integer('max_pilots')->default(8); // máximo 8 pilotos por serie
            $table->integer('transfer_to_final')->default(0); // cuántos avanzan directo a final
            $table->integer('transfer_to_semifinal')->default(0); // cuántos avanzan a semifinal
            $table->integer('transfer_to_quarterfinal')->default(0); // cuántos avanzan a cuartos
            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['matchday_id', 'category_id']);
            $table->unique(['matchday_id', 'category_id', 'series_number'], 'unique_series_per_matchday_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('race_series');
    }
};
