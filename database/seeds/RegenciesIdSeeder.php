<?php

namespace Database\Seeders;

use App\Models\Regency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegenciesIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $idregency = Regency::all();
        $province = DB::table('provinces')->where('country','Indonesia')->get();
        $regencies = [];
        foreach ($idregency as $regency) {
            $matchedProvince = $province->firstWhere('old_id', $regency->province_id);
            if ($matchedProvince) {
                $regencies[] = [
                    'id' => $regency->id, 
                    'province_id' => $matchedProvince->id, 
                    'name' => $regency->name, 
                    'country' => 'Indonesia', 
                ];
            }
        }

        DB::table('regencies')->insert($regencies);
    }
}
