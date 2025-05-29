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
        Schema::create('inventory_count_form', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id');
            $table->date('inventory_date')->nullable();
         
            $table->string('prepared_by_name')->nullable();
            $table->string('reviewed_by_name')->nullable();
            $table->string('prepared_by_position')->nullable();
            $table->string('reviewed_by_position')->nullable();
            $table->timestamps();
    
            $table->foreign('entity_id')->references('entity_id')->on('entities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_count');
    }
};
