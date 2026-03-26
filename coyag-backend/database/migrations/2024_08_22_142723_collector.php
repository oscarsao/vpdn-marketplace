<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('collector_id')->nullable();
        });
        Schema::create('collectors', function (Blueprint $table) {
            $table->id();
            $table->string('collector_id');
            $table->string('owner');
            $table->text('inputs');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('smartlinks', function (Blueprint $table) {
            $table->id();
            $table->string('owner');
            $table->integer('views')->nullable();
            $table->text('businesses');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('collector_id');
        });
        Schema::dropIfExists('collectors');
        Schema::dropIfExists('smartlinks');
    }
};
