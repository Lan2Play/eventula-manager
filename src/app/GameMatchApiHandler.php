<?php

namespace App;

use App\EventTournament;
use App\MatchMaking;
use App\MatchReplay;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Auth;

class GameMatchApiHandler
{
    public static function getGameMatchApiHandlerSelectArray()
    {
        $return = array(
            "0" => "None",
            "1" => "Get5",
            "2" => "PugSharp",
            "3" => "PugSharp 2v2",
        );
        return $return;
    }

    public function getGameMatchApiHandler($matchApiHandlerId): IGameMatchApiHandler
    {
        switch ($matchApiHandlerId) {
            case "1":
                return new Get5MatchApiHandler();
            case "2":
                return new PugSharpMatchApiHandler();
            case "3":
                return new PugSharpMatchApiHandler2v2();
            default:
                throw new Exception("MatchApiHandler \"" . GameMatchApiHandler::getGameMatchApiHandlerSelectArray()[$matchApiHandlerId] . "\" is not able to execute commands.");
        }
    }
}

/**
 * IGameMatchApiHandler
 * @param $matchtype (0 for tournament, 1 for matchmaking)
 * @return View
 */
interface IGameMatchApiHandler
{
    public function getconfig($matchid, $nummaps, $players_per_team, $apiurl, $apikey);
    public function getuserthirdpartyrequirements();
    public function addteam($name);
    public function addplayer($teamName, $thirdpartyid, $thirdpartyname, $userid, $username);
    public function authorizeserver(Request $request, GameServer $gameserver);
    public function golive(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber);
    public function updateround(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber);
    public function updateplayer(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber, string $player);
    public function finalizemap(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber);
    public function finalize(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid);
    public function freeserver(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid);
    public function uploaddemo(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid);
}

class Get5MatchApiHandler implements IGameMatchApiHandler
{
    private $result;

    public function __construct()
    {
        $this->result = new \stdClass();
        $this->result->min_spectators_to_ready = 0;
        $this->result->skip_veto = false;
        $this->result->veto_first = "team1";
        $this->result->side_type = "standard";
        $this->result->maplist = array(
            "de_vertigo",
            "de_dust2",
            "de_inferno",
            "de_mirage",
            "de_nuke",
            "de_overpass",
            "de_ancient"
        );
    }

    public function getuserthirdpartyrequirements()
    {
        $return = array(
            "thirdpartyid" => "steamid",
            "thirdpartyname" => "steamname",
        );
        return $return;
    }

    public function addteam($name)
    {
        if (!isset($this->result->team1)) {
            $this->result->team1 = new \stdClass();
            $this->result->team1->name = $name;
            $this->result->team1->tag = $name;
            $this->result->team1->flag = "DE";
        } elseif (!isset($this->result->team2)) {
            $this->result->team2 = new \stdClass();
            $this->result->team2->name = $name;
            $this->result->team2->tag = $name;
            $this->result->team2->flag = "DE";
        } else {
            throw new Exception("MatchApiHandler for get5 does not support more than 2 Teams!");
        }
    }

    public function addplayer($teamName, $thirdpartyid, $thirdpartyname, $userid, $username)
    {
        $team = null;

        if ($teamName == $this->result->team1->name) {
            $team = $this->result->team1;
        } elseif ($teamName == $this->result->team2->name) {
            $team = $this->result->team2;
        }

        if (!isset($team->players)) {
            $team->players = new \stdClass();
        }

        $team->players->{$thirdpartyid} = $thirdpartyname;
    }

    public function getconfig($matchid, $nummaps, $players_per_team, $apiurl, $apikey)
    {
        $this->result->matchid = "$matchid";
        $this->result->num_maps = intval($nummaps);
        $this->result->players_per_team = intval($players_per_team);
        $this->result->min_players_to_ready = intval($players_per_team);
        if ($apikey != null && $apiurl != null) {
            $this->result->cvars = new \stdClass();
            $this->result->cvars->get5_eventula_apistats_key = $apikey;
            $this->result->cvars->get5_eventula_apistats_url = $apiurl;

            $this->result->cvars->get5_demo_upload_header_key = "Authorization";
            $this->result->cvars->get5_demo_upload_header_value = "Bearer " . $apikey;
            $this->result->cvars->get5_demo_upload_url = $apiurl . "demo";
        }

        return $this->result;
    }


    public function authorizeserver(Request $request, GameServer $gameserver)
    {
        if (auth('sanctum')->user()->id != $gameserver->id) {
            return false;
        } else {
            return true;
        }
    }




    public function golive(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        if ($match != null && $tournament == null) {
            if (!$match->setStatus('LIVE')) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff
            return true;
        }
        return false;
    }

    public function updateround(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {

        if ($match != null && $tournament == null) {
            $loop = 1;
            foreach ($match->teams as $team) {
                $team->team_score = $request->{"team" . $loop . "score"};
                if (!$team->save()) {
                    return false;
                }
                $loop++;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            $tournament->updateMatchScores($challongematchid, $request->{"team1score"}, $request->{"team2score"});
            return true;
        }
        return false;
    }
    public function updateplayer(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber, string $player)
    {
        if ($match != null && $tournament == null) {
            //matchmaking stuff
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff
            return true;
        }
        return false;
    }

    public function finalizemap(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        if ($match != null && $tournament == null) {
            if (!$this->updateround($request, $match, null, null, $mapnumber)) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            if (($mapnumber + 1) == $tournament->getnummaps($challongematchid)) {
                $tournament->updateMatch($challongematchid, $request->{"team1score"}, $request->{"team2score"});
                return true;
            }
            if (!$this->updateround($request, null, $tournament, $challongematchid, $mapnumber)) {
                return false;
            }
            return true;
        }
        return false;
    }
    public function finalize(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        if ($match != null && $tournament == null) {
            if (!$match->setStatus('COMPLETE')) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff

        }
        return false;
    }

    public function freeserver(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        if ($match != null && $tournament == null) {

            if (!$match->matchMakingServer->delete()) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {

            $evtms = EventTournamentMatchServer::where(['challonge_match_id' => $challongematchid])->first();

            if (isset($evtms)) {
                if (!$evtms->delete()) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function uploaddemo(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        $demoname = str_replace(' ', '_', $request->headers->get('Get5-DemoName'));
        // not used? remove?
        // $matchId = $request->headers->get('Get5-MatchId');
        // $mapNumber = $request->headers->get('Get5-MapNumber');
        // $serverId = $request->headers->get('Get5-ServerId');

        if ($match != null && $tournament == null) {
            $destinationPathDemo =  MatchReplay::createReplayPath($match->game, $demoname);

            if (Storage::disk('public')->put($destinationPathDemo, $request->getContent()) == false) {
                return false;
            }

            $replay = new MatchReplay();
            $replay->name = $demoname;
            $replay->matchmaking_id = $match->id;
            if (!$replay->save()) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            $destinationPathDemo =  MatchReplay::createReplayPath($tournament->game, $demoname);

            if (Storage::disk('public')->put($destinationPathDemo, $request->getContent()) == false) {
                return false;
            }

            $replay = new MatchReplay();
            $replay->name = $demoname;
            $replay->challonge_match_id = $challongematchid;
            if (!$replay->save()) {
                return false;
            }
            return true;
        }
        return false;
    }
}


class PugSharpMatchApiHandler implements IGameMatchApiHandler
{
    private $result;

    public function __construct()
    {
        $this->result = new \stdClass();
        $this->result->maplist = array(
            "de_vertigo",
            "de_dust2",
            "de_inferno",
            "de_mirage",
            "de_nuke",
            "de_overpass",
            "de_ancient"
        );
        $this->result->max_rounds = 24;
        $this->result->max_overtime_rounds = 6;
    }

    public function getuserthirdpartyrequirements()
    {
        $return = array(
            "thirdpartyid" => "steamid",
            "thirdpartyname" => "steamname",
        );
        return $return;
    }

    public function addteam($name)
    {
        if (!isset($this->result->team1)) {
            $this->result->team1 = new \stdClass();
            $this->result->team1->name = $name;
            $this->result->team1->tag = $name;
        } elseif (!isset($this->result->team2)) {
            $this->result->team2 = new \stdClass();
            $this->result->team2->name = $name;
            $this->result->team2->tag = $name;
        } else {
            throw new Exception("MatchApiHandler for PugSharp does not support more than 2 Teams!");
        }
    }

    public function addplayer($teamName, $thirdpartyid, $thirdpartyname, $userid, $username)
    {
        $team = null;

        if ($teamName == $this->result->team1->name) {
            $team = $this->result->team1;
        } elseif ($teamName == $this->result->team2->name) {
            $team = $this->result->team2;
        }

        if (!isset($team->players)) {
            $team->players = new \stdClass();
        }

        $team->players->{$thirdpartyid} = $thirdpartyname;
    }

    public function getconfig($matchid, $nummaps, $players_per_team, $apiurl, $apikey)
    {
        $this->result->matchid = "$matchid";
        $this->result->num_maps = intval($nummaps);
        $this->result->players_per_team = intval($players_per_team);
        $this->result->min_players_to_ready = intval($players_per_team);

        if ($apikey != null && $apiurl != null) {
            $this->result->eventula_apistats_url = $apiurl;
            $this->result->eventula_demo_upload_url = $apiurl . "demo";
        }

        return $this->result;
    }


    public function authorizeserver(Request $request, GameServer $gameserver)
    {
        if (auth('sanctum')->user()->id != $gameserver->id) {
            return false;
        } else {
            return true;
        }
    }




    public function golive(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        if ($match != null && $tournament == null) {
            if (!$match->setStatus('LIVE')) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff
            return true;
        }
        return false;
    }

    public function updateround(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {

        if ($match != null && $tournament == null) {
            $loop = 1;
            foreach ($match->teams as $team) {
                $team->team_score = $request->{"team" . $loop . "score"};
                if (!$team->save()) {
                    return false;
                }
                $loop++;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            $tournament->updateMatchScores($challongematchid, $request->{"team1score"}, $request->{"team2score"});
            return true;
        }
        return false;
    }
    public function updateplayer(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber, string $player)
    {
        if ($match != null && $tournament == null) {
            //matchmaking stuff
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff
            return true;
        }
        return false;
    }

    public function finalizemap(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        if ($match != null && $tournament == null) {
            if (!$this->updateround($request, $match, null, null, $mapnumber)) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            if (($mapnumber + 1) == $tournament->getnummaps($challongematchid)) {
                $tournament->updateMatch($challongematchid, $request->{"team1score"}, $request->{"team2score"});
                return true;
            }
            if (!$this->updateround($request, null, $tournament, $challongematchid, $mapnumber)) {
                return false;
            }
            return true;
        }
        return false;
    }
    public function finalize(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        if ($match != null && $tournament == null) {
            if (!$match->setStatus('COMPLETE')) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff

        }
        return false;
    }

    public function freeserver(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        if ($match != null && $tournament == null) {

            if (!$match->matchMakingServer->delete()) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {

            $evtms = EventTournamentMatchServer::where(['challonge_match_id' => $challongematchid])->first();

            if (isset($evtms)) {
                if (!$evtms->delete()) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function uploaddemo(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        $demoname = str_replace(' ', '_', $request->headers->get('PugSharp-DemoName'));

        if ($match != null && $tournament == null) {
            $destinationPathDemo =  MatchReplay::createReplayPath($match->game, $demoname);

            if (Storage::disk('public')->put($destinationPathDemo, $request->getContent()) == false) {
                return false;
            }

            $replay = new MatchReplay();
            $replay->name = $demoname;
            $replay->matchmaking_id = $match->id;
            if (!$replay->save()) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            $destinationPathDemo =  MatchReplay::createReplayPath($tournament->game, $demoname);

            if (Storage::disk('public')->put($destinationPathDemo, $request->getContent()) == false) {
                return false;
            }

            $replay = new MatchReplay();
            $replay->name = $demoname;
            $replay->challonge_match_id = $challongematchid;
            if (!$replay->save()) {
                return false;
            }
            return true;
        }
        return false;
    }
}


class PugSharpMatchApiHandler2v2 implements IGameMatchApiHandler
{
    private $result;

    public function __construct()
    {
        $this->result = new \stdClass();
        $this->result->maplist = array(
            "de_rooftop",
            "de_overpass",
            "de_vertigo",
            "de_nuke",
            "de_inferno"
        );
        $this->result->max_rounds = 16;
        $this->result->max_overtime_rounds = 6;
    }

    public function getuserthirdpartyrequirements()
    {
        $return = array(
            "thirdpartyid" => "steamid",
            "thirdpartyname" => "steamname",
        );
        return $return;
    }

    public function addteam($name)
    {
        if (!isset($this->result->team1)) {
            $this->result->team1 = new \stdClass();
            $this->result->team1->name = $name;
            $this->result->team1->tag = $name;
        } elseif (!isset($this->result->team2)) {
            $this->result->team2 = new \stdClass();
            $this->result->team2->name = $name;
            $this->result->team2->tag = $name;
        } else {
            throw new Exception("MatchApiHandler for PugSharp does not support more than 2 Teams!");
        }
    }

    public function addplayer($teamName, $thirdpartyid, $thirdpartyname, $userid, $username)
    {
        $team = null;

        if ($teamName == $this->result->team1->name) {
            $team = $this->result->team1;
        } elseif ($teamName == $this->result->team2->name) {
            $team = $this->result->team2;
        }

        if (!isset($team->players)) {
            $team->players = new \stdClass();
        }

        $team->players->{$thirdpartyid} = $thirdpartyname;
    }

    public function getconfig($matchid, $nummaps, $players_per_team, $apiurl, $apikey)
    {
        $this->result->matchid = "$matchid";
        $this->result->num_maps = intval($nummaps);
        $this->result->players_per_team = intval($players_per_team);
        $this->result->min_players_to_ready = intval($players_per_team);

        if ($apikey != null && $apiurl != null) {
            $this->result->eventula_apistats_url = $apiurl;
            $this->result->eventula_demo_upload_url = $apiurl . "demo";
        }

        return $this->result;
    }


    public function authorizeserver(Request $request, GameServer $gameserver)
    {
        if (auth('sanctum')->user()->id != $gameserver->id) {
            return false;
        } else {
            return true;
        }
    }




    public function golive(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        if ($match != null && $tournament == null) {
            if (!$match->setStatus('LIVE')) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff
            return true;
        }
        return false;
    }

    public function updateround(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {

        if ($match != null && $tournament == null) {
            $loop = 1;
            foreach ($match->teams as $team) {
                $team->team_score = $request->{"team" . $loop . "score"};
                if (!$team->save()) {
                    return false;
                }
                $loop++;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            $tournament->updateMatchScores($challongematchid, $request->{"team1score"}, $request->{"team2score"});
            return true;
        }
        return false;
    }
    public function updateplayer(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber, string $player)
    {
        if ($match != null && $tournament == null) {
            //matchmaking stuff
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff
            return true;
        }
        return false;
    }

    public function finalizemap(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        if ($match != null && $tournament == null) {
            if (!$this->updateround($request, $match, null, null, $mapnumber)) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            if (($mapnumber + 1) == $tournament->getnummaps($challongematchid)) {
                $tournament->updateMatch($challongematchid, $request->{"team1score"}, $request->{"team2score"});
                return true;
            }
            if (!$this->updateround($request, null, $tournament, $challongematchid, $mapnumber)) {
                return false;
            }
            return true;
        }
        return false;
    }
    public function finalize(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        if ($match != null && $tournament == null) {
            if (!$match->setStatus('COMPLETE')) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            //tournament stuff

        }
        return false;
    }

    public function freeserver(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        if ($match != null && $tournament == null) {

            if (!$match->matchMakingServer->delete()) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {

            $evtms = EventTournamentMatchServer::where(['challonge_match_id' => $challongematchid])->first();

            if (isset($evtms)) {
                if (!$evtms->delete()) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function uploaddemo(Request $request, MatchMaking $match = null, EventTournament $tournament = null, ?int $challongematchid)
    {
        $demoname = str_replace(' ', '_', $request->headers->get('PugSharp-DemoName'));

        if ($match != null && $tournament == null) {
            $destinationPathDemo =  MatchReplay::createReplayPath($match->game, $demoname);

            if (Storage::disk('public')->put($destinationPathDemo, $request->getContent()) == false) {
                return false;
            }

            $replay = new MatchReplay();
            $replay->name = $demoname;
            $replay->matchmaking_id = $match->id;
            if (!$replay->save()) {
                return false;
            }
            return true;
        }
        if ($match == null && $tournament != null && $challongematchid != null) {
            $destinationPathDemo =  MatchReplay::createReplayPath($tournament->game, $demoname);

            if (Storage::disk('public')->put($destinationPathDemo, $request->getContent()) == false) {
                return false;
            }

            $replay = new MatchReplay();
            $replay->name = $demoname;
            $replay->challonge_match_id = $challongematchid;
            if (!$replay->save()) {
                return false;
            }
            return true;
        }
        return false;
    }
}