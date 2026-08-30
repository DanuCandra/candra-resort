<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '62'.fake()->unique()->numerify('8##########'),
            'role' => UserRole::Guest,
            'is_active' => true,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function receptionist(): static
    {
        return $this->state(fn (): array => [
            'username' => 'rec_'.Str::lower(Str::random(12)),
            'employee_code' => 'REC-'.Str::upper(Str::random(12)),
            'role' => UserRole::Receptionist,
        ]);
    }

    public function owner(): static
    {
        return $this->state(fn (): array => [
            'username' => 'own_'.Str::lower(Str::random(12)),
            'employee_code' => 'OWN-'.Str::upper(Str::random(12)),
            'role' => UserRole::Owner,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
