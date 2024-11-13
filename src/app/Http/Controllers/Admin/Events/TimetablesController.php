<?php

namespace App\Http\Controllers\Admin\Events;

use DB;
use Session;

use App\User;
use App\Event;
use App\EventTicket;
use App\EventSeating;
use App\EventTimetable;
use App\EventTimetableData;
use App\EventParticipant;
use App\EventParticipantType;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TimetablesController extends Controller
{
    /**
     * Show Timetables Index Page
     * @param  Event  $event
     * @return View
     */
    public function index(Event $event)
    {
        return view('admin.events.timetables.index')
            ->with('event', $event);
    }

    /**
     * Show Timetable Page
     * @param  Event          $event
     * @param  EventTimetable $timetable
     * @return View
     */
    public function show(Event $event, EventTimetable $timetable)
    {
        $timetable->data = $timetable->data->sortBy('start_time');
        return view('admin.events.timetables.show')
            ->with('event', $event)
            ->with('timetable', $timetable);
    }

    /**
     * Store Timetable to Database
     * @param  Event   $event
     * @param  Request $request
     * @return Redirect
     */
    public function store(Event $event, Request $request)
    {
        $rules = [
            'name' => 'required',
        ];
        $messages = [
            'name.required' => 'Name is required',
        ];
        $this->validate($request, $rules, $messages);

        $timetable              = new EventTimetable();
        $timetable->name        = $request->name;
        $timetable->event_id    = $event->id;

        if (!$timetable->save()) {
            Session::flash('alert-danger', 'Cannot save Timetable!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully saved Timetable!');
        return Redirect::to('admin/events/' . $event->slug . '/timetables/' . $timetable->slug);
    }

    /**
     * Update Timetable
     * @param  Event          $event
     * @param  EventTimetable $timetable
     * @param  Request        $request
     * @return redirect
     */
    public function update(Event $event, EventTimetable $timetable, Request $request)
    {
        $rules = [
            'name'      => 'filled',
            'status'    => 'in:DRAFT,PUBLISHED',
            'primary'   => 'boolean',
        ];
        $messages = [
            'name.filled'       => 'Name cannot be empty',
            'status.in'         => 'Status must be DRAFT or PUBLISHED',
            'primary.boolean'   => 'Primary must be boolean',
        ];
        $this->validate($request, $rules, $messages);

        if (isset($request->name)) {
            $timetable->name    = $request->name;
        }

        if (isset($request->status)) {
            $timetable->status  = $request->status;
        }

        $timetable->primary     = ($request->primary ? true : false);

        if (!$timetable->save()) {
            Session::flash('alert-danger', 'Cannot update Timetable!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated Timetable!');
        return Redirect::back();
    }

    /**
     * Delete Timetable from Database
     * @param  Event          $event
     * @param  EventTimetable $timetable
     * @param  Request        $request
     * @return Redirect
     */
    public function destroy(Event $event, EventTimetable $timetable, Request $request)
    {
        if (!$timetable->delete()) {
            Session::flash('alert-danger', 'Cannot delete Timetable!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted Timetable!');
        return Redirect::back();
    }
}
