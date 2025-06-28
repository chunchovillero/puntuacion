<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdatePilotsExperienceLevelEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Delete any existing pilots with problematic data
        DB::statement("DELETE FROM pilots WHERE experience_level NOT IN ('beginner', 'intermediate', 'advanced', 'expert', 'professional')");
        
        // Convert existing English values to Spanish
        DB::statement("UPDATE pilots SET experience_level = 'principiante' WHERE experience_level = 'beginner'");
        DB::statement("UPDATE pilots SET experience_level = 'intermedio' WHERE experience_level = 'intermediate'");
        DB::statement("UPDATE pilots SET experience_level = 'avanzado' WHERE experience_level = 'advanced'");
        DB::statement("UPDATE pilots SET experience_level = 'profesional' WHERE experience_level IN ('expert', 'professional')");
        
        // Then alter the column to use Spanish enum values
        DB::statement("ALTER TABLE pilots MODIFY COLUMN experience_level ENUM('principiante', 'intermedio', 'avanzado', 'profesional') DEFAULT 'principiante'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to English values
        DB::statement("UPDATE pilots SET experience_level = 'beginner' WHERE experience_level = 'principiante'");
        DB::statement("UPDATE pilots SET experience_level = 'intermediate' WHERE experience_level = 'intermedio'");
        DB::statement("UPDATE pilots SET experience_level = 'advanced' WHERE experience_level = 'avanzado'");
        DB::statement("UPDATE pilots SET experience_level = 'professional' WHERE experience_level = 'profesional'");
        
        DB::statement("ALTER TABLE pilots MODIFY COLUMN experience_level ENUM('beginner', 'intermediate', 'advanced', 'expert', 'professional') DEFAULT 'beginner'");
    }
}
