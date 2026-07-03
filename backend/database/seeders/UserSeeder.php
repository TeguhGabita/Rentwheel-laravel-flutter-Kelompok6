<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'admin RentWheel',
            'email' => 'admin@rentwheel.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        $user = User::create([
            'name' => 'User RentWheel',
            'email' => 'user@rentwheel.test',
            'password' => Hash::make('password'),
        ]);
        $user->assignRole('user');
    }
}
