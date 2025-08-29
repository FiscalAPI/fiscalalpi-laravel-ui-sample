<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fiscalapiId' => $this->faker->optional()->uuid(),
            'legalName' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('Admin12345!'),
            'capitalRegime' => $this->faker->optional()->randomElement(['S.A. de C.V.', 'S.A.', 'S. de R.L.']),
            'satTaxRegimeId' => $this->faker->optional()->randomElement(['601', '603', '605', '612']),
            'satCfdiUseId' => $this->faker->optional()->randomElement(['G01', 'G02', 'G03']),
            'tin' => $this->faker->optional()->regexify('[A-Z]{4}[0-9]{6}[A-Z0-9]{3}'),
            'zipCode' => $this->faker->optional()->regexify('[0-9]{5}'),
            'taxPassword' => $this->faker->optional()->password(),
        ];
    }
}
