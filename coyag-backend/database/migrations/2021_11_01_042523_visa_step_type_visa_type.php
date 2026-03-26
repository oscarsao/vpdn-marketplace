<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VisaStepTypeVisaType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visa_step_type_visa_type', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visa_step_type_id');
            $table->foreignId('visa_type_id');

            $table->timestamps();
            //No hay sofdelete para no dejar restros de clave compuesta

            $table->foreign('visa_step_type_id')->references('id')->on('visa_step_types');
            $table->foreign('visa_type_id')->references('id')->on('visa_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visa_step_types_visa_type');
    }
}
