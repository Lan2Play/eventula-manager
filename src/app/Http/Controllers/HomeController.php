<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Helpers;
use Arr;
use Settings;


use App\Event;
use App\User;
use App\SliderImage;
use App\NewsArticle;
use App\EventTimetable;
use App\EventTimetableData;
use App\EventParticipant;
use App\EventTournamentTeam;
use App\EventTournamentParticipant;
use App\MatchMaking;
use App\MatchMakingTeam;

use App\Http\Requests;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Artesaos\SEOTools\Facades\SEOTools;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;

use Debugbar;

class HomeController extends Controller
{
    /**
     * Show Index Page
     * @return Function
     */
    public function index()
    {

        // Check for Event
        $user = Auth::user();
        if ($user && !empty($user->eventParticipants)) {
            foreach ($user->eventParticipants as $participant) {
                Debugbar::addMessage("Participant: " . json_encode($participant), 'Event');
                if ((date('Y-m-d H:i:s') >= $participant->event->start) &&
                    (date('Y-m-d H:i:s') <= $participant->event->end) &&
                    ($participant->signed_in || $participant->event->online_event) &&
                    ($participant->free || $participant->staff || $participant->purchase->status == "Success"))
                {
                    Debugbar::addMessage("Participant gets event", 'Event');

                    return $this->event();
                }
            }
        }
        return $this->net();
    }

    /**
     * Show New Page
     * @return View
     */
    public function net()
    {
        $topAttendees = array();
        foreach (EventParticipant::groupBy('user_id', 'event_id')->get() as $attendee) {
            if ($attendee->event && $attendee->event->status == 'PUBLISHED' && $attendee->event->end < \Carbon\Carbon::today()) {
                $recent = false;
                if (!$attendee->user->admin && array_key_exists($attendee->user->id, $topAttendees)) {
                    $topAttendees[$attendee->user->id]->event_count++;
                    $recent = true;
                }
                if (!$attendee->user->admin && !$recent) {
                    $attendee->user->event_count = 1;
                    $topAttendees[$attendee->user->id] = $attendee->user;
                }
            }
        }
        usort($topAttendees, function ($a, $b) {
            return $b['event_count'] <=> $a['event_count'];
        });

        $topWinners = array();
        foreach (EventTournamentTeam::where('final_rank', 1)->get() as $winner_team) {
            $recent = false;
            foreach ($winner_team->tournamentParticipants as $winner) {
                if (array_key_exists($winner->eventParticipant->user->id, $topWinners)) {
                    $topWinners[$winner->eventParticipant->user->id]->win_count++;
                    $recent = true;
                }
                if (!$recent) {
                    $winner->eventParticipant->user->win_count = 1;
                    $topWinners[$winner->eventParticipant->user->id] = $winner->eventParticipant->user;
                }
            }
        }
        foreach (EventTournamentParticipant::where('final_rank', 1)->get() as $winner) {
            $recent = false;
            if (array_key_exists($winner->eventParticipant->user->id, $topWinners)) {
                $topWinners[$winner->eventParticipant->user->id]->win_count++;
                $recent = true;
            }
            if (!$recent) {
                $winner->eventParticipant->user->win_count = 1;
                $topWinners[$winner->eventParticipant->user->id] = $winner->eventParticipant->user;
            }
        }
        usort($topWinners, function ($a, $b) {
            return $b['win_count'] <=> $a['win_count'];
        });

        $gameServerList = Helpers::getPublicGameServers();

        return view("home")
            ->withNextEvent(
                Event::where('end', '>=', \Carbon\Carbon::now())
                    ->orderBy(DB::raw('ABS(DATEDIFF(events.end, NOW()))'))->first()
            )
            ->withTopAttendees(array_slice($topAttendees, 0, 5))
            ->withTopWinners(array_slice($topWinners, 0, 5))
            ->withGameServerList($gameServerList)
            ->withNewsArticles(NewsArticle::limit(2)->orderBy('created_at', 'desc')->get())
            ->withEvents(Event::all())
            ->withSliderImages(SliderImage::getImages('frontpage'))
        ;
    }

    /**
     * Show About us Page
     * @return View
     */
    public function about()
    {
        return view("about");
    }

    /**
     * Show Terms and Conditions Page
     * @return View
     */
    public function terms()
    {
        return view("terms");
    }

    /**
     * Show LegalNotice Page
     * @return View
     */
    public function legalNotice()
    {
        return view("legalnotice");
    }

    /**
     * Show Contact Page
     * @return View
     */
    public function contact()
    {
        return view("contact");
    }

    /**
     * Show Event Page
     * @return View
     */
    public function event()
    {
        $signedIn = true;
        $gameServerList = Helpers::getCasualGameServers();
        $event = Event::where('start', '<', date("Y-m-d H:i:s"))->where('end', '>', date("Y-m-d H:i:s"))->orderBy('id', 'desc')->first();

        $event->load('eventParticipants.user');
        $event->load('timetables');
        foreach ($event->timetables as $timetable) {
            $timetable->data = EventTimetableData::where('event_timetable_id', $timetable->id)
                ->orderBy('start_time', 'asc')
                ->get();
        }

        // TODO - Refactor
        $user = Auth::user();
        if ($user) {
            $clauses = ['user_id' => $user->id, 'event_id' => $event->id];
            $user->eventParticipation = EventParticipant::where($clauses)->get();
        }

        $ticketFlagSignedIn = false;
        if ($user) {
            $user->setActiveEventParticipant($event);
            if ($user->eventParticipation != null || isset($user->eventParticipation)) {
                foreach ($user->eventParticipation as $participant) {
                    if ($participant->event_id == $event->id) {
                        $ticketFlagSignedIn = true;
                    }
                }
            }
        }

        $currentuser                  = Auth::id();
        $openpublicmatches = MatchMaking::where(['ispublic' => 1, 'status' => 'OPEN'])->orderByDesc('created_at')->paginate(4, ['*'], 'openpubmatches');
        // $liveclosedpublicmatches = MatchMaking::where(['ispublic' => 1, 'status' => 'WAITFORPLAYERS'])->orWhere(['ispublic' => 1, 'status' => 'LIVE'])->orWhere(['ispublic' => 1, 'status' => 'COMPLETE'])->orderByDesc('created_at')->paginate(4, ['*'], 'closedpubmatches');
        $liveclosedpublicmatches = MatchMaking::where(function ($query) {
            $query->where('ispublic', 1);
            $query->where('status', 'WAITFORPLAYERS');
        })->orWhere(function ($query) {
            $query->where('ispublic', 1);
            $query->where('status', 'LIVE');
        })->orWhere(function ($query) {
            $query->where('ispublic', 1);
            $query->where('status', 'COMPLETE');
        })->orderByDesc('created_at')->paginate(4, ['*'], 'closedpubmatches');;

        $ownedmatches = MatchMaking::where(['owner_id' => $currentuser])->orderByDesc('created_at')->paginate(4, ['*'], 'owenedpage')->fragment('ownedmatches');
        $memberedteams = Auth::user()->matchMakingTeams()->orderByDesc('created_at')->paginate(4, ['*'], 'memberedmatches')->fragment('memberedmatches');
        $currentuseropenlivependingdraftmatches = array();

        foreach (MatchMaking::where(['status' => 'OPEN'])->orWhere(['status' => 'LIVE'])->orWhere(['status' => 'DRAFT'])->orWhere(['status' => 'PENDING'])->get() as $match)
        {
            if ($match->getMatchTeamPlayer(Auth::id()))
            {
                $currentuseropenlivependingdraftmatches[$match->id] = $match->id;
            }
        }

        return view("events.home")
            ->withOpenPublicMatches($openpublicmatches)
            ->withLiveClosedPublicMatches($liveclosedpublicmatches)
            ->withMemberedTeams($memberedteams)
            ->withOwnedMatches($ownedmatches)
            ->withCurrentUserOpenLivePendingDraftMatches($currentuseropenlivependingdraftmatches)
            ->withisMatchMakingEnabled(Settings::isMatchMakingEnabled())
            ->withEvent($event)
            ->withGameServerList($gameServerList)
            ->withTicketFlagSignedIn($ticketFlagSignedIn)
            ->withSignedIn($signedIn)
            ->withUser($user);
    }

    /**
     * Show Big Screen Page
     * @param  Event  $event
     * @return View
     */
    public function bigScreen(Event $event)
    {
        return view("events.big")->withEvent($event);
    }
}
