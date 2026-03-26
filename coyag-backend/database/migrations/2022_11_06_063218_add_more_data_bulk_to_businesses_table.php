<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreDataBulkToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            // ID dentro de la Plataforma de Origen
            $table->unsignedBigInteger('id_business_platform')->after('bad_month_revenue')->nullable();
            $table->float('price_per_sqm', 12, 2)->nullable(); // Precio por metro cuadrado rent/size
            $table->string('advertiser_owner_type', 64)->nullable(); // Indica si el dueño es particular, Prefesional...
            $table->string('facade_size')->nullable(); // Tamaño de la fachada
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['id_business_platform', 'price_per_sqm', 'advertiser_owner_type', 'facade_size']);
        });
    }
}
