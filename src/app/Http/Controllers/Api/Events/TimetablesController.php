<?php

namespace App\Http\Controllers\Api\Events;

use App\Event;

use App\EventParticipantType;
use App\EventTimetable;
use App\Http\Controllers\Controller;

class TimetablesController extends Controller
{

    /**
     * Show all Timetables
     * @param  $event
     * @return EventTimetables
     */
    public function index($event)
    {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }

        if (!$event) {
            abort(404, "Event not found.");
        }

        $event = Event::where('id', $event->id)->first();
        return $event->timetables;
    }

    /**
     * Show Timetable
     * @param  $event
     * @param  EventTimetable $timetable
     * @return EventTimetable
     */
    public function show($event, $timetable)
    {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }
        if (is_numeric($timetable)) {
            $timetable = $event->timetables()->with('data')->where('id', $timetable)->first();

        } else {
            $timetable = $event->timetables()->with('data')->where('slug', $timetable)->first();
        }

        if (!$event || !$timetable) {
            abort(404, "Event not found.");
        }

        return $timetable;
    }
}
