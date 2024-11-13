<?php

namespace App\Http\Controllers\Events;

use DB;
use Auth;
use Session;
use DateTime;

use App\User;
use App\Event;
use App\EventParticipant;
use App\EventTournament;
use App\EventTournamentParticipant;
use App\EventTournamentTeam;
use App\GameMatchApiHandler;
use Helpers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class TournamentsController extends Controller
{
    /**
     * Show Tournaments
     * @param  Event $event
     * @return Tournaments
     */
    public function index(Event $event)
    {
        return $event->tournaments;
    }

    /**
     * Show Tournaments Page
     * @param  Event            $event
     * @param  EventTournament  $tournament
     * @param  Request          $request
     * @return View
     */
    public function show(Event $event, EventTournament $tournament, Request $request)
    {
        if (!$user = Auth::user()) {
            Redirect::to('/');
        }

        if (!empty($user)) {

            $user->setActiveEventParticipant($event);
        }

        return view('events.tournaments.show')
            ->with('tournament', $tournament)
            ->with('event', $event)
            ->with('user', $user);
    }


    /**
     * Register to Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return [type]
     */
    public function registerSingle(Event $event, EventTournament $tournament, Request $request)
    {
        if ($tournament->status != 'OPEN') {
            Session::flash('alert-danger', __('events.tournament_signups_not_permitted'));
            return Redirect::back();
        }



        if (!$tournament->event->eventParticipants()->where('id', $request->event_participant_id)->first()) {
            Session::flash('alert-danger', __('events.tournament_not_signed_in'));
            return Redirect::back();
        }

        if ($tournament->getParticipant($request->event_participant_id)) {
            Session::flash('alert-danger', __('events.tournament_already_signed_up'));
            return Redirect::back();
        }

        if (
            isset($request->event_tournament_team_id) &&
            $tournamentTeam = $tournament->tournamentTeams()->where('id', $request->event_tournament_team_id)->first()
        ) {
            if ($tournamentTeam->tournamentParticipants->count() == substr($tournament->team_size, 0, 1)) {
                Session::flash('alert-danger', __('events.tournament_team_full'));
                return Redirect::back();
            }
        }

        if ($tournament->game->gamematchapihandler != 0 && $tournament->match_autoapi)
        {
            if (!Helpers::checkUserFields(User::where('id', '=', EventParticipant::where('id', '=', $request->event_participant_id)->first()->user_id)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler)->getuserthirdpartyrequirements()))
            {
                Session::flash('alert-danger', __('events.tournament_cannot_join_thirdparty'));
                return Redirect::back();
            }

        }

        // Get the EventParticipant only Once is better?
        $eventParticipant = $event->eventParticipants()->where('id', $request->event_participant_id)->first();
        // Check if staff is trying to register
        if ($eventParticipant->staff && !$event->tournaments_staff) {
            Session::flash('alert-danger', __('events.tournament_staff_not_permitted'));
            return Redirect::back();
        }

        // Check if a freebie is trying to register
        if ($eventParticipant->free && !$event->tournaments_freebie) {
            Session::flash('alert-danger', __('events.tournament_freebie_not_permitted'));
            return Redirect::back();
        }

        // TODO - Refactor
        $tournamentParticipant                              = new EventTournamentParticipant();
        $tournamentParticipant->event_participant_id        = $request->event_participant_id;
        $tournamentParticipant->event_tournament_id         = $tournament->id;
        $tournamentParticipant->event_tournament_team_id    = @$request->event_tournament_team_id;

        if (!$tournamentParticipant->save()) {
            Session::flash('alert-danger', __('events.tournament_cannot_add_participant'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('events.tournament_sucessfully_registered'));
        return Redirect::back();
    }

    /**
     * Register Team to Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return Redirect
     */
    public function registerTeam(Event $event, EventTournament $tournament, Request $request)
    {
        if ($tournament->status != 'OPEN') {
            Session::flash('alert-danger', __('events.tournament_signups_not_permitted'));
            return Redirect::back();
        }

        if (!$tournament->event->eventParticipants()->where('id', $request->event_participant_id)->first()) {
            Session::flash('alert-danger', __('events.tournament_not_signed_in'));
            return Redirect::back();
        }

        if ($tournament->getParticipant($request->event_participant_id)) {
            Session::flash('alert-danger', __('events.tournament_already_signed_up'));
            return Redirect::back();
        }

        if ($tournament->game->gamematchapihandler != 0 && $tournament->match_autoapi)
        {
            if (!Helpers::checkUserFields(User::where('id', '=', EventParticipant::where('id', '=', $request->event_participant_id)->first()->user_id)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler)->getuserthirdpartyrequirements()))
            {
                Session::flash('alert-danger', __('events.tournament_cannot_join_thirdparty'));
                return Redirect::back();
            }

        }

        $tournamentTeam                         = new EventTournamentTeam();
        $tournamentTeam->event_tournament_id    = $tournament->id;
        $tournamentTeam->name                   = $request->team_name;

        if (!$tournamentTeam->save()) {
            Session::flash('alert-danger', __('events.tournament_can_not_add_team'));
            return Redirect::back();
        }

        // TODO - Refactor
        $tournamentParticipant                              = new EventTournamentParticipant();
        $tournamentParticipant->event_participant_id        = $request->event_participant_id;
        $tournamentParticipant->event_tournament_id         = $tournament->id;
        $tournamentParticipant->event_tournament_team_id    = $tournamentTeam->id;

        if (!$tournamentParticipant->save()) {
            Session::flash('alert-danger', __('events.tournament_cannot_add_participant'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('events.tournament_team_created'));
        return Redirect::back();
    }

    /**
     * Register Pug to Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return Redirect
     */
    public function registerPug(Event $event, EventTournament $tournament, Request $request)
    {
        if ($tournament->status != 'OPEN') {
            Session::flash('alert-danger', __('events.tournament_signups_not_permitted'));
            return Redirect::back();
        }

        if (!$tournament->event->eventParticipants()->where('id', $request->event_participant_id)->first()) {
            Session::flash('alert-danger', __('events.tournament_not_signed_in'));
            return Redirect::back();
        }

        if ($tournament->getParticipant($request->event_participant_id)) {
            Session::flash('alert-danger', __('events.tournament_already_signed_up'));
            return Redirect::back();
        }

        if ($tournament->game->gamematchapihandler != 0 && $tournament->match_autoapi)
        {
            if (!Helpers::checkUserFields(User::where('id', '=', EventParticipant::where('id', '=', $request->event_participant_id)->first()->user_id)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler)->getuserthirdpartyrequirements()))
            {
                Session::flash('alert-danger', __('events.tournament_cannot_join_thirdparty'));
                return Redirect::back();
            }

        }

        $tournamentParticipant                          = new EventTournamentParticipant();
        $tournamentParticipant->event_participant_id    = $request->event_participant_id;
        $tournamentParticipant->event_tournament_id     = $tournament->id;
        $tournamentParticipant->pug                     = true;

        if (!$tournamentParticipant->save()) {
            Session::flash('alert-danger', __('events.tournament_cannot_add_pug'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('events.tournament_sucessfully_registered'));
        return Redirect::back();
    }

    /**
     * Unregister from Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return Redirect
     */
    public function unregister(Event $event, EventTournament $tournament, Request $request)
    {
        if (!$tournamentParticipant = $tournament->getParticipant($request->event_participant_id)) {
            Session::flash('alert-danger', __('events.tournament_not_signed_up'));
            return Redirect::back();
        }

        if (!$tournamentParticipant->delete()) {
            Session::flash('alert-danger', __('events.tournament_cannot_remove'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('events.tournament_sucessfully_removed'));
        return Redirect::back();
    }
}
