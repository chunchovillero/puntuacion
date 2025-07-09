<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChampionshipRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('championship_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('championship_id')->constrained()->onDelete('cascade');
            $table->foreignId('pilot_id')->constrained()->onDelete('cascade');
            $table->string('bib_number', 10); // Dorsal del piloto en este campeonato
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('registration_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Un piloto no puede registrarse dos veces en el mismo campeonato
            $table->unique(['championship_id', 'pilot_id']);
            // Un dorsal no se puede repetir en el mismo campeonato
            $table->unique(['championship_id', 'bib_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('championship_registrations');
    }
}
