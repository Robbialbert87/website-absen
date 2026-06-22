<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin'),
            'password_changed_at' => now(),
        ]);

        $admin->assignRole('admin');
    }
}
