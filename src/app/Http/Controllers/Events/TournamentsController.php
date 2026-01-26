<?php

namespace App\Http\Controllers\Events;

use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Session;

use App\User;
use App\Event;
use App\Ticket;
use App\EventTournament;
use App\EventTournamentParticipant;
use App\EventTournamentTeam;
use App\GameMatchApiHandler;
use Helpers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

// TODO: add checks for ticketId in request and ownership of the ticket.
// TODO: add validation checks for all controller methods.
class TournamentsController extends Controller
{
    /**
     * Show Tournaments
     * TODO investigate why we need this..
     * @param  Event $event
     * @return array
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
     * Register Team to Tournament
     * TODO: Refactor/Correct the behavior for online events
     * @param Event $event
     * @param EventTournament $tournament
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function registerTeam(Event $event, EventTournament $tournament, Request $request)
    {
        if ($tournament->status != 'OPEN') {
            Session::flash('alert-danger', __('events.tournament_signups_not_permitted'));
            return Redirect::back();
        }

        if (!$request->ticket_id == null) {
            $ticket = Ticket::find($request->ticket_id);
            if (!$ticket) {
                Session::flash('alert-danger', __('tickets.not_found'));
                return Redirect::back();
            }
        }

        if (!$tournament->event->tickets()->where('id', $request->ticket_id)->first()) {
            Session::flash('alert-danger', __('events.tournament_not_signed_in'));
            return Redirect::back();
        }

        if ($tournament->getParticipant($request->ticket_id)) {
            Session::flash('alert-danger', __('events.tournament_already_signed_up'));
            return Redirect::back();
        }

        if (!$tournament->event->tournaments_freebies && $ticket->free) {
            Session::flash('alert-danger', __('events.tournament_freebie_not_permitted'));
            return Redirect::back();
        }

        if (!$tournament->event->tournaments_staff && $ticket->staff) {
            Session::flash('alert-danger', __('events.tournament_staff_not_permitted'));
            return Redirect::back();
        }

        if ($tournament->match_autoapi && $tournament->game->gamematchapihandler != 0)
        {
            if (!Helpers::checkUserFields(User::where('id', '=', Ticket::where('id', '=', $request->ticket_id)->first()->user_id)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler)->getuserthirdpartyrequirements()))
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
        $tournamentParticipant->ticket_id        = $ticket->id;
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
     * Register to Tournament
     * @param Event $event
     * @param EventTournament $tournament
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function registerSingle(Event $event, EventTournament $tournament, Request $request)
    {
        if ($tournament->status != 'OPEN') {
            Session::flash('alert-danger', __('events.tournament_signups_not_permitted'));
            return Redirect::back();
        }

        if (!$request->ticket_id == null) {
            $ticket = Ticket::find($request->ticket_id);
            if (!$ticket) {
                Session::flash('alert-danger', __('tickets.not_found'));
                return Redirect::back();
            }
        }
        // TODO this should maybe use the a tickets() function where we check for singed-In tickets?
        if (!$tournament->event->tickets()->where('id', $request->ticket_id)->first()) {
            Session::flash('alert-danger', __('events.tournament_not_signed_in'));
            return Redirect::back();
        }
        
        if (!$tournament->event->tournaments_freebies && $ticket->free) {
            Session::flash('alert-danger', __('events.tournament_freebie_not_permitted'));
            return Redirect::back();
        }

        if (!$tournament->event->tournaments_staff && $ticket->staff) {
            Session::flash('alert-danger', __('events.tournament_staff_not_permitted'));
            return Redirect::back();
        }

        if ($tournament->getParticipant($request->ticket_id)) {
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

        if ($tournament->game && $tournament->game->gamematchapihandler != 0 && $tournament->match_autoapi)
        {
            if (!Helpers::checkUserFields(User::where('id', '=', Ticket::where('id', '=', $request->ticket_id)->first()->user_id)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler)->getuserthirdpartyrequirements()))
            {
                Session::flash('alert-danger', __('events.tournament_cannot_join_thirdparty'));
                return Redirect::back();
            }

        }

        // TODO - Refactor
        $tournamentParticipant                              = new EventTournamentParticipant();
        $tournamentParticipant->ticket_id        = $request->ticket_id;
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
     * Register Pug to Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return RedirectResponse
     */
    public function registerPug(Event $event, EventTournament $tournament, Request $request)
    {


        $ticket = $tournament->event->tickets()->where('id', $request->ticket_id)->first();

        if (!$ticket) {
            Session::flash('alert-danger', __('tickets.alert_ticket_not_found'));
            return Redirect::back();
        }

        if ($tournament->status != 'OPEN') {
            Session::flash('alert-danger', __('events.tournament_signups_not_permitted'));
            return Redirect::back();
        }

        if (!$ticket->signed_in) {
            Session::flash('alert-danger', __('events.tournament_not_signed_in'));
            return Redirect::back();
        }

        if ($tournament->getParticipant($ticket->id)) {
            Session::flash('alert-danger', __('events.tournament_already_signed_up'));
            return Redirect::back();
        }

        if (!$tournament->event->tournaments_freebies && $ticket->free) {
            Session::flash('alert-danger', __('events.tournament_freebie_not_permitted'));
            return Redirect::back();
        }

        if (!$tournament->event->tournaments_staff && $ticket->staff) {
            Session::flash('alert-danger', __('events.tournament_staff_not_permitted'));
            return Redirect::back();
        }

        if ($tournament->game->gamematchapihandler != 0 && $tournament->match_autoapi)
        {
            if (!Helpers::checkUserFields(User::where('id', '=', Ticket::where('id', '=', $request->ticket_id)->first()->user_id)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler)->getuserthirdpartyrequirements()))
            {
                Session::flash('alert-danger', __('events.tournament_cannot_join_thirdparty'));
                return Redirect::back();
            }

        }

        $tournamentParticipant                          = new EventTournamentParticipant();
        $tournamentParticipant->ticket_id    = $request->ticket_id;
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
     * @return RedirectResponse
     */
    public function unregister(Event $event, EventTournament $tournament, Request $request)
    {
        if (!$tournamentParticipant = $tournament->getParticipant($request->ticket_id)) {
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
