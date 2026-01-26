<?php

namespace App\Http\Controllers\Api\Events;

use App\Event;

use App\Http\Controllers\Controller;

// TODO Check this Controller with the new Ticket formerly known as Participant
class TicketController extends Controller
{
    /**
     * Show Tickets
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

        $return = [
            'count' => $event->tickets->count(),
            'participants' => array(),
        ];

        if (!$event->private_participants) {
            foreach ($event->tickets as $ticket) {
                $seat = "Not Seated";
                if ($ticket->seat) {
                    $seat = $ticket->seat->seat;
                }
                $return["participants"][] = [
                    'username' => $ticket->user->steamname ?? $ticket->user->username,
                    'seat' => $seat,
                ];
            }
        }
        return $return;
    }
}
