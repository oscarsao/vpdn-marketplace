<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VisaDocumentTypeVisaType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visa_document_type_visa_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_document_type_id');
            $table->foreignId('visa_type_id');

            $table->timestamps();
            //No puede haber softdelete porque al borrar va a quedar la clave compuesta

            $table->foreign('visa_document_type_id')->references('id')->on('visa_document_types');
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
        Schema::dropIfExists('visa_document_type_visa_type');
    }
}
