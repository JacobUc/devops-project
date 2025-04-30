<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id('id_driver');
            $table->string('name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('curp')->unique();
            $table->string('address');
            $table->decimal('monthly_salary', 10, 2);
            $table->string('license_number')->unique();
            $table->date('system_entry_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};

