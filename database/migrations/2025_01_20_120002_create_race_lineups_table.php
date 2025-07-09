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
        Schema::create('race_lineups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_heat_id')->constrained()->onDelete('cascade');
            $table->foreignId('pilot_id')->constrained()->onDelete('cascade');
            $table->integer('gate_position'); // posición en el partidor (1-8)
            $table->integer('finish_position')->nullable(); // posición de llegada (1-8)
            $table->decimal('lap_time', 8, 3)->nullable(); // tiempo de vuelta en segundos
            $table->boolean('dnf')->default(false); // Did Not Finish
            $table->boolean('dsq')->default(false); // Disqualified
            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['race_heat_id', 'gate_position']);
            $table->unique(['race_heat_id', 'pilot_id'], 'unique_pilot_per_heat');
            $table->unique(['race_heat_id', 'gate_position'], 'unique_gate_per_heat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('race_lineups');
    }
};
