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
            $table->string('job_id');
            $table->foreignId('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');
        });
        Schema::table('collectors', function (Blueprint $table) {
            $table->string('job_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('job_id');
            $table->dropForeign('businesses_province_id_foreign');
            $table->dropColumn('province_id');
        });
        Schema::table('collectors', function (Blueprint $table) {
            $table->dropColumn('job_id');
        });
    }
};
