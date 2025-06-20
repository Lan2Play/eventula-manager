<?php

namespace App\Http\Controllers\Api\Events;

use DB;
use Auth;
use Session;
use Settings;
use Colors;

use App\User;
use App\Event;
use App\Ticket;
use App\TicketType;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TicketsController extends Controller
{
    /**
     * Show all Event Tickets
     * @param  $event
     * @return EventTickets
     */
    public function index($event)
    {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }
        if (!$event) {
            abort(404, "Event not found.");
        }

        $return = array();
        foreach ($event->tickets as $ticket) {
            $return[] = [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'type' => $ticket->type,
                'price' => $ticket->price,
                'quantity' => $ticket->quantity,
            ];
        }

        return $return;
    }

    /**
     * Show Event Ticket
     * @param  $event
     * @param  TicketType $ticket
     * @return TicketType
     */
    public function show($event, $ticket)
    {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }
     	if (is_numeric($ticket)) {
            $ticket =  $event->tickets()->where('id', $ticket)->first();
        }
        if (!$event || !$ticket) {
            abort(404, "Event not found.");
        }

        $return = [
            'id' => $ticket->id,
            'name' => $ticket->name,
            'type' => $ticket->type,
            'price' => $ticket->price,
            'quantity' => $ticket->quantity,
        ];

        return $return;
    }
}
