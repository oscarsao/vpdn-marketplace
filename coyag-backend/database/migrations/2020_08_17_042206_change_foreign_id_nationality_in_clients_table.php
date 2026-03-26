<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeignIdNationalityInClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('clients_first_nationality_id_foreign');
            $table->dropForeign('clients_second_nationality_id_foreign');
            $table->foreign('first_nationality_id')->references('id')->on('countries');
            $table->foreign('second_nationality_id')->references('id')->on('countries');
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
            $table->dropForeign('clients_second_nationality_id_foreign');
            $table->dropForeign('clients_first_nationality_id_foreign');
            /*
            Se comenta porque en este punto la tabla nationalities NO contiene datos
            Por ende, dará un error de integridad
            $table->foreign('second_nationality_id')->references('id')->on('nationalities');
            $table->foreign('first_nationality_id')->references('id')->on('nationalities');
            */
        });
    }
}
