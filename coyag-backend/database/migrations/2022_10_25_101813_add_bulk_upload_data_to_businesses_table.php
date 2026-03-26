<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBulkUploadDataToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('source_platform', 100)->nullable()->default('Manual'); //Manual, Idealista, Milanuncios...
            $table->string('source_url')->nullable(); // URL dentro de la plataforma
            $table->string('source_timestamp', 100)->nullable(); // Columna timestamp dentro del archivo de excel (fecha extracción de data)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['source_platform', 'source_url', 'source_timestamp']);
        });
    }
}
