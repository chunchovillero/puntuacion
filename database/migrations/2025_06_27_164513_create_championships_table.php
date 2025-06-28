<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChampionshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('championships', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // Ej: "Campeonato Metropolitano"
            $table->integer('year'); // Año del campeonato (2024, 2025, etc.)
            $table->text('description')->nullable();
            $table->date('start_date')->nullable(); // Fecha de inicio del campeonato
            $table->date('end_date')->nullable(); // Fecha de fin del campeonato
            $table->integer('total_matchdays')->default(0); // Total de jornadas programadas
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');
            $table->json('rules')->nullable(); // Reglas del campeonato en JSON
            $table->decimal('entry_fee', 8, 2)->nullable(); // Cuota de inscripción
            $table->text('prizes')->nullable(); // Información de premios
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->unique(['name', 'year']); // Un solo campeonato por nombre y año
            $table->index(['year', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('championships');
    }
}
