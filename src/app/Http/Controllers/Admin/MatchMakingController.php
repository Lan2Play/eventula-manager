<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use DateTime;
use Storage;
use Settings;
use Arr;
use Debugbar;

use App\User;
use App\Event;
use App\Game;
use App\MatchMaking;
use App\MatchMakingTeam;
use App\MatchMakingTeamPlayer;
use App\Jobs\GameServerAsign;
use App\GameMatchApiHandler;
use Helpers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Lanops\Challonge\Models\Match;

class MatchMakingController extends Controller
{
    /**
     * Show MatchMaking Index Page
     * @return View
     */
    public function index()
    {
        $allusers = User::all();
        $matches = MatchMaking::all()->sortByDesc('created_at');
        $pendingmatches = MatchMaking::where("status","PENDING")->get()->sortByDesc('updated_at');
        $livematches = MatchMaking::where("status","LIVE")->orWhere("status","WAITFORPLAYERS")->get()->sortByDesc('updated_at');

        $selectallusers = array();

        foreach($allusers as $user) {
            $selectallusers[$user->id] = $user->username;
        }
        return view('admin.matchmaking.index')
            ->with('matches', $matches)
            ->with('pendingMatches', $pendingmatches)
            ->with('liveMatches', $livematches)
            ->with('isMatchMakingEnabled', Settings::isMatchMakingEnabled())
            ->with('users', $selectallusers);
    }


    /**
     * Show Matchmaking
     * @param MatchMaking $match
     * @return View
     */
    public function show(MatchMaking $match)
    {
        $allusers = User::all();
        $selectallusers = array();
        $availableusers = array();



        foreach($allusers as $user) {
            $selectallusers[$user->id] = $user->username;
        }





        foreach($allusers as $user) {

            $alreadyjoined = false;
            foreach ($match->teams as $team)
            {
                if (Arr::first($team->players, function($value, $key)use($user){return $value->user_id == $user->id;},false))
                {
                    $alreadyjoined = true;
                }
            }

            if (!$alreadyjoined)
            {
                $availableusers[$user->id] = $user->username;
            }
        }






        return view('admin.matchmaking.show')
            ->with('match', $match)
            ->with('availableUsers', $availableusers)
            ->with('users', $selectallusers);

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
            'team1owner'    => 'required|exists:users,id',
            'team_size'     => 'required|in:1v1,2v2,3v3,4v4,5v5,6v6',
            'team_count'     => 'required|integer',
            'ownerid'               => 'required|exists:users,id',
        ];
        $messages = [

            'team_size.required'    => 'Team size is required',
            'team_size.in'    => 'Team size must be one of: 1v1,2v2,3v3,4v4,5v5,6v6',
            'team_count.required'    => 'Team Count is required',
            'team_count.integer'    => 'Team size must be an integer',
            'team1owner.required'    => 'ownerid is required',
            'team1owner.exists'          => 'ownerid must be a valid user id',
            'ownerid.required'    => 'ownerid is required',
            'ownerid.exists'          => 'ownerid must be a valid user id',

        ];
        $this->validate($request, $rules, $messages);




        $match                             = new MatchMaking();

        $game_id = null;
        if (isset($request->game_id)) {
            if (Game::where('id', $request->game_id)->first()) {
                $tempgame = Game::where('id', $request->game_id)->first();

                if ($tempgame->gamematchapihandler != 0 && $tempgame->matchmaking_autoapi)
                {
                    if (!Helpers::checkUserFields(User::where('id', '=', $request->team1owner)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($tempgame->gamematchapihandler)->getuserthirdpartyrequirements()))
                    {
                        Session::flash('alert-danger', "you cannot create the match with the selected team 1 owner, because the nessecary third party account link on the user is missing");
                        return Redirect::back();
                    }

                }

                $game_id = $request->game_id;
                if ($tempgame->min_team_count > 0 && $tempgame->max_team_count > 0)
                {

                    if ($request->team_count < $tempgame->min_team_count)
                    {
                        Session::flash('alert-danger', 'teamcount smaller than selected games minimal teamcount');
                        return Redirect::back();
                    }

                    if ($request->team_count > $tempgame->max_team_count)
                    {
                        Session::flash('alert-danger', 'teamcount bigger than selected games maximal teamcount');
                        return Redirect::back();
                    }


                }
            }
        }



        $match->game_id                    = $game_id;
        $match->status                     = 'DRAFT';
        $match->ispublic                     = ($request->ispublic ? true : false);
        $match->team_size                  = $request->team_size[0];
        $match->team_count                  = $request->team_count;
        $match->owner_id                   = $request->ownerid;
        $match->invite_tag                 = "match_" . Str::random();

        if (!$match->save()) {
            Session::flash('alert-danger', 'Cannot create Match!');
            return Redirect::back();
        }

        $team1                             = new MatchMakingTeam();
        $team1->name                       = $request->team1name;
        $team1->team_owner_id                 = $request->team1owner;
        $team1->team_invite_tag             = "team_" . Str::random();
        $team1->match_id                    =$match->id;
        if (!$team1->save()) {

            if (!$match->delete()) {
                Session::flash('alert-danger', 'Cannot create Team 1 but cannot delete Match! broken Database entry!');
                return Redirect::back();
            }
            else {
                Session::flash('alert-danger', 'Cannot create Team 1! Match not created!');
                return Redirect::back();
            }

        }

        $teamplayerone                             = new MatchMakingTeamPlayer();
        $teamplayerone->matchmaking_team_id                       = $team1->id;
        $teamplayerone->user_id                  = $request->team1owner;
        if (!$teamplayerone->save()) {




            if (!$team1->delete()) {
                Session::flash('alert-danger', 'Cannot create teamplayer 1 but cannot delete team! broken Database entry!');
                return Redirect::back();
            }
            else {

                if (!$match->delete()) {
                    Session::flash('alert-danger', 'Cannot create teamplayer 1 but cannot delete Match! broken Database entry!');
                    return Redirect::back();
                }
                else {
                    Session::flash('alert-danger', 'Cannot create teamplayer 1! Match and team not created!');
                    return Redirect::back();
                }
            }
        }

        Session::flash('alert-success', 'Successfully created Match!');
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
        $rules = [
            'team_size'     => 'required|in:1v1,2v2,3v3,4v4,5v5,6v6',
            'ownerid'               => 'required|exists:users,id',
            'team_count'     => 'required|integer',
        ];
        $messages = [

            'team_size.required'    => 'Team size is required',
            'ownerid.required'    => 'ownerid is required',
            'ownerid.exists'          => 'ownerid must be a valid user id',
            'team_count.required'    => 'Team Count is required',
            'team_count.integer'    => 'Team size must be an integer',

        ];

        $this->validate($request, $rules, $messages);

        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" ||  $match->status == "WAITFORPLAYERS"||  $match->status == "PENDING")
        {
            Session::flash('alert-danger', "you cannot update a match while it is live, waiting for players, pending or complete!");
            return Redirect::back();
        }

        $game_id = null;
        if (isset($request->game_id)) {
            if (Game::where('id', $request->game_id)->first()) {
                $tempgame = Game::where('id', $request->game_id)->first();
                $game_id = $request->game_id;
                if ($tempgame->min_team_count > 0 && $tempgame->max_team_count > 0)
                {

                    if ($request->team_count < $tempgame->min_team_count)
                    {
                        Session::flash('alert-danger', 'teamcount smaller than selected games minimal teamcount');
                        return Redirect::back();
                    }

                    if ($request->team_count > $tempgame->max_team_count)
                    {
                        Session::flash('alert-danger', 'teamcount bigger than selected games maximal teamcount');
                        return Redirect::back();
                    }


                }
            }
        }

        foreach ($match->teams as $team)
        {
            if ($team->players->count() > $request->team_size[0])
            {
                Session::flash('alert-danger', 'at least one team has to many players for this team size!');
                return Redirect::back();
            }
        }

        $match->game_id                    = $game_id;
        $match->ispublic                   = ($request->ispublic ? true : false);
        $match->team_size                  = $request->team_size[0];
        $match->team_count                 = $request->team_count;
        $match->owner_id                   = $request->ownerid;

        if (!$match->save()) {
            Session::flash('alert-danger', 'Cannot update Match!');
            return Redirect::back();
        }


        Session::flash('alert-success', 'Successfully updated Match!');
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
            'teamowner'               => 'required|exists:users,id',
        ];
        $messages = [
            'teamname.required'    => 'Team name is required',
            'teamowner.required'    => 'userid is required',
            'teamowner.exists'          => 'userid must be a valid user id',

        ];
        $this->validate($request, $rules, $messages);

        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" ||  $match->status == "WAITFORPLAYERS" ||  $match->status == "PENDING")
        {
            Session::flash('alert-danger', "you cannot add a team while the match is live,waitforplayers,pending or complete!");
            return Redirect::back();
        }


        if ($match->team_count != 0 && $match->team_count == $match->teams->count())
        {
            Session::flash('alert-danger', "no more teams could be added because of the team count limit!");
            return Redirect::back();
        }


        foreach($match->teams as $team)
        {
            if (Arr::first($team->players, function($value, $key)use($request){return $value->user_id == $request->teamowner;},false))
            {
                Session::flash('alert-danger', "specifyed owner is already in a team!");
                return Redirect::back();
            }
        }

        $team                             = new MatchMakingTeam();
        $team->name                       = $request->teamname;
        $team->team_owner_id              = $request->teamowner;
        $team->match_id                   = $match->id;
        $team->team_invite_tag            = "team_" . Str::random();

        if (!$team->save()) {
            Session::flash('alert-danger', 'Cannot create Team !');
            return Redirect::back();
        }

        $teamplayertwo                           = new MatchMakingTeamPlayer();
        $teamplayertwo->matchmaking_team_id      = $team->id;
        $teamplayertwo->user_id                  = $request->teamowner;
        if (!$teamplayertwo->save()) {
            Session::flash('alert-danger', 'Cannot create Teamplayer for Team 2 Owner!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully added team!');
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
            'teamname'          => 'required',
            'teamowner'               => 'required|exists:users,id',
        ];
        $messages = [
            'teamname.required'    => 'Team name is required',
            'teamowner.required'    => 'userid is required',
            'teamowner.exists'          => 'userid must be a valid user id',

        ];
        $this->validate($request, $rules, $messages);

        if ($match->game->gamematchapihandler != 0 && $match->game->matchmaking_autoapi)
        {
            if (!Helpers::checkUserFields(User::where('id', '=', $request->teamowner)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler)->getuserthirdpartyrequirements()))
            {
                Session::flash('alert-danger', "you cannot set this teamowner because the nessecary third party account link on the user is missing");
                return Redirect::back();
            }

        }

        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" ||  $match->status == "WAITFORPLAYERS" ||  $match->status == "PENDING")
        {
            Session::flash('alert-danger', "you cannot update a team while the match is live, waitforplayers, pending or complete!");
            return Redirect::back();
        }

        if ($team->team_owner_id != $request->teamowner)
        {

            foreach($match->teams as $matchteam)
            {
                if (Arr::first($matchteam->players, function($value, $key)use($request){return $value->user_id == $request->teamowner;},false))
                {
                    Session::flash('alert-danger', "specifyed owner is already in a team!");
                    return Redirect::back();
                }
            }


                if (!MatchMakingTeamPlayer::where(['user_id' => $team->team_owner_id, 'matchmaking_team_id' => $team->id])->delete()) {
                    Session::flash('alert-danger', 'Cannot delete old owner from team!');
                    return Redirect::back();
                }

                $teamplayertwo                           = new MatchMakingTeamPlayer();
                $teamplayertwo->matchmaking_team_id      = $team->id;
                $teamplayertwo->user_id                  = $request->teamowner;
                if (!$teamplayertwo->save()) {
                    Session::flash('alert-danger', 'Cannot create Teamplayer for Team Owner!');
                    return Redirect::back();
                }

            $team->team_owner_id                 = $request->teamowner;

            }



        $team->name                       = $request->teamname;
        if (!$team->save()) {
            Session::flash('alert-danger', 'Cannot save Team !');
            return Redirect::back();
        }




        Session::flash('alert-success', 'Successfully updated Team!');
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
        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" ||  $match->status == "WAITFORPLAYERS" ||  $match->status == "PENDING")
        {
            Session::flash('alert-danger', "you cannot delete a team while the match is live, waitforplayers,pending or complete!");
            return Redirect::back();
        }
        if ($team-> id == $match->oldestTeam->id)
        {
            Session::flash('alert-danger', 'you cannot delete the initial team!');
            return Redirect::back();
        }
        if (!$team->players()->delete()) {
            Session::flash('alert-danger', 'Cannot delete teamplayers!');
            return Redirect::back();
        }
        if (!$team->delete()) {
            Session::flash('alert-danger', 'Cannot delete team!');
            return Redirect::back();

        }

        Session::flash('alert-success', 'deleted team!');
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




         $rules = [
            'userid'               => 'required|exists:users,id',
        ];
        $messages = [
            'userid.required'    => 'userid is required',
            'userid.exists'          => 'userid must be a valid user id',

        ];
        $this->validate($request, $rules, $messages);


        if ($match->game->gamematchapihandler != 0 && $match->game->matchmaking_autoapi)
        {
            if (!Helpers::checkUserFields(User::where('id', '=', $request->userid)->first(),(new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler)->getuserthirdpartyrequirements()))
            {
                Session::flash('alert-danger', "you cannot add this user to the team because the nessecary third party account link on the user is missing");
                return Redirect::back();
            }

        }




        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" ||  $match->status == "WAITFORPLAYERS" ||  $match->status == "PENDING")
        {
            Session::flash('alert-danger', "you cannot add a user to the team while the match is live, waitforplayers,pending or complete!");
            return Redirect::back();
        }




        if ($team->players->count() >= $match->team_size)
        {
            Session::flash('alert-danger', 'Team is already full!');
            return Redirect::back();
        }


        $teamplayer                             = new MatchMakingTeamPlayer();
        $teamplayer->matchmaking_team_id                       = $team->id;
        $teamplayer->user_id                  = $request->userid;
        if (!$teamplayer->save()) {
            Session::flash('alert-danger', 'Cannot create Teamplayer!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully added Teamplayer!');
        return Redirect::back();
    }

    /**
     * removes user from match and team Database
     * @param  MatchMaking $match
     * @param MatchMakingTeam $matchmakingteam
     * @return Redirect
     */
    public function deleteuserfrommatch(MatchMaking $match, MatchMakingTeam $team, MatchMakingTeamPlayer $teamplayer, Request $request)
    {


        if ($match->status == "LIVE" ||  $match->status == "COMPLETE" ||  $match->status == "WAITFORPLAYERS" ||  $match->status == "PENDING")
        {
            Session::flash('alert-danger', "you cannot delete a user from the team while the match is live, waitforplayers, pending or complete!");
            return Redirect::back();
        }



        if (!$teamplayer->delete()) {
            Session::flash('alert-danger', 'Cannot delete Teamplayer!');
            return Redirect::back();
        }



            Session::flash('alert-success', 'Successfully deleted Teamplayer!');
            return Redirect::back();




    }


    /**
     * Delete Match from Database
     * @param  MatchMaking $match
     * @return Redirect
     */
    public function destroy(MatchMaking $match)
    {
        foreach ($match->matchReplays as $matchReplay)
        {
            if(!$matchReplay->deleteReplayFile())
            {
                Session::flash('alert-danger', 'Cannot delete Replay files!');
                return Redirect::back();
            }
            if(!$matchReplay->delete())
            {
                Session::flash('alert-danger', 'Cannot delete Replays!');
                return Redirect::back();
            }
        }
        if (!$match->players()->delete()) {
            Session::flash('alert-danger', 'Cannot delete Players!');
            return Redirect::back();
        }
        if (!$match->teams()->delete()) {
            Session::flash('alert-danger', 'Cannot delete Teams!');
            return Redirect::back();
        }
        if (!$match->delete()) {
            Session::flash('alert-danger', 'Cannot delete Match!');
            return Redirect::to('admin/matchmaking/');
        }

        Session::flash('alert-success', 'Successfully deleted Match!');
        return Redirect::to('admin/matchmaking/');
    }

    /**
     * Start Match
     * @param  MatchMaking $match
     * @return Redirect
     */
    public function start(MatchMaking $match)
    {


        if ($match->status == 'LIVE' || $match->status == 'COMPLETED' || $match->status == 'WAITFORPLAYERS') {
            Session::flash('alert-danger', 'Match is already live, waitforplayers or completed');
            return Redirect::back();
        }

        if ($match->teams->count() < $match->team_count)
        {
            Session::flash('alert-danger', 'Not all required teams are there');
            return Redirect::back();
        }

        foreach ($match->teams as $team)
        {
            if ($team->players->count() != $match->team_size)
            {
                Session::flash('alert-danger', 'at least one team has not enough players to start the match !');
                return Redirect::back();
            }
        }

        if ($match->status == "PENDING")
        {
            if (!$match->setStatus('LIVE')) {
                Session::flash('alert-danger', 'Cannot start Match!');
                return Redirect::back();
            }

            Session::flash('alert-success', 'Match Started!');
            return Redirect::back();
        }

        if (isset($match->game) && $match->game->matchmaking_autostart) {

            GameServerAsign::dispatch($match,null,null)->onQueue('gameserver');

        if (isset($match->game) && $match->game->matchmaking_autoapi)
        {
            if (!$match->setStatus('WAITFORPLAYERS')) {
                Session::flash('alert-danger', 'Cannot start Match!');
                return Redirect::back();
            }

            Session::flash('alert-success', 'Match Started!');
            return Redirect::back();
        }
        else
        {
            if (!$match->setStatus('LIVE')) {
                Session::flash('alert-danger', 'Cannot start Match!');
                return Redirect::back();
            }

            Session::flash('alert-success', 'Match Started!');
            return Redirect::back();
        }
        }
        else
        {
            if (!$match->setStatus('PENDING')) {
                Session::flash('alert-danger', 'Cannot start Match!');
                return Redirect::back();
            }
            Session::flash('alert-success', 'Match Started but pending, select a Server to start it!');
            return Redirect::back();
        }


    }

           /**
     * open Match
     * @param  MatchMaking $match
     * @return Redirect
     */
    public function open(MatchMaking $match)
    {

        if ($match->status == 'OPEN' || $match->status == 'LIVE' || $match->status == 'COMPLETED' || $match->status == 'WAITFORPLAYERS'|| $match->status == 'PENDING') {
            Session::flash('alert-danger', 'Match is already open/ live/ waitforplayers/pending or completed');
            return Redirect::back();
        }


        if (!$match->setStatus('OPEN')) {
            Session::flash('alert-danger', 'Cannot open Match!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Match Opened!');
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

        foreach ($match->teams as $team)
        {
            $teamvalue = null;
            foreach($request->all() as $key => $value) {

                if(Str::startsWith($key, 'teamscore_') && Str::of($key)->endsWith($team->id))
                {

                    if (is_numeric($value))
                    {
                        $teamvalue = $value;
                    }

                }

            }

            if ($teamvalue == null)
            {
                Session::flash('alert-danger', 'for at least one team no score was specified');
                return Redirect::back();
            }
        }

        foreach ($match->teams as $team)
        {
            foreach($request->all() as $key => $value) {

                if(Str::startsWith($key, 'teamscore_') && Str::of($key)->endsWith($team->id))
                {

                    $team->team_score = $value;
                    if (!$team->save()) {
                        Session::flash('alert-danger', 'Score for at least one team could not be setted!');
                        return Redirect::back();
                    }


                }

            }


        }


        if (!$match->setStatus('COMPLETE')) {
            Session::flash('alert-danger', 'Cannot finalize. Match is still live!');
            return Redirect::back();
        }

        if(isset($match->matchMakingServer))
        {
            if (!$match->matchMakingServer->delete()) {
                Session::flash('alert-danger', 'cannot delete matchmakingserver. Server will not be available for other matches until you remove the assignment manually.');
                return Redirect::back();
            }
        }

        Session::flash('alert-success', 'Match Finalized!');
        return Redirect::back();
    }




}
