<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id');
            $table->foreignId('employee_id')->nullable(); // Empleado que "Atenderá la solicitud"

            $table->enum('type', ['lite plan', 'standard plan', 'premium plan', 'financial analysis - lite plan', 'pack 1 financial analysis - standard plan', 'pack 3 financial analysis - standard plan', 'pack 5 financial analysis - standard plan', 'video call']);
            $table->boolean('flag_attended')->nullable()->default(false);
            $table->timestamp('attended_at')->nullable();
            $table->string('observation')->nullable();

            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('client_requests');
    }
}
