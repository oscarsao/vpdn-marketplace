<?php

use App\Business;
use App\Sector;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSectorIdInBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {

            $businesses = DB::table('businesses')->get();
            $businesses = collect($businesses)->map(function($x){ return (array)$x; })->toArray();

            foreach($businesses as $business)
            {
                //Esta doble consulta se debe porque en este punto sector_id es desconocido para el Modelo Business
                $b = Business::find($business['id']);
                $b->sector()->attach($business['sector_id']);
            }

            $table->dropForeign('businesses_sector_id_foreign');
            $table->dropColumn('sector_id');
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
            // Sin embargo, si hay un retorno la data se habríá perdido
            $table->foreignId('sector_id')->default(1);
            $table->foreign('sector_id')->references('id')->on('sectors');
        });
    }
}
