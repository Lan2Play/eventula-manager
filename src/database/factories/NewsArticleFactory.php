<?php

namespace Database\Factories;

use App\NewsArticle;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsArticleFactory extends Factory
{
    protected $model = NewsArticle::class;

    public function definition(): array
    {
        return [
            'title'      => $this->faker->sentence(4),
            'text'       => $this->faker->paragraphs(3, true),
            'public'     => 1,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn() => ['public' => 0]);
    }
}
