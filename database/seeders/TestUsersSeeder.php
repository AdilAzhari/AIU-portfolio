<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $issuerRole = Role::where('name', 'issuer')->first();
        $studentRole = Role::where('name', 'student')->first();
        $verifierRole = Role::where('name', 'verifier')->first();

        // Create Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@aiu.edu',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
            'ethereum_address' => '0xf39Fd6e51aad88F6F4ce6aB8827279cffFb92266', // Hardhat account #0
        ]);

        // Create Issuer users (Faculty/Teachers)
        User::create([
            'name' => 'Dr. Sarah Johnson',
            'email' => 'issuer@aiu.edu',
            'password' => Hash::make('password'),
            'role_id' => $issuerRole->id,
            'email_verified_at' => now(),
            'ethereum_address' => '0x70997970C51812dc3A010C7d01b50e0d17dc79C8', // Hardhat account #1
        ]);

        User::create([
            'name' => 'Prof. Michael Chen',
            'email' => 'professor@aiu.edu',
            'password' => Hash::make('password'),
            'role_id' => $issuerRole->id,
            'email_verified_at' => now(),
            'ethereum_address' => '0x3C44CdDdB6a900fa2b585dd299e03d12FA4293BC', // Hardhat account #2
        ]);

        // Create Student users
        User::create([
            'name' => 'Ahmed Mohamed',
            'email' => 'student@aiu.edu',
            'password' => Hash::make('password'),
            'role_id' => $studentRole->id,
            'email_verified_at' => now(),
            'ethereum_address' => '0x90F79bf6EB2c4f870365E785982E1f101E93b906', // Hardhat account #3
        ]);

        User::create([
            'name' => 'Fatima Ali',
            'email' => 'student2@aiu.edu',
            'password' => Hash::make('password'),
            'role_id' => $studentRole->id,
            'email_verified_at' => now(),
            'ethereum_address' => '0x15d34AAf54267DB7D7c367839AAf71A00a2C6A65', // Hardhat account #4
        ]);

        // Create Verifier user (External verifier/employer)
        User::create([
            'name' => 'HR Manager - Tech Corp',
            'email' => 'verifier@company.com',
            'password' => Hash::make('password'),
            'role_id' => $verifierRole->id,
            'email_verified_at' => now(),
            'ethereum_address' => '0x9965507D1a55bcC2695C58ba16FB37d819B0A4dc', // Hardhat account #5
        ]);

        $this->command->info('✅ Test users created successfully!');
        $this->command->info('');
        $this->command->info('📋 Login Credentials:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('Admin:      admin@aiu.edu / password');
        $this->command->info('Issuer:     issuer@aiu.edu / password');
        $this->command->info('Issuer 2:   professor@aiu.edu / password');
        $this->command->info('Student:    student@aiu.edu / password');
        $this->command->info('Student 2:  student2@aiu.edu / password');
        $this->command->info('Verifier:   verifier@company.com / password');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
