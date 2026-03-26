<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeTypeToEmailManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_management', function (Blueprint $table) {
            DB::statement("ALTER TABLE email_management MODIFY COLUMN type ENUM('welcome', 'plan_expiration_prenotification', 'plan_expiration_notification')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_management', function (Blueprint $table) {
            DB::statement("ALTER TABLE email_management MODIFY COLUMN type ENUM('welcome')");
        });
    }
}
