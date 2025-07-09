<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRutToPilotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pilots', function (Blueprint $table) {
            $table->string('rut', 12)->unique()->after('last_name')->comment('RUT chileno sin puntos y con guión');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pilots', function (Blueprint $table) {
            $table->dropUnique(['rut']);
            $table->dropColumn('rut');
        });
    }
}
