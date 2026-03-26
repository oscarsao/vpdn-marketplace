<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisaDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visa_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->unique();
            $table->boolean('flag_client')->default(false); // Indica si por defecto el documento es obligatorio para el cliente
            $table->string('list_of_visa_type', 128); // Lista de Ids de tipos de Visa
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visa_document_types');
    }
}
