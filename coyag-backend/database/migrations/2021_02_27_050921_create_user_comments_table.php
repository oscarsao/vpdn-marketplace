<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_comment_type_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('employee_id');

            $table->string('comment');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_comment_type_id')->references('id')->on('user_comment_types');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_comments');
    }
}
