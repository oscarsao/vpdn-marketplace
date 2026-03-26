<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddNewStatusInVideoCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_calls', function (Blueprint $table) {
                DB::statement("ALTER TABLE video_calls MODIFY COLUMN status ENUM('Por atender', 'Atendida', 'Cancelada', 'No atendida', 'Agendada') DEFAULT 'Por atender'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_calls', function (Blueprint $table) {
            //Realmente no afectaría el funcionamiento
            //DB::statement("ALTER TABLE video_calls MODIFY COLUMN status ENUM('Por atender', 'Atendida', 'Cancelada', 'No atendida')");
        });
    }
}
