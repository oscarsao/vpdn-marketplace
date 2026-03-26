<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsInPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            //Este borrado de columnas se da porque se eliminará lo de comprobación de pago
            //Ya que esto se está manejando de forma "manual"
            $table->dropColumn('flag_approved');
            $table->dropColumn('employee_payment_id');
            $table->dropColumn('approval_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean("flag_approved")->nullable();
            $table->unsignedBigInteger('employee_payment_id')->nullable();
            $table->timestamp('approval_date')->nullable();
        });
    }
}
