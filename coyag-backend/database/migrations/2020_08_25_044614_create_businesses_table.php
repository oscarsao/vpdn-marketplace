<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('business_type_id');
            $table->unsignedBigInteger('sector_id');
            $table->unsignedBigInteger('employee_id'); // Empleado que creó el negocio

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('zip_code', 12)->nullable();
            $table->integer('size')->nullable(); // mts cuadrados
            $table->smallInteger('age')->nullable(); // antigüedad/años
            $table->string('landline', 30)->nullable();
            $table->string('mobile_phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('link')->nullable();
            $table->text('private_comment')->nullable();
            $table->text('data_of_interest')->nullable(); // Comentarios públicos
            $table->text('relevant_advantages')->nullable(); //ventajas resaltantes/comparativas
            $table->integer('monthly_billing')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_landline')->nullable();
            $table->string('contact_mobile_phone', 30)->nullable();
            $table->string('contact_email')->nullable();
            $table->integer('amount_requested_by_seller')->nullable();
            $table->integer('amount_offered_by_us')->nullable();
            $table->integer('investment')->nullable();
            $table->string('royalty')->nullable(); // Puede indicar monto fijo o %, periodo de tiempo, entre otros
            $table->integer('rental')->nullable();
            $table->string('contract')->nullable();
            $table->string('minimum_population')->nullable();
            $table->string('canon_of_advertising')->nullable(); // Puede indicar monto fijo o %, periodo de tiempo, entre otros
            $table->string('canon_of_entrance')->nullable(); // Monto que cobra la franquicia

            $table->boolean('flag_exclusive')->default(false);
            $table->boolean('flag_active')->default(true); //Indica si el inmueble se lista o no, en vistas públicas
            $table->boolean('flag_sold')->default(false); // Indica si el status está vendido
            $table->boolean('flag_outstanding')->default(false); // Indica si es destacado

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('business_type_id')->references('id')->on('business_types');
            $table->foreign('sector_id')->references('id')->on('sectors');
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('businesses');
    }
}
