<?php

namespace Database\Factories;

use App\EventInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventInformationFactory extends Factory
{
    protected $model = EventInformation::class;

    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'text' => $this->faker->paragraphs(3, true),
        ];
    }
}
