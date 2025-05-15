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
        Schema::create('property_card', function (Blueprint $table) {
            $table->id('property_card_id');
            // $table->unsignedBigInteger('entity_id');
              $table->foreignId('entity_id')->constrained('entities', 'entity_id')->onDelete('cascade');
            $table->string('property_number', 100)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('movement_record_id')->nullable(); // will be set later
            $table->timestamps();
    
            // $table->foreign('entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_card');
    }
};
