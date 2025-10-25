<?php

namespace App\Http\Controllers\Admin\Events;


use Illuminate\View\View;
use Session;

use App\Event;
use App\Ticket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TicketController extends Controller
{
    /**
     * Show Tickets Index Page
     * @param  Event  $event
     * @return View
     */
    public function index(Event $event, Request $request)
    {
        $query = $event->allEventTickets();

        // Filter by sign-in status
        $signedIn = $request->query->get('signed_in', 'any');
        if ($signedIn !== '' && $signedIn !== 'any') {
            if ($signedIn == 'yes') {
                $query->where('signed_in', true);
            } elseif ($signedIn == 'no') {
                $query->where('signed_in', false);
            }
        }

        // Filter by payment status
        $paymentFilter = $request->query->get('payment','none');

        switch ($paymentFilter) {
            case 'success':
                // Paid tickets: has purchase with SUCCESS status
                $query->whereHas('purchase', function ($q) {
                    $q->where('status', \App\Purchase::STATUS_SUCCESS);
                });
                break;

            case 'free':
                // Free/Staff/Gift tickets
                $query->where(function ($q) {
                    $q->where('free', true)
                        ->orWhere('staff', true)
                        ->orWhere('gift', true);
                });
                break;

            case 'unpaid':
                // Unpaid tickets: has purchase but not SUCCESS, or has no purchase and is not free/staff/gift
                $query->where(function ($q) {
                    $q->whereHas('purchase', function ($subQ) {
                        $subQ->where('status', '!=', \App\Purchase::STATUS_SUCCESS);
                    })
                        ->orWhere(function ($subQ) {
                            $subQ->whereDoesntHave('purchase')
                                ->where('free', false)
                                ->where('staff', false)
                                ->where('gift', false);
                        });
                });
                break;
        }

        if ($request->query->has('search') && $request->query->get('search') != 'none') {
            $searchText = trim($request->query->get('search', ''));
            if ($searchText !== '') {
                $like = '%' . $searchText . '%';
                $query->where(function ($q) use ($like) {
                    // Search on related user fields
                    $q->whereHas('user', function ($uq) use ($like) {
                        $uq->where('username', 'like', $like)
                            ->orWhere('firstname', 'like', $like)
                            ->orWhere('surname', 'like', $like)
                            ->orWhere('email', 'like', $like)
                            ->orWhere('steamname', 'like', $like);
                    });
                });
            }
        }



        return view('admin.events.participants.index')
            ->with('event', $event)
            ->with('participants', $query->paginate(20));
    }

    /**
     * Show Tickets Page
     * @param  Event            $event
     * @param  Ticket $ticket
     * @return View
     */
    public function show(Event $event, Ticket $ticket)
    {
        //TODO change participant to ticket in the view
        return view('admin.events.participants.show')
            ->with('event', $event)
            ->with('participant', $ticket);
    }

    /**
     * Update Ticket
     * @param  Event            $event
     * @param  Ticket $ticket
     * @param  Request          $request
     * @return Redirect
     */
    public function update(Event $event, Ticket $ticket, Request $request)
    {
        if ($ticket->event->slug != $event->slug) {
            Session::flash('alert-danger', 'The selected ticket does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }

        $action = $request->input('action');

        if ($action === 'change_manager') {
            $ticket->manager_id = $request->input('manager_id');
            if (!$ticket->save()) {
                Session::flash('alert-danger', 'Failed to update ticket manager!');
                return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
            }
            Session::flash('alert-success', 'Ticket manager updated successfully!');
        } elseif ($action === 'change_user') {
            $ticket->user_id = $request->input('user_id');
            if (!$ticket->save()) {
                Session::flash('alert-danger', 'Failed to update ticket user!');
                return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
            }
            Session::flash('alert-success', 'Ticket user updated successfully!');
        } else {
            Session::flash('alert-warning', 'No action specified!');
        }

        return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
    }

    /**
     * Sign in to Event
     * @param  Event            $event
     * @param  Ticket $ticket
     * @return Redirect
     */
    public function signIn(Event $event, Ticket $ticket)
    {
        if ($ticket->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if ($ticket->revoked) {
            Session::flash('alert-danger', 'Cannot sign in a revoked Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
        }
        if ($ticket->purchase && $ticket->purchase->status != \App\Purchase::STATUS_SUCCESS) {
            Session::flash('alert-danger', 'Cannot sign in a Participant that has not paid!');
            return Redirect::to('admin/purchases/' . $ticket->purchase->id);
        }
        if (!$ticket->setSignIn()) {
            Session::flash('alert-danger', 'Cannot sign in Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
        }
        Session::flash('alert-success', 'Participant Signed in!');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    public function transfer(Event $event, Ticket $ticket, Request $request)
    {
        if ($ticket->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if ($ticket->ticket && $ticket->purchase->status != "Success") {
            Session::flash('alert-danger', 'Cannot sign in Participant because the payment is not completed!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
        }
        $rules = [
            'event_id'  => 'required',
            'event_id'  => 'exists:events,id',
        ];
        $messages = [
            'event_id|required' => 'A Event ID is required.',
            'event_id|exists'   => 'A Event ID must exist.',
        ];
        $this->validate($request, $rules, $messages);
        if ($ticket->signed_in) {
            Session::flash('alert-warning', 'Cannot tranfer Participant already signed in!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
        }
        if (!$ticket->transfer($request->event_id)) {
            Session::flash('alert-danger', 'Cannot tranfer Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
        }
        Session::flash('alert-success', 'Participant Transferred!');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    /**
     * Sign out all participants for the event
     * @param  Event  $event
     * @return View
     */
    public function signoutall(Event $event)
    {
        foreach ($event->tickets()->get() as $ticket)
        {
            if (!$ticket->setSignIn(false)) {
                Session::flash('alert-danger', 'Cannot sign out Participant! '. $ticket->name);
                return Redirect::to('admin/events/' . $event->slug . '/participants/');
            }
        }
        Session::flash('alert-success', 'Participants signed out!');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    /**
     * Sign out a single participant for the event
     * @param  Event  $event
     * @param  Ticket $ticket
     * @return View
     */
    public function signout(Event $event, Ticket $ticket)
    {
        if ($ticket->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if ($ticket->revoked) {
            Session::flash('alert-danger', 'Cannot sign out a revoked Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
        }
        if (!$ticket->setSignIn(false)) {
            Session::flash('alert-danger', 'Cannot sign out Participant! '. $ticket->name);
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }

        Session::flash('alert-success', 'Participant ' . $ticket->name . ' signed out!');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    function revoke(Event $event, Ticket $ticket)
    {
        if ($ticket->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if (!$ticket->setRevoked()) {
            Session::flash('alert-danger', 'Cannot revoke Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
        }
        Session::flash('alert-success', 'Participant has been revoked');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    function delete(Event $event, Ticket $ticket)
    {
        if ($ticket->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if (!$ticket->delete()) {
            Session::flash('alert-danger', 'Cannot delete participant');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $ticket->id);
        }
        Session::flash('alert-success', 'Participants deleted');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

}
