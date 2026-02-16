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
            ],
            [
                'username' => 'dev2',
                'password' => Hash::make('hellobyte2!'),
            ],
            [
                'username' => 'player1',
                'password' => Hash::make('helloworld1!'),
            ],
            [
                'username' => 'player2',
                'password' => Hash::make('helloworld2!'),
            ],
        ]);


        administrator::insert([
            [
                'username' => 'admin1',
                'password' => Hash::make('hellouniverse1!'),
            ],
            [
                'username' => 'admin2',
                'password' => Hash::make('hellouniverse2!'),
            ],
        ]);


    }
}
