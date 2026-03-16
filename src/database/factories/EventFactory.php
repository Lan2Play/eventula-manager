<?php

namespace Database\Factories;

use App\Event;
use App\EventVenue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $venue = EventVenue::factory()->create();
        $start = Carbon::now()->addWeeks(2);
        $end   = Carbon::now()->addWeeks(2)->addDays(2);

        $displayName = 'Test Event ' . $this->faker->unique()->numerify('###');

        return [
            'display_name'   => $displayName,
            'nice_name'      => $displayName,
            'event_venue_id' => $venue->id,
            'start'          => $start->format('Y-m-d H:i:s'),
            'end'            => $end->format('Y-m-d H:i:s'),
            'desc_long'      => $this->faker->paragraphs(2, true),
            'desc_short'     => $this->faker->sentence(),
            'status'         => Event::STATUS_PUBLISHED,
            'capacity'       => 30,
        ];
    }

    /** State: draft (not visible to public) */
    public function draft(): static
    {
        return $this->state(fn() => ['status' => 'DRAFT']);
    }

    /** State: event is in the past */
    public function past(): static
    {
        return $this->state(fn() => [
            'start' => Carbon::now()->subDays(10)->format('Y-m-d H:i:s'),
            'end'   => Carbon::now()->subDays(8)->format('Y-m-d H:i:s'),
        ]);
    }

    /** State: event is currently running */
    public function ongoing(): static
    {
        return $this->state(fn() => [
            'start' => Carbon::now()->subHour()->format('Y-m-d H:i:s'),
            'end'   => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
    }

    /** State: event is in the future */
    public function future(): static
    {
        return $this->state(fn() => [
            'start' => Carbon::now()->addWeek()->format('Y-m-d H:i:s'),
            'end'   => Carbon::now()->addWeek()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
    }
}
