<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumbersInVisaStepTypeVisaTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visa_step_type_visa_type', function (Blueprint $table) {
            $table->unsignedTinyInteger('number_client')->nullable();
            $table->unsignedTinyInteger('number_advisor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visa_step_type_visa_type', function (Blueprint $table) {
            $table->dropColumn(['number_advisor', 'number_client']);
        });
    }
}
