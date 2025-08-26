<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            // Project uses Portuguese column names on `usuarios` table
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'bi' => fake()->unique()->bothify('########??'),
            'sexo' => fake()->randomElement(['M', 'F']),
            'data_nascimento' => fake()->optional()->date(),
            // telefone is json in the migration; store as JSON string
            'telefone' => json_encode([fake()->phoneNumber()]),
            // senha column stores hashed password
            'senha' => static::$password ??= Hash::make('password'),
            // optional relations left null by default
            'perfil_id' => null,
            'agencia_id' => null,
            'endereco' => null,
            'cidade' => null,
            'provincia' => null,
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
}
