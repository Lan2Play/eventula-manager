<?php

namespace App\Http\Controllers\Admin\Events;

use Session;


use App\Event;
use App\EventTimetable;
use App\EventTimetableData;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TimetableDataController extends Controller
{
    /**
     * Store Timetable Data to Database
     * @param  Event          $event
     * @param  EventTimetable $timetable
     * @param  Request        $request
     * @return Redirect
     */
    public function store(Event $event, EventTimetable $timetable, Request $request)
    {
        $rules = [
            'name'          => 'required',
            'start_time'    => 'required'
        ];
        $messages = [
            'name.required'         => 'Name is required',
            'start_time.required'   => 'Start Time is required',
        ];
        $this->validate($request, $rules, $messages);

        $data                       = new EventTimetableData();
        $data->event_timetable_id   = $timetable->id;
        $data->start_time           = $request->start_time;
        $data->name                 = $request->name;
        $data->desc                 = $request->desc;
        
        if (!$data->save()) {
            Session::flash('alert-danger', 'Cannot save Timetable Slot!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully saved Timetable Slot!');
        return Redirect::back();
    }

    /**
     * Update Timetable Data
     * @param  Event              $event
     * @param  EventTimetable     $timetable
     * @param  EventTimetableData $data
     * @param  Request            $request
     * @return Redirect
     */
    public function update(Event $event, EventTimetable $timetable, EventTimetableData $data, Request $request)
    {
        $rules = [
            'name'          => 'filled',
            'start_time'    => 'filled'
        ];
        $messages = [
            'name.filled'       => 'Name cannot be empty',
            'start_time.filled' => 'Start Time cannot be empty',
        ];
        $this->validate($request, $rules, $messages);

        $data->start_time   = $request->start_time;
        $data->name         = $request->name;
        $data->desc         = $request->desc;

        if (!$data->save()) {
            Session::flash('alert-danger', 'Cannot update Timetable Slot!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated Timetable Slot!');
        return Redirect::back();
    }

    /**
     * Destroy Timetable Data
     * @param  Event              $event
     * @param  EventTimetable     $timetable
     * @param  EventTimetableData $data
     */
    public function destroy(Event $event, EventTimetable $timetable, EventTimetableData $data)
    {
        try {
            $data->delete();
            Session::flash('alert-success', 'Successfully deleted Timetable Slot!');
        } catch (\Exception $e) {
            // Log the exception message
            \Log::error('Error deleting Timetable Slot: ' . $e->getMessage());
            Session::flash('alert-danger', 'Cannot delete Timetable Slot!');
        }
        return Redirect::back();
    }
}
