<?php

namespace App\Http\Controllers\Events;

use DB;
use Auth;
use Illuminate\Http\RedirectResponse;
use Session;
use Settings;
use Colors;

use App\User;
use App\Event;
use App\EventParticipant;
use App\EventTicket;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController as Payment;

class TicketsController extends Controller
{
    /**
     * Purchase Ticket
     * @param  Request     $request
     * @param  EventTicket $ticket
     * @return RedirectResponse
     */
    public function purchase(Request $request, EventTicket $ticket)
    {
        /** @var User $user */
        $user = User::where('id', $request->user_id)->first();

        if ($user == null) {
            Session::flash('alert-danger', 'User not found.');
            return Redirect::to('/events/' . $ticket->event->slug);
        }
        if ($ticket->event->status != 'PUBLISHED' && $ticket->event->status != 'PRIVATE'  && $ticket->event->status != 'REGISTEREDONLY') {
            Session::flash(
                'alert-danger',
                'Event is currently in ' . strtolower($ticket->event->status) . '. You cannot buy tickets yet.'
            );
            return Redirect::to('/events/' . $ticket->event->slug);
        }

        if (date('Y-m-d H:i:s') >= $ticket->event->end) {
            Session::flash('alert-danger', 'You cannot buy tickets for previous events.');
            return Redirect::to('/events/' . $ticket->event->slug);
        }

        if ($ticket->sale_start != null && date('Y-m-d H:i:s') <= $ticket->sale_start) {
            Session::flash('alert-danger', __('tickets.ticket_not_yet'));
            return Redirect::to('/events/' . $ticket->event->slug);
        }

        if ($ticket->sale_end != null && date('Y-m-d H:i:s') >= $ticket->sale_end) {
            Session::flash('alert-danger', __('tickets.ticket_not_anymore'));
            return Redirect::to('/events/' . $ticket->event->slug);
        }

        if (($ticket->event->no_tickets_per_user ?? 0) > 0) {
            $ticketCount = $user->getAllTickets($ticket->event->id)->count();
            if ($ticketCount + $request->quantity > $ticket->event->no_tickets_per_user) {
                Session::flash(
                    'alert-danger',
                    __(
                        'tickets.max_ticket_event_count_reached',
                        [
                            'ticketname' => $ticket->name,
                            'ticketamount' => $request->quantity,
                            'maxamount' => $ticket->event->no_tickets_per_user,
                            'currentamount' => $ticketCount
                        ]
                    )
                );
                return Redirect::to("/events/{$ticket->event->slug}");
            }
        }
        if ($ticket->hasTicketGroup()) {
            $ticketCount = $user->getAllTicketsInTicketGroup($ticket->event, $ticket)->count();
            if ($ticketCount + $request->quantity > $ticket->ticketGroup->tickets_per_user) {
                Session::flash(
                    'alert-danger',
                    __(
                        'tickets.max_ticket_group_count_reached',
                        [
                            'ticketname' => $ticket->name,
                            'ticketamount' => $request->quantity,
                            'maxamount' => $ticket->ticketGroup->tickets_per_user,
                            'ticketgroup' => $ticket->ticketGroup->name,
                            'currentamount' => $ticketCount
                        ]
                    )
                );
                return Redirect::to("/events/{$ticket->event->slug}");
            }
        }
        $user_event_tickets = $user->getAllTicketsOfType($ticket->event, $ticket)->count();
        if (
            is_numeric($ticket->no_tickets_per_user) &&
            $ticket->no_tickets_per_user > 0 &&
            $user_event_tickets + $request->quantity > $ticket->no_tickets_per_user
        ) {
            Session::flash(
                'alert-danger',
                __(
                    'tickets.max_ticket_type_count_reached',
                    [
                        'ticketname' => $ticket->name,
                        'ticketamount' => $request->quantity,
                        'maxamount' => $ticket->no_tickets_per_user,
                        'currentamount' => $user_event_tickets,
                        'maxticketcount' => $ticket->no_tickets_per_user,
                    ]
                )
            );
            return Redirect::to('/events/' . $ticket->event->slug);
        }

        $params = [
            'tickets' => [
                $ticket->id => $request->quantity,
            ],
        ];
        Session::put(Settings::getOrgName() . '-basket', $params);
        Session::save();
        return Redirect::to('/payment/checkout');
    }

    /**
     * Retrieve ticket via QR code
     * @param  EventParticipant $participant
     * @return Redirect
     */
    public function retrieve(EventParticipant $participant)
    {
        $user = Auth::user();
        if ($user->admin == 1) {
            return Redirect::to('/admin/events/' . $participant->event->slug . '/participants/' . $participant->id); // redirect to site
        }
        return Redirect::to('/events/' . $participant->event_id); // redirect to site
    }
}
