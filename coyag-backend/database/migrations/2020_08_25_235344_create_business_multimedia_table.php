<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessMultimediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_multimedia', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('business_id');

            $table->enum('type', ['print', 'image', 'video']);

            $table->string('link_video')->nullable();

            $table->string('original_name')->nullable();
            $table->string('name')->nullable();
            $table->string('extension')->nullable();
            $table->integer('size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('path')->nullable();
            $table->string('full_path')->nullable();
            $table->string('real_path')->nullable();
            $table->string('real_full_path')->nullable();


            $table->timestamps();

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
        Schema::dropIfExists('business_multimedia');
    }
}
