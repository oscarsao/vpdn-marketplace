<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddedServicePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('added_service_payment', function (Blueprint $table) {
            /* Tabla relación fentre AddedService y Payment*/
            $table->id();

            $table->foreignId('added_service_id');
            $table->foreignId('payment_id');

            $table->timestamps();

            $table->foreign('added_service_id')->references('id')->on('added_services');
            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('added_service_payment', function (Blueprint $table) {
            Schema::dropIfExists('added_service_payment');
        });
    }
}
