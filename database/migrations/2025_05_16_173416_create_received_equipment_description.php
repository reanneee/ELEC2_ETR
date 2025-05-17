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
        Schema::create('received_equipment_description', function (Blueprint $table) {
            $table->id('description_id'); // Primary Key
            $table->unsignedBigInteger('equipment_id'); // Foreign Key to received_equipment.equipment_id
            $table->string('description');
            $table->integer('quantity');
            $table->string('unit')->nullable(); // Added unit field
            $table->timestamps();

            $table->foreign('equipment_id')
                ->references('equipment_id')
                ->on('received_equipment')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_equipment_description');
    }
};