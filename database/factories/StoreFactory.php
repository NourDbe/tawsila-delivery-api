<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'phone' => fake()->numerify('09########'),
            'address' => fake()->address(),
            'is_suspended' => false,
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn (): array => [
            'is_suspended' => true,
        ]);
    }
}
