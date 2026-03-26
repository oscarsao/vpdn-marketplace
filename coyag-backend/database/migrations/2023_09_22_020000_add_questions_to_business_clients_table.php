<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuestionsToBusinessClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_clients', function (Blueprint $table) {
            $table->string('prefered_operation')->nullable();
            $table->string('current_businesses')->nullable();
            $table->date('estimated_date')->nullable();
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
            $table->dropColumn('prefered_operation');
            $table->dropColumn('current_businesses');
            $table->dropColumn('estimated_date');
        });
    }
}
