<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('building_name');        // e.g., Admin Building, IT Room
            $table->string('office_name')->nullable();  // e.g., Supply Office
            $table->string('officer_name')->nullable(); // e.g., Mr. Juan Dela Cruz
            $table->timestamps();
        });

        // Insert initial data
        DB::table('locations')->insert([
            ['id' => 1,  'building_name' => 'Academic Building I'],
            ['id' => 2,  'building_name' => 'Academic Building 2'],
            ['id' => 3,  'building_name' => 'Engineering Building 1'],
            ['id' => 4,  'building_name' => 'Engineering Building 2'],
            ['id' => 5,  'building_name' => 'Education Building'],
            ['id' => 6,  'building_name' => 'Students Activity Center'],
            ['id' => 7,  'building_name' => 'PTBI'],
            ['id' => 8,  'building_name' => 'DOST'],
            ['id' => 9,  'building_name' => 'School of Advanced Studies'],
            ['id' => 10, 'building_name' => 'Building'],
            ['id' => 11, 'building_name' => 'Student Wash Room School Canteen'],
            ['id' => 12, 'building_name' => 'Engineering Laboratory Building'],
            ['id' => 13, 'building_name' => 'Engineering Research Building'],
            ['id' => 14, 'building_name' => 'Student Center Theater'],
            ['id' => 15, 'building_name' => 'Guard House'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
