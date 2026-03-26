<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropFlagClientAndListOfVisaTypeInVisaDocumentTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visa_document_types', function (Blueprint $table) {
            $table->dropColumn(['flag_client', 'list_of_visa_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visa_document_types', function (Blueprint $table) {
            $table->boolean('flag_client')->default(false);
            $table->string('list_of_visa_type', 128)->nullable();
        });
    }
}
