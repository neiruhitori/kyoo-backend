<?php

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
        User::create([
            'name' => 'Admin Kyoo',
            'email' => 'admin@kyoo.id',
            'password' => 'secret123',
            'phone' => '123123123',
            'role' => 'admin_kyoo'
        ]);
    }
}
