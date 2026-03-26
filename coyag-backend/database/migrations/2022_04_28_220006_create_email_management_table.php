<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_management', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id');

            $table->foreignId('employee_id')->default(1);

            $table->enum('type', ['welcome']);

            $table->boolean('is_send')->default(false);

            $table->dateTime('sending_date')->nullable();

            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');

            $table->foreign('employee_id')->references('id')->on('employees');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_management');
    }
}
