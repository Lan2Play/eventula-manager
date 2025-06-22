<?php

namespace Database\Factories;

use App\EventTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventTicketFactory extends Factory
{
    protected $model = EventTicket::class;

    public function definition()
    {
        return [
            'name' => 'Weekend Ticket',
            'type' => 'weekend',
            'price' => '30',
            'seatable' => true,
            'sale_start' => null,
            'sale_end' => null,
        ];
    }
}
