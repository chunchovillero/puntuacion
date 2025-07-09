<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // create, update, delete
            $table->string('model_type'); // App\Models\Pilot, App\Models\Championship, etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID del modelo afectado
            $table->string('model_name')->nullable(); // Nombre/titulo del modelo para referencia
            $table->json('old_values')->nullable(); // Valores anteriores (para updates/deletes)
            $table->json('new_values')->nullable(); // Valores nuevos (para creates/updates)
            $table->text('description')->nullable(); // Descripción legible de la acción
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
