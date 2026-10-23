<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'game_id' => null,
            'rating' => null,
            'category' => 'general',
            'title' => null,
            'body' => fake()->paragraph(),
            'contains_spoilers' => false,
        ];
    }
}
