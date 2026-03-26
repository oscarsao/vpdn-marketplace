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
            $table->string('garage')->nullable();
            $table->string('storage')->nullable();
            $table->string('pool')->nullable();
            $table->string('rooms')->nullable();
            $table->string('bathrooms')->nullable();
            $table->string('floors')->nullable();
            $table->string('floor')->nullable();
            $table->string('year_built')->nullable();
            $table->string('courtyard')->nullable();
            $table->string('elevator')->nullable();
            $table->string('new_or_used')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['garage', 'storage', 'pool', 'rooms', 'bathrooms', 'floors', 'floor', 'year_built', 'courtyard', 'elevator', 'new_or_used']);
        });
    }
};
