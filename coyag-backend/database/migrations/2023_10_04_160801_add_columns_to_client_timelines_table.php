<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToClientTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_timelines', function (Blueprint $table) {
            // Mismos parámetros pero en inglés para facilitar los Queries
            $table->string('module_eng')->after('module');
            $table->string('type_crud_eng')->after('type_crud');
            // Servirá para guardar metadatos sobre el activity log
            $table->json('properties')->nullable()->after('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_timelines', function (Blueprint $table) {
            $table->dropColumn(['properties', 'type_crud_eng', 'module_eng']);
        });
    }
}
