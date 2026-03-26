<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTitleAndMessageColumnsInNotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_types', function (Blueprint $table) {
            $table->string('title', 192)->change();
            $table->string('message')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_types', function (Blueprint $table) {
            /*
            Lo dejo comentado porque puede romperse ya que puede tener un string mayor al que se está cambiando

            $table->string('message', 192)->change();
            $table->string('title', 96)->change();
            */
        });
    }
}
