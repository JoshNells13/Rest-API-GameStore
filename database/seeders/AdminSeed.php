<?php

namespace Database\Seeders;

use App\Models\administrator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        administrator::create([
            'username' => 'admin11',
            'password' => Hash::make('admin123')
        ]);
    }
}
