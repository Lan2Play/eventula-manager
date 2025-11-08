<?php

namespace App;
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