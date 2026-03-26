<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visa_requirements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('visa_document_type_id');
            $table->foreignId('added_service_id');
            $table->foreignId('client_id');
            $table->foreignId('family_id')->nullable();
            $table->foreignId('file_id')->nullable();

            $table->date('expiration_date')->nullable();
            $table->enum('status', ['Sin Cargar', 'Cargado', 'Aceptado', 'Rechazado'])->default('Sin Cargar');

            $table->string('passport_number', 64)->nullable();
            $table->date('application_date')->nullable(); //Momento en que se solicitó el documento (Partida o acta)
            $table->date('date_of_issue')->nullable(); // Fecha de emisión

            $table->timestamps();

            $table->foreign('visa_document_type_id')->references('id')->on('visa_document_types');
            $table->foreign('added_service_id')->references('id')->on('added_services');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('family_id')->references('id')->on('families');
            $table->foreign('file_id')->references('id')->on('files');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_visa_requirements');
    }
}
