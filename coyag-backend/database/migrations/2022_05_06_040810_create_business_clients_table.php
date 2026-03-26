<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_clients', function (Blueprint $table) {
            $table->id();


            $table->foreignId('client_id');


            $table->string('provinces_list')->nullable();

            $table->integer('min_investment')->nullable();

            $table->integer('max_investment')->nullable();

            $table->string('sectors_list')->nullable();

            $table->string('business_types_list')->nullable();


            $table->timestamps();


            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_clients');
    }
}
