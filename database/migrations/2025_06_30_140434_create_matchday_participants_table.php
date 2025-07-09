<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchdayParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matchday_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matchday_id');
            $table->unsignedBigInteger('pilot_id');
            $table->unsignedBigInteger('category_id');
            $table->string('registration_number')->nullable(); // Número de dorsal
            $table->decimal('entry_fee_paid', 8, 2)->nullable(); // Cuota pagada
            $table->enum('status', ['registered', 'confirmed', 'cancelled', 'no_show'])->default('registered');
            $table->text('notes')->nullable(); // Notas del organizador
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamps();

            // Claves foráneas
            $table->foreign('matchday_id')->references('id')->on('matchdays')->onDelete('cascade');
            $table->foreign('pilot_id')->references('id')->on('pilots')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // Un piloto no puede inscribirse dos veces en la misma jornada
            $table->unique(['matchday_id', 'pilot_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matchday_participants');
    }
}
