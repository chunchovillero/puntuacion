<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matchdays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('championship_id');
            $table->integer('number'); // Número de jornada (1, 2, 3, etc.)
            $table->string('name', 100)->nullable(); // Ej: "Jornada 1", "Final", etc.
            $table->date('date'); // Fecha de la jornada
            $table->time('start_time')->nullable(); // Hora de inicio
            $table->time('end_time')->nullable(); // Hora de fin
            $table->string('venue', 200); // Pista donde se realizará
            $table->string('address', 300)->nullable(); // Dirección de la pista
            
            // Organizador puede ser un club o AMBMX
            $table->unsignedBigInteger('organizer_club_id')->nullable(); // Si es organizado por un club
            $table->string('organizer_name', 100)->nullable(); // Si es organizado por AMBMX u otra organización
            $table->string('organizer_contact', 100)->nullable(); // Contacto del organizador
            $table->string('organizer_phone', 20)->nullable(); // Teléfono del organizador
            
            $table->text('description')->nullable(); // Descripción de la jornada
            $table->json('categories')->nullable(); // Categorías que participan en JSON
            $table->decimal('entry_fee', 8, 2)->nullable(); // Cuota de inscripción específica
            $table->text('requirements')->nullable(); // Requisitos especiales
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->text('results')->nullable(); // Resultados en texto o JSON
            $table->timestamps();
            
            $table->foreign('championship_id')->references('id')->on('championships')->onDelete('cascade');
            $table->foreign('organizer_club_id')->references('id')->on('clubs')->onDelete('set null');
            
            $table->unique(['championship_id', 'number']); // Números únicos por campeonato
            $table->index(['championship_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matchdays');
    }
}
