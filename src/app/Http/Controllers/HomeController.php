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

use Carbon\Carbon;

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
        $user = Auth::user();
        // redirect to home page if no event participants are available
        if (!$user || empty($user->eventParticipants)) {
            return $this->home();
        }

        // Loop trough the eventParticipants 
        // The first one, whos event is currently running and that is active redirects to the event page
        foreach ($user->eventParticipants as $participant) {
            if ($participant->event->isRunningCurrently() && $participant->isActive()) {
                return $this->event();
            }
        }

        // redirect to home page by default
        return $this->home();
    }



    /**
     * Show Home Page
     * @return View
     */
    public function home()
    {
        return view("home")
            ->withNextEvent(Event::nextUpcoming()->first())
            ->withTopAttendees(Helpers::getTopAttendees())
            ->withTopWinners(Helpers::getTopWinners())
            ->withGameServerList(Helpers::getPublicGameServers())
            ->withNewsArticles(NewsArticle::latestArticles()->get())
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

        $event =  Event::current()->first();
        // Check if event is null and handle it
        if (!$event) {
            return redirect()->route('home.index')->with('error', 'No active event found.');
        }

        // Loading can be done like this in one call of load function
        $event->load(
            'eventParticipants.user',
        );


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
