<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeClientColumnToBusinessMultimediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_multimedia', function (Blueprint $table) {
            $table->string('type_client', 32)->nullable()->default('all');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_multimedia', function (Blueprint $table) {
            $table->dropColumn('type_client');
        });
    }
}
