<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            // 'category_id' => $this->getCategory('news'),
            // 'author_id' => $this->getAuthor('author-yaroslav'),
            // 'game_id' => $this->getGame('witcher-3-wild-hunt'),
            'is_active' => true,
            // 'name' => fake()->name(),
            // 'email' => fake()->safeEmail(),
            // 'email_verified_at' => now(),
            // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            // 'remember_token' => Str::random(10),
        ];
    }
}
