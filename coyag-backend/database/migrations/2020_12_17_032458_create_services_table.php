<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->boolean('flag_active')->default(true);
            $table->enum('type', ['Plan', 'AddOn']);
            //Posibles Rol que puede o debe tener el usuario, dependiendo el tipo de servicio
            //Si se deja vacío significa que el Plan o AddOn es para todos los usuarios
            $table->string('roles_slug')->nullable();
            //name_payment y slug_payment se da porque en futuras migraciones los pagos dependerán del servicio contratado
            //Y no habrá un tipo de pago
            $table->string('name_payment')->unique();
            $table->string('slug_payment')->unique();
            $table->string('recommended_price')->nullable();
            $table->boolean('flag_recurring_payment')->default(false); //Indica si el servicio acepta pago recurrente
            $table->boolean('flag_payment_in_installments')->default(false); //Indica si el servicio acepta pago por cuotas

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
        Schema::dropIfExists('services');
    }
}
