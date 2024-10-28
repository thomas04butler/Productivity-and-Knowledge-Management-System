<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $prevThreeMonths = Carbon::now()->subMonths(3);
        $nextSixMonths = Carbon::now()->addMonths(6);

        $createdAt = fake()->dateTimeBetween($prevThreeMonths, now()->modify('-1 week'));
        $deadline = fake()->dateTimeBetween($createdAt, $nextSixMonths);

        return [
            'created_at' => $createdAt->format('Y-m-d'),
            'name' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'deadline' => $deadline->format('Y-m-d'),
            // assign the project leader to be one of the team leaders at random
            'user_id' => User::factory(),
        ];
    }
}
