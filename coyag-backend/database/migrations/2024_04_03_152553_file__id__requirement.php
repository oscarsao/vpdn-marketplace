<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            // left to do
            $table->foreignId('employee_id')->nullable()->change();
            
            $table->foreignId('id_visa_requirement')->nullable();
            $table->foreign('id_visa_requirement')->references('id')->on('visa_requirements');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->foreignId('employee_id')->change();
            
            $table->dropColumn('id_visa_requirement');
        });
    }
};