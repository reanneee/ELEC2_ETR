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
        Schema::create('entities', function (Blueprint $table) {
            $table->id('entity_id');
            $table->string('entity_name');
        
            // Define branch_id as unsigned and foreign key
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')
                  ->references('branch_id')
                  ->on('branches')
                  ->onDelete('cascade');
        
            $table->unsignedBigInteger('fund_cluster_id');
            $table->foreign('fund_cluster_id')
                  ->references('id')
                  ->on('fund_clusters')
                  ->onDelete('cascade');
        
            $table->timestamps();
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};