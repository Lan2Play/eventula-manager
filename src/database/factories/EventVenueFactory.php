<?php

namespace Database\Factories;

use App\EventVenue;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventVenueFactory extends Factory
{
    protected $model = EventVenue::class;

    public function definition(): array
    {
        return [
            'display_name'     => 'Venue ' . ucfirst($this->faker->unique()->word()),
            'address_1'        => $this->faker->buildingNumber() . ' ' . $this->faker->streetSuffix(),
            'address_street'   => $this->faker->streetName(),
            'address_city'     => $this->faker->city(),
            'address_postcode' => $this->faker->postcode(),
            'address_country'  => $this->faker->country(),
        ];
    }
}
