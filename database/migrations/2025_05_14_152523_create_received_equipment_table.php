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
        Schema::create('received_equipment', function (Blueprint $table) {
            $table->id('equipment_id');
        
            $table->foreignId('entity_id')
                ->references('entity_id')
                ->on('entities')
                ->onDelete('cascade');
        
            $table->date('date_acquired')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('received_by_name')->nullable();
            $table->string('received_by_designation')->nullable();
            $table->string('verified_by_name')->nullable();
            $table->string('verified_by_designation')->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('par_no')->unique()->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_equipment');
    }
};