<?php

namespace App\Http\Controllers\Admin\Events;

use DB;
use Auth;
use Session;
use DateTime;
use Storage;
use Debugbar;

use App\User;
use App\Event;
use App\Game;
use App\EventParticipant;
use App\EventTournament;
use App\EventTournamentMatchServer;
use App\EventTournamentParticipant;
use App\EventTournamentTeam;
use App\Jobs\GameServerAsign;
use App\GameMatchApiHandler;
use Helpers;


use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class TournamentsController extends Controller
{
    /**
     * Show Tournaments Index Page
     * @param  Event  $event
     * @return View
     */
    public function index(Event $event)
    {
        return view('admin.events.tournaments.index')
            ->with('event', $event)
            ->with('tournaments', $event->tournaments()->paginate(10))
        ;
    }

    /**
     * Show Tournaments Page
     * @param  Event            $event
     * @param  EventTournament  $tournament
     * @return View
     */
    public function show(Event $event, EventTournament $tournament)
    {
        return view('admin.events.tournaments.show')
            ->with('event', $event)
            ->with('tournament', $tournament);
    }

    /**
     * Store Tournament to Database
     * @param  Event   $event
     * @param  Request $request
     * @return Redirect
     */
    public function store(Event $event, Request $request)
    {
        if (config('challonge.api_key') == null) {
            Session::flash('message', 'Cannot create Tournament! Please enter the Challonge API Key in Settings');
            return Redirect::back();
        }
        $rules = [
            'name'                  => 'required',
            'format'                => 'required|in:single elimination,double elimination,round robin,list',
            'bestof'                => 'required|in:one,three,threefinal,threesemifinalfinal',
            'grand_finals_modifier' => 'required|in:skip,singlematch,doublematch',
            'team_size'     => 'required|in:1v1,2v2,3v3,4v4,5v5,6v6',
            'description'   => 'required',
            'rules'         => 'required',
            'image'         => 'image',
        ];
        $messages = [
            'name.required'                         => 'Tournament name is required',
            'format.required'                       => 'Format is required',
            'format.in'                             => 'Single Elimation, Double Elimination, List or Round Robin only',
            'bestof.required'                       => 'bestof is required',
            'bestof.in'                             => 'Best of one, Best of three, Best of three finals, Best of three semifinals + finals',
            'grand_finals_modifier.required'        => 'Grand Finals modifier is required',
            'grand_finals_modifier.in'              => 'skip, singlematch ,doublematch',
            'team_size.required'                    => 'Team size is required',
            'team_size.in'                          => 'Team Size must be in format 1v1, 2v2, 3v3 etc',
            'description.required'                  => 'Description is required',
            'rules.required'                        => 'Rules are required',
            'image.image'                           => 'Tournament image must be a Image'
        ];
        $this->validate($request, $rules, $messages);

        $game_id = null;
        if (isset($request->game_id)) {
            if (Game::where('id', $request->game_id)->first()) {
                $game_id = $request->game_id;
            }
        }
        $tournament                             = new EventTournament();
        $tournament->event_id                   = $event->id;
        $tournament->challonge_tournament_url   = Str::random(16);
        $tournament->name                       = $request->name;
        $tournament->game_id                    = $game_id;
        $tournament->format                     = $request->format;
        $tournament->team_size                  = $request->team_size;
        $tournament->description                = $request->description;
        $tournament->bestof                     = $request->bestof;
        $tournament->grand_finals_modifier      = $request->grand_finals_modifier;
        $tournament->rules                      = $request->rules;
        $tournament->allow_bronze               = ($request->allow_bronze ? true : false);
        $tournament->allow_player_teams         = ($request->allow_player_teams ? true : false);
        $tournament->random_teams               = ($request->random_teams ? true : false);
        $tournament->match_autoapi              = ($request->match_autoapi ? true : false);
        $tournament->match_autostart            = ($request->match_autostart ? true : false);


        $tournament->status                     = 'DRAFT';

        if (!$tournament->save()) {
            Session::flash('message', 'Cannot create Tournament!');
            return Redirect::back();
        }

        Session::flash('message', 'Successfully created Tournament!');
        return Redirect::back();
    }

    /**
     * Update Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return Redirect
     */
    public function update(Event $event, EventTournament $tournament, Request $request)
    {
        $rules = [
            'name'          => 'filled',
            'status'        => 'in:DRAFT,OPEN,CLOSED,LIVE,COMPLETE',
            'description'   => 'filled',
            'rules'         => 'filled',
            'bestof'        => 'in:one,three,threefinal,threesemifinalfinal',
        ];
        $messages = [
            'name.filled'           => 'Tournament name cannot be empty',
            'status.in'             => 'Status must be DRAFT, OPEN, CLOSED, LIVE or COMPLETE',
            'description.filled'    => 'Description cannot be empty',
            'bestof.in'             => 'Best of one, Best of three, Best of three finals, Best of three semifinals + finals',
            'rules.filled'          => 'Rules cannot be empty',
        ];
        $this->validate($request, $rules, $messages);

        if (isset($request->status) && $request->status != $tournament->status) {
            if (!$tournament->setStatus($request->status)) {
                Session::flash('alert-danger', 'Tournament status cannot be updated!');
                return Redirect::back();
            }
        }

        if ($tournament->status != 'LIVE') {
            $tournament->name           = $request->name;
            $tournament->description    = $request->description;
            $tournament->rules          = $request->rules;
            $tournament->bestof         = $request->bestof;


            $disallowed_array = ['OPEN', 'CLOSED', 'LIVE', 'COMPLETED'];
            if (!in_array($tournament->status, $disallowed_array)) {
                $game_id = null;
                if (isset($request->game_id)) {
                    if (Game::where('id', $request->game_id)->first()) {
                        $game_id = $request->game_id;
                    }
                }

                $tournament->game_id                    = $game_id;
            }
        }

        $tournament->match_autoapi               = ($request->match_autoapi ? true : false);
        $tournament->match_autostart               = ($request->match_autostart ? true : false);


        if (!$tournament->save()) {
            session::flash('alert-danger', 'Cannot update Tournament!');
            return Redirect::back();
        }

        session::flash('alert-success', 'Successfully updated Tournament!');
        return Redirect::back();
    }

    /**
     * Delete Tournament from Database
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @return Redirect
     */
    public function destroy(Event $event, EventTournament $tournament)
    {
        if (!$tournament->delete()) {
            Session::flash('alert-danger', 'Cannot delete Tournament!');
            return Redirect::to('admin/events/' . $event->slug . '/tournaments');
        }

        Session::flash('alert-success', 'Successfully deleted Tournament!');
        return Redirect::to('admin/events/' . $event->slug . '/tournaments');
    }

    /**
     * Start Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @return Redirect
     */
    public function start(Event $event, EventTournament $tournament)
    {
        if ($tournament->tournamentParticipants->count() < 2) {
            Session::flash('alert-danger', 'Tournament doesnt have enough participants');
            return Redirect::back();
        }

        if ($tournament->status == 'LIVE' || $tournament->status == 'COMPLETED') {
            Session::flash('alert-danger', 'Tournament is already live or completed');
            return Redirect::back();
        }

        if($tournament->random_teams) {
            // Create Random Teams
            $shuffledParticipants = $tournament->tournamentParticipants;//->toArray();// (new \ArrayObject($tournament->tournamentParticipants))->getArrayCopy();

            // Debugbar::addMessage("Teams: " . json_encode($shuffledParticipants), 'Tournament');

            $shuffledParticipants = $shuffledParticipants->shuffle();
            // shuffle($shuffledParticipants);

            $teamSize = intval ($tournament->team_size[0]);
            // Debugbar::addMessage("TeamSize: $teamSize($tournament->team_size)", 'Tournament');

            // $teams = array_chunk($shuffledParticipants, $teamSize);
            $teams = $shuffledParticipants->chunk($teamSize);

            // Debugbar::addMessage("Teams: " . json_encode($teams), 'Tournament');

            // $key = 0;
            // $team = $shuffledParticipants;
            foreach($teams as $key=>$team) {
                // Debugbar::addMessage("TeamType: " . gettype($team) . " Team: " . json_encode($team), 'Tournament');
                $tournamentTeam                         = new EventTournamentTeam();
                $tournamentTeam->event_tournament_id    = $tournament->id;
                $tournamentTeam->name                   = "Team " . ($key + 1);

                if (!$tournamentTeam->save()) {
                    Session::flash('alert-danger', "Couldnt save random Team " + ($key + 1));
                    return Redirect::back();
                }

                Debugbar::addMessage("EventTournamentTeam: " . json_encode($tournamentTeam), 'Tournament');

                foreach($team as $teamParticipant) {
                    // Debugbar::addMessage("TeamParticipantType: " . gettype($teamParticipant) . " TeamParticipant: " . json_encode($teamParticipant), 'Tournament');
                    $teamParticipant->event_tournament_team_id    = $tournamentTeam->id;
                    $teamParticipant->event_tournament_id         = $tournament->id;
                    $teamParticipant->event_tournament_team_id    = $tournamentTeam->id;

                    if (!$teamParticipant->save()) {
                        Session::flash('alert-danger', "Couldn´t add a player to Team " . ($key + 1));
                        return Redirect::back();
                    }
                }
            }

            $tournament->load('tournamentTeams');
        }

        if (!$tournament->tournamentTeams->isEmpty()) {
            foreach ($tournament->tournamentTeams as $team) {
                $team->load('tournamentParticipants');
                if ($team->tournamentParticipants->isEmpty()) {
                    Debugbar::addMessage("Team is empty: $team->name", 'Tournament');
                    if (!$team->delete()) {
                        Session::flash('message', 'Error connecting to Challonge!');
                        return Redirect::to('admin/events/' . $event->slug . '/tournaments');
                    }
                }
            }
        }

        if (!$tournament->setStatus('LIVE')) {
            Session::flash('alert-danger', 'Cannot start Tournament!');
            return Redirect::back();
        }


        if (isset($tournament->game) && $tournament->match_autostart)
        {


            $nextmatches = $tournament->getNextMatches();

            foreach ($nextmatches as $nextmatch)
            {
                GameServerAsign::dispatch(null,$tournament,$nextmatch->id)->onQueue('gameserver');

            }


        }

        Session::flash('alert-success', 'Tournament Started!');
        return Redirect::back();
    }

    /**
     * Finalize Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @return Redirect
     */
    public function finalize(Event $event, EventTournament $tournament)
    {
        if (!$tournament->setStatus('COMPLETE')) {
            Session::flash('alert-danger', 'Cannot finalize. Tournament is still live!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Tournament Finalized!');
        return Redirect::back();
    }


    /**
     * Enable live Editing
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @return Redirect
     */
    public function enableliveediting(Event $event, EventTournament $tournament)
    {

        $tournament->enable_live_editing  = true;

        if (!$tournament->save()) {
            session::flash('alert-danger', 'Cannot enable liveediting!');
            return Redirect::back();
        }

        session::flash('alert-success', 'Successfully enabled liveediting!');
        return Redirect::back();
    }

    /**
     * Disable live Editing
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @return Redirect
     */
    public function disableliveediting(Event $event, EventTournament $tournament)
    {

        $tournament->enable_live_editing  = false;

        if (!$tournament->save()) {
            session::flash('alert-danger', 'Cannot disable liveediting!');
            return Redirect::back();
        }

        session::flash('alert-success', 'Successfully disabled liveediting!');
        return Redirect::back();
    }

    /**
     * Update Participant Team
     * @param  Event                      $event
     * @param  EventTournament            $tournament
     * @param  EventTournamentParticipant $participant
     * @param  Request                    $request
     * @return Redirect
     */
    public function updateParticipantTeam(
        Event $event,
        EventTournament $tournament,
        EventTournamentParticipant $participant,
        Request $request
    ) {
        $rules = [
            'event_tournament_team_id'  => 'required'
        ];
        $messages = [
            'event_tournament_team_id|required' => 'A Team ID is required.'
        ];
        $this->validate($request, $rules, $messages);

        $participant->event_tournament_team_id = $request->event_tournament_team_id;

        if (!$participant->save()) {
            Session::flash('alert-danger', 'Cannot update Participant!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully updated Participant!');
        return Redirect::back();
    }


       /**
     * Add Pug to Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  EventParticipant $participant
     * @param  Request         $request
     * @return Redirect
     */
    public function addPug(Event $event, EventTournament $tournament, EventParticipant $participant, Request $request)
    {
         if (!$tournament->event->eventParticipants()->where('id', $participant->id)->first()) {
            Session::flash('alert-danger', __('events.tournament_not_signed_in'));
            return Redirect::back();
        }

        if ($tournament->getParticipant($participant->id)) {
            Session::flash('alert-danger', __('events.tournament_already_signed_up'));
            return Redirect::back();
        }

        if (isset($tournament->game))
        {
            if ($tournament->game->gamematchapihandler != 0 && $tournament->match_autoapi)
            {
                if (!Helpers::checkUserFields(User::where('id', '=', $participant->user_id)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler)->getuserthirdpartyrequirements()))
                {
                    Session::flash('alert-danger', __('events.tournament_cannot_join_thirdparty'));
                    return Redirect::back();
                }

            }
        }


        $tournamentParticipant                          = new EventTournamentParticipant();
        $tournamentParticipant->event_participant_id    = $participant->id;
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
     * Add player to Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return [type]
     */
    public function addSingle(Event $event, EventTournament $tournament, EventParticipant $participant, Request $request)
    {
        if (!$tournament->event->eventParticipants()->where('id', $participant->id)->first()) {
            Session::flash('alert-danger', __('events.tournament_not_signed_in'));
            return Redirect::back();
        }

        if ($tournament->getParticipant($participant->id)) {
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

        if (isset($tournament->game))
        {
            if ($tournament->game->gamematchapihandler != 0 && $tournament->match_autoapi)
            {
                if (!Helpers::checkUserFields(User::where('id', '=', $participant->user_id)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler)->getuserthirdpartyrequirements()))
                {
                    Session::flash('alert-danger', __('events.tournament_cannot_join_thirdparty'));
                    return Redirect::back();
                }

            }
        }

        // TODO - Refactor
        $tournamentParticipant                              = new EventTournamentParticipant();
        $tournamentParticipant->event_participant_id        = $participant->id;
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
     * add Team to Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return Redirect
     */
    public function addTeam(Event $event, EventTournament $tournament, Request $request)
    {

        $tournamentTeam                         = new EventTournamentTeam();
        $tournamentTeam->event_tournament_id    = $tournament->id;
        $tournamentTeam->name                   = $request->team_name;

        if (!$tournamentTeam->save()) {
            Session::flash('alert-danger', __('events.tournament_can_not_add_team'));
            return Redirect::back();
        }


        Session::flash('alert-success', __('events.tournament_team_created'));
        return Redirect::back();
    }

    /**
     * a Participant from Tournament
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @param  EventParticipant $participant
     * @return Redirect
     */
    public function unregisterParticipant(Event $event, EventTournament $tournament, EventParticipant $participant, Request $request)
    {


        if (!$tournamentParticipant = $tournament->getParticipant($participant->id)) {

            Session::flash('alert-danger', 'Participant is not signed up.');
            return Redirect::back();
        }

        if (!$tournamentParticipant->delete()) {
            Session::flash('alert-danger', 'Cannot remove. Please try again.');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Participant has been successfully removed from the Tournament.');
        return Redirect::back();
    }

    /**
     * Update Match on Challone API
     * @param  Event           $event
     * @param  EventTournament $tournament
     * @param  Request         $request
     * @return Redirect
     */
    public function updateMatch(Event $event, EventTournament $tournament, Request $request)
    {
        $rules = [
            'player1_score'         => 'required',
            'player2_score'         => 'required',
            'tournament_match_id'   => 'required',
            'player_winner_verify'  => 'in:player1,player2'
        ];
        $messages = [
            'player1_score.required'        => 'Player 1 score is required.',
            'player2_score.required'        => 'Player 2 score is required.',
            'tournament_match_id.required'  => 'Tournament match ID is required',
            'player_winner_verify.in'       => 'Player winner Verify must be player1 or player2'
        ];
        $this->validate($request, $rules, $messages);

        if (!$tournament->updateMatch(
            $request->tournament_match_id,
            $request->player1_score,
            $request->player2_score,
            $request->player_winner_verify
        )
        ) {
            Session::flash('alert-danger', 'Cannot update match scores!');
            return Redirect::back();
        }

        if (isset($tournament->game) && $tournament->match_autostart)
        {


            $nextmatches = $tournament->getNextMatches();

            foreach ($nextmatches as $nextmatch)
            {
                GameServerAsign::dispatch(null,$tournament,$nextmatch->id)->onQueue('gameserver');

            }


        }

        $evtms= EventTournamentMatchServer::where(['challonge_match_id' => $request->tournament_match_id])->first();

        if(isset($evtms))
        {
            if (!$evtms->delete())
            {
                Session::flash('alert-danger', 'Cannot delete EventTournamentMatchServer entry, you have to remove the assignment manually');
                return Redirect::back();
            }


        }




        Session::flash('alert-success', 'Successfully updated match scores!');
        return Redirect::back();
    }

    /**
     * Legacy - Fix All tournament scores before the Tournament Localization Patch
     */
    public function fixScores()
    {
        EventTournament::getAllScoresRetroActively();
    }
}
