<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a specific test user
        if (!User::where('email', 'user@localhost.com')->exists()) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'user@localhost.com',
                'phone' => '0123456789',
                'password' => Hash::make('Admin@1234'),
            ]);
            $this->command->info("✅ Test user created: user@localhost.com / password");

            // Add addresses for test user
            Address::create([
                'user_id' => $user->id,
                'first_name' => "Yashvir",
                'last_name' => "Pal",
                'type' => 'billing',
                'address_line1' => '123 Main Street',
                'address_line2' => 'Apt 101',
                'city' => 'Cityville',
                'state' => 'Stateville',
                'country' => 'Countryland',
                'zip' => '123456',
                'phone' => '1234567890',
                'status' => true,
            ]);

            Address::create([
                'user_id' => $user->id,
                'first_name' => "Yash",
                'last_name' => "Pal",
                'type' => 'shipping',
                'address_line1' => '456 Second Street',
                'address_line2' => '',
                'city' => 'Townsville',
                'state' => 'Stateville',
                'country' => 'Countryland',
                'zip' => '678900',
                'phone' => '0987654321',
                'status' => true,
            ]);
        }

        // Create 20 random users
        $users = User::factory()->count(20)->create();

        // Add addresses for each random user
        foreach ($users as $user) {
            Address::create([
                'user_id' => $user->id,
                'type' => 'billing',
                'address_line1' => fake()->streetAddress(),
                'address_line2' => fake()->secondaryAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'country' => fake()->country(),
                'zip' => fake()->postcode(),
                'phone' => fake()->phoneNumber(),
                'status' => 1,
            ]);

            Address::create([
                'user_id' => $user->id,
                'type' => 'shipping',
                'address_line1' => fake()->streetAddress(),
                'address_line2' => fake()->secondaryAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'country' => fake()->country(),
                'zip' => fake()->postcode(),
                'phone' => fake()->phoneNumber(),
                'status' => 1,
            ]);
        }

        $this->command->info("✅ Addresses created for all users");
    }
}
