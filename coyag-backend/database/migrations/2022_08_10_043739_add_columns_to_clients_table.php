<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Is subscribed to business preference email?
            $table->boolean('is_subscribed_biz_pref')->default(false);
            $table->string('biz_pref_unsubscribe_token')->nullable()->default(null);
            $table->string('reason_biz_pref_unsubscribe')->nullable()->default('Debe ser activado por el Cliente');

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
            $table->dropColumn(['is_subscribed_biz_pref', 'biz_pref_unsubscribe_token', 'reason_biz_pref_unsubscribe']);
        });
    }
}
