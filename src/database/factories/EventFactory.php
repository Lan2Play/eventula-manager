<?php

namespace Database\Factories;

use App\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $event_name = 'EventNameHere ' . $this->faker->randomDigitNotNull;
        $start_date = date_format($this->faker->dateTimeBetween('+1 months', '+2 months'), "Y-m-d");
        $end_date = date('Y-m-d', strtotime($start_date . ' + 2 days'));
        
        return [
            'display_name' => $event_name,
            'nice_name' => strtolower(str_replace(' ', '-', $event_name)),
            'slug' => strtolower(str_replace(' ', '-', $event_name)),
            'start' => $start_date . ' 16:00:00',
            'end' => $end_date . ' 18:00:00',
            'desc_long' => $this->faker->sentences(5, true),
            'desc_short' => $this->faker->sentences(1, true),
            'status' => 'published',
        ];
    }
}
