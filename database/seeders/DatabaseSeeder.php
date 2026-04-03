<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@rakomsis.com',
            'password' => bcrypt('rakomsis' . date('Y')),
            'is_employee' => true,
        ])->employee()->create([
            'code' => 'EMP' . str_pad(1, 4, '0', STR_PAD_LEFT), // Auto-generated employee code
            'position' => 'Administrator',
        ]);

        $this->call([
            ProductSeeder::class,
        ]);
    }
}
