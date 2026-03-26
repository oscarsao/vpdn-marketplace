<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        Schema::table('collectors', function (Blueprint $table) {
            $table->string('collector_id')->nullable()->change();
            $table->string('owner')->nullable()->change();
            $table->text('inputs')->nullable()->change();
        });
        Schema::table('smartlinks', function (Blueprint $table) {
            $table->string('owner')->nullable()->change();
            $table->text('businesses')->nullable()->change();
        });
    }
};
