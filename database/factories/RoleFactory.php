<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                RoleEnum::ADMIN->value,
                RoleEnum::ISSUER->value,
                RoleEnum::STUDENT->value,
                RoleEnum::VERIFIER->value,
            ]),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => RoleEnum::ADMIN->value,
        ]);
    }

    public function issuer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => RoleEnum::ISSUER->value,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => RoleEnum::STUDENT->value,
        ]);
    }

    public function verifier(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => RoleEnum::VERIFIER->value,
        ]);
    }
}
