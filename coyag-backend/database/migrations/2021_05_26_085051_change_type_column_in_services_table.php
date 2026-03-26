<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeTypeColumnInServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Agregando inmigration
            DB::statement("ALTER TABLE services MODIFY COLUMN type ENUM('Plan', 'AddOn', 'Inmigration')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            //No retorno el cambio porque puede haber servicios del tipo Inmigration. Y cambiar el valor de type no encaja en las otras opciones
        });
    }
}
