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
        Schema::create('race_heats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_series_id')->constrained()->onDelete('cascade');
            $table->integer('heat_number'); // 1, 2, 3 (manga 1, 2, 3)
            $table->string('name'); // ej: "Manga 1", "Manga 2", "Manga 3"
            $table->datetime('scheduled_time')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['race_series_id', 'heat_number']);
            $table->unique(['race_series_id', 'heat_number'], 'unique_heat_per_series');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('race_heats');
    }
};
