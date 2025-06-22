<?php

namespace Database\Factories;

use App\EventSeatingPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventSeatingPlanFactory extends Factory
{
    protected $model = EventSeatingPlan::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'columns' => 8,
            'rows' => 6,
            'headers' => 'A,B,C,D,E,F,G,H',
            'status' => 'published',
        ];
    }
}
