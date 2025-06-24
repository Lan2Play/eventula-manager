<?php

namespace App\Http\Controllers\Admin\Events;

use DB;
use Auth;
use Session;

use App\User;
use App\Event;
use App\Ticket;
use App\TicketType;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ParticipantsController extends Controller
{
    /**
     * Show Participants Index Page
     * @param  Event  $event
     * @return View
     */
    public function index(Event $event)
    {
        return view('admin.events.participants.index')
            ->with('event', $event)
            ->with('participants', $event->allEventParticipants()->paginate(20));
    }

    /**
     * Show Participants Page
     * @param  Event            $event
     * @param  Ticket $participant
     * @return View
     */
    public function show(Event $event, Ticket $participant)
    {
        return view('admin.events.participants.show')
            ->with('event', $event)
            ->with('participant', $participant);
    }

    /**
     * Update Participant
     * @param  Event            $event
     * @param  Ticket $participant
     * @param  Request          $request
     */
    public function update(Event $event, Ticket $participant, Request $request)
    {
        //DEBUG
        dd('edit me');
    }

    /**
     * Sign in to Event
     * @param  Event            $event
     * @param  Ticket $participant
     * @return Redirect
     */
    public function signIn(Event $event, Ticket $participant)
    {
        if ($participant->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if ($participant->ticket && $participant->purchase->status != "Success") {
            Session::flash('alert-danger', 'Cannot sign in Participant because the payment is not completed!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
        }
        if ($participant->revoked) {
            Session::flash('alert-danger', 'Cannot sign in a revoked Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
        }
        if (!$participant->setSignIn()) {
            Session::flash('alert-danger', 'Cannot sign in Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
        }
        Session::flash('alert-success', 'Participant Signed in!');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    public function transfer(Event $event, Ticket $participant, Request $request)
    {
        if ($participant->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if ($participant->ticket && $participant->purchase->status != "Success") {
            Session::flash('alert-danger', 'Cannot sign in Participant because the payment is not completed!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
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
        if ($participant->signed_in) {
            Session::flash('alert-warning', 'Cannot tranfer Participant already signed in!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
        }
        if (!$participant->transfer($request->event_id)) {
            Session::flash('alert-danger', 'Cannot tranfer Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
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
        foreach ($event->tickets()->get() as $participant)
        {
            if (!$participant->setSignIn(false)) {
                Session::flash('alert-danger', 'Cannot sign out Participant! '. $participant->name);
                return Redirect::to('admin/events/' . $event->slug . '/participants/');
            }
        }
        Session::flash('alert-success', 'Participants signed out!');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    /**
     * Sign out a single participant for the event
     * @param  Event  $event
     * @param  Ticket $participant
     * @return View
     */
    public function signout(Event $event, Ticket $participant)
    {
        if ($participant->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if ($participant->revoked) {
            Session::flash('alert-danger', 'Cannot sign out a revoked Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
        }
        if (!$participant->setSignIn(false)) {
            Session::flash('alert-danger', 'Cannot sign out Participant! '. $participant->name);
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }

        Session::flash('alert-success', 'Participant ' . $participant->name . ' signed out!');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    function revoke(Event $event, Ticket $participant)
    {
        if ($participant->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if (!$participant->setRevoked()) {
            Session::flash('alert-danger', 'Cannot revoke Participant!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
        }
        Session::flash('alert-success', 'Participant has been revoked');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

    function delete(Event $event, Ticket $participant)
    {
        if ($participant->event->slug != $event->slug)
        {
            Session::flash('alert-danger', 'The selected participant does not belong to the selected event!');
            return Redirect::to('admin/events/' . $event->slug . '/participants/');
        }
        if (!$participant->delete()) {
            Session::flash('alert-danger', 'Cannot delete participant');
            return Redirect::to('admin/events/' . $event->slug . '/participants/' . $participant->id);
        }
        Session::flash('alert-success', 'Participants deleted');
        return Redirect::to('admin/events/' . $event->slug . '/participants/');
    }

}
