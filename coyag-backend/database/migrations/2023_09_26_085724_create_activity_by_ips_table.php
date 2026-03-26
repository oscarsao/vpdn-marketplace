<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityByIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_by_ips', function (Blueprint $table) {
            $table->id();

            $table->ipAddress('ip');
            $table->enum('activity', ['create_client']);
            $table->enum('status', ['complete', 'incomplete'])->default('incomplete');
            $table->foreignId('user_id')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_by_ips');
    }
}
