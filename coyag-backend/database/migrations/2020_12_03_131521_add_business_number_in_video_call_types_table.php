<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessNumberInVideoCallTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_call_types', function (Blueprint $table) {
            //Indica cuantos negocios (De cuantos negocios se puede hablar) puede tener el tipo de videollamada
            $table->enum('business_number', ['0', '1', 'max'])->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_call_types', function (Blueprint $table) {
            $table->dropColumn('business_number');
        });
    }
}
