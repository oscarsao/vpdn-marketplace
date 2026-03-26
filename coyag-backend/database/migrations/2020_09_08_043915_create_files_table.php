<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            //Con respecto a COYAG eliminé real_path y real_full_path
            $table->id();

            $table->foreignId('document_id');
            $table->foreignId('client_id');   // Cliente al que pertenece el archivo
            $table->foreignId('employee_id'); // Empleado que subió el archivo al sistema

            $table->string('original_name');
            $table->string('extension');
            $table->integer('size');
            $table->string('mime_type');
            $table->string('path');
            $table->string('full_path');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
