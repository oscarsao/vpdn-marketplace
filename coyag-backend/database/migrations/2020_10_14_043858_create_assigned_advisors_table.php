<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignedAdvisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigned_advisors', function (Blueprint $table) {

            /* De esta tabla no se genera relaciones lógicas en Laravel */

            $table->id();

            $table->foreignId('client_id');
            $table->foreignId('employee_id'); //Asesor Comercial asignado
            $table->foreignId('generator_employee_id'); // Quien asigna el asesor comercial al empleado

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('generator_employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assigned_advisors');
    }
}
