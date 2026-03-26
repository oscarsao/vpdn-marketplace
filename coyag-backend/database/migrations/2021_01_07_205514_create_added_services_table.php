<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddedServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('added_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id');
            $table->foreignId('service_id');

            $table->boolean('flag_active_plan')->nullable(); // Solo para los planes e inmigration, No AddOn
            $table->timestamp('plan_deactivated_at')->nullable(); // Solo para los planes e inmigration, No AddOn

            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('added_services');
    }
}
