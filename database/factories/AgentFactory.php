<?php

namespace Database\Factories;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agent>
 */
class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => UserFactory::new(),
            'code' => strtoupper(fake()->bothify('AGENT-####')),
            'commission_rate' => fake()->randomFloat(2, 0, 0.5),
            'total_sales' => fake()->randomFloat(2, 0, 100000),
            'total_commission' => fake()->randomFloat(2, 0, 5000),
            'balance' => fake()->randomFloat(2, 0, 5000),
            'withdrawn' => fake()->randomFloat(2, 0, 5000),
            'pending_withdrawal' => fake()->randomFloat(2, 0, 5000)
        ];
    }
}
