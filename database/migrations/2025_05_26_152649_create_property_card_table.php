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
        Schema::create('property_cards', function (Blueprint $table) {
            $table->id('property_card_id');

     
            $table->unsignedBigInteger('received_equipment_item_id');

            $table->integer('qty_physical');
            $table->string('condition');
            $table->string('remarks');
            $table->string('issue_transfer_disposal');
            $table->string('received_by_name');
            $table->string('article');
            $table->foreignId('locations_id')->constrained('locations')->cascadeOnDelete();

            $table->timestamps();

           
            $table->foreign('received_equipment_item_id')
                ->references('item_id')
                ->on('received_equipment_item')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_cards');
    }
};
