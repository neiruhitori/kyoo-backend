<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use ParseCsv\Csv;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RegencyLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->getCsvData(database_path('csv/regencies.csv'));
        $data = collect($data)->map(function ($value) {
            return [
                'regency_id' => $value['code'],
                'lat' => floatval($value['lat']),
                'long' => floatval($value['long'])
            ];
        })->all();

        DB::table('regency_locations')->insert($data);
    }

    protected function getCsvData($path)
    {
        $csv = new Csv();
        $csv->auto($path);

        return $csv->data;
    }
}
