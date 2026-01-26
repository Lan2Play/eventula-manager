<?php

namespace App\Http\Controllers\Api\Events;

use App\EventAnnouncement;

use App\Event;
use App\EventParticipantType;
use App\Http\Controllers\Controller;

class AnnouncementsController extends Controller
{

    /**
     * Show all Announcements
     * @param  $event
     * @return EventAnnouncements
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
        return $event->announcements;
    }

    /**
     * Show Announcement
     * @param  $event
     * @param  EventAnnouncement $announcement
     * @return EventAnnouncement
     */
    public function show($event, $announcement)
    {
        if (is_numeric($event)) {
            $event = Event::where('id', $event)->first();
        } else {
            $event = Event::where('slug', $event)->first();
        }
        if (is_numeric($announcement)) {
            $announcement = $event->announcements()->where('id', $announcement)->first();
        } else {
            abort(401, "Announcement ID not correct");
        }

        if (!$event || !$announcement) {
            abort(404, "Event not found.");
        }

        return $announcement;
    }
}
