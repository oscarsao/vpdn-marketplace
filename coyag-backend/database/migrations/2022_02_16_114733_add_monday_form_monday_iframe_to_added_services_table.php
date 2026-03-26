<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMondayFormMondayIframeToAddedServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('added_services', function (Blueprint $table) {
            $table->string('monday_form')->nullable();
            $table->string('monday_iframe')->nullable();
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
            $table->dropColumn(['monday_form', 'monday_iframe']);
        });
    }
}
