<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsInFinancialAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_analyses', function (Blueprint $table) {
            $table->dropForeign('financial_analyses_payment_id_foreign');
            $table->dropColumn('payment_id');
            $table->dropColumn('available');
            $table->foreignId('added_service_id');
            $table->foreign('added_service_id')->references('id')->on('added_services');
            $table->renameColumn('status', 'flag_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_analyses', function (Blueprint $table) {
            $table->renameColumn('flag_active', 'status');
            $table->dropForeign('financial_analyses_added_service_id_foreign');
            $table->dropColumn('added_service_id');
            $table->smallInteger('available')->nullable(); // -1 indicaría que son ilimitados
            $table->foreignId('payment_id');
            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }
}
