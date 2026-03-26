<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable();
            $table->string('original_profile_image')->nullable(); // Imagen de tamaño original
            $table->string('thumbnail_profile_image')->nullable(); // Imagen reducida, pero visible
            $table->string('avatar_profile_image')->nullable(); // Imagen muy pequeña que va en el área de notificaciones
            $table->boolean('flag_login')->default(false); // Indica si el usuario se puede autenticar
            $table->string('observation_flag_login')->default('No se permite la autenticación porque el Usuario es nuevo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['observation_flag_login', 'flag_login', 'avatar_profile_image', 'thumbnail_profile_image', 'original_profile_image', 'username']);
        });
    }
}
