<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DropBusinessIdAndVideoCallInClientRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_requests', function (Blueprint $table) {
            $table->dropColumn('business_id');
            DB::statement("ALTER TABLE client_requests MODIFY COLUMN type ENUM('lite plan', 'standard plan', 'premium plan', 'financial analysis - lite plan', 'pack 1 financial analysis - standard plan', 'pack 3 financial analysis - standard plan', 'pack 5 financial analysis - standard plan')");
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
            $table->unsignedBigInteger('business_id')->nullable();
            DB::statement("ALTER TABLE client_requests MODIFY COLUMN type ENUM('lite plan', 'standard plan', 'premium plan', 'financial analysis - lite plan', 'pack 1 financial analysis - standard plan', 'pack 3 financial analysis - standard plan', 'pack 5 financial analysis - standard plan', 'video call')");
        });
    }
}
