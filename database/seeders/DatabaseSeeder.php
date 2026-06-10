<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            [
                'name' => 'System Admin',
                'email' => 'admin@phrmdo.test',
                'role' => 'admin',
            ],
            [
                'name' => 'Inventory Staff',
                'email' => 'staff@phrmdo.test',
                'role' => 'staff',
            ],
            [
                'name' => 'Report Viewer',
                'email' => 'viewer@phrmdo.test',
                'role' => 'viewer',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
