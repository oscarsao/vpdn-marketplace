<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConvertedToStandardDateInClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
            Cambio con respecto a COYAG:

            - converted_to_standard_date

        */
        Schema::table('clients', function (Blueprint $table) {
            $table->timestamp('converted_to_standard_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('converted_to_standard_date');
        });
    }
}
