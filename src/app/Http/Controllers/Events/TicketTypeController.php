<?php

namespace App\Http\Controllers\Events;

use Auth;
use Illuminate\Http\RedirectResponse;
use Session;
use Settings;

use App\User;
use App\Event;
use App\Ticket;
use App\TicketType;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class TicketTypeController extends Controller
{
    /**
     * Purchase Ticket
     * @param  Request     $request
     * @param  TicketType $ticketType
     * @return RedirectResponse
     */
    public function purchase(Request $request, TicketType $ticketType)
    {
        /** @var User $user */
        $user = User::where('id', $request->user_id)->first();

        if ($user == null) {
            Session::flash('alert-danger', __('tickets.alert_user_not_found'));;
            return Redirect::to('/events/' . $ticketType->event->slug);
        }

        if (!$ticketType->event) {
            Session::flash('alert-danger', __('tickets.alert_event_not_found'));
            return Redirect::to('/');
        }
        debug($ticketType->event);
        //TODO add translations for alerts
        $event = $ticketType->event;
        if (!in_array($event->status, [Event::STATUS_PUBLISHED, Event::STATUS_PRIVATE, Event::STATUS_REGISTEREDONLY])) {
            Session::flash(
                'alert-danger',
                __('tickets.alert_event_not_yet_published', ['state'=> $event->status])

            );
            return Redirect::to('/events/' . $event->slug);
        }


        if (date('Y-m-d H:i:s') >= $ticketType->event->end) {
            Session::flash('alert-danger', __('tickets.alert_event_ended'));;
            return Redirect::to('/events/' . $ticketType->event->slug);
        }

        if ($ticketType->sale_start != null && date('Y-m-d H:i:s') <= $ticketType->sale_start) {
            Session::flash('alert-danger', __('tickets.alert_ticket_not_yet'));
            return Redirect::to('/events/' . $ticketType->event->slug);
        }

        if ($ticketType->sale_end != null && date('Y-m-d H:i:s') >= $ticketType->sale_end) {
            Session::flash('alert-danger', __('tickets.alert_ticket_not_anymore'));
            return Redirect::to('/events/' . $ticketType->event->slug);
        }

        if (($ticketType->event->no_tickets_per_user ?? 0) > 0) {
            $ticketCount = $user->getAllTickets($ticketType->event->id)->count();
            if ($ticketCount + $request->quantity > $ticketType->event->no_tickets_per_user) {
                Session::flash(
                    'alert-danger',
                    __(
                        'tickets.max_ticket_event_count_reached',
                        [
                            'ticketname' => $ticketType->name,
                            'ticketamount' => $request->quantity,
                            'maxamount' => $ticketType->event->no_tickets_per_user,
                            'currentamount' => $ticketCount
                        ]
                    )
                );
                return Redirect::to("/events/{$ticketType->event->slug}");
            }
        }
        if ($ticketType->hasTicketGroup()) {
            $ticketCount = $user->getAllTicketsInTicketGroup($ticketType->event, $ticketType)->count();
            if ($ticketCount + $request->quantity > $ticketType->ticketGroup->tickets_per_user) {
                Session::flash(
                    'alert-danger',
                    __(
                        'tickets.max_ticket_group_count_reached',
                        [
                            'ticketname' => $ticketType->name,
                            'ticketamount' => $request->quantity,
                            'maxamount' => $ticketType->ticketGroup->tickets_per_user,
                            'ticketgroup' => $ticketType->ticketGroup->name,
                            'currentamount' => $ticketCount
                        ]
                    )
                );
                return Redirect::to("/events/{$ticketType->event->slug}");
            }
        }
        $user_event_tickets = $user->getAllTicketsOfType($ticketType->event, $ticketType)->count();
        if (
            is_numeric($ticketType->no_tickets_per_user) &&
            $ticketType->no_tickets_per_user > 0 &&
            $user_event_tickets + $request->quantity > $ticketType->no_tickets_per_user
        ) {
            Session::flash(
                'alert-danger',
                __(
                    'tickets.max_ticket_type_count_reached',
                    [
                        'ticketname' => $ticketType->name,
                        'ticketamount' => $request->quantity,
                        'maxamount' => $ticketType->no_tickets_per_user,
                        'currentamount' => $user_event_tickets,
                        'maxticketcount' => $ticketType->no_tickets_per_user,
                    ]
                )
            );
            return Redirect::to('/events/' . $ticketType->event->slug);
        }

        $params = [
            'tickets' => [
                $ticketType->id => $request->quantity,
            ],
        ];
        Session::put(Settings::getOrgName() . '-basket', $params);
        Session::save();
        return Redirect::to('/payment/checkout');
    }

    /**
     * Retrieve ticket via QR code
     * @param  Ticket $ticket
     * @return RedirectResponse
     */
    public function retrieve(Ticket $ticket)
    {
        $user = Auth::user();
        if ($user->admin == 1) {
            return Redirect::to('/admin/events/' . $ticket->event->slug . '/participants/' . $ticket->id); // redirect to site
        }
        return Redirect::to('/events/' . $ticket->event_id); // redirect to site
    }
}
