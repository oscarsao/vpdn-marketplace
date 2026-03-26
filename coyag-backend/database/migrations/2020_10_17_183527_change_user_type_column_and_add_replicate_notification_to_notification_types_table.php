<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeUserTypeColumnAndAddReplicateNotificationToNotificationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_types', function (Blueprint $table) {
            //Agregando adviser
            DB::statement("ALTER TABLE notification_types MODIFY COLUMN user_type ENUM('adviser', 'client', 'employee')");

            //Si el tipo de notificación es para un asesor,
            //aquí colocará el slug de notificación para que le llegue a los jefes
            $table->string('replicate_notification', 64)->nullable();
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
            $table->dropColumn('replicate_notification');
            DB::statement("ALTER TABLE notification_types MODIFY COLUMN user_type ENUM('adviser', 'client', 'employee')");
        });
    }
}
