<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusiestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('busiests', function (Blueprint $table) {
            /** Imagen para horarios concurridos */
            $table->id();

            $table->foreignId('business_id');
            $table->foreignId('file_id');

            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses');
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
        Schema::dropIfExists('busiests');
    }
}
