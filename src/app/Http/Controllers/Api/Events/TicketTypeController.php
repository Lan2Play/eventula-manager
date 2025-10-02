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

//TODO cehck this controller with TicketType formerly known as Ticket
class TicketTypeController extends Controller
{
    /**
     * Show all TicketTypes for Event $event
     *
     * @param  $event
     * @return array
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
     * @param  TicketType $ticketType
     * @return array
     */
    public function show($event, $ticketType)
    {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }
     	if (is_numeric($ticketType)) {
            $ticketType =  $event->ticketTypes()->where('id', $ticketType)->first();
        }
        if (!$event || !$ticketType) {
            abort(404, "Event not found.");
        }

        return [
            'id' => $ticketType->id,
            'name' => $ticketType->name,
            'type' => $ticketType->type,
            'price' => $ticketType->price,
            'quantity' => $ticketType->quantity,
        ];
    }
}
