<?php

namespace App\Http\Controllers\Events;

use App\Ticket;
use App\TicketType;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Session;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TicketController extends Controller
{
    /**
     * Gift Ticket
     * @param  Ticket $ticket
     * @param  Request          $request
     * @return Redirect
     */
    public function gift(Ticket $ticket, Request $request)
    {
        if ($ticket->gift != true && $ticket->gift_sendee == null) {
            $ticket->gift = true;
            $ticket->gift_accepted = false;
            $ticket->gift_accepted_url = "gift_" . Str::random();
            $ticket->gift_sendee = $ticket->user_id;
            if ($ticket->save()) {
                $request->session()->flash(
                    'alert-success',
                    'Ticket gifted Successfully! - Give your friend the URL below.'
                );
                return Redirect::back();
            }
            $request->session()->flash('alert-danger', 'Somthing went wrong. Please try again later.');
            return Redirect::back();
        }
        $request->session()->flash('alert-danger', 'This Ticket has already Gifted.');
        return Redirect::back();
    }

    /**
     * Revoke Gifted Ticket
     * @param  Ticket $ticket
     * @param  boolean          $accepted
     * @return Redirect
     */
    public function revokeGift(Ticket $ticket, $accepted = false)
    {
        if ($ticket->gift == true) {
            if ($ticket->gift_accepted != true) {
                if ($accepted !== true) {
                    $ticket->gift = null;
                    $ticket->gift_accepted = null;
                    $ticket->gift_sendee = null;
                }
                $ticket->gift_accepted_url = null;
                if ($ticket->save()) {
                    Session::flash('alert-success', 'Ticket gift revoked Successfully!');
                    return Redirect::back();
                }
            }
        }
        Session::flash('alert-danger', 'This Ticket is already Gifted.');
        return Redirect::back();
    }

    /**
     * Accept Gifted Ticket
     * @param  Request $request
     * @return Redirect
     */
    public function acceptGift(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $ticket = Ticket::where(['gift_accepted_url' => $request->url])->first();
            if ($ticket != null) {

                /* check if maximum count of tickets for event ticketType is already owned */
                $clauses = ['id' => $ticket->ticket_id, 'event_id' => $ticket->event_id];
                $ticketType = TicketType::where($clauses)->get()->first();

                $no_of_owned_tickets = 0;

                $eventParticipants = $user->getAllTickets($ticket->event_id);
                foreach ($eventParticipants as $eventParticipant){
                    if ($ticketType->id = $eventParticipant->ticket_id){
                        $no_of_owned_tickets++;
                    }
                }

                if ($no_of_owned_tickets + 1 <= $ticketType->no_tickets_per_user) {
                    $ticket->gift_accepted = true;
                    $ticket->user_id = $user->id;
                    $ticket->gift_accepted_url = null;
                    if ($ticket->save()) {
                        $request->session()->flash(
                            'alert-success',
                            'Gift Successfully accepted! Please visit the event page to pick a seat'
                        );
                        return Redirect::to('account');
                    }
                    $request->session()->flash('alert-danger', 'Something went wrong. Please try again later.');
                    return Redirect::to('account');
                }
                $request->session()->flash('alert-danger', "You already own the maximum allowed number of event ticketType: '" .$ticketType->name. "'.");
                return Redirect::to('account');
            }
            $request->session()->flash('alert-danger', 'Redemption code not found.');
            return Redirect::to('account');
        }
        $request->session()->flash('alert-danger', 'Please Login.');
        return Redirect::to('login');
    }

    public function exportParticipantAsFile(Ticket $ticket, string $fileType): Response|StreamedResponse {
        $user = Auth::user();
        if ($user->id != $ticket->user_id) {
            $viewErrorBag = (new ViewErrorBag())->put('default',
                new MessageBag([
                    0 => [__('tickets.not_allowed')]
                ])
            );
            return response()->view('errors.403', ['errors' => $viewErrorBag], Response::HTTP_FORBIDDEN);
        }

        /** @var ResponseFactory $response */
        switch (strtolower($fileType)) {
            case 'pdf':
                $response = response()->stream(
                    function () use ($ticket) {
                        echo $ticket->getPdf();
                    },
                    Response::HTTP_OK,
                    [
                        'Content-Type' => 'application/pdf'
                    ]
                );
                break;
            default:
                $viewErrorBag = (new ViewErrorBag())->put('default',
                    new MessageBag([
                        0 => [__('tickets.wrong_file_format')]
                    ])
                );
                $response = response()->view('errors.404', ['errors' => $viewErrorBag], Response::HTTP_NOT_FOUND);
        }
        return $response;
    }
}