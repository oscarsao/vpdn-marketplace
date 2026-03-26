<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');

            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')->references('id')->on('areas');
            
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            
            $table->unsignedBigInteger('gestor_id')->nullable();
            $table->foreign('gestor_id')->references('id')->on('employees');

            // $table->enum('status', ['ToDo', 'Progress', 'Completed', 'Attention', 'Blocked', 'Template'])->default('ToDo');
            $table->string('status')->default('ToDo');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contract_steps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            
            $table->unsignedBigInteger('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts');

            $table->string('type')->default('Complete'); // Complete, Files, Date

            $table->string('status')->default('ToDo'); // ['ToDo', 'Client_review', 'Gestor_review', 'Complete']

            $table->text('revision');

            $table->unsignedBigInteger('order')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });


        Schema::table('files', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_steps_id')->nullable();
            $table->foreign('contract_steps_id')->references('id')->on('contract_steps');
        });

        /** 
         * I hate you :D
         * You reafirmed my hate for you
         */
        Schema::dropIfExists('busiests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('areas');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_steps');

        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign(['contract_steps_id']);
            $table->dropColumn('contract_steps_id');
        });

        Schema::create('busiests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id');
            $table->foreignId('business_id');
            $table->foreign('file_id')->references('id')->on('files');
            $table->foreign('business_id')->references('id')->on('businesses');
            $table->timestamps();
        });
    }
};
