<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@localhost.com',
                'password' => 'Admin@1234',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@localhost.com',
                'password' => 'Admin@1234',
            ],
        ];

        foreach ($admins as $admin) {
            if (!Admin::where('email', $admin['email'])->exists()) {
                Admin::create([
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'password' => Hash::make($admin['password']),
                ]);
                $this->command->info("✅ Admin user created: {$admin['email']} / {$admin['password']}");
            } else {
                $this->command->info("ℹ️ Admin user already exists: {$admin['email']}, skipping...");
            }
        }
    }
}
