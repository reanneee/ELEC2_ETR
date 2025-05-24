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

            // Foreign key to funds
            $table->foreignId('fund_id')->constrained('funds')->onDelete('cascade');

            // Reference to original equipment item
            $table->string('original_property_no'); // Reference to equipment_items.property_no

            // Generated code, based on 5th-8th digits of original_property_no (MM-DD)
            $table->string('reference_mmdd', 5); // e.g., "05-03"

            // New generated property_no in format 0001-00, 0002-00, etc.
            $table->string('new_property_no')->unique();

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
