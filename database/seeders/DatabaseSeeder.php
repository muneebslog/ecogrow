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
        // User::factory(10)->create();

        // Idempotent: db:seed must be safe to re-run against a database that
        // already has this user (a prior seed run, or someone having
        // registered with this email for real) without crashing on the
        // unique email constraint.
        if (! User::query()->where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->call([
            SpeciesSeeder::class,
            BadgeSeeder::class,
        ]);
    }
}
