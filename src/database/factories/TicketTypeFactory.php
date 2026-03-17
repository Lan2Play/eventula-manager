<?php

namespace Database\Factories;

use App\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTypeFactory extends Factory
{
    protected $model = TicketType::class;

    public function definition(): array
    {
        return [
            'name'       => 'Weekend Ticket',
            'type'       => 'weekend',
            'price'      => '30',
            'seatable'   => true,
            'sale_start' => null,
            'sale_end'   => null,
        ];
    }
}
