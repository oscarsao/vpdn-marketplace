<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('second_name')->nullable();
            $table->string('surname');
            $table->string('second_surname')->nullable();
            $table->string('identification_card')->nullable();
            $table->string('civil_status')->nullable();
            $table->date('birthdate')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('address')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('corporate_email')->nullable();
            $table->string('nif', 30)->nullable();
            $table->string('fiscal_address')->nullable();
            $table->string('job_title')->nullable();
            $table->decimal('salary', 8, 2)->nullable();
			$table->unsignedBigInteger('headquarters_id')->default(1);
            $table->string('bank_account', 30)->nullable();
            $table->string('mobile_phone', 20);
			$table->string('landline', 20)->nullable();
            $table->string('corporate_mobile_phone', 20)->nullable();
			$table->string('corporate_local_phone', 20)->nullable();
            $table->string('emergency_contact')->nullable();
			$table->string('emergency_phone', 20)->nullable();
            $table->string('blood_type', 10)->nullable();
			$table->unsignedBigInteger('department_id')->default(1);
            $table->string('color')->nullable();
            $table->boolean('flag_permission')->default(false); //Indica si el empleado está de permiso - reposo - vacaciones
            $table->string('observation_flag_permission')->nullable();            

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('headquarters_id')->references('id')->on('headquarters');
            $table->foreign('department_id')->references('id')->on('departments');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
