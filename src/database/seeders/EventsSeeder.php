<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Event;
use App\EventVenue;
use App\EventTicket;
use App\EventTimetable;
use App\EventInformation;
use App\EventSeatingPlan;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ## House Cleaning
        \DB::table('events')->delete();
        \DB::table('event_tickets')->delete();
        \DB::table('event_timetables')->delete();
        \DB::table('event_tournaments')->delete();
        \DB::table('event_venues')->delete();
        \DB::table('event_information')->delete();

        ## Venue
        $venue = EventVenue::factory()->create();

        ## Events
        Event::factory()->create([
            'event_venue_id'    => $venue->id,
            'status'            => 'PUBLISHED',
            'capacity'          => 30,
        ])->each(
            function ($event) {
                EventTicket::factory()->create([
                    'event_id' => $event->id,
                ]);
                EventTimetable::factory()->create([
                    'event_id' => $event->id,
                ]);
                EventInformation::factory()->count(5)->create([
                    'event_id' => $event->id,
                ]);
                EventSeatingPlan::factory()->create([
                    'event_id' => $event->id,
                ]);
            }
        );
    }
}
