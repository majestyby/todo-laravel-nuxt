<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Task> */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.7)->paragraph(),
            'due_date' => fake()->optional(0.6)->dateTimeBetween('now', '+30 days'),
            'status' => fake()->randomElement(TaskStatus::cases()),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => TaskStatus::Pending]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => TaskStatus::InProgress]);
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => TaskStatus::Completed]);
    }
}
