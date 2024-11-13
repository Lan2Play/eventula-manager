<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Settings;
use Arr;

use App\User;
use App\Game;
use App\Http\Controllers\Admin\GameServerCommandsController;
use App\MatchMaking;
use App\MatchMakingTeam;
use App\MatchMakingTeamPlayer;
use App\Jobs\GameServerAsign;
use App\GameMatchApiHandler;
use Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MatchMakingController extends Controller
{
    /**
     * Show MatchMaking Index Page
     * @return View
     */
    public function index()
    {
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

        foreach (MatchMaking::where(['status' => 'OPEN'])->orWhere(['status' => 'LIVE'])->orWhere(['status' => 'DRAFT'])->orWhere(['status' => 'PENDING'])->get() as $match) {
            if ($match->getMatchTeamPlayer(Auth::id())) {
                $currentuseropenlivependingdraftmatches[$match->id] = $match->id;
            }
        }

        return view('matchmaking.index')
            ->with('openPublicMatches', $openpublicmatches)
            ->with('liveClosedPublicMatches', $liveclosedpublicmatches)
            ->with('memberedTeams', $memberedteams)
            ->with('ownedMatches', $ownedmatches)
            ->with('currentUserOpenLivePendingDraftMatches', $currentuseropenlivependingdraftmatches)
            ->with('isMatchMakingEnabled', Settings::isMatchMakingEnabled());
    }

    /**
     * Show Matchmaking
     * @param MatchMaking $match
     * @param Request $request
     * @return View
     */
    public function show(MatchMaking $match, Request $request)
    {
        $teamjoin = null;
        if (isset($request)) {
            if (isset($request->teamjoin)) {
                if ($match->teams->where("id", $request->teamjoin)->count() > 0) {
                    $teamjoin = $match->teams->where("id", $request->teamjoin)->first();
                }
            }
        }

        $invite = null;
        if (isset($request)) {
            if (isset($request->invite)) {
                if (MatchMaking::where("invite_tag", $request->invite)->count() > 0) {
                    $invite = MatchMaking::where("invite_tag", $request->invite)->first();
                }
            }
        }

        $allusers = User::all();
        $selectallusers = array();
        $availableusers = array();



        foreach ($allusers as $user) {
            $selectallusers[$user->id] = $user->username;
        }





        foreach ($allusers as $user) {

            $alreadyjoined = false;
            foreach ($match->teams as $team) {
                if (Arr::first($team->players, function ($value, $key) use ($user) {
                    return $value->user_id == $user->id;
                }, false)) {
                    $alreadyjoined = true;
                }
            }

            if (!$alreadyjoined) {
                $availableusers[$user->id] = $user->username;
            }
        }






        return view('matchmaking.show')
            ->with('match', $match)
            ->with('availableUsers', $availableusers)
            ->with('users', $selectallusers)
            ->with('teamJoin', $teamjoin)
            ->with('invite', $invite);
    }

    /**
     * Store Match to Database
     * @param  Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        $rules = [
            'team1name'     => 'required|string',
            'team_size'     => 'required|in:1v1,2v2,3v3,4v4,5v5,6v6',
            'team_count'     => 'required|integer',
        ];
        $messages = [

            'team_size.required'    => __('matchmaking.team_size_required'),
            'team_size.in'    => __('matchmaking.team_size_mustbeoneof'),
            'team_count.required'    => __('matchmaking.team_count_required'),
            'team_count.integer'    => __('matchmaking.team_count_integer'),
        ];
        $this->validate($request, $rules, $messages);


        $currentuseropenlivependingdraftmatches = array();

        foreach (MatchMaking::where(['status' => 'OPEN'])->orWhere(['status' => 'LIVE'])->orWhere(['status' => 'DRAFT'])->orWhere(['status' => 'PENDING'])->get() as $match) {
            if ($match->getMatchTeamPlayer(Auth::id())) {
                $currentuseropenlivependingdraftmatches[$match->id] = $match->id;
            }
        }

        if (Settings::getSystemsMatchMakingMaxopenperuser() != 0 && count($currentuseropenlivependingdraftmatches) >= Settings::getSystemsMatchMakingMaxopenperuser()) {
            Session::flash('alert-danger', __('matchmaking.maxopened'));
            return Redirect::back();
        }

        $match                             = new MatchMaking();

        $game_id = null;
        if (isset($request->game_id)) {
            if (Game::where('id', $request->game_id)->first()) {
                $tempgame = Game::where('id', $request->game_id)->first();

                if ($tempgame->gamematchapihandler != 0 && $tempgame->matchmaking_autoapi) {
                    if (!Helpers::checkUserFields(User::where('id', '=', Auth::id())->first(), (new GameMatchApiHandler())->getGameMatchApiHandler($tempgame->gamematchapihandler)->getuserthirdpartyrequirements())) {
                        Session::flash('alert-danger', __('matchmaking.cannotcreatethirdparty'));
                        return Redirect::back();
                    }
                }

                $game_id = $request->game_id;
                if ($tempgame->min_team_count > 0 && $tempgame->max_team_count > 0) {

                    if ($request->team_count < $tempgame->min_team_count) {
                        Session::flash('alert-danger', __('matchmaking.teamcount_smallerthangamesmin') . $tempgame->min_team_count);
                        return Redirect::back();
                    }

                    if ($request->team_count > $tempgame->max_team_count) {
                        Session::flash('alert-danger', __('matchmaking.teamcount_biggerthangamesmax') . $tempgame->max_team_count);
                        return Redirect::back();
                    }
                }
            }
        }



        $match->game_id                    = $game_id;
        $match->status                     = 'OPEN';
        $match->ispublic                     = ($request->ispublic ? true : false);
        $match->team_size                  = $request->team_size[0];
        $match->team_count                  = $request->team_count;
        $match->owner_id                   = Auth::id();
        $match->invite_tag                 = "match_" . Str::random();

        if (!$match->save()) {
            Session::flash('alert-danger', __('matchmaking.cannotcreatematch'));
            return Redirect::back();
        }

        $team1                             = new MatchMakingTeam();
        $team1->name                       = $request->team1name;
        $team1->team_owner_id                 = Auth::id();
        $team1->team_invite_tag             = "team_" . Str::random();
        $team1->match_id                    = $match->id;
        if (!$team1->save()) {

            if (!$match->delete()) {
                Session::flash('alert-danger', __('matchmaking.cannotcreatteambutcannotdeletematch'));
                return Redirect::back();
            } else {
                Session::flash('alert-danger', __('matchmaking.cannotcreateteam1'));
                return Redirect::back();
            }
        }

        $teamplayerone                             = new MatchMakingTeamPlayer();
        $teamplayerone->matchmaking_team_id                       = $team1->id;
        $teamplayerone->user_id                  = Auth::id();
        if (!$teamplayerone->save()) {




            if (!$team1->delete()) {
                Session::flash('alert-danger', __('matchmaking.cannotcreateteamplayer1butcannotdeleteteam'));
                return Redirect::back();
            } else {

                if (!$match->delete()) {
                    Session::flash('alert-danger', __('matchmaking.cannotcreateteamplayer1butcannotdeletematch'));
                    return Redirect::back();
                } else {
                    Session::flash('alert-danger', __('matchmaking.cannotcreateteamplayer1'));
                    return Redirect::back();
                }
            }
        }

        Session::flash('alert-success', __('matchmaking.successfullycreatedmatch'));
        return Redirect::back();
    }

    /**
     * Store Match to Database
     * @param MatchMaking $match
     * @param  Request $request
     * @return Redirect
     */
    public function update(MatchMaking $match, Request $request)
    {

        $currentuser                  = Auth::id();

        if ($match->owner_id != $currentuser) {
            Session::flash('alert-danger', __('matchmaking.cannotupdatematchnotowner'));
            return Redirect::back();
        }

        $rules = [
            'team_size'     => 'required|in:1v1,2v2,3v3,4v4,5v5,6v6',
            'team_count'     => 'required|integer',
        ];
        $messages = [

            'team_size.required'    => __('matchmaking.team_size_required'),
            'team_count.required'    => __('matchmaking.team_count_required'),
            'team_count.integer'    => __('matchmaking.team_count_integer'),

        ];

        $this->validate($request, $rules, $messages);

        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.cannotupdatematchstatus'));
            return Redirect::back();
        }

        $game_id = null;
        if (isset($request->game_id)) {
            if (Game::where('id', $request->game_id)->first()) {
                $tempgame = Game::where('id', $request->game_id)->first();
                $game_id = $request->game_id;
                if ($tempgame->min_team_count > 0 && $tempgame->max_team_count > 0) {

                    if ($request->team_count < $tempgame->min_team_count) {
                        Session::flash('alert-danger', __('matchmaking.teamcount_smallerthangamesmin') . $tempgame->min_team_count);

                        return Redirect::back();
                    }

                    if ($request->team_count > $tempgame->max_team_count) {
                        Session::flash('alert-danger', __('matchmaking.teamcount_biggerthangamesmax') . $tempgame->max_team_count);
                        return Redirect::back();
                    }
                }
            }
        }

        foreach ($match->teams as $team) {
            if ($team->players->count() > $request->team_size[0]) {
                Session::flash('alert-danger', __('matchmaking.tomanyplayersforteamsize'));
                return Redirect::back();
            }
        }

        $match->game_id                    = $game_id;
        $match->ispublic                   = ($request->ispublic ? true : false);
        $match->team_size                  = $request->team_size[0];
        $match->team_count                 = $request->team_count;

        if (!$match->save()) {
            Session::flash('alert-danger', __('matchmaking.cannotupdatematch'));
            return Redirect::back();
        }


        Session::flash('alert-success', __('matchmaking.successfullyupdatedmatch'));
        return Redirect::back();
    }

    /**
     * add team to match
     * @param MatchMaking $match
     * @param  Request $request
     * @return Redirect
     */
    public function addteam(MatchMaking $match, Request $request)
    {
        $rules = [
            'teamname'          => 'required',
        ];
        $messages = [
            'teamname.required'    => __('matchmaking.teamname_required'),

        ];
        $this->validate($request, $rules, $messages);

        if ($match->game->gamematchapihandler != 0 && $match->game->matchmaking_autoapi) {
            if (!Helpers::checkUserFields(User::where('id', '=', Auth::id())->first(), (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler)->getuserthirdpartyrequirements())) {
                Session::flash('alert-danger', __('matchmaking.cannotjointhirdparty'));
                return Redirect::back();
            }
        }

        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.cannotaddteamstatus'));
            return Redirect::back();
        }


        if ($match->team_count != 0 && $match->team_count == $match->teams->count()) {
            Session::flash('alert-danger', __('matchmaking.cannotaddteamcount'));
            return Redirect::back();
        }


        foreach ($match->teams as $team) {
            if (Arr::first($team->players, function ($value, $key) use ($request) {
                return $value->user_id == Auth::id();
            }, false)) {
                Session::flash('alert-danger', __('matchmaking.youalreadyareinateam'));
                return Redirect::back();
            }
        }



        $team                             = new MatchMakingTeam();
        $team->name                       = $request->teamname;
        $team->team_owner_id                 = Auth::id();
        $team->match_id                      = $match->id;
        $team->team_invite_tag             = "team_" . Str::random();

        if (!$team->save()) {
            Session::flash('alert-danger', __('matchmaking.cannotcreateteam'));
            return Redirect::back();
        }

        $teamplayertwo                           = new MatchMakingTeamPlayer();
        $teamplayertwo->matchmaking_team_id      = $team->id;
        $teamplayertwo->user_id                  = Auth::id();
        if (!$teamplayertwo->save()) {
            Session::flash('alert-danger', __('matchmaking.cannotcreateteamplayerforowner'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('matchmaking.successfullyaddedteam'));
        return Redirect::back();
    }

    /**
     * update team
     * @param MatchMaking $match
     * @param MatchMakingTeam $team
     * @param  Request $request
     * @return Redirect
     */
    public function updateteam(MatchMaking $match, MatchMakingTeam $team,  Request $request)
    {
        $rules = [
            'editteamname'          => 'required',
        ];
        $messages = [
            'editteamname.required'    => __('matchmaking.teamname_required'),

        ];
        $this->validate($request, $rules, $messages);

        if ($team->team_owner_id != Auth::id() && $match->owner_id != Auth::id()) {
            Session::flash('alert-danger', __('matchmaking.cannotupdateteamnotowner'));
            return Redirect::back();
        }

        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.cannotupdateteamstatus'));
            return Redirect::back();
        }



        $team->name                       = $request->editteamname;
        if (!$team->save()) {
            Session::flash('alert-danger', __('matchmaking.cannotsaveteam'));
            return Redirect::back();
        }




        Session::flash('alert-success', __('matchmaking.successfullyupdatedteam'));
        return Redirect::back();
    }

    /**
     * delete team
     * @param MatchMaking $match
     * @param MatchMakingTeam $team
     * @param  Request $request
     * @return Redirect
     */
    public function deleteteam(MatchMaking $match, MatchMakingTeam $team,  Request $request)
    {
        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.cannotdeleteteamstatus'));
            return Redirect::back();
        }
        if ($team->id == $match->oldestTeam->id) {
            Session::flash('alert-danger', __('matchmaking.cannotdeleteinitialteam'));
            return Redirect::back();
        }
        if (!$team->players()->delete()) {
            Session::flash('alert-danger', __('matchmaking.cannotdeleteteamplayers'));
            return Redirect::back();
        }
        if (!$team->delete()) {
            Session::flash('alert-danger', __('matchmaking.cannotdeleteteam'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('matchmaking.deletedteam'));
        return Redirect::back();
    }

    /**
     * add user to match and team Database
     * @param MatchMaking $match
     * @param MatchMakingTeam $matchmakingteam
     * @param  Request $request
     * @return Redirect
     */
    public function addusertomatch(MatchMaking $match, MatchMakingTeam $team, Request $request)
    {
        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.cannnotjoinstatus'));
            return Redirect::back();
        }


        if ($team->players->count() >= $match->team_size) {
            Session::flash('alert-danger', __('matchmaking.cannotjoinalreadyfull'));
            return Redirect::back();
        }

        if ($match->game->gamematchapihandler != 0 && $match->game->matchmaking_autoapi) {
            if (!Helpers::checkUserFields(User::where('id', '=', Auth::id())->first(), (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler)->getuserthirdpartyrequirements())) {
                Session::flash('alert-danger', __('matchmaking.cannotjointhirdparty'));
                return Redirect::back();
            }
        }


        $teamplayer                             = new MatchMakingTeamPlayer();
        $teamplayer->matchmaking_team_id                       = $team->id;
        $teamplayer->user_id                  = Auth::id();
        if (!$teamplayer->save()) {
            Session::flash('alert-danger', __('matchmaking.cannotcreateteamplayer'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('matchmaking.successfiullyaddedteamplayer'));
        return Redirect::to('/matchmaking/' . $match->id);
    }

    /**
     * removes user from match and team Database
     * @param  MatchMaking $match
     * @param MatchMakingTeam $matchmakingteam
     * @return Redirect
     */
    public function deleteuserfrommatch(MatchMaking $match, MatchMakingTeam $team, MatchMakingTeamPlayer $teamplayer, Request $request)
    {

        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.cannotleavestatus'));
            return Redirect::back();
        }



        if (!$teamplayer->delete()) {
            Session::flash('alert-danger', __('matchmaking.cannotdeleteteamplayer'));
            return Redirect::back();
        }



        Session::flash('alert-success', __('matchmaking.successfullydeletedteamplayer'));
        return Redirect::back();
    }


    /**
     * removes user from match and team Database
     * @param  MatchMaking $match
     * @param MatchMakingTeam $matchmakingteam
     * @return Redirect
     */
    public function changeuserteam(MatchMaking $match, MatchMakingTeam $team, MatchMakingTeamPlayer $teamplayer, Request $request)
    {

        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.cannotleavestatus'));
            return Redirect::back();
        }


        if (!$teamplayer->delete()) {
            Session::flash('alert-danger', __('matchmaking.cannotdeleteteamplayer'));
            return Redirect::back();
        }

        $teamplayer                             = new MatchMakingTeamPlayer();
        $teamplayer->matchmaking_team_id                       = $team->id;
        $teamplayer->user_id                  = Auth::id();


        if (!$teamplayer->save()) {
            Session::flash('alert-danger', __('matchmaking.cannotcreateteamplayer'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('matchmaking.successfullychangedteamplayer'));
        return Redirect::back();
    }



    /**
     * Delete Match from Database
     * @param  MatchMaking $match
     * @return Redirect
     */
    public function destroy(MatchMaking $match)
    {
        $currentuser                  = Auth::id();

        if ($match->owner_id != $currentuser) {
            Session::flash('alert-danger', __('matchmaking.cannotdeletematchnotowner'));
            return Redirect::back();
        }



        if (!$match->players()->delete()) {
            Session::flash('alert-danger', __('matchmaking.cannotdeleteplayers'));
            return Redirect::back();
        }
        if (!$match->teams()->delete()) {
            Session::flash('alert-danger', __('matchmaking.cannotdeleteteams'));
            return Redirect::back();
        }

        foreach ($match->matchReplays as $matchReplay) {
            if (!$matchReplay->deleteReplayFile()) {
                Session::flash('alert-danger', __('matchmaking.cannotdeletereplayfiles'));
                return Redirect::back();
            }
            if (!$matchReplay->delete()) {
                Session::flash('alert-danger', __('matchmaking.cannotdeletereplays'));
                return Redirect::back();
            }
        }

        if (!$match->delete()) {
            Session::flash('alert-danger', __('matchmaking.cannotdeletematch'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('matchmaking.successfullydeletedmatch'));
        return Redirect::to('/matchmaking');
    }

    /**
     * Start Match
     * @param  MatchMaking $match
     * @return Redirect
     */
    public function start(MatchMaking $match)
    {
        $currentuser                  = Auth::id();

        if ($match->owner_id != $currentuser) {
            Session::flash('alert-danger', __('matchmaking.cannotstartmatchnotowner'));
            return Redirect::back();
        }

        if ($match->status == 'LIVE' || $match->status == 'COMPLETED' || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.matchalreadystartedorcompleted'));
            return Redirect::back();
        }

        if ($match->teams->count() < $match->team_count) {
            Session::flash('alert-danger', __('matchmaking.notallrequiredteamsarethere'));
            return Redirect::back();
        }

        foreach ($match->teams as $team) {
            if ($team->players->count() != $match->team_size) {
                Session::flash('alert-danger', __('matchmaking.notenoughplayerstostart'));
                return Redirect::back();
            }
        }

        if (isset($match->game) && $match->game->matchmaking_autostart) {
            GameServerAsign::dispatch($match, null, null)->onQueue('gameserver');


            if (isset($match->game) && $match->game->matchmaking_autoapi) {
                if (!$match->setStatus('WAITFORPLAYERS')) {
                    Session::flash('alert-danger', __('matchmaking.cannotstartmatch'));
                    return Redirect::back();
                }

                Session::flash('alert-success', __('matchmaking.matchstarted'));
                return Redirect::back();
            } else {
                if (!$match->setStatus('LIVE')) {
                    Session::flash('alert-danger', __('matchmaking.cannotstartmatch'));
                    return Redirect::back();
                }

                Session::flash('alert-success', __('matchmaking.matchstarted'));
                return Redirect::back();
            }

            if (!$match->setStatus('LIVE')) {
                Session::flash('alert-danger', __('matchmaking.cannotstartmatch'));
                return Redirect::back();
            }
            Session::flash('alert-success', __('matchmaking.matchstarted'));
            return Redirect::back();
        } else {
            if (!$match->setStatus('PENDING')) {
                Session::flash('alert-danger', __('matchmaking.cannotstartmatch'));
                return Redirect::back();
            }
            Session::flash('alert-success', __('matchmaking.matchpending'));
            return Redirect::back();
        }
    }

    /**
     * scramble TeamParticipants of Match
     * @param  MatchMaking $match
     * @return Redirect
     */
    public function scramble(MatchMaking $match)
    {
        $players = $match->players;
        $players = $players->shuffle();

        $teamSize = intval($match->team_size);
        $teams = $players->chunk($teamSize);

        foreach ($teams as $key => $team) {
            $teamToUpdate = $match->teams[$key];
            // After scrambling there is no team owner available.
            $teamToUpdate->team_owner_id = null;
            if (!$teamToUpdate->save()) {
                Session::flash('alert-danger', "Couldn´t set team owner id");
                return Redirect::back();
            }

            foreach ($team as $teamPlayer) {
                $teamPlayer->matchmaking_team_id = $teamToUpdate->id;
                if (!$teamPlayer->save()) {
                    Session::flash('alert-danger', "Couldn´t add a player to Team " . ($key + 1));
                    return Redirect::back();
                }
            }
        }

        return Redirect::back();
    }

    /**
     * open Match
     * @param  MatchMaking $match
     * @return Redirect
     */
    public function open(MatchMaking $match)
    {
        $currentuser                  = Auth::id();

        if ($match->owner_id != $currentuser) {
            Session::flash('alert-danger', __('matchmaking.cannotopenmatchnotowner'));
            return Redirect::back();
        }

        if ($match->status == 'OPEN' || $match->status == 'LIVE' || $match->status == 'COMPLETED' || $match->status == 'WAITFORPLAYERS' || $match->status == 'PENDING') {
            Session::flash('alert-danger', __('matchmaking.matchalreadyopenliveorcompleted'));
            return Redirect::back();
        }


        if (!$match->setStatus('OPEN')) {
            Session::flash('alert-danger', __('matchmaking.cannotopenmatch'));
            return Redirect::back();
        }

        Session::flash('alert-success', __('matchmaking.matchopened'));
        return Redirect::back();
    }

    /**
     * Finalize Match
     * @param  MatchMaking $match
     * @param Request $request
     * @return Redirect
     */
    public function finalize(MatchMaking $match, Request $request)
    {
        $currentuser                  = Auth::id();

        if ($match->owner_id != $currentuser) {
            Session::flash('alert-danger', __('matchmaking.cannotfinalizenotowner'));
            return Redirect::back();
        }
        foreach ($match->teams as $team) {
            $teamvalue = null;
            foreach ($request->all() as $key => $value) {

                if (Str::startsWith($key, 'teamscore_') && Str::of($key)->endsWith($team->id)) {

                    if (is_numeric($value)) {
                        $teamvalue = $value;
                    }
                }
            }

            if ($teamvalue == null) {
                Session::flash('alert-danger', __('matchmaking.missingscoreforteam'));
                return Redirect::back();
            }
        }

        foreach ($match->teams as $team) {
            foreach ($request->all() as $key => $value) {

                if (Str::startsWith($key, 'teamscore_') && Str::of($key)->endsWith($team->id)) {

                    $team->team_score = $value;
                    if (!$team->save()) {
                        Session::flash('alert-danger', __('matchmaking.scorecouldnotbesetted'));
                        return Redirect::back();
                    }
                }
            }
        }


        if (!$match->setStatus('COMPLETE')) {
            Session::flash('alert-danger', __('matchmaking.cannotfinalize'));
            return Redirect::back();
        }

        if (isset($match->matchMakingServer)) {
            if (!$match->matchMakingServer->delete()) {
                Session::flash('alert-danger', __('matchmaking.cannotdeletemmserver'));
                return Redirect::back();
            }
        }


        Session::flash('alert-success', __('matchmaking.matchfinalized'));
        return Redirect::back();
    }


    /**
     * Matchmaking invite
     * @param  Request $request
     * @return Redirect
     */
    public function showInvite(Request $request)
    {
        $user = Auth::user();
        if ($user) {

            $teaminvite = MatchMakingTeam::where("team_invite_tag", $request->url)->first();
            $matchinvite = MatchMaking::where("invite_tag", $request->url)->first();

            if (isset($teaminvite) && $teaminvite->count() > 0) {
                return Redirect::to('/matchmaking/' . $teaminvite->match->id . "/?teamjoin=" . $teaminvite->id);
            }
            if (isset($matchinvite) && $matchinvite->count() > 0) {
                return Redirect::to('/matchmaking/' . $matchinvite->id . "/?invite=" . $matchinvite->invite_tag);
            }
            if (!isset($matchinvite) || isset($teaminvite) || ($teaminvite->count() == 0 && $matchinvite->count() == 0)) {
                $request->session()->flash('alert-danger', __('matchmaking.invitationnotfound'));
                return Redirect::to('/');
            }
        }
        $request->session()->flash('alert-danger', __('matchmaking.pleaselogin'));
        return Redirect::to('login');
    }
}
