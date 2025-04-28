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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_assignment');
            $table->string('name');
            $table->date('route_date');
            $table->boolean('was_successful')->nullable();
            $table->text('problem_description')->nullable();
            $table->text('comments')->nullable();
            $table->double('start_latitude', 10, 6);
            $table->double('start_longitude', 10, 6);
            $table->double('end_latitude', 10, 6);
            $table->double('end_longitude', 10, 6);            
            $table->timestamps();

            //Laves foráneas
            $table->foreign('id_assignment')->references('id_assignment')->on('assignments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
