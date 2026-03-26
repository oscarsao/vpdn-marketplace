<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddsColumnsToBusiness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->smallInteger('rental_contract_years')->nullable();
            $table->smallInteger('franchise_contract_years')->nullable();
            $table->smallInteger('rental_contract_years_left')->nullable();
            $table->smallInteger('franchise_contract_years_left')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['rental_contract_years', 'franchise_contract_years', 'rental_contract_years_left', 'franchise_contract_years_left']);
        });
    }
}
