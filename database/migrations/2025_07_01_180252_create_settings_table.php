<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 191)->unique();
            $table->text('value')->nullable();
            $table->string('type', 50)->default('text'); // text, boolean, integer, float, json, select, textarea
            $table->string('description', 500)->nullable();
            $table->string('group', 100)->default('general'); // general, system, email, notifications, etc.
            $table->json('options')->nullable(); // Para campos select, opciones adicionales
            $table->timestamps();
            
            $table->index(['group']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
