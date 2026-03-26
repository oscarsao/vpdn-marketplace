<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_analyses', function (Blueprint $table) {
            $table->id();

            // Payment: Contiene el ID del Cliente, el Empleado que lo cargó y el empleado que lo aprobó
            $table->foreignId('payment_id')->nullable();

            $table->smallInteger('available')->nullable(); // -1 indicaría que son ilimitados
            $table->smallInteger('accomplished')->default(0);

            // Puede ser que el usuario deje de ser premium o ya se realizaron todos sus análisis
            $table->boolean('status')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_analyses');
    }
}
