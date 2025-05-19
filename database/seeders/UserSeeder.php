<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'sofyanhadi197@gmail.com'],
            [
                'name'              => 'Sofyan Hadi',
                'password'          => Hash::make('123456'),
                'email_verified_at' => now(),
            ]
        );
    }
}
