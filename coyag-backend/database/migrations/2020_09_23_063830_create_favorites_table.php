<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            /* Tabla relación favoritos (businesses & clients*/

            $table->id();

            $table->foreignId('client_id');
            $table->foreignId('business_id');

            $table->timestamps();
            //No puede haber softdelete porque al borrar va a quedar la clave compuesta

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('business_id')->references('id')->on('businesses');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}
