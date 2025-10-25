<?php

namespace App\Http\Controllers\Events;

use App\Events\Event;
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

    /**
     * Resets the manager of a ticket to the owner
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetManager($event, Ticket $ticket, Request $request) {
        $user = Auth::user();
        if( $user->id == $ticket->owner_id || $user->getAdmin() ) {
            $ticket->manager_id = $ticket->owner_id;
            $ticket->save();
            return Redirect::back();
        }
        return Redirect::back();
    }

    /**
     * Resets the user of a ticket to the owner
     * @param Ticket $ticket
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetUser($event, Ticket $ticket, Request $request) {
        $user = Auth::user();
        if( $user->id == $ticket->owner_id || $user->getAdmin() ) {
            $ticket->user_id = $ticket->owner_id;
            $ticket->save();
            return Redirect::back();
        }
        return Redirect::back();
    }

    /**
     * Update Participant
     * @param  Event            $event
     * @param  Ticket $ticket
     * @param  Request          $request
     * @return Redirect
     */
    public function update($event, Ticket $ticket, Request $request)
    {
        $user = Auth::user();

        // Check if the user has permission to update this ticket
        $isOwner = $user->id == $ticket->owner_id;
        $isManager = $user->id == $ticket->manager_id;
        $isAdmin = $user->getAdmin();

        if (!$isOwner && !$isManager && !$isAdmin) {
            Session::flash('alert-danger', 'You do not have permission to update this ticket!');
            return Redirect::back();
        }

        // Check if the ticket belongs to the event
        if ($ticket->event->slug != $event) {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::back();
        }

        $action = $request->input('action');
        if ($action === 'change_user' && $ticket->signed_in && !$isAdmin ) {
            Session::flash('alert-danger', 'You cannot change the user of a signed in ticket!');
            return Redirect::back();
        }

        if ($action === 'change_manager') {
            // Only owner can change the manager
            if (!$isOwner && !$isAdmin) {
                Session::flash('alert-danger', 'Only the ticket owner can change the manager!');
                return Redirect::back();
            }

            $managerId = $request->input('manager_id');

            // Validate that the manager_id exists in users table
            if ($managerId && !\App\User::where('id', $managerId)->exists()) {
                Session::flash('alert-danger', 'The selected manager does not exist!');
                return Redirect::back();
            }

            $ticket->manager_id = $managerId;
            if (!$ticket->save()) {
                Session::flash('alert-danger', 'Failed to update ticket manager!');
                return Redirect::back();
            }
            Session::flash('alert-success', 'Ticket manager updated successfully!');
        } elseif ($action === 'change_user') {
            // Manager and owner can change user_id
            if (!$isOwner && !$isManager && !$isAdmin) {
                Session::flash('alert-danger', 'Only the ticket owner or manager can change the user!');
                return Redirect::back();
            }

            $userId = $request->input('user_id');

            // Validate that the user_id exists in users table
            if (!$userId || !\App\User::where('id', $userId)->exists()) {
                Session::flash('alert-danger', 'The selected user does not exist!');
                return Redirect::back();
            }

            // Make sure owner_id is set if it's not already
            if (!$ticket->owner_id) {
                $ticket->owner_id = $ticket->user_id;
            }

            $ticket->user_id = $userId;
            if (!$ticket->save()) {
                Session::flash('alert-danger', 'Failed to update ticket user!');
                return Redirect::back();
            }
            Session::flash('alert-success', 'Ticket user updated successfully!');
        } else {
            Session::flash('alert-warning', 'No action specified!');
        }

        return Redirect::back();
    }

    public function exportParticipantAsFile(Ticket $ticket, string $fileType): Response|StreamedResponse {
        $user = Auth::user();
        
        // Check if the user has permission to export this ticket
        $hasPermission = $user->id == $ticket->user_id || 
                         $user->id == $ticket->manager_id || 
                         $user->id == $ticket->owner_id ||
                         $user->getAdmin();
        if (!$hasPermission) {
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