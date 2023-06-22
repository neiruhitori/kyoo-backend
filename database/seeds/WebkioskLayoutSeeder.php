<?php

namespace Database\Seeders;

use App\Models\WebkioskLayout;
use Illuminate\Database\Seeder;

class WebkioskLayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WebkioskLayout::insert([
            [
                'name' => 'Layout 1',
                'code' => 'layout_1',
                'image' => 'img/webkiosk/layout-1.png',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Layout 2',
                'code' => 'layout_2',
                'image' => 'img/webkiosk/layout-2.png',
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }
}
