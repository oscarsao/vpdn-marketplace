<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesInBusinessMultimediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_multimedia', function (Blueprint $table) {
            // Estas columnas son innecesarias
            $table->dropColumn(['name', 'real_path', 'real_full_path']);

            // Renombrando columna a un nombre más acorde
            $table->renameColumn('full_path', 'large_image_path');

            // Agregando columnas para soportar las nuevas resoluciones
            $table->string('medium_image_path')->nullable();
            $table->string('small_image_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_multimedia', function (Blueprint $table) {
            $table->dropColumn(['small_image_path', 'medium_image_path']);
            $table->renameColumn('large_image_path', 'full_path');
            $table->string('real_full_path')->nullable();
            $table->string('real_path')->nullable();
            $table->string('name')->nullable();
        });
    }
}
