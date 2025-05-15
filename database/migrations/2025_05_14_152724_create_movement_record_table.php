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
        Schema::create('movement_record', function (Blueprint $table) {
            $table->id('movement_record_id');
            $table->unsignedBigInteger('property_card_id');
            $table->date('movement_date')->nullable();
            $table->string('par', 100)->nullable();
            $table->boolean('receipt')->default(false);
            $table->integer('qty')->nullable();
            $table->string('issue_transfer_disposal', 50)->nullable();
            $table->string('office_officer')->nullable();
            $table->integer('balance')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
    
            $table->foreign('property_card_id')->references('property_card_id')->on('property_card')->onDelete('cascade');
        });
    
        // Add foreign key after both tables exist
        Schema::table('property_card', function (Blueprint $table) {
            $table->foreign('movement_record_id')->references('movement_record_id')->on('movement_record')->onDelete('set null');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movement_record');
    }
};
