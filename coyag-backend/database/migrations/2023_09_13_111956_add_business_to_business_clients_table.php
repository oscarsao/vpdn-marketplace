<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessToBusinessClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_clients', function (Blueprint $table) {
            // Guarda los IDs de los negocios con los cuales hace match
            $table->json('business_list')->nullable()->after('business_types_list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_clients', function (Blueprint $table) {
            $table->dropColumn('business_list');
        });
    }
}
