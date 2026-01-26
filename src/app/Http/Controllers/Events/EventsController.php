<?php

namespace App\Http\Controllers\Events;

use DB;
use Auth;
use Helpers;

use App\Setting;

use App\Event;
use App\EventTimetable;
use App\EventTimetableData;
use App\Ticket;
use App\EventParticipantType;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

class EventsController extends Controller
{
    /**
     * Show Events Index Page
     * @return View
     */
    public function index()
    {
        return view('events.index')->with('events', Event::all());
    }

    /**
     * Show Event Page
     * @param  Event $event
     * @return View
     */
    public function show(Event $event)
    {
        $user = Auth::user();
        if ($user && !empty($user->eventParticipants)) {
            foreach ($user->eventParticipants as $participant) {
                if (($event->id == $participant->event->id
                    && (date('Y-m-d H:i:s') >= $participant->event->start)
                    && (date('Y-m-d H:i:s') <= $participant->event->end)
                    && $participant->signed_in
                    && $participant->purchase->status == "Success"
                ) || ($event->id == $participant->event->id
                    && (date('Y-m-d H:i:s') >= $participant->event->start)
                    && (date('Y-m-d H:i:s') <= $participant->event->end)
                    && $participant->event->online_event
                    && $participant->purchase->status == "Success"
                ) ) {
                    return redirect('/');
                }
            }
        }
        if ($user) {
            $clauses = ['user_id' => $user->id, 'event_id' => $event->id];
            $user->eventParticipation = Ticket::where($clauses)->get();
            $user->setActiveEventParticipant($event);
        }
        if (!$event->polls->isEmpty()) {
            foreach ($event->polls as $poll) {
                $poll->sortOptions();
            }
        }
        $seoKeywords = Helpers::getSeoKeywords();
        $seoKeywords[] = $event->display_name;
        $seoKeywords[] = "Start Date: " . $event->start;
        SEOMeta::setDescription(Helpers::getSeoCustomDescription($event->desc_short));
        SEOMeta::addKeyword($seoKeywords);
        OpenGraph::setDescription(Helpers::getSeoCustomDescription($event->desc_short));
        OpenGraph::addProperty('type', 'article');

        // Eager load seating plans with all necessary relationships to avoid N+1 queries
        $event->load([
            'seatingPlans.seats.eventTicket.user',
            'tickets.user',
            'tickets.seat.seatingPlan',
            'ticketTypes.tickets',
            'ticketTypes.ticketGroup'
        ]);

        // Prefetch user tickets with relationships if user is logged in
        $userTickets = collect();
        if ($user) {
            $userTickets = Ticket::where('event_id', $event->id)
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('manager_id', $user->id)
                        ->orWhere('owner_id', $user->id);
                })
                ->where('revoked', 0)
                ->with(['seat', 'ticketType'])
                ->get();
        }

        return view('events.show')
            ->with('event', $event)
            ->with('userTickets', $userTickets);
    }

    /**
     * Generate ICS element for download
     * @param  Event $event
     * @return ICS Text File
     */
    public function generateICS(Event $event)
    {
        $eventStart = Carbon::parse($event->start)->format('Ymd\THis\Z');
        $eventEnd = Carbon::parse($event->end)->format('Ymd\THis\Z');
        $orgName = Setting::getOrgName();
        $eventName = $event->display_name;
        $eventDescription = strip_tags((string) $event->desc_long);
        $venue = $event->venue;
        $addressParts = [
            $venue->address_1,
            $venue->address_2,
            $venue->address_street,
            $venue->address_city,
            $venue->address_postcode,
            $venue->address_country
        ];
        $address = implode(', ', array_filter($addressParts, function($value) { return !is_null($value) && $value !== ''; }));
        $address = str_replace([',', ';'], ['\,', '\;'], $address);

        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//" . $orgName . "//" . $eventName . "//EN\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "UID:" . uniqid() . "\r\n";
        $icsContent .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $icsContent .= "DTSTART:" . $eventStart . "\r\n";
        $icsContent .= "DTEND:" . $eventEnd . "\r\n";
        $icsContent .= "SUMMARY:" . $eventName . "\r\n";
        $icsContent .= "DESCRIPTION:" . $eventDescription . "\r\n";
        $icsContent .= "LOCATION:" . $address . "\r\n";
        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR\r\n";

        return response($icsContent, 200)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="event.ics"');
    }
}
