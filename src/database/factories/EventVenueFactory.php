<?php

namespace Database\Factories;

use App\EventVenue;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventVenueFactory extends Factory
{
    protected $model = EventVenue::class;

    public function definition()
    {
        $venue_name = 'VenueNameHere ' . $this->faker->randomDigitNotNull;
        
        return [
            'display_name' => $venue_name,
            'slug' => strtolower(str_replace(' ', '-', $venue_name)),
            'address_1' => $this->faker->secondaryAddress(),
            'address_street' => $this->faker->streetName(),
            'address_city' => $this->faker->city(),
            'address_postcode' => $this->faker->postcode(),
            'address_country' => $this->faker->country(),
        ];
    }
}
