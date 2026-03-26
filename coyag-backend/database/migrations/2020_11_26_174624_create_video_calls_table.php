<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_calls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('video_call_type_id');

            //Quien hace la solictud de la videollamada
            $table->foreignId('client_id');

            //Si la videollamada aún no se ha efectuado, entonces es a quien se le asignó
            //Si la videollamada ya se efectuó entonces fue quien la respondió
            $table->foreignId('employee_id');

            //Sobre el negocio o los negocios del cual se hablarán
            $table->string('list_of_business')->nullable();

            $table->enum('status', ['Por atender', 'Atendida', 'Cancelada', 'No atendida'])->default('Por atender');

            //Disponibilidad del cliente para realizar la videollamada
            $table->string('client_availability');

            //Fecha y hora final de la videollamada
            $table->dateTime('date_and_time')->nullable();

            //Reporte que completa el asesor o quien haya atendido la videollamada
            $table->text('report')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('video_call_type_id')->references('id')->on('video_call_types');
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
        Schema::dropIfExists('video_calls');
    }
}
