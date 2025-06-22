<?php

namespace Database\Factories;

use App\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition()
    {
        return [
            // Default empty definition - specific attributes will be passed in seeders
        ];
    }
}
