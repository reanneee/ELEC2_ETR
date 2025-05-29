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
        Schema::create('linked_equipment_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fund_id')->constrained('funds')->onDelete('cascade');

            $table->string('original_property_no');

            $table->string('reference_mmdd', 5); 

            $table->string('new_property_no')->unique();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->timestamps();
            $table->index(['reference_mmdd']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_equipment_items');
    }
};
