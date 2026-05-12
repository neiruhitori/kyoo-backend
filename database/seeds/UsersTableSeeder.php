<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@kyoo.id'],
            [
                'name' => 'Admin Kyoo2',
                'password' => 'Secret123',
                'phone' => '123123123',
                'role' => 'admin_kyoo'
            ]
        );
    }
}
