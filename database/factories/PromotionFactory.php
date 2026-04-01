<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->bothify('PROMO-####')),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'image' => $this->faker->imageUrl(),
            'promotion_rules' => $this->faker->randomElement(['all', 'new_customers', 'specific_length_of_term']),
            'billing_cycle' => $this->faker->randomElement(['monthly', 'yearly']),
            'specific_length_of_term' => $this->faker->optional()->numberBetween(1, 12), // Only applicable if promotion_rules is set to specific_length_of_term
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed_amount']),
            'discount_value' => $this->faker->randomFloat(2, 0, 100), // The value of the discount, either a percentage or a fixed amount depending on the discount_type
        ];
    }
}
