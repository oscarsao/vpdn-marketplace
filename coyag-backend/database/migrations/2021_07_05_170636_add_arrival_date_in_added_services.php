<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArrivalDateInAddedServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('added_services', function (Blueprint $table) {
            // Este es un valor que por ahora solo será usado con el Plan Inmigration y Primera residencia
            $table->date('arrival_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('added_services', function (Blueprint $table) {
            $table->dropColumn('arrival_date');
        });
    }
}
