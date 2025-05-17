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
        Schema::create('received_equipment_item', function (Blueprint $table) {
            $table->id('item_id'); // Primary Key
            $table->unsignedBigInteger('description_id'); // Foreign Key
            $table->string('serial_no')->nullable();
            $table->string('property_no');
            $table->date('date_acquired'); // Added date acquired field
            $table->decimal('amount', 10, 2); // Added amount field
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('description_id')
                ->references('description_id')
                ->on('received_equipment_description')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_equipment_item');
    }
};