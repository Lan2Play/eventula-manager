<?php

namespace App\Http\Controllers\Api\Events;

use DB;
use Auth;

use App\Event;
use App\EventTimetable;
use App\EventTimetableData;
use App\Ticket;
use App\EventParticipantType;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EventsController extends Controller
{
    /**
     * Show Events
     * @return View
     */
    public function index()
    {
        $return = array();
        foreach (Event::all() as $event) {
            $return[] = [
                'name' => $event->display_name,
                'capacity' => $event->capacity,
                'start' => $event->start,
                'end' => $event->end,
                'desc' => [
                    'short' => $event->desc_short,
                    'long' => $event->desc_long,
                ],
                'address' => [
                    'line_1' => $event->venue->address_1,
                    'line_2' => $event->venue->address_2,
                    'street' => $event->venue->address_street,
                    'city' => $event->venue->address_city,
                    'postcode' => $event->venue->address_postcode,
                    'country' => $event->venue->address_country,
                ],
                'api' => [
                    'base' => 'http://' . $_SERVER['HTTP_HOST'] . '/api/events/' . $event->slug,
                    'tickets' => '/tickets',
                    'participants' => '/participants',
                    'timetables' => '/timetables',
                    'tournaments' => '/tournaments',
                ],
                'url' => [
                    'base' => 'http://' . $_SERVER['HTTP_HOST'] . '/events/' . $event->slug,
                    'tickets' => '#tickets',
                    'participants' => '#participants',
                    'timetables' => '#timetables',
                    'tournaments' => '#tournaments',
                ],
                'eventula_tags' => $event->tags()->pluck('tag_id'),
            ];
        }
        return $return;
    }

    /**
     * Show Upcoming Events
     * @return View
     */
    public function showUpcoming()
    {
        foreach (Event::where('start', '>', \Carbon\Carbon::today())->get() as $event) {
            $return[] = [
                'name' => $event->display_name,
                'capacity' => $event->capacity,
                'start' => $event->start,
                'end' => $event->end,
                'desc' => [
                    'short' => $event->desc_short,
                    'long' => $event->desc_long,
                ],
                'address' => [
                    'line_1' => $event->venue->address_1,
                    'line_2' => $event->venue->address_2,
                    'street' => $event->venue->address_street,
                    'city' => $event->venue->address_city,
                    'postcode' => $event->venue->address_postcode,
                    'country' => $event->venue->address_country,
                ],
                'api' => [
                    'base' => 'http://' . $_SERVER['HTTP_HOST'] . '/api/events/' . $event->slug,
                    'tickets' => '/tickets',
                    'participants' => '/participants',
                    'timetables' => '/timetables',
                    'tournaments' => '/tournaments',
                ],
                'url' => [
                    'base' => 'http://' . $_SERVER['HTTP_HOST'] . '/events/' . $event->slug,
                    'tickets' => '#tickets',
                    'participants' => '#participants',
                    'timetables' => '#timetables',
                    'tournaments' => '#tournaments',
                ],
            ];
        }
        return $return;
    }
    
    /**
     * Show Event
     * @param  $event
     * @return Event
     */
    public function show($event)
    {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }

        if (!$event) {
            abort(404, "Event not found.");
        }

        $return = [
            'name' => $event->display_name,
            'capacity' => $event->capacity,
            'start' => $event->start,
            'end' => $event->end,
            'desc' => [
                'short' => $event->desc_short,
                'long' => $event->desc_long,
            ],
            'address' => [
                'line_1' => $event->venue->address_1,
                'line_2' => $event->venue->address_2,
                'street' => $event->venue->address_street,
                'city' => $event->venue->address_city,
                'postcode' => $event->venue->address_postcode,
                'country' => $event->venue->address_country,
            ],
            'api' => [
                'base' => 'http://' . $_SERVER['HTTP_HOST'] . '/api/events/' . $event->slug,
                'tickets' => '/tickets',
                'participants' => '/participants',
                'timetables' => '/timetables',
                'tournaments' => '/tournaments',
            ],
            'url' => [
                'base' => 'http://' . $_SERVER['HTTP_HOST'] . '/events/' . $event->slug,
                'tickets' => '#tickets',
                'participants' => '#participants',
                'timetables' => '#timetables',
                'tournaments' => '#tournaments',
            ],
        ];
        return $return;
    }
}
