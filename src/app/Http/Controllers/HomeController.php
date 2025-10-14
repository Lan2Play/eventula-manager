<?php

namespace App\Http\Controllers;

use Auth;
use Helpers;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Settings;


use App\Event;
use App\SliderImage;
use App\NewsArticle;
use App\Ticket;
use App\MatchMaking;

class HomeController extends Controller
{
    /**
     * Show Index Page
     * @return View
     */
    public function index()
    {
        $user = Auth::user();

        // Redirect if no user is found
        if (!$user) {
            return $this->home();
        }

        // efficient query with eager loading and filtering
        $activeTicket = $user->tickets()
            ->with(['event', 'purchase']) // Eager loading for relations
            ->whereHas('event', function ($query) {
                $query->where('start', '<=', now())
                    ->where('end', '>=', now()); // only running events
            })
            ->whereHas('purchase', function ($query) {
                $query->where('status', 'Success'); // only purchases with success
            })
            ->where('revoked', false) // non revoked tickets
            ->where(function ($query) {
                $query->where('signed_in', true)
                    ->orWhereHas('event', function ($subQuery) {
                        $subQuery->where('online_event', true);
                    });
            })
            ->orderBy('created_at') // first active ticket
            ->first();

        // if a ticket is found directly go to the event page
        if ($activeTicket) {
            return $this->event();
        }

        // Defaults to home
        return $this->home();
    }



    /**
     * Show Home Page
     * @return View
     */
    public function home()
    {
        return view("home")
            ->with('nextEvent', Event::nextUpcoming()->first())
            ->with('topAttendees', Helpers::getTopAttendees())
            ->with('topWinners', Helpers::getTopWinners())
            ->with('gameServerList', Helpers::getPublicGameServers())
            ->with('newsArticles', NewsArticle::latestArticles()->get())
            ->with('events', Event::all())
            ->with('sliderImages', SliderImage::getImages('frontpage'))
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|RedirectResponse|View|object
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
            'tickets.user',
            'tickets.seat.seatingPlan'
        );


        // TODO - Refactor
        $user = Auth::user();
        if ($user) {
            $clauses = ['user_id' => $user->id, 'event_id' => $event->id];
            $user->eventParticipation = Ticket::where($clauses)->get();
        }

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
            ->with('openPublicMatches', $openpublicmatches)
            ->with('liveClosedPublicMatches', $liveclosedpublicmatches)
            ->with('memberedTeams', $memberedteams)
            ->with('ownedMatches', $ownedmatches)
            ->with('currentUserOpenLivePendingDraftMatches', $currentuseropenlivependingdraftmatches)
            ->with('isMatchMakingEnabled', Settings::isMatchMakingEnabled())
            ->with('event', $event)
            ->with('gameServerList', $gameServerList)
            ->with('ticketFlagSignedIn', $ticketFlagSignedIn)
            ->with('signedIn', $signedIn)
            ->with('user', $user)
            ->with('userTickets', $userTickets);
    }

    /**
     * Show Big Screen Page
     * @param  Event  $event
     * @return View
     */
    public function bigScreen(Event $event)
    {
        return view("events.big")->with('event', $event);
    }
}
