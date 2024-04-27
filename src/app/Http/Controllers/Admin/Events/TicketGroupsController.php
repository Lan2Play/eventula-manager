<?php

namespace App\Http\Controllers\Admin\Events;

use App\Event;
use App\EventTicketGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Session;

class TicketGroupsController extends Controller
{
    protected function verifyRequestValues(Request $request): void
    {
        $rules = [
            'ticket-group-name' => 'required',
            'ticket-group-tickets' => 'required|numeric',
        ];
        $messages = [
            'ticket-group-name.required' => 'Ticket group name is required',
            'ticket-group-tickets.numeric' => 'Tickets per user must be a number',
            'ticket-group-tickets.required' => 'Tickets per user is required',
        ];
        $this->validate($request, $rules, $messages);
    }

    protected function updateTicketGroup(Request $request, EventTicketGroup $group): void
    {
        $group->name = $request->get('ticket-group-name');
        $group->tickets_per_user = $request->get('ticket-group-tickets');
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->verifyRequestValues($request);

        $group = new EventTicketGroup();
        $group->event_id = $event->id;
        $this->updateTicketGroup($request, $group);

        if (!$group->save()) {
            Session::flash('alert-danger', 'Ticket group could not be saved');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Ticket group saved successfully');
        return Redirect::to("/admin/events/{$event->slug}/tickets");
    }

    public function show(Event $event, EventTicketGroup $ticketGroup): View
    {
        return view('admin.events.ticketgroups.show')
            ->withEvent($event)
            ->withTicketGroup($ticketGroup);
    }

    public function update(Request $request, Event $event, EventTicketGroup $ticketGroup): RedirectResponse
    {
        $this->verifyRequestValues($request);
        $this->updateTicketGroup($request, $ticketGroup);

        if ($ticketGroup->save()) {
            Session::flash('alert-success', 'Ticket group updated successfully');
            return Redirect::to("/admin/events/{$event->slug}/tickets");
        }
        Session::flash('alert-danger', 'Ticket group could not be updated');

        return Redirect::back();
    }

    public function delete(Request $request, Event $event, EventTicketGroup $ticketGroup): RedirectResponse
    {
        if ($ticketGroup->delete()) {
            Session::flash('alert-success', "Ticket group \"{$ticketGroup->name}\" deleted successfully");
        } else {
            Session::flash('alert-danger', 'Ticket group could not be deleted');
        }

        return Redirect::back();
    }
}