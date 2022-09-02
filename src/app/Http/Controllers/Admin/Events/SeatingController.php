<?php

namespace App\Http\Controllers\Admin\Events;

use DB;
use Session;
use Storage;

use App\User;
use App\Event;
use App\EventTicket;
use App\EventSeating;
use App\EventSeatingPlan;
use app\EventSeatingPlanSeat;
use App\EventParticipant;
use App\EventParticipantType;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SeatingController extends Controller
{
    /**
     * Show Seating Index Page
     * @param  Event  $event
     * @return View
     */
    public function index(Event $event)
    {
        return view('admin.events.seating.index')
            ->withEvent($event)
            ->withSeatingPlans($event->seatingPlans()->paginate(10));
    }

    /**
     * Show Seating Page
     * @param  Event            $event
     * @param  EventSeatingPlan $seatingPlan
     * @return View
     */
    public function show(Event $event, EventSeatingPlan $seatingPlan)
    {
        return view('admin.events.seating.show')
            ->withEvent($event)
            ->withSeatingPlan($seatingPlan)
            ->withSeats($seatingPlan->seats()->where('status', 'ACTIVE')->paginate(15, ['*'], 'se'));
    }

    /**
     * Add Seating Plan to Database
     * @param  Event   $event
     * @param  Request $request
     * @return Redirect
     */
    public function store(Event $event, Request $request)
    {
        $rules = [
            "name"      => "required",
            "columns"   => "required|integer",
            "rows"      => "required|integer|max:26",
            'image'     => 'image',
        ];
        $messages = [
            'name.required'     => 'Name is required',
            'columns.required'  => 'Columns is required',
            'columns.integer'   => 'Columns must be a number',
            'rows.required'     => 'Rows is required',
            'rows.max'          => 'Max. 26 Rows are allowed',
            'rows.integer'      => 'Rows must be a number',
            'image.image'       => 'Seating image must be a image',
        ];
        $this->validate($request, $rules, $messages);

        $seatingPlan                = new EventSeatingPlan();
        $seatingPlan->event_id      = $event->id;
        $seatingPlan->name          = $request->name;
        $seatingPlan->name_short    = @$request->name_short;

        $alphabet = range('A', 'Z');
        for ($i = 0; $i < $request->rows; $i++) {
            $seatingHeaders[] = $alphabet[$i];
        }
        $seatingPlan->headers  = implode(',', $seatingHeaders);
        $seatingPlan->columns  = $request->columns;
        $seatingPlan->rows     = $request->rows;

        if ($request->file('image') !== null) {
            $seatingPlan->image_path = str_replace(
                'public/',
                '/storage/',
                Storage::put(
                    'public/images/events/' . $event->slug . '/seating/' . $seatingPlan->slug,
                    $request->file('image')
                )
            );
        }

        if (!$seatingPlan->save()) {
            Session::flash('alert-danger', 'Could not save Seating Plan!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully saved Seating Plan!');
        return Redirect::to('admin/events/' . $event->slug . '/seating/' . $seatingPlan->slug);
    }

    /**
     * Update Seating Plan
     * @param  Event            $event
     * @param  EventSeatingPlan $seatingPlan
     * @param  Request          $request
     * @return Redirect
     */
    public function update(Event $event, EventSeatingPlan $seatingPlan, Request $request)
    {
        $rules = [
            "columns"   => "integer",
            "rows"      => "integer|max:26",
            'image'     => 'image',
            'status'    => 'in:draft,preview,published',
            'locked'    => 'boolean',
            'name'      => 'filled',
        ];
        $messages = [
            'columns.integer'   => 'Columns must be a number',
            'rows.integer'      => 'Rows must be a number',
            'rows.max'          => 'Max. 26 Rows are allowed',
            'image.image'       => 'Seating image must be a image',
            'status.in'         => 'Status must be draft or published',
            'locked.boolean'    => 'Locked must be a boolean',
            'name.filled'       => 'Name cannout be empty',
        ];
        $this->validate($request, $rules, $messages);

        if (isset($request->name)) {
            $seatingPlan->name          = $request->name;
        }

        if (isset($request->name_short)) {
            $seatingPlan->name_short        = $request->name_short;
        }

        if (isset($request->status)) {
            $seatingPlan->status        = $request->status;
        }

        $seatingPlan->locked            = ($request->locked ? true : false);

        if (isset($request->rows) || isset($request->columns)) {
            if ($seatingPlan->columns != $request->columns || $seatingPlan->rows != $request->rows) {
                $alphabet = range('A', 'Z');
                for ($i = 0; $i < $request->rows; $i++) {
                    $seatingHeaders[]   = $alphabet[$i];
                }
                $seatingPlan->headers   = implode(',', $seatingHeaders);
                $seatingPlan->columns   = $request->columns;
                $seatingPlan->rows      = $request->rows;
            }
        }

        if ($request->file('image') !== null) {
            Storage::delete($seatingPlan->image_path);
            $seatingPlan->image_path = str_replace(
                'public/',
                '/storage/',
                Storage::put(
                    'public/images/events/' . $event->slug . '/seating/' . $seatingPlan->slug,
                    $request->file('image')
                )
            );
        }

        if (!$seatingPlan->save()) {
            Session::flash('alert-danger', 'Could not update Seating Plan!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated Seating Plan!');
        return Redirect::back();
    }

    /**
     * Delete Seating Plan
     * @param  Event            $event
     * @param  EventSeatingPlan $seatingPlan
     * @return Redirect
     */
    public function destroy(Event $event, EventSeatingPlan $seatingPlan)
    {
        if (!$seatingPlan->delete()) {
            Session::flash('alert-danger', 'Could not delete Seating Plan!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted Seating Plan!');
        return Redirect::to('/admin/events/' . $event->slug . '/seating');
    }

    /**
     * Seat Participant
     * @param  Event            $event
     * @param  EventSeatingPlan $seatingPlan
     * @param  Request          $request
     * @return Redirect
     */
    public function storeSeat(Event $event, EventSeatingPlan $seatingPlan, Request $request)
    {

        if (
            !in_array(substr($request->seat_number_modal, 0, 1), explode(',', $seatingPlan->headers)) ||
            substr($request->seat_number_modal, 1, 1) <= 0 ||
            substr($request->seat_number_modal, 1, 1) > $seatingPlan->columns
        ) {
            Session::flash('alert-danger', 'Invalid seat selection!');
            return Redirect::back();
        }

        if (!isset($request->seat_status_select_modal) || trim($request->seat_status_select_modal) == '') {
            Session::flash('alert-danger', 'A status has to be selected!');
            return Redirect::back();
        }

        if (isset($request->participant_id_modal) && trim($request->participant_id_modal) != '') {
            $clauses = ['event_participant_id' => $request->participant_id_modal];
            $previousSeat = EventSeating::where($clauses)->first();
            if ($previousSeat != null && $previousSeat->status == 'ACTIVE' && strtoupper($request->seat_status_select_modal) == 'ACTIVE') {
                $previousSeat->delete();
            }
        }

        if (isset($request->participant_select_modal) && trim($request->participant_select_modal) != '') {
            $clauses = ['id' => $request->participant_select_modal];
            $participant = EventParticipant::where($clauses)->first();
            if (($participant->ticket && !$participant->ticket->seatable)) {
                Session::flash('alert-danger', 'Ticket is not eligible for a seat!');
                return Redirect::back();
            }
        }

        $clauses = ['event_participant_id' => $request->participant_select_modal];
        $previousSeat = EventSeating::where($clauses)->first();
        if ($previousSeat != null && $previousSeat->status == 'ACTIVE' &&  strtoupper($request->seat_status_select_modal) == 'ACTIVE') {
            $previousSeat->delete();
        }

        $clauses = ['seat' => $request->seat_number_modal, 'event_seating_plan_id' => $seatingPlan->id];
        $seat = EventSeating::where($clauses)->first();
        if ($seat != null) {
            Session::flash('alert-danger', 'Seat is still occupied. Please try again!');
            return Redirect::back();
        }

        //if status is set to active, the seat has to be paired with an event participant
        if ((!isset($request->participant_select_modal) && trim($request->participant_select_modal) == '')
            &&  strtoupper($request->seat_status_select_modal) == 'ACTIVE'
        ) {
            Session::flash('alert-danger', 'Seat can not be active and not have an event participant!');
            return Redirect::back();
        }

        //if an participant is selected for seating, status can't be inactive
        if ((isset($request->participant_select_modal) && trim($request->participant_select_modal) != '')
            &&  strtoupper($request->seat_status_select_modal) == 'INACTIVE'
        ) {
            Session::flash('alert-danger', 'Seat for event participants can not be inactive!');
            return Redirect::back();
        }

        $newSeat                         = new EventSeating();
        $newSeat->seat                   = $request->seat_number_modal;
        $newSeat->event_participant_id   = $request->participant_select_modal;
        $newSeat->event_seating_plan_id  = $seatingPlan->id;
        $newSeat->status                 = $request->seat_status_select_modal;

        if (!$newSeat->save()) {
            Session::flash('alert-danger', 'Could not update Seat!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated Seat!');
        return Redirect::back();
    }

    /**
     * Remove Participant Seating
     * @param  Event            $event
     * @param  EventSeatingPlan $seatingPlan
     * @param  Request          $request
     * @return Redirect
     */
    public function destroySeat(Event $event, EventSeatingPlan $seatingPlan, Request $request)
    {
        if (!$seat = $seatingPlan->seats()->where('seat', $request->seat_number)->first()) {
            Session::flash('alert-danger', 'Could not find seat!');
            return Redirect::back();
        }

        if (!$seat->delete()) {
            Session::flash('alert-danger', 'Could not clear seat!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Seat Updated!');
        return Redirect::back();
    }
}
