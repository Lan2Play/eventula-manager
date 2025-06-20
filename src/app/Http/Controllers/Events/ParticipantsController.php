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

class ParticipantsController extends Controller
{
    /**
     * Gift Ticket
     * @param  Ticket $participant
     * @param  Request          $request
     * @return Redirect
     */
    public function gift(Ticket $participant, Request $request)
    {
        if ($participant->gift != true && $participant->gift_sendee == null) {
            $participant->gift = true;
            $participant->gift_accepted = false;
            $participant->gift_accepted_url = "gift_" . Str::random();
            $participant->gift_sendee = $participant->user_id;
            if ($participant->save()) {
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
     * @param  Ticket $participant
     * @param  boolean          $accepted
     * @return Redirect
     */
    public function revokeGift(Ticket $participant, $accepted = false)
    {
        if ($participant->gift == true) {
            if ($participant->gift_accepted != true) {
                if ($accepted !== true) {
                    $participant->gift = null;
                    $participant->gift_accepted = null;
                    $participant->gift_sendee = null;
                }
                $participant->gift_accepted_url = null;
                if ($participant->save()) {
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
            $participant = Ticket::where(['gift_accepted_url' => $request->url])->first();
            if ($participant != null) {

                /* check if maximum count of tickets for event ticket is already owned */
                $clauses = ['id' => $participant->ticket_id, 'event_id' => $participant->event_id];
                $ticket = TicketType::where($clauses)->get()->first();

                $no_of_owned_tickets = 0;

                $eventParticipants = $user->getAllTickets($participant->event_id);
                foreach ($eventParticipants as $eventParticipant){
                    if ($ticket->id = $eventParticipant->ticket_id){
                        $no_of_owned_tickets++;
                    }
                }

                if ($no_of_owned_tickets + 1 <= $ticket->no_tickets_per_user) {
                    $participant->gift_accepted = true;
                    $participant->user_id = $user->id;
                    $participant->gift_accepted_url = null;
                    if ($participant->save()) {
                        $request->session()->flash(
                            'alert-success',
                            'Gift Successfully accepted! Please visit the event page to pick a seat'
                        );
                        return Redirect::to('account');
                    }
                    $request->session()->flash('alert-danger', 'Something went wrong. Please try again later.');
                    return Redirect::to('account');
                }
                $request->session()->flash('alert-danger', "You already own the maximum allowed number of event ticket: '" .$ticket->name. "'.");
                return Redirect::to('account');
            }
            $request->session()->flash('alert-danger', 'Redemption code not found.');
            return Redirect::to('account');
        }
        $request->session()->flash('alert-danger', 'Please Login.');
        return Redirect::to('login');
    }

    public function exportParticipantAsFile(Ticket $participant, string $fileType): Response|StreamedResponse {
        $user = Auth::user();
        if ($user->id != $participant->user_id) {
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
                    function () use ($participant) {
                        echo $participant->getPdf();
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