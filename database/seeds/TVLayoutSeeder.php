<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TVLayout;

class TVLayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TVLayout::create([
            'name' => 'Layout 1',
            'image' => 'img/layout-1.png'
        ]);
    }
}
