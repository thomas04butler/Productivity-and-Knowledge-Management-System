<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prevNineMonths = Carbon::now()->subMonths(9);
        $nextSixMonths = Carbon::now()->addMonths(6);

        $createdAt = fake()->dateTimeBetween($prevNineMonths, now()->modify('-1 week'));
        $deadline = fake()->dateTimeBetween($createdAt, $nextSixMonths);
        $completedAt = fake()->dateTimeBetween($createdAt->modify('+1 week'), now());

        return [
            'user_id' => User::factory(),
            'created_at' => $createdAt->format('Y-m-d'),
            'project_id' => Project::factory(),
            'name' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'required_hours' => fake()->numberBetween(1, 100),
            'completed_hours' => 0,
            'completed_at' => fake()->boolean(50) ? $completedAt->format('Y-m-d') : null,
            'deadline' => $deadline->format('Y-m-d'),
        ];
    }
}
