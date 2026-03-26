<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMondayGanttVpToAddedServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('added_services', function (Blueprint $table) {
            $table->string('monday_gantt_vp')->nullable();
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
            $table->dropColumn('monday_gantt_vp');
        });
    }
}
