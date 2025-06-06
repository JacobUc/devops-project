<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id('id_driver');
            $table->string('name'); 
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('curp');
            $table->string('address');
            $table->decimal('monthly_salary');
            $table->string('license_number');
            $table->date('system_entry_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
