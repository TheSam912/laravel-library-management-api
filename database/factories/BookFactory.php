<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->isbn13(),
            'description' => $this->faker->paragraph(),
            'author_id' => Author::inRandomOrder()->first()->id ?? Author::factory(),
            'genre' => $this->faker->randomElement(['Fiction', 'Non-Fiction', 'Science Fiction', 'Biography', 'Mystery']),
            'published_date' => $this->faker->date(),
            'total_copies' => $this->faker->numberBetween(1, 50),
            'available_copies' => $this->faker->numberBetween(0, 50),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'cover_image' => $this->faker->imageUrl(200, 300, 'books', true, 'Book Cover'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
