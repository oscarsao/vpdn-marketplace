<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTypeColumnAndAddServiceIdInClientRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_requests', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->foreignId('service_id');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_requests', function (Blueprint $table) {
            $table->enum('type', ['lite plan', 'standard plan', 'premium plan', 'financial analysis - lite plan', 'pack 1 financial analysis - standard plan', 'pack 3 financial analysis - standard plan', 'pack 5 financial analysis - standard plan']);
            $table->dropForeign('client_requests_service_id_foreign');
            $table->dropColumn('service_id');
        });
    }
}
