<?php

namespace App\Services;

use App\User;
use Faker\Factory;
use App\Events\CorporateUserCreatedEvent;

class UserService
{
    public function createCorporate(array $data)
    {
        if (!isset($data['corporate_id'])) {
            throw new \Exception('Corporate ID harus diisi');
        }

        $userByEmail = User::where('email', $data['email'])->first();
        if ($userByEmail) {
            throw new \Exception('Email sudah terdaftar');
        }

        $userByPhone = User::where('phone', $data['phone'])->first();
        if ($userByPhone) {
            throw new \Exception('No. HP sudah terdaftar');
        }

        $password = Factory::create()->password;

        $user = User::create([
            'corporate_id' => $data['corporate_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $password,
            'role' => 'admin_corporate'
        ]);

        CorporateUserCreatedEvent::dispatch($user);
    }
}