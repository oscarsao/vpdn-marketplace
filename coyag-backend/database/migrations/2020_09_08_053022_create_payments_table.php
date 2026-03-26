<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {

            /* La diferencia con COYAG es que en este migrate se agrega approval_date
             * Antes estaba en otro migrate
             * */

            $table->id();

            $table->foreignId('client_id');    // Cliente al que pertenece el pago
            $table->foreignId('employee_id');  // Empleado que subió el pago
            $table->foreignId('file_id');  // Comprobante de pago

            $table->string("bank", 128);
            $table->string("no_transaction", 64);
            $table->float("amount", 12, 2);
            $table->string("observation")->nullable();
            $table->boolean("flag_approved")->nullable();

            // Empleado que aprobó el pago, No es clave foránea
            $table->unsignedBigInteger('employee_payment_id')->nullable();
            $table->timestamp('approval_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('file_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
