<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewFieldsToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {

            $table->string('working_hours')->nullable();
            $table->integer('full_time_employees')->nullable();
            $table->integer('employees_part_time')->nullable();
            $table->integer('managers')->nullable();
            $table->integer('gross_revenue')->nullable();
            $table->integer('gross_profit')->nullable();
            $table->integer('expenses')->nullable();
            $table->integer('net')->nullable();
            $table->integer('owner_salary')->nullable();

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
            $table->dropColumn(['working_hours',
                                'full_time_employees',
                                'employees_part_time',
                                'managers',
                                'gross_revenue',
                                'gross_profit',
                                'expenses',
                                'net',
                                'owner_salary']);
        });
    }
}
