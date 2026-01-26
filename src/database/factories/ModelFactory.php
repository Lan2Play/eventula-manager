<?php
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name'              => $faker->name,
        'email'             => $faker->email,
        'password'          => bcrypt(Str::random(10)),
        'remember_token'    => Str::random(10),
    ];
});

## Events

$factory->define(App\Event::class, function (Faker\Generator $faker) {
    $event_name = 'EventNameHere ' . $faker->randomDigitNotNull;
    $start_date = date_format($faker->dateTimeBetween('+1 months', '+2 months'), "Y-m-d");
    $end_date   = date('Y-m-d', strtotime($start_date. ' + 2 days'));
    return [
        'display_name'      => $event_name,
        'nice_name'         => strtolower(str_replace(' ', '-', $event_name)),
        'slug'              => strtolower(str_replace(' ', '-', $event_name)),
        'start'             => $start_date . ' 16:00:00',
        'end'               => $end_date . ' 18:00:00',
        'desc_long'         => $faker->sentences($nb = 5, $asText = true),
        'desc_short'        => $faker->sentences($nb = 1, $asText = true),
        'status'            => 'published',
    ];
});

## Event Tickets

$factory->define(App\TicketType::class, function (Faker\Generator $faker) {
    return [
        'name'          => 'Weekend Ticket',
        'type'          => 'weekend',
        'price'         => '30',
        'seatable'      => true,
        'sale_start'    => null,
        'sale_end'      => null,
    ];
});

## Event Timetable

$factory->define(App\EventTimetable::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->words($nb = 3, $asText = true),
        'status'        => 'published',
    ];
});

## Event Timetable Data

## Event Seating

$factory->define(App\EventSeatingPlan::class, function (Faker\Generator $faker) {
    return [
        'name'      => $faker->words($nb = 3, $asText = true),
        'columns'   => 8,
        'rows'      => 6,
        'headers'   => 'A,B,C,D,E,F,G,H',
        'status'    => 'published',
    ];
});

## Venue

$factory->define(App\EventVenue::class, function (Faker\Generator $faker) {
    $venue_name = 'VenueNameHere ' . $faker->randomDigitNotNull;
    return [
        'display_name'      => $venue_name,
        'slug'              => strtolower(str_replace(' ', '-', $venue_name)),
        'address_1'         => $faker->secondaryAddress(),
        'address_street'    => $faker->streetName(),
        'address_city'      => $faker->city(),
        'address_postcode'  => $faker->postcode(),
        'address_country'   => $faker->country(),
    ];
});

## Event Information
$factory->define(App\EventInformation::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->words($nb = 3, $asText = true),
        'text'  => $faker->paragraphs($nb = 3, $asText = true),
    ];
});

## Settings
$factory->define(App\Setting::class, function (Faker\Generator $faker) {
    return [
    ];
});

## Appearance
$factory->define(App\Appearance::class, function (Faker\Generator $faker) {
    return [
    ];
});

## SliderImages
$factory->define(App\SliderImage::class, function (Faker\Generator $faker) {
    return [
    ];
});

## Games
$factory->define(App\Game::class, function (Faker\Generator $faker) {
    return [
    ];
});


## GameServers
$factory->define(App\GameServer::class, function (Faker\Generator $faker) {
    return [
    ];
});

## GameServerCommands
$factory->define(App\GameServerCommand::class, function (Faker\Generator $faker) {
    return [
    ];
});

## GameServerCommands
$factory->define(App\GameServerCommandParameter::class, function (Faker\Generator $faker) {
    return [
    ];
});


## News Article
$factory->define(App\NewsArticle::class, function (Faker\Generator $faker) {
    return [
        'title'     => $faker->words($nb = 3, $asText = true),
        'text'      => $faker->paragraphs($nb = 3, $asText = true),
    ];
});

## Shop Category
$factory->define(App\ShopItemCategory::class, function (Faker\Generator $faker) {
    $name = $faker->words(random_int(1, 2), $asText = true);
    return [
        'name'      => $name,
        'status'    => 'PUBLISHED',
        'slug'      => strtolower(str_replace(' ', '-', $name)),
    ];
});

## Shop Item
$factory->define(App\ShopItem::class, function (Faker\Generator $faker) {
    $name = $faker->words($nb = random_int(1, 3), $asText = true);
    $rng = random_int(1, 3);
    $price = number_format(random_int(1, 100), 2);
    $price_credit = random_int(1, 999);
    if ($rng == 1) {
        $price = null;
    }
    if ($rng == 2) {
        $price_credit = null;
    }
    return [
        'name'                  => $name,
        'slug'                  => strtolower(str_replace(' ', '-', $name)),
        'featured'              => random_int(0, 1),
        'description'           => $faker->paragraphs($nb = 2, $asText = true),
        'price'                 => $price,
        'price_credit'          => $price_credit,
        'stock'                 => random_int(0, 10),
        'status'                => 'PUBLISHED',
        'added_by'              => 1,
    ];
});

## Shop Item Image
$factory->define(App\ShopItemImage::class, function (Faker\Generator $faker) {
    return [
    ];
});
