<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_vehicles_table.php
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('id_vehicle');
            $table->string('brand'); // marca
            $table->string('model'); // modelo
            $table->string('vin')->unique(); // vin
            $table->string('plate_number')->unique(); // placa
            $table->date('purchase_date'); // fecha_compra
            $table->decimal('cost', 10, 2); // costo
            $table->string('photo'); // foto
            $table->timestamp('registration_date'); // fecha_registro
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
