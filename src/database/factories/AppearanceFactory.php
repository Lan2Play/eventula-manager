<?php

namespace Database\Factories;

use App\Appearance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppearanceFactory extends Factory
{
    protected $model = Appearance::class;

    public function definition()
    {
        return [
            // Default empty definition - specific attributes will be passed in seeders
        ];
    }
}
