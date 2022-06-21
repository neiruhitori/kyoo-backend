<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
    
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            IndoRegionProvinceSeeder::class,
            IndoRegionRegencySeeder::class,
            BranchTypesTableSeeder::class,
            IndustryCategorySeeder::class,
            FreeExhibitionLicenseSeeder::class,
            TVLayoutSeeder::class,
            AdditionalFeatureSeeder::class
        ]);
    }
}
