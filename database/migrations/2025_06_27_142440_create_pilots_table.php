<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePilotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pilots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nickname')->nullable();
            $table->text('description')->nullable();
            $table->date('birth_date');
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('bike_brand')->nullable();
            $table->string('bike_model')->nullable();
            $table->year('bike_year')->nullable();
            $table->enum('experience_level', ['beginner', 'intermediate', 'advanced', 'expert', 'professional'])->default('beginner');
            $table->json('specialties')->nullable(); // ["race", "freestyle", "dirt", "park", "street"]
            $table->json('achievements')->nullable();
            $table->date('joined_club_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'injured', 'suspended'])->default('active');
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->string('blood_type')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_number')->nullable();
            $table->json('social_media')->nullable(); // {"instagram": "@pilot", "facebook": "..."}
            $table->integer('ranking_points')->default(0);
            $table->timestamps();
            
            // Índices
            $table->index(['club_id', 'status']);
            $table->index(['experience_level']);
            $table->index(['ranking_points']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pilots');
    }
}
