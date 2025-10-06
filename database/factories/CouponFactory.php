<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $type = $this->faker->randomElement(['PHAN_TRAM', 'TIEN']);

        return [
            'name' => $this->faker->words(3, true), // tên ngẫu nhiên
            'code' => strtoupper(\Str::random(8)), // random code
            'type' => $type,
            'fee' => $type === 'PHAN_TRAM' 
                ? $this->faker->numberBetween(5, 50)  // từ 5% đến 50%
                : $this->faker->numberBetween(10000, 200000), // từ 10k đến 200k
            'date_start_apply' => \Carbon::now()->subDays(rand(0, 10))->toDateString(),
            'date_end_apply' => \Carbon::now()->addDays(rand(5, 60))->toDateString(),
            'created_user_id' => 1,
            'updated_user_id' => null,
            'created_at' => now(),
            'updated_at' => null,
        ];
    }
}
