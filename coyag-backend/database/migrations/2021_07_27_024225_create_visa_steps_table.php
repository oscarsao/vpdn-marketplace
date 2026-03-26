<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visa_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id');
            $table->foreignId('added_service_id');

            $table->unsignedTinyInteger('number')->nullable();

            $table->string('name', 128);
            $table->text('client_description')->nullable();
            $table->text('advisor_description')->nullable();
            $table->enum('status', ['Sin Iniciar', 'En Proceso', 'Completado'])->default('Sin Iniciar');

            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('added_service_id')->references('id')->on('added_services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visa_steps');
    }
}
