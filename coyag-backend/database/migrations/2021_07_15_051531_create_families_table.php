<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id');
            $table->foreignId('family_type_id');
            $table->foreignId('first_nationality_id')->default(1);
            $table->foreignId('second_nationality_id')->default(1);

            $table->string('names');
            $table->string('surnames');
            $table->string('email')->nullable()->unique();
            $table->string('phone_office', 20)->nullable();
            $table->string('phone_mobile', 20)->nullable();
            $table->string('landline', 20)->nullable();
            $table->date('birthdate')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('family_type_id')->references('id')->on('family_types');
            $table->foreign('first_nationality_id')->references('id')->on('countries');
            $table->foreign('second_nationality_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('families');
    }
}
