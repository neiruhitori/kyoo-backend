<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\SGProvince;
use App\Models\VNProvinces;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $indoRegion = Province::select('id','name','timezone')->get();
        $sgRegion = SGProvince::select('name','timezone')->get();
        $vnRegion = VNProvinces::select('name','timezone')->get();
        $data = [];
        foreach ($indoRegion as $region) {
            $data[] = [
                'old_id' => $region->id, // ID lama hanya untuk Indonesia
                'country' => 'Indonesia',
                'name' => $region->name,
                'timezone' => $region->timezone,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($sgRegion as $region) {
            $data[] = [
                'old_id' => null, 
                'country' => 'Singapore',
                'name' => $region->name,
                'timezone' => $region->timezone,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($vnRegion as $region) {
            $data[] = [
                'old_id' => null, 
                'country' => 'Vietnam',
                'name' => $region->name,
                'timezone' => $region->timezone,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('provinces')->insert($data);
    }
}
