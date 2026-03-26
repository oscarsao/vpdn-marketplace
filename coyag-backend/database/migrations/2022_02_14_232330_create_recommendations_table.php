<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommendations', function (Blueprint $table) {
            /* Recomendaciones de Negocios a Clientes */
            $table->id();

            $table->foreignId('client_id');

            $table->foreignId('business_id');

            $table->date('finished_at'); // Fin de la recomendación para este cliente

            $table->timestamps();

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
        Schema::dropIfExists('recommendations');
    }
}
