<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'username' => 'dev1',
                'password' => Hash::make('hellobyte1!'),
                'role' => 'developer',
                'last_login_at' => now(),
            ],
            [
                'username' => 'dev2',
                'password' => Hash::make('hellobyte2!'),
                'role' => 'developer',
                'last_login_at' => now(),
            ],
            [
                'username' => 'player1',
                'password' => Hash::make('helloworld1!'),
                'role' => 'player',
                'last_login_at' => now(),
            ],
            [
                'username' => 'player2',
                'password' => Hash::make('helloworld2!'),
                'role' => 'player',
                'last_login_at' => now(),
            ],
            [
                'username' => 'admin1',
                'password' => Hash::make('hellouniverse1!'),
                'role' => 'admin',
                'last_login_at' => now(),
            ],
            [
                'username' => 'admin2',
                'password' => Hash::make('hellouniverse2!'),
                'role' => 'admin',
                'last_login_at' => now(),
            ],
        ]);
    }
}
