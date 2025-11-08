<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TMT2MatchApiHandler implements IGameMatchApiHandler{

    private result;

    public function __construct() {
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
    public function getconfig($matchid, $nummaps, $players_per_team, $apiurl, $apikey)
    {
        // TODO: Implement getconfig() method.
    }

    public function getuserthirdpartyrequirements()
    {
        return array(
            "thirdpartyid" => "steamid",
            "thirdpartyname" => "steamname",
        );
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
            throw new Exception("MatchApiHandler for TMT2 does not support more than 2 Teams!");
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

    public function authorizeserver(Request $request, GameServer $gameserver)
    {
        // TODO: Implement authorizeserver() method.
    }

    public function golive(Request $request, \App\MatchMaking $match = null, \App\EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        // TODO: Implement golive() method.
    }

    public function updateround(Request $request, \App\MatchMaking $match = null, \App\EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        // TODO: Implement updateround() method.
    }

    public function updateplayer(Request $request, \App\MatchMaking $match = null, \App\EventTournament $tournament = null, ?int $challongematchid, int $mapnumber, string $player)
    {
        // TODO: Implement updateplayer() method.
    }

    public function finalizemap(Request $request, \App\MatchMaking $match = null, \App\EventTournament $tournament = null, ?int $challongematchid, int $mapnumber)
    {
        // TODO: Implement finalizemap() method.
    }

    public function finalize(Request $request, \App\MatchMaking $match = null, \App\EventTournament $tournament = null, ?int $challongematchid)
    {
        // TODO: Implement finalize() method.
    }

    public function freeserver(Request $request, \App\MatchMaking $match = null, \App\EventTournament $tournament = null, ?int $challongematchid)
    {
        // TODO: Implement freeserver() method.
    }

    public function uploaddemo(Request $request, \App\MatchMaking $match = null, \App\EventTournament $tournament = null, ?int $challongematchid)
    {
        // TODO: Implement uploaddemo() method.
    }
}