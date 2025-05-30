<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('id_assignment');
            $table->date('assignment_date');
            $table->unsignedBigInteger('id_driver');
            $table->unsignedBigInteger('id_vehicle');
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_driver')->references('id_driver')->on('drivers')->onDelete('cascade');
            $table->foreign('id_vehicle')->references('id_vehicle')->on('vehicles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
