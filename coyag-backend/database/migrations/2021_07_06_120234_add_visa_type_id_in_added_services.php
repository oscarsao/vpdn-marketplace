<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisaTypeIdInAddedServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('added_services', function (Blueprint $table) {
            $table->foreignId('visa_type_id')->nullable();

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
        Schema::table('added_services', function (Blueprint $table) {
            $table->dropForeign('added_services_visa_type_id_foreign');
            $table->dropColumn('visa_type_id');
        });
    }
}
