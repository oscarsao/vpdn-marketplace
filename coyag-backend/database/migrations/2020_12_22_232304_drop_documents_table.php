<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            Schema::dropIfExists('documents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('documents', function (Blueprint $table) {
            //Esta tabla es equivalente a documentos del sistema crm
            $table->id();
            $table->foreignId('document_type_id');
            $table->string('name', 128);
            $table->string('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_type_id')->references('id')->on('document_types');
        });
    }
}
