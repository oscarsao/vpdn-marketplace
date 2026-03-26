<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('businesses', function (Blueprint $table) {
            $table->integer('profit_sale')->unsigned()->nullable();
            $table->integer('profit_rent')->unsigned()->nullable();
            $table->integer('reform_price')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['profit_sale', 'profit_rent', 'reform_price']);
        });
    }
};
