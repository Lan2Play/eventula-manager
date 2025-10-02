<?php

namespace App\Http\Controllers\Adminapi\Events;


use App\Event;
use App\Ticket;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Show Participants
     * // TODO Refactor eventParticipants() call on user
     * @param  $event
     * @return Ticket
     */
    public function getParticipants(Request $request)
    {
        $user = auth('sanctum')->user();

        if ($user && !empty($user->eventParticipants)) {
            foreach ($user->eventParticipants as $ticket) {
                if ((date('Y-m-d H:i:s') >= $ticket->event->start) &&
                    (date('Y-m-d H:i:s') <= $ticket->event->end) &&
                    ($ticket->signed_in || $ticket->event->online_event)
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
        $return["event"] = [
            'online_event' => $ticket->event->online_event,
        ];

        foreach ($event->eventParticipants as $ticket) {

            $seat = "Not Seated";
            if ($ticket->seat) {
                $seat = $ticket->seat->seat;
            }
            $return["participants"][] = [
                'participant' => $ticket,
                'user' => $ticket->user,
                'purchase' => $ticket->purchase,
                'seat' => $seat,
            ];
        }


        return $return;
    }

    /**
     * Get participant
     * @param  Ticket $ticket
     * @return Redirect
     */
    public function getTicket(Ticket $ticket)
    {
            return [
            'successful' => true,
            'reason' => '',
            'participant' => Ticket::with(['user','ticket', 'purchase','seat'])->where('id',$ticket->id)->get()->first(),
        ];
    }

    /**
     * Sign in to user to current Event
     * @param  Ticket $ticket
     * @return Redirect
     */
    public function signIn(Ticket $ticket)
    {
        if ($ticket->revoked) {
            return [
                'successful' => false,
                'reason' => 'Cannot sign in revoked Participant',
                'participant' => $ticket,
            ];
        }
        if (!$ticket->setSignIn()) {
            return [
                'successful' => false,
                'reason' => 'Cannot sign in Participant',
                'participant' => $ticket,
            ];
        }
        return [
            'successful' => true,
            'reason' => '',
            'participant' => $ticket,
        ];
    }
}
