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
        Schema::create('equipment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('received_equipment_id');
            $table->foreign('received_equipment_id')
                  ->references('equipment_id') // <- Correct column
                  ->on('received_equipment')
                  ->onDelete('cascade');
            
            $table->string('serial_no')->nullable(); // Serial may be optional
            $table->string('property_no')->unique(); // e.g., 2024-05-03-001
            $table->enum('status', ['available', 'assigned', 'maintenance'])->default('available');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->timestamps();
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_items');
    }
};
