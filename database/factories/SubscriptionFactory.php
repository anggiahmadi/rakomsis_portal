<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('SUBS-####-????'),
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->unique()->safeEmail(),
            'price_type' => $this->faker->randomElement(['per_user', 'per_location']),
            'billing_cycle' => $this->faker->randomElement(['monthly', 'yearly']),
            'quantity' => $this->faker->numberBetween(1, 100),
            'length_of_term' => $this->faker->numberBetween(1, 12),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'tax_percentage' => $this->faker->randomFloat(2, 0, 20),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'tax' => $this->faker->randomFloat(2, 0, 200),
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'discount' => $this->faker->randomFloat(2, 0, 100),
            'subtotal' => $this->faker->randomFloat(2, 10, 10000),
            'total' => $this->faker->randomFloat(2, 10, 10000),
            'agent_commission' => $this->faker->randomFloat(2, 0, 500),
            'payment_status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'subscription_status' => $this->faker->randomElement(['active', 'cancelled', 'expired']),
        ];
    }
}
