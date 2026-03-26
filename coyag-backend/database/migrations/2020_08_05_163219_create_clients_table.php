<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {

            /*
                Cambios con respecto a COYAG:

                - converted_to_lite_date
                - converted_to_premium_date
                - phone_mobile->nullable()

            */

            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('first_nationality_id')->default(1);
            $table->unsignedBigInteger('second_nationality_id')->default(1);
            $table->string('names');
            $table->string('surnames');
            $table->string('phone_office', 20)->nullable();
            $table->string('phone_mobile', 20)->nullable();
            $table->string('landline', 20)->nullable();
            $table->unsignedBigInteger('country_id')->default(1);
            $table->date('birthdate')->nullable();
            $table->string('home_address')->nullable();
            $table->string('office_address')->nullable();
			$table->unsignedBigInteger('titulation_id')->default(1);
            $table->tinyInteger('working_years')->nullable();
            $table->tinyInteger('family_members')->nullable();
            $table->timestamp('converted_to_lite_date')->nullable();
            $table->timestamp('converted_to_premium_date')->nullable();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('first_nationality_id')->references('id')->on('nationalities');
            $table->foreign('second_nationality_id')->references('id')->on('nationalities');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('titulation_id')->references('id')->on('titulations');
            

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
