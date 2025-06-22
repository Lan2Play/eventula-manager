<?php

namespace Database\Factories;

use App\NewsArticle;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsArticleFactory extends Factory
{
    protected $model = NewsArticle::class;

    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'text' => $this->faker->paragraphs(3, true),
        ];
    }
}
