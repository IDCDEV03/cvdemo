<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\Role;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       User::create([
        'prefix' => 'Mr.',
        'lastname' => 'Test',
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'role' => Role::Admin,            
        ]);

        User::create([
             'prefix' => 'Mr.',
        'lastname' => 'Test',
            'name' => 'Agency',
            'username' => 'agency',
            'email' => 'agency@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => Role::Agency,
        ]);

        User::create([
             'prefix' => 'Mr.',
            'lastname' => 'Test',
            'name' => 'Manager',
            'username' => 'manager',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => Role::Manager,
        ]);

        User::create([
             'prefix' => 'Mr.',
        'lastname' => 'Test',
            'name' => 'Regular',
            'username' => 'user001',
            'email' => 'user@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => Role::User,
        ]);
    }
}
