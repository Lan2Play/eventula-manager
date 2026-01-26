<?php

namespace App\Http\Controllers\Api\Events;

use App\Event;
use App\EventParticipantType;

use App\Http\Controllers\Controller;


class TournamentsController extends Controller
{

    /**
     * Show all Timetables
     * @param  $event
     * @return EventTournaments
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
        return $event->tournaments;
    }

    /**
     * Show Timetable
     * @param  $event
     * @param  EventTournament $tournament
     * @return EventTournament
     */
    public function show($event, $tournament)
    {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }
        if (is_numeric($tournament)) {
            $tournament = $event->tournaments()->where('id', $tournament)->first();

        } else {
            $tournament = $event->tournaments()->where('slug', $tournament)->first();
        }

        if (!$event || !$tournament) {
            abort(404, "Event not found.");
        }

        return $tournament;
    }

    public function showChallonge( $event, $tournament) {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }
        if (is_numeric($tournament)) {
            $tournament = $event->tournaments()->where('id', $tournament)->first();

        } else {
            $tournament = $event->tournaments()->where('slug', $tournament)->first();
        }

        if (!$event || !$tournament) {
            abort(404, "Event not found.");
        }
        return "https://challonge.com/de/" . $tournament->challonge_tournament_url . "/module";
    }
}
