<?php

namespace App\Http\Controllers\Events;

use DB;
use Auth;
use Session;
use Helpers;

use App\User;
use App\Event;
use App\TicketType;
use App\EventSeating;
use App\EventSeatingPlan;
use App\Ticket;
use App\EventParticipantType;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class SeatingController extends Controller
{
    /**
     * Show Seating
     * @param  Event  $event
     * @return SeatingPlans
     */
    public function show(Event $event)
    {
        $seatingArray = array();
        foreach ($event->participants as $participant) {
            array_push($seatingArray, array($participant->seat => $participant->user->username));
        }
        return json_encode($seatingArray);
    }

    /**
     * Seat Participant
     * @param  Event            $event
     * @param  EventSeatingPlan $seatingPlan
     * @param  Request          $request
     * @return Redirect
     */
    public function store(Event $event, EventSeatingPlan $seatingPlan, Request $request)
    {
        $rules = [
            'participant_id'    => 'required',
            'user_id'           => 'required',
            "seat_column"       => "required|integer",
            "seat_row"          => "required|integer|max:26",
        ];
        $messages = [
            'participant_id.required'   => 'A participant_id is required',
            'user_id.required'          => 'A user_id is required',
            'seat_column.required'      => 'A column is required',
            'seat_column.integer'       => 'Columns must be a number',
            'seat_row.required'         => 'A row is required',
            'seat_row.integer'          => 'Rows must be a number',
            'seat_row.max'              => 'Max. 26 Rows are allowed',
            
        ];
        
        $this->validate($request, $rules, $messages);
        
        $participant = $event->EventParticipants()->where('id', $request->participant_id)->first();

        if ($participant->ticket && !$participant->ticket->seatable) {
            // Ticket not seatable
            Session::flash('alert-danger', 'That ticket is not seatable');
            return Redirect::to('events/' . $event->slug);
        }
        if ($participant->seat != null) {
            $participant->seat()->delete();
        }
        //Unseated ticket found
        if (!$event->getSeat($seatingPlan->id, $request->seat_column, $request->seat_row)) {
            //Seat does not Exists
            $newSeat                           = new EventSeating();
            $newSeat->column                   = $request->seat_column; 
            $newSeat->row                      = $request->seat_row;
            $newSeat->event_participant_id     = $participant->id;
            $newSeat->event_seating_plan_id    = $seatingPlan->id;
            $newSeat->save();
            $request->session()->flash(
                'alert-success',
                'You have been successfully assigned to seat ' . $newSeat->getName() . ' in plan ' . $seatingPlan->name . '!'
            );
            return Redirect::to('events/' . $event->slug);
        }
        $seat = $event->getSeat($seatingPlan->id, $request->seat_column, $request->seat_row);
        $request->session()->flash('alert-danger', 'Seat ' . $seat->getName() . ' in plan ' . $seatingPlan->name . ' is alredy taken');
        return Redirect::to('events/' . $event->slug);
    }

    /**
     * Remove Participant Seating
     * @param  Event            $event
     * @param  EventSeatingPlan $seatingPlan
     * @param  Request          $request
     * @return Redirect
     */
    public function destroy(Event $event, EventSeatingPlan $seatingPlan, Request $request)
    {
        $rules = [
            'participant_id'        => 'required',
            'seat_column_delete'    => 'required',
            'seat_row_delete'       => 'required',
        ];
        $messages = [
            'participant_id|required'       => 'A participant_id is required',
            'seat_column_delete|required'   => 'A seat column is required',
            'seat_row_delete|required'      => 'A seat row is required',
            
        ];

        $this->validate($request, $rules, $messages);
        
        $clauses = [
            'event_participant_id'  => $request->participant_id,
            'column'                => $request->seat_column_delete,    
            'row'                   => $request->seat_row_delete,
            'event_seating_plan_id' => $seatingPlan->id
        ];

        if (!$seat = $seatingPlan->seats()->where($clauses)->first()) {
            Session::flash('alert-danger', 'Could not find seating');
            return Redirect::back();
        }
        
        $seatName = $seat->getName();
        $seatingPlanName = $seatingPlan->name;
        
        if (!$seat->delete()) {
            Session::flash('alert-danger', 'Could not remove seating ' . $seatName . ' from plan ' . $seatingPlanName . '!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully removed seating '. $seatName . ' from plan ' . $seatingPlanName . '!');
        return Redirect::back();
    }
}
