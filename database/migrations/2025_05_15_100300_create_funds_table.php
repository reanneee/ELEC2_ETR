<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Required for DB::table()

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->string('account_title');
            $table->string('account_code')->unique();
            $table->string('code')->unique();
            $table->timestamps();
        });

        DB::table('funds')->insert([
            ['id' => 1, 'account_title' => 'Land', 'account_code' => '10601010-00', 'code' => '0101', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'account_title' => 'Land Improvements, Aquaculture Structures', 'account_code' => '10602010-00', 'code' => '0201', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'account_title' => 'Land Improvement, Reforestation Projects', 'account_code' => '10602020-00', 'code' => '0202', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'account_title' => 'Other Land Improvements', 'account_code' => '10602990-00', 'code' => '0299', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'account_title' => 'Road Networks', 'account_code' => '10603010-00', 'code' => '0301', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'account_title' => 'Flood Control Systems', 'account_code' => '10603020-00', 'code' => '0302', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'account_title' => 'Sewer Systems', 'account_code' => '10603030-00', 'code' => '0303', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'account_title' => 'Water Supply Systems', 'account_code' => '10603040-00', 'code' => '0304', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'account_title' => 'Power Supply Systems', 'account_code' => '10603050-00', 'code' => '0305', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'account_title' => 'Communication Networks', 'account_code' => '10603060-00', 'code' => '0306', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'account_title' => 'Seaport Systems', 'account_code' => '10603070-00', 'code' => '0307', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'account_title' => 'Airport Systems', 'account_code' => '10603080-00', 'code' => '0308', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'account_title' => 'Parks, Plazas and Monuments', 'account_code' => '10603090-00', 'code' => '0309', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'account_title' => 'Other Infrastructure Assets', 'account_code' => '10603990-00', 'code' => '0399', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'account_title' => 'Buildings', 'account_code' => '10604010-00', 'code' => '0401', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'account_title' => 'School Buildings', 'account_code' => '10604020-00', 'code' => '0402', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'account_title' => 'Hospitals and Health Centers', 'account_code' => '10604030-00', 'code' => '0403', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'account_title' => 'Markets', 'account_code' => '10604040-00', 'code' => '0404', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'account_title' => 'Slaughterhouses', 'account_code' => '10604050-00', 'code' => '0405', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'account_title' => 'Hostels and Dormitories', 'account_code' => '10604060-00', 'code' => '0406', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'account_title' => 'Other Structures', 'account_code' => '10604990-00', 'code' => '0499', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'account_title' => 'Machinery', 'account_code' => '10605010-00', 'code' => '0501', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'account_title' => 'Office Equipment', 'account_code' => '10605020-00', 'code' => '0502', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'account_title' => 'Information and Communication Technology Equipment', 'account_code' => '10605030-00', 'code' => '0503', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'account_title' => 'Agricultural and Forestry Equipment', 'account_code' => '10605040-00', 'code' => '0504', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'account_title' => 'Marine and Fishery Equipment', 'account_code' => '10605050-00', 'code' => '0505', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'account_title' => 'Airport Equipment', 'account_code' => '10605060-00', 'code' => '0506', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'account_title' => 'Communication Equipment', 'account_code' => '10605070-00', 'code' => '0507', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'account_title' => 'Construction and Heavy Equipment', 'account_code' => '10605080-00', 'code' => '0508', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'account_title' => 'Disaster Response and Rescue Equipment', 'account_code' => '10605090-00', 'code' => '0509', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'account_title' => 'Military, Police and Security Equipment', 'account_code' => '10605100-00', 'code' => '0510', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'account_title' => 'Medical Equipment', 'account_code' => '10605110-00', 'code' => '0511', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'account_title' => 'Printing Equipment', 'account_code' => '10605120-00', 'code' => '0512', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'account_title' => 'Sport Equipment', 'account_code' => '10605130-00', 'code' => '0513', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'account_title' => 'Technical and Scientific Equipment', 'account_code' => '10605140-00', 'code' => '0514', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 36, 'account_title' => 'Other Equipment', 'account_code' => '10605990-00', 'code' => '0599', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 37, 'account_title' => 'Motor Vehicles', 'account_code' => '10606010-00', 'code' => '0601', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 38, 'account_title' => 'Trains', 'account_code' => '10606020-00', 'code' => '0602', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 39, 'account_title' => 'Aircrafts and Aircrafts Ground Equipment', 'account_code' => '10606030-00', 'code' => '0603', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 40, 'account_title' => 'Watercrafts', 'account_code' => '10606040-00', 'code' => '0604', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 41, 'account_title' => 'Other Transportation Equipment', 'account_code' => '10606990-00', 'code' => '0699', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 42, 'account_title' => 'Furniture and Fixtures', 'account_code' => '10607010-00', 'code' => '0701', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 43, 'account_title' => 'Books', 'account_code' => '10607020-00', 'code' => '0702', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funds');
    }
};
