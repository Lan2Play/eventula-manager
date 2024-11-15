<?php

namespace App\Libraries;

use App\EventTicketGroup;
use App\Event;
use App\EventSeatingPlan;
use Session;
use Illuminate\Http\Request;
use Exception;
use DB;
use App\GameServerCommandParameter;
use App\EventTournament;
use App\EventParticipant;
use App\User;
use App\GameServer;
use App\EventTicket;
use GuzzleHttp\Client;
use \Carbon\Carbon as Carbon;
use GrahamCampbell\ResultType\Result;
use Throwable;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use HaydenPierce\ClassFinder\ClassFinder;

class Helpers
{
    
    
    /**
     * Gets an array of all supported languages from the language directory.
     *
     * @return array
     */
    public static function getSupportedLocales()
    {
        return array_map('basename', array_filter(glob(app()->langPath().'/*'), 'is_dir'));
    }


    /**
     * Validate if the given locale exists in the supported languages.
     *
     * @param  string  $locale
     * @return bool
     */
    public static function isValidLocale($locale)
    {
        return in_array($locale, self::getSupportedLocales());
    }

    // TODO - refactor - eg getGameSelectArray - specifially the selectArray part
    /**
     * Get Venues
     * @param  boolean $obj Return as Object
     * @return Array|Object
     */
    public static function getVenues($obj = false)
    {
        $venues = \App\EventVenue::all();
        $return = array();
        foreach ($venues as $venue) {
            $return[$venue->id] = $venue->display_name;
        }
        if (!$obj) {
            $return[] = 'None';
        }
        if ($obj) {
            return json_decode(json_encode($return), false);
        }
        return $return;
    }

    /**
     * Paginate
     * @param  Collection  $items
     * @param  integer $perPage
     * @param  integer $page
     * @param  Array|Object $options
     * @return LengthAwarePaginator
     */
    public static function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
            ]
        );
    }


    /**
     * Get Events
     * @param  string  $order
     * @param  integer $limit
     * @param  boolean $obj   Return as Object
     * @param  string $pagename
     * @return Array|Object
     */
    public static function getEvents($order = 'DESC', $limit = 0, $obj = false, $pagename = "")
    {
        $return = array();
        if ($limit != 0) {

            if ($pagename != "") {
                $events = \App\Event::orderBy('start', $order)->paginate($limit, ['*'], $pagename);
            } else {
                $events = \App\Event::orderBy('start', $order)->paginate($limit);
            }
        } else {
            $events = \App\Event::orderBy('start', 'DESC')->get();
        }
        $return = array();
        foreach ($events as $event) {
            $return[$event->id] = $event;
        }
        if ($obj) {
            return json_decode(json_encode($return), false);
        }
        return $return;
    }

    /**
     * Get Event Names
     * @param  string  $order
     * @param  integer $limit
     * @param  boolean $future
     * @param  boolean $obj   Return as Object
     * @return Array|Object
     */
    public static function getEventNames($order = 'DESC', $limit = 0, $future = false, $obj = false)
    {
        $return = array();
        if ($limit != 0) {
            if ($future) {
                $events = \App\Event::where('end', '>=', date('Y-m-d'))->orderBy('start', $order)->paginate($limit);
            } else {
                $events = \App\Event::orderBy('start', $order)->paginate($limit);
            }
        } else {
            if ($future) {
                $events = \App\Event::where('end', '>=', date('Y-m-d'))->orderBy('start', 'DESC')->get();
            } else {
                $events = \App\Event::orderBy('start', 'DESC')->get();
            }
        }
        if (!$obj) {
            $return[] = 'None';
        }
        foreach ($events as $event) {
            $return[$event->id] = $event->display_name;
        }
        if ($obj) {
            return json_decode(json_encode($return), false);
        }
        return $return;
    }

    /**
     * Get Total Events Count
     * @return int
     */
    public static function getEventTotal()
    {
        $events = \App\Event::count();
        return Settings::getEventCountOffset() + $events;
    }

    /**
     * Get Next Event Name.
     *
     * @return string
     */
    public static function getNextEventName()
    {
        if ($event = Event::nextUpcoming()->first()) {
            return ($event->status == 'DRAFT' || $event->status == 'PREVIEW')
                ? $event->display_name . ' - ' . $event->status
                : $event->display_name;
        }
        return 'Coming soon...';
    }

    /**
     * Get Next Event Slug.
     *
     * @return string
     */
    public static function getNextEventSlug()
    {
        return ($event = Event::nextUpcoming()->first()) ? $event->slug : '#';
    }

    /**
     * Get Next Event Description.
     *
     * @return string
     */
    public static function getNextEventDesc()
    {
        return ($event = Event::nextUpcoming()->first()) ? $event->desc_long : 'Coming soon...';
    }

    /**
     * Get Next Event Start Date.
     *
     * @return string
     */
    public static function getNextEventStartDate()
    {
        return ($event = Event::nextUpcoming()->first())
            ? date("d-m-Y H:i", strtotime($event->start))
            : 'Coming soon...';
    }

    /**
     * Get Next Event End Date.
     *
     * @return string
     */
    public static function getNextEventEndDate()
    {
        return ($event = Event::nextUpcoming()->first())
            ? date("d-m-Y H:i", strtotime($event->end))
            : 'Coming soon...';
    }


    /**
     * Get Total Event Participants Count
     * @return int
     */
    public static function getEventParticipantTotal()
    {
        $participants = \App\EventParticipant::count();
        return Settings::getParticipantCountOffset() + $participants;
    }

    /**
     * Get Active Tournaments count for User
     * @param  $event_id
     * @return int
     */
    public static function getUserActiveTournaments($event_id)
    {
        $user = \Auth::user();
        $active_tournament_counter = 0;
        foreach ($user->eventParticipants as $event_participant) {
            foreach ($event_participant->tournamentParticipants as $tournament_participant) {
                if (
                    $tournament_participant->eventTournament->event_id == $event_id &&
                    $tournament_participant->eventTournament->status != 'COMPLETE'
                ) {
                    $active_tournament_counter++;
                }
            }
        }
        return $active_tournament_counter;
    }

    /**
     * Format Challonge Rankings
     * @param  $final_rank
     * @return String
     */
    public static function getChallongeRankFormat($final_rank)
    {
        if ($final_rank == '1') {
            return '1st';
        }
        if ($final_rank == '2') {
            return '2nd';
        }
        if ($final_rank == '3') {
            return '3rd';
        }
        if (substr($final_rank, -1) == '1') {
            return $final_rank . 'st';
        }
        if (substr($final_rank, -1) == '2') {
            return $final_rank . 'nd';
        }
        if (substr($final_rank, -1) == '3') {
            return $final_rank . 'rd';
        }
        return $final_rank . 'th';
    }

    /**
     * Get Basket Total
     * @param  $basket
     * @return int
     */
    public static function getBasketTotal($basket)
    {
        $return = 0;
        foreach ($basket as $ticket_id => $quantity) {
            $ticket = \App\EventTicket::where('id', $ticket_id)->first();
            $return += ($ticket->price * $quantity);
        }
        return $return;
    }

    /**
     * array_key_exists with regex
     * @param  $pattern
     * @param  $array
     * @return int
     */
    public static function pregArrayKeyExists($pattern, $array)
    {
        $keys = array_keys($array);
        return (int) preg_grep($pattern, $keys);
    }

    /**
     * Get GameCommandHandler Select Array
     * @param  $publicOnly
     * @return Array
     */
    public static function getGameCommandHandlerSelectArray($publicOnly = true)
    {
        return \App\GameCommandHandler::getGameCommandHandlerSelectArray($publicOnly);
    }

    /**
     * Get GameMatchApiHandler Select Array
     * @param  $publicOnly
     * @return Array
     */
    public static function getGameMatchApiHandlerSelectArray()
    {
        return \App\GameMatchApiHandler::getGameMatchApiHandlerSelectArray();
    }

    /**
     * Get Games Select Array
     * @param  $publicOnly
     * @return Array
     */
    public static function getGameSelectArray($publicOnly = true)
    {
        return \App\Game::getGameSelectArray($publicOnly);
    }

    /**
     * Get Matchmaking Games Select Array
     * @param  $publicOnly
     * @return Array
     */
    public static function getMatchmakingGameSelectArray($publicOnly = true)
    {
        return \App\Game::getMatchmakingGameSelectArray($publicOnly);
    }

    /**
     * Get Games Select Array
     * @param  $publicOnly
     * @return Array
     */
    public static function getGameServerCommandScopeSelectArray($publicOnly = true)
    {
        return \App\GameServerCommand::getGameServerCommandScopeSelectArray($publicOnly);
    }

    /**
     * Get Games Select Array
     * @return Array
     */
    public static function getCasualGameServers()
    {
        $returnarray = array();
        foreach (GameServer::where(['type' => 'Casual', 'isenabled' => true])->get() as $gameserver) {
            if (!array_key_exists($gameserver->game->slug, $returnarray)) {
                $returnarray[$gameserver->game->slug] = array();
            }
            $returnarray[$gameserver->game->slug][] = $gameserver;
        }

        return $returnarray;
    }

    /**
     * get public casual server
     * @return Array
     */
    public static function getPublicGameServers()
    {
        $returnarray = array();
        foreach (GameServer::where(['ispublic' => true, 'type' => 'Casual', 'isenabled' => true])->get() as $gameserver) {
            if (!array_key_exists($gameserver->game->slug, $returnarray)) {
                $returnarray[$gameserver->game->slug] = array();
            }
            $returnarray[$gameserver->game->slug][] = $gameserver;
        }

        return $returnarray;
    }

    /**
     * Get Shop Categories Select Array
     * @param  $publicOnly
     * @return Array
     */
    public static function getShopCategoriesSelectArray($publicOnly = true)
    {
        return \App\ShopItemCategory::getShopCategoriesSelectArray($publicOnly);
    }

    /**
     * Format Shopping Basket into Readable format
     * @param $itemId
     * @return Boolean
     */
    public static function formatBasket($basket)
    {
        if (array_key_exists('shop', $basket)) {
            $formattedBasket = \App\ShopItem::whereIn('id', array_keys($basket['shop']))->get();
        }
        if (array_key_exists('tickets', $basket)) {
            $formattedBasket = \App\EventTicket::whereIn('id', array_keys($basket['tickets']))->get();
        }
        if (!$formattedBasket) {
            return false;
        }
        $formattedBasket->total = 0;
        $formattedBasket->total_credit = 0;
        $formattedBasket->allow_payment = true;
        $formattedBasket->allow_credit = true;
        foreach ($formattedBasket as $item) {
            if (array_key_exists('shop', $basket)) {
                $item->quantity = $basket['shop'][$item->id];
                if ($item->price != null && $item->price != 0) {
                    $formattedBasket->total += $item->price * $item->quantity;
                }
                if ($item->price_credit != null && $item->price_credit != 0) {
                    $formattedBasket->total_credit += $item->price_credit * $item->quantity;
                }
            } else {
                $item->quantity = $basket['tickets'][$item->id];
                $formattedBasket->total += $item->price * $item->quantity;
                $formattedBasket->total_credit += $item->price_credit * $item->quantity;
            }
            if ($item->price_credit == null || $item->price_credit == 0) {
                $formattedBasket->allow_credit = false;
            }
            if ($item->price == null || $item->price == 0) {
                $formattedBasket->allow_payment = false;
            }
        }
        return $formattedBasket;
    }

    /**
     * Get CSS Version Number for Cache Busting
     * @return int
     */
    public static function getCssVersion()
    {
        return \App\Appearance::getCssVersion();
    }

    /**
     * Get Card Expiry Month Dates
     * @return array
     */
    public static function getCardExpiryMonthDates()
    {
        $return = array();
        for ($i = 1; $i <= 12; $i++) {
            $date = $i;
            // if ($date <= 9) {
            //     $date = '0' . $i;
            // }
            $return[$date] = $date;
        }
        return $return;
    }

    /**
     * Get Card Expiry Year Dates
     * @return array
     */
    public static function getCardExpiryYearDates()
    {
        $return = array();
        for ($i = (int)date('y'); $i <= 99; $i++) {
            $date = $i;
            // if ($date <= 9) {
            //     $date = '0' . $i;
            // }
            $return[$date] = $date;
        }
        return $return;
    }

    /**
     * Get Supported Event Tags from Eventula
     * @return array
     */
    public static function getEventulaEventTags()
    {
        if (config('eventula.url') == "DISABLE") {
            return false;
        }

        $client = new Client();
        try {
            $response = $client->get(config('eventula.url') . '/api/tags/events');
        } catch (\Exception $e) {
            return false;
        }
        return json_decode($response->getBody());
    }

    /**
     * Get Countries for Select
     * @return array
     */
    public static function getSelectCountries()
    {
        $countriesArray = [
            "Afghanistan",
            "Åland Islands",
            "Albania",
            "Algeria",
            "American Samoa",
            "Andorra",
            "Angola",
            "Anguilla",
            "Antarctica",
            "Antigua and Barbuda",
            "Argentina",
            "Armenia",
            "Aruba",
            "Australia",
            "Austria",
            "Azerbaijan",
            "Bahamas",
            "Bahrain",
            "Bangladesh",
            "Barbados",
            "Belarus",
            "Belgium",
            "Belize",
            "Benin",
            "Bermuda",
            "Bhutan",
            "Bolivia",
            "Bosnia and Herzegovina",
            "Botswana",
            "Bouvet Island",
            "Brazil",
            "British Indian Ocean Territory",
            "Brunei Darussalam",
            "Bulgaria",
            "Burkina Faso",
            "Burundi",
            "Cambodia",
            "Cameroon",
            "Canada",
            "Cape Verde",
            "Cayman Islands",
            "Central African Republic",
            "Chad",
            "Chile",
            "China",
            "Christmas Island",
            "Cocos (Keeling) Islands",
            "Colombia",
            "Comoros",
            "Congo",
            "Congo, The Democratic Republic of The",
            "Cook Islands",
            "Costa Rica",
            "Cote D'ivoire",
            "Croatia",
            "Cuba",
            "Cyprus",
            "Czech Republic",
            "Denmark",
            "Djibouti",
            "Dominica",
            "Dominican Republic",
            "Ecuador",
            "Egypt",
            "El Salvador",
            "Equatorial Guinea",
            "Eritrea",
            "Estonia",
            "Ethiopia",
            "Falkland Islands (Malvinas)",
            "Faroe Islands",
            "Fiji",
            "Finland",
            "France",
            "French Guiana",
            "French Polynesia",
            "French Southern Territories",
            "Gabon",
            "Gambia",
            "Georgia",
            "Germany",
            "Ghana",
            "Gibraltar",
            "Greece",
            "Greenland",
            "Grenada",
            "Guadeloupe",
            "Guam",
            "Guatemala",
            "Guernsey",
            "Guinea",
            "Guinea-bissau",
            "Guyana",
            "Haiti",
            "Heard Island and Mcdonald Islands",
            "Holy See (Vatican City State)",
            "Honduras",
            "Hong Kong",
            "Hungary",
            "Iceland",
            "India",
            "Indonesia",
            "Iran, Islamic Republic of",
            "Iraq",
            "Ireland",
            "Isle of Man",
            "Israel",
            "Italy",
            "Jamaica",
            "Japan",
            "Jersey",
            "Jordan",
            "Kazakhstan",
            "Kenya",
            "Kiribati",
            "Korea, Democratic People's Republic of",
            "Korea, Republic of",
            "Kuwait",
            "Kyrgyzstan",
            "Lao People's Democratic Republic",
            "Latvia",
            "Lebanon",
            "Lesotho",
            "Liberia",
            "Libyan Arab Jamahiriya",
            "Liechtenstein",
            "Lithuania",
            "Luxembourg",
            "Macao",
            "Macedonia, The Former Yugoslav Republic of",
            "Madagascar",
            "Malawi",
            "Malaysia",
            "Maldives",
            "Mali",
            "Malta",
            "Marshall Islands",
            "Martinique",
            "Mauritania",
            "Mauritius",
            "Mayotte",
            "Mexico",
            "Micronesia, Federated States of",
            "Moldova, Republic of",
            "Monaco",
            "Mongolia",
            "Montenegro",
            "Montserrat",
            "Morocco",
            "Mozambique",
            "Myanmar",
            "Namibia",
            "Nauru",
            "Nepal",
            "Netherlands",
            "Netherlands Antilles",
            "New Caledonia",
            "New Zealand",
            "Nicaragua",
            "Niger",
            "Nigeria",
            "Niue",
            "Norfolk Island",
            "Northern Mariana Islands",
            "Norway",
            "Oman",
            "Pakistan",
            "Palau",
            "Palestinian Territory, Occupied",
            "Panama",
            "Papua New Guinea",
            "Paraguay",
            "Peru",
            "Philippines",
            "Pitcairn",
            "Poland",
            "Portugal",
            "Puerto Rico",
            "Qatar",
            "Reunion",
            "Romania",
            "Russian Federation",
            "Rwanda",
            "Saint Helena",
            "Saint Kitts and Nevis",
            "Saint Lucia",
            "Saint Pierre and Miquelon",
            "Saint Vincent and The Grenadines",
            "Samoa",
            "San Marino",
            "Sao Tome and Principe",
            "Saudi Arabia",
            "Senegal",
            "Serbia",
            "Seychelles",
            "Sierra Leone",
            "Singapore",
            "Slovakia",
            "Slovenia",
            "Solomon Islands",
            "Somalia",
            "South Africa",
            "South Georgia and The South Sandwich Islands",
            "Spain",
            "Sri Lanka",
            "Sudan",
            "Suriname",
            "Svalbard and Jan Mayen",
            "Swaziland",
            "Sweden",
            "Switzerland",
            "Syrian Arab Republic",
            "Taiwan, Province of China",
            "Tajikistan",
            "Tanzania, United Republic of",
            "Thailand",
            "Timor-leste",
            "Togo",
            "Tokelau",
            "Tonga",
            "Trinidad and Tobago",
            "Tunisia",
            "Turkey",
            "Turkmenistan",
            "Turks and Caicos Islands",
            "Tuvalu",
            "Uganda",
            "Ukraine",
            "United Arab Emirates",
            "United Kingdom",
            "United States",
            "United States Minor Outlying Islands",
            "Uruguay",
            "Uzbekistan",
            "Vanuatu",
            "Venezuela",
            "Viet Nam",
            "Virgin Islands, British",
            "Virgin Islands, U.S.",
            "Wallis and Futuna",
            "Western Sahara",
            "Yemen",
            "Zambia",
            "Zimbabwe",
        ];
        return $countriesArray;
    }

    /**
     * Get Ticket quatntity for Select
     * @return array
     */
    public static function getTicketQuantitySelection(EventTicket $ticket, $remainingcapacity, $defaultCapacity = 10)
    {
        $ticketCount = min(
            $remainingcapacity > 0 ? $remainingcapacity : $defaultCapacity,
            $ticket->quantity > 0 ? $ticket->quantity : $defaultCapacity,
            ($ticket->no_tickets_per_user ?? 0) > 0 ? $ticket->no_tickets_per_user : $defaultCapacity,
            ($ticket->ticketGroup?->tickets_per_user ?? 0) > 0 ? $ticket->ticketGroup->tickets_per_user : $defaultCapacity,
            ($ticket->event->no_tickets_per_user ?? 0) > 0 ? $ticket->event->no_tickets_per_user : $defaultCapacity
        );

        $result = array();
        for ($i = 1; $i <= $ticketCount; $i++) {
            $result[$i] = $i;
        }

        return $result;
    }

    public static function getTicketGroupSelection()
    {
        $result = ['' => '-- ungrouped --'];
        foreach (EventTicketGroup::all(['id', 'name']) as $row) {
            $result[$row['id']] = $row['name'];
        }

        return $result;
    }

    /**
     * Resolve the command parameters
     *
     * @param  string $command
     * @param  Request $request
     * @param  object $availableParameters
     *
     * @return string The resolved command
     */
    public static function resolveServerCommandParameters(string $command, ?Request $request, $availableParameters)
    {
        // Set Variables to be usable in Commands
        $result = "";

        // Example Variable {>variable}
        $commandParts = preg_split("/[\{\}]+/", $command);
        foreach ($commandParts as $key => $commandPart) {
            if (strlen($commandPart) <= 0) {
                continue;
            }

            if ($commandPart[0] != '>') {
                $result = $result . $commandPart;
            } else {
                try {
                    $commandPart = ltrim($commandPart, '>');
                    $secondChar = strpos($commandPart, "§");
                    $commandPart = ltrim($commandPart, '§');



                    if ($request && isset($request->{$commandPart})) {
                        $gameServerCommandParameter = GameServerCommandParameter::where('slug', $commandPart)->first();
                        $result =  $result . $gameServerCommandParameter->getParameterSelectArray()[$request->{$commandPart}];
                    } else {
                        $explodedVariableParts = explode("->", $commandPart);
                        foreach ($explodedVariableParts as $key => $explodedVariablePart) {
                            if (isset($commandPartValue)) {
                                $commandPartValue = $commandPartValue->{$explodedVariablePart};
                            } else {
                                $commandPartValue = $availableParameters->{$explodedVariablePart};
                            }
                        }

                        if (isset($commandPartValue) && !empty($commandPartValue)) {
                            $result = $result . $commandPartValue;
                        } else {
                            if ($secondChar) {
                                Session::flash('alert-danger', "Can not resolve command parameter \"$commandPart\"!");
                            }
                        }

                        unset($commandPartValue);
                    }
                } catch (Exception $e) {
                    if ($secondChar) {
                        Session::flash('alert-danger', 'error while resolving command!' . $command . ' ' . var_export($e->getMessage(), true));
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Return true if selected user is part of the match
     *
     * @param  EventTournament $tournament
     * @param   $match
     * @param  User $user
     *
     * @return boolean True if user is part of match
     */
    public static function isMatchPlayer(EventTournament $tournament, $match, User $user)
    {
        $team1 = $tournament->getTeamByChallongeId($match->player1_id);
        $team2 = $tournament->getTeamByChallongeId($match->player2_id);

        foreach ($team1->tournamentParticipants as $key => $team1Participant) {
            if ($team1Participant->eventParticipant->user->id == $user->id) {
                return true;
            }
        }
        foreach ($team2->tournamentParticipants as $key => $team1Participant) {
            if ($team1Participant->eventParticipant->user->id == $user->id) {
                return true;
            }
        }

        return false;
    }

    public static function rethrowIfDebug(\Throwable $e)
    {
        if (env('APP_DEBUG')) {
            throw $e;
        }
    }

    public static function checkUserFields(User $user, array $properties)
    {
        $result = true;
        foreach ($properties as $property) {

            if (!$user->$property || $user->$property == "") {
                $result = false;
            }
        }
        return $result;
    }

    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function getLatinAlphabetLetterIndex($alphabetLetter)
    {
        $latinAlphabetUpper = range('A', 'Z');

        foreach ($latinAlphabetUpper as $key => $value) {
            if ($value == $alphabetLetter) {
                return $key + 1;
            }
        }

        $latinAlphabetLower = range('a', 'z');

        foreach ($latinAlphabetLower as $key => $value) {
            if ($value == $alphabetLetter) {
                return $key + 1;
            }
        }

        return -1;
    }

    public static function getLatinAlphabetUpperLetterByIndex($index)
    {
        return range('A', 'Z')[$index - 1];
    }

    public static function getGameTemplates()
    {
        $classenames = ClassFinder::getClassesInNamespace('Database\Seeders\GameTemplates');

        $gameTemplates = collect();

        foreach ($classenames as $classname) {
            $gameTemplates->put($classname, new $classname);
        }
        return $gameTemplates;
    }

    public static function getPoweredByLine()
    {
        return ' | powered by Lan2Play Eventula Manager';
    }
    public static function getSeoKeywords()
    {
        return explode(',', config('settings.seo_keywords') . ',Lan2Play Eventula Manager');
    }

    public static function getSeoDescription()
    {
        return config('settings.org_tagline') . Helpers::getPoweredByLine();
    }

    public static function getSeoCustomDescription($description)
    {
        return $description . Helpers::getPoweredByLine();
    }

    public static function getExistingSeatingPlansSelect()
    {
        $result = [];
        foreach (EventSeatingPlan::all(['id', 'name', 'event_id']) as $plan) {
            $event = Event::where('id', $plan->event_id)->first();
            $result[$event->display_name] ??= [];
            $result[$event->display_name][$plan->id] = $plan->name;
        }
        return $result;
    }

    /**
     * Get top attendees by event count.
     *
     * @param  int  $count
     * @return Collection
     */
    public static function getTopAttendees(int $count = 5): Collection
    {
        $users = User::where('admin', 0)->get();
        return $users->filter(function ($user) {
            return $user->unique_attended_event_count > 0;
        })
            ->sortByDesc('unique_attended_event_count')
            ->take($count);
    }

    /**
     * Get top winners by win count.
     *
     * @param int $count
     * @return Collection
     */
    public static function getTopWinners(int $count = 5): Collection
    {
        $users = User::all();
        return $users->filter(function ($user) {
            return $user->win_count > 0;
        })
            ->sortByDesc('win_count')
            ->take($count);
    }
}
