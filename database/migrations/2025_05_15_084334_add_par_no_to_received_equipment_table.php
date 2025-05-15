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
        Schema::table('received_equipment', function (Blueprint $table) {
            $table->string('par_no')->unique()->after('equipment_id');
        });
    }
    
    public function down(): void
    {
        Schema::table('received_equipment', function (Blueprint $table) {
            $table->dropColumn('par_no');
        });
    }
    
};
