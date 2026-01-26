<?php

namespace App\Http\Controllers\Userapi\Events;


use App\Event;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TicketsController extends Controller
{
    /**
     * Show Participants
     * @param  $event
     * @return EventParticipants
     */
    public function getTickets(Request $request)
    {
        $user = auth('sanctum')->user();

        if ($user && !empty($user->tickets)) {
            foreach ($user->tickets as $ticket) {
                if ((date('Y-m-d H:i:s') >= $ticket->event->start) &&
                    (date('Y-m-d H:i:s') <= $ticket->event->end) &&
                    ($ticket->signed_in || $ticket->event->online_event) &&
                    ($ticket->purchase->status == "Success")
                ) {
                    $event = Event::where('start', '<', date("Y-m-d H:i:s"))->where('end', '>', date("Y-m-d H:i:s"))->orderBy('id', 'desc')->first();
                    break;
                }
            }
        }

        if (!isset($event)) {
            abort(404, "Event not found.");
        }

        $return = array();
        foreach ($event->tickets as $ticket) {
            $seat = "Not Seated";
            if ($ticket->seat) {
                $seat = $ticket->seat->seat;
            }
            $return[] = [
                'id' => $ticket->user->id,
                'firstname' => $ticket->user->firstname,
                'username' => $ticket->user->username,
                'username_nice' => $ticket->user->username_nice,
                'steamname' => $ticket->user->steamname,
                'admin' => $ticket->user->admin,
                'seat' => $seat,
            ];
        }


        return $return;
    }
}
