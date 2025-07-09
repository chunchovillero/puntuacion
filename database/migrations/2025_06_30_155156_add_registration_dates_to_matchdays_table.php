<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationDatesToMatchdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('matchdays', function (Blueprint $table) {
            $table->datetime('registration_start_date')->nullable()->after('description');
            $table->datetime('registration_end_date')->nullable()->after('registration_start_date');
            $table->boolean('public_registration_enabled')->default(true)->after('registration_end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('matchdays', function (Blueprint $table) {
            $table->dropColumn(['registration_start_date', 'registration_end_date', 'public_registration_enabled']);
        });
    }
}
