<?php

namespace Database\Seeders;

use App\Models\administrator;
use App\Models\User as ModelsUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelsUser::insert([
            [
                'username' => 'dev1',
                'password' => Hash::make('hellobyte1!'),
                'last_login_at' => now(),
            ],
            [
                'username' => 'dev2',
                'password' => Hash::make('hellobyte2!'),
                'last_login_at' => now(),
            ],
            [
                'username' => 'player1',
                'password' => Hash::make('helloworld1!'),
                'last_login_at' => now(),
            ],
            [
                'username' => 'player2',
                'password' => Hash::make('helloworld2!'),
                'last_login_at' => now(),
            ],
        ]);


        administrator::insert([
            [
                'username' => 'admin1',
                'password' => Hash::make('hellouniverse1!'),
                'last_login_at' => now(),
            ],
            [
                'username' => 'admin2',
                'password' => Hash::make('hellouniverse2!'),
                'last_login_at' => now(),
            ],
        ]);


    }
}
