<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewDataInVisaStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visa_steps', function (Blueprint $table) {
            $table->unsignedTinyInteger('number_client')->nullable();
            $table->unsignedTinyInteger('number_advisor')->nullable();
            $table->dateTime('date_completed')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visa_steps', function (Blueprint $table) {
            //
        });
    }
}
