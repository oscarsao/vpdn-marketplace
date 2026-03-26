<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSizePhonesToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('phone_office', 30)->nullable()->change();
            $table->string('phone_mobile', 30)->nullable()->change();
            $table->string('landline', 30)->nullable()->change();
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
            $table->string('landline', 20)->nullable()->change();
            $table->string('phone_mobile', 20)->nullable()->change();
            $table->string('phone_office', 20)->nullable()->change();
        });
    }
}
