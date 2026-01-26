<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Event;
use App\EventVenue;
use App\TicketType;
use App\EventTimetable;
use App\EventInformation;
use App\EventSeatingPlan;
use Faker\Factory as Faker;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        ## House Cleaning
        \DB::table('events')->delete();
        \DB::table('ticket_types')->delete();
        \DB::table('event_timetables')->delete();
        \DB::table('event_tournaments')->delete();
        \DB::table('event_venues')->delete();
        \DB::table('event_information')->delete();

        ## Venue
        $venue = factory(EventVenue::class)->create();

        ## Events
        factory(Event::class)->create([
            'event_venue_id'    => $venue->id,
            'status'            => 'PUBLISHED',
            'capacity'          => 30,
        ])->each(
            function ($event) {
                factory(TicketType::class)->create([
                    'event_id' => $event->id,
                ]);
                factory(EventTimetable::class)->create([
                    'event_id' => $event->id,
                ]);
                factory(EventInformation::class, 5)->create([
                    'event_id' => $event->id,
                ]);
                factory(EventSeatingPlan::class)->create([
                    'event_id' => $event->id,
                ]);
            }
        );
    }
}
