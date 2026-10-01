<?php

namespace Database\Factories;

use App\Models\Categories;
use Illuminate\Database\Eloquent\Factories\Factory;

class BooksFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Categories::query()->inRandomOrder()->value('id') ?? Categories::factory(),
            'isbn' => fake()->unique()->numerify('978-###-###-####'),
            'title' => fake()->sentence(4),
            'author' => fake()->name(),
            'publisher' => fake()->company(),
            'publication_year' => fake()->numberBetween(1990, (int) date('Y')),
            'stock' => fake()->numberBetween(0, 20),
            'cover_image' => null,
            'synopsis' => fake()->paragraph(),
        ];
    }
}
