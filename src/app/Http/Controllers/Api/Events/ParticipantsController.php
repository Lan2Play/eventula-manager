<?php

namespace App\Http\Controllers\Api\Events;

use DB;
use Auth;
use Session;
use App\User;
use App\Event;
use App\Ticket;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class ParticipantsController extends Controller
{
    /**
     * Show Participants
     * @param  $event
     * @return EventParticipants
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
            'count' => $event->eventParticipants->count(),
            'participants' => array(),
        ];

        if (!$event->private_participants) {
            foreach ($event->eventParticipants as $participant) {
                $seat = "Not Seated";
                if ($participant->seat) {
                    $seat = $participant->seat->seat;
                }
                $return["participants"][] = [
                    'username' => $participant->user->steamname ?? $participant->user->username,
                    'seat' => $seat,
                ];
            }
        }
        return $return;
    }
}
