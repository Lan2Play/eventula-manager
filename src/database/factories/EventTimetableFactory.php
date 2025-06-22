<?php

namespace Database\Factories;

use App\EventTimetable;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventTimetableFactory extends Factory
{
    protected $model = EventTimetable::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'status' => 'published',
        ];
    }
}
