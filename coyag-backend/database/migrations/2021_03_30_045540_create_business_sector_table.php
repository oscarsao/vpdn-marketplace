<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessSectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_sector', function (Blueprint $table) {
            /* Tabla relación entre las tablas businesses & sectors*/
            $table->id();

            $table->foreignId('business_id');
            $table->foreignId('sector_id');

            $table->timestamps();
            //No puede haber softdelete porque al borrar va a quedar la clave compuesta

            $table->foreign('business_id')->references('id')->on('businesses');
            $table->foreign('sector_id')->references('id')->on('sectors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_sector');
    }
}
