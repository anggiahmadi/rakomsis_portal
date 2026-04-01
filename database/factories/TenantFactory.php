<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('tenant-####'), // Unique code for tenant identification and auto generation
            'domain' => $this->faker->unique()->domainName(), // Unique domain for tenant access (e.g., tenant1.rakomsis.com)
            'name' => $this->faker->company(), // Name of the tenant
            'address' => $this->faker->address(), // Address of the tenant
            'business_type' => $this->faker ->randomElement(['SaaS', 'E-commerce', 'Consulting', 'Education', 'Healthcare']), // Type of business the tenant operates
        ];
    }
}
