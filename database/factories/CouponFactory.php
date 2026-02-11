<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Coupon;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['percentage', 'fixed']);
        $value = $type === 'percentage' ? $this->faker->numberBetween(5, 50) : $this->faker->numberBetween(10, 200);

        return [
            'title' => $this->faker->words(3, true),
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'value' => $value,
            'status' => $this->faker->randomElement([1,0]),
            'starts_at' => Carbon::now()->subDays(rand(0, 10)),
            'expires_at' => Carbon::now()->addDays(rand(5, 30)),
        ];
    }
}
