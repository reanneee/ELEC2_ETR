<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Include DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id('branch_id');
            $table->string('branch_name');
            $table->timestamps();
        });

        DB::table('branches')->insert([
            ['branch_name' => 'Pangasinan State University Alaminos', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Pangasinan State University Asingan', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Pangasinan State University Bayambang', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Pangasinan State University Binmaley', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Pangasinan State University Infanta', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Pangasinan State University Lingayen', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Pangasinan State University San Carlos City', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Pangasinan State University Santa Maria', 'created_at' => now(), 'updated_at' => now()],
            ['branch_name' => 'Pangasinan State University Urdaneta City', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
