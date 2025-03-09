<?php

namespace Database\Seeders;

use App\Models\Regency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bnRegency = [
            'Belait' => ['Kuala Belait','Seria'],
            'Tutong' => ['Tutong'],
            'Temburong' => ['Bangar'],
            'Brunei-Muara' => ['Bandar Seri Begawan']
        ];
        $tlRegency = [
            'Dili' => ['Dili'],
            'Baucau' => ['Baucau'],
            'Cova Lima' => ['Suai'],
            'Bobonaro' => ['Maliana'],
            'Viqueque' => ['Viqueque'],
            'Lautém' => ['Lospalos'],
            'Manufahi' => ['Same'],
            'Ainaro' => ['Ainaro'],
            'Ermera' => ['Ermera'],
        ];
         $myRegency = [
            'Federal Territory' => ['Kuala Lumpur','Putrajaya'],
            'Johor' => ['Johor Bahru'],
            'Penang' => ['George Town'],
            'Perak' => ['Ipoh'],
            'Selangor' => ['Shah Alam'],
            'Sabah' => ['Kota Kinabalu','Sandakan','Tawau'],
            'Sarawak' => ['Kuching','Miri'],
            'Kedah' => ['Alor Setar'],
            'Negeri Sembilan' => ['Seremban'],
            'Pahang' => ['Kuantan'],
            'Kelantan' => ['Kota Bharu'],
            'Terengganu' => ['Kuala Terengganu'],
            'Melaka' => ['Melaka City'],
        ];
        $thRegency = [
            'Bangkok' => ['Bangkok'],
            'Chiang Mai' => ['Chiang Mai'],
            'Chiang Rai' => ['Chiang Rai'],
            'Phuket' => ['Phuket'],
            'Chonburi' => ['Pattaya'],
            'Songkhla' => ['Hat Yai'],
            'Nakhon Ratchasima' => ['Nakhon Ratchasima'],
            'Udon Thani' => ['Udon Thani'],
            'Khon Kaen' => ['Khon Kaen'],
            'Surat Thani' => ['Surat Thani'],
            'Nakhon Si Thammarat' => ['Nakhon Si Thammarat'],
            'Prachuap Khiri Khan' => ['Hua Hin'],
            'Phra Nakhon Si Ayutthaya' => ['Ayutthaya'],
            'Trang' => ['Trang'],
            'Rayong' => ['Rayong'],
            'Tak' => ['Mae Sot'],
        ];
        $auRegency = [
            'New South Wales' => ['Sydney','Newcastle','Wollongong'],
            'Victoria' => ['Melbourne','Geelong'],
            'Queensland' => ['Brisbane','Gold Coast','Cairns','Townsville'],
            'South Australia' => ['Adelaide'],
            'Western Australia' => ['Perth'],
            'Tasmania' => ['Hobart'],
            'Northern Territory' => ['Darwin','Alice Springs'],
            'Australian Capital Territory' => ['Canberra'],
        ];
    
        $bnprovinces = DB::table('provinces')->whereIn('name', array_keys($bnRegency))->get()->keyBy('name');
        $tlprovinces = DB::table('provinces')->whereIn('name', array_keys($tlRegency))->get()->keyBy('name');
        $thprovinces = DB::table('provinces')->whereIn('name', array_keys($thRegency))->get()->keyBy('name');
        $myprovinces = DB::table('provinces')->whereIn('name', array_keys($myRegency))->get()->keyBy('name');
        $auprovinces = DB::table('provinces')->whereIn('name', array_keys($auRegency))->get()->keyBy('name');
        $idregency = Regency::all();

        $regencies = [];
        
        foreach ($bnRegency as $provinceName => $regencyNames) {
            if (isset($bnprovinces[$provinceName])) {
                $provinceId = $bnprovinces[$provinceName]->id;
                foreach ($regencyNames as $regencyName) {
                    $regencies[] = [
                        'province_id' => $provinceId,
                        'country' => 'Brunei',
                        'name' => $regencyName
                    ];
                }
            }
        }
        foreach ($tlRegency as $provinceName => $regencyNames) {
            if (isset($tlprovinces[$provinceName])) {
                $provinceId = $tlprovinces[$provinceName]->id;
                foreach ($regencyNames as $regencyName) {
                    $regencies[] = [
                        'province_id' => $provinceId,
                        'country' => 'Timor-Leste',
                        'name' => $regencyName
                    ];
                }
            }
        }
        foreach ($myRegency as $provinceName => $regencyNames) {
            if (isset($myprovinces[$provinceName])) {
                $provinceId = $myprovinces[$provinceName]->id;
                foreach ($regencyNames as $regencyName) {
                    $regencies[] = [
                        'province_id' => $provinceId,
                        'country' => 'Malaysia',
                        'name' => $regencyName
                    ];
                }
            }
        }
        foreach ($thRegency as $provinceName => $regencyNames) {
            if (isset($thprovinces[$provinceName])) {
                $provinceId = $thprovinces[$provinceName]->id;
                foreach ($regencyNames as $regencyName) {
                    $regencies[] = [
                        'province_id' => $provinceId,
                        'country' => 'Thailand',
                        'name' => $regencyName
                    ];
                }
            }
        }
        foreach ($auRegency as $provinceName => $regencyNames) {
            if (isset($auprovinces[$provinceName])) {
                $provinceId = $auprovinces[$provinceName]->id;
                foreach ($regencyNames as $regencyName) {
                    $regencies[] = [
                        'province_id' => $provinceId,
                        'country' => 'Australia',
                        'name' => $regencyName
                    ];
                }
            }
        }
        

        DB::table('regencies')->insert($regencies);
        

    }
}
