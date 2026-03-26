<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_timelines', function (Blueprint $table) {
            //ClientTimeline solo almacena evento SOBRE el usuario
            $table->id();

            $table->foreignId('client_id');
            $table->foreignId('employee_id');

            $table->string('module'); //Indica que módulo se vio afectado, ejemplo: Documentos, Familia
            $table->string('type_crud'); //Indica si es create, store, update o delete
            $table->string('message')->nullable();

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
        Schema::dropIfExists('client_timelines');
    }
}
