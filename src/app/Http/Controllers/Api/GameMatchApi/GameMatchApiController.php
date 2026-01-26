<?php

namespace App\Http\Controllers\Api\GameMatchApi;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use URL;

use App\Event;
use App\EventTournament;
use App\EventTournamentMatchServer;
use App\GameMatchApiHandler;

use App\Http\Controllers\Controller;
use App\MatchMaking;

use Illuminate\Http\Request;

class GameMatchApiController extends Controller
{
    /**
     * tournamentMatchConfig
     * @return JsonResponse
     */
    public function tournamentMatchConfig(Event $event, EventTournament $tournament, int $challongeMatchId, int $nummaps, Request $request)
    {
        $match = $tournament->getMatch($challongeMatchId);
        if (!$match) {
            return "No Match found for $challongeMatchId";
        }
        if (isset($tournament->game) && isset($tournament->game->gamematchapihandler)) {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler);

            $team1 = $tournament->getTeamByChallongeId($match->player1_id);
            $team2 = $tournament->getTeamByChallongeId($match->player2_id);

            $gamematchapihandler->addteam($team1->name);
            $gamematchapihandler->addteam($team2->name);

            $thirdpartyidprop = $gamematchapihandler->getuserthirdpartyrequirements()["thirdpartyid"];
            $thirdpartynameprop = $gamematchapihandler->getuserthirdpartyrequirements()["thirdpartyname"];

            foreach ($team1->tournamentParticipants as $key => $team1Participant) {
                $eventParticipant = $team1Participant->eventTicket;
                $user = $eventParticipant->user;
                $gamematchapihandler->addplayer($team1->name, $user->$thirdpartyidprop, $user->$thirdpartynameprop, $user->id, $user->username);
            }
            foreach ($team2->tournamentParticipants as $key => $team2Participant) {
                $eventParticipant = $team2Participant->eventTicket;
                $user = $eventParticipant->user;
                $gamematchapihandler->addplayer($team2->name, $user->$thirdpartyidprop, $user->$thirdpartynameprop, $user->id, $user->username);
            }

            $matchserver = EventTournamentMatchServer::getTournamentMatchServer($challongeMatchId);
            if (!isset($matchserver->gameServer)) {
                return "Error: Gameserver not selected!";
            }
            if (!$gamematchapihandler->authorizeserver($request, $matchserver->gameServer)) {
                return "Error: Gameserver Secret Key is wrong!";
            }
            if (isset($matchserver->gameServer->gameserver_secret) && $tournament->match_autoapi) {
                $apiurl = URL::to('/') . "/api/events/" . $tournament->event->slug . "/tournaments/" . $tournament->slug . "/" . $challongeMatchId . "/";
                $result = $gamematchapihandler->getconfig($challongeMatchId, $nummaps, $tournament->team_size[0], $apiurl, $matchserver->gameServer->gameserver_secret);
            } else {
                $result = $gamematchapihandler->getconfig($challongeMatchId, $nummaps, $tournament->team_size[0], null, null);
            }
        } else {
            return "no gamematchapihandler for match available!";
        }


        return response()->json($result)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * tournamentMatchGolive
     * @param Event $event
     * @param EventTournament $tournament
     * @param int $challongeMatchId
     * @param int $mapnumber
     * @param Request $request
     * @param int $mapnumber
     * @return string
     */
    public function tournamentMatchGolive(Event $event, EventTournament $tournament, int $challongeMatchId, int $mapnumber, Request $request)
    {
        $gameserver = EventTournamentMatchServer::getTournamentMatchServer($challongeMatchId);
        if (!isset($gameserver)) {
            return "Error: No GameServer setuped for this match!";
        }
        if (!isset($tournament->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $gameserver->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->golive($request, null, $tournament, $challongeMatchId, $mapnumber)) {
            return "Error: GoLive failed!";
        }

        return 'Match Started successfully!';
    }

    /**
     * tournamentMatchFinalize
     * @param Event $event
     * @param EventTournament $tournament
     * @param int $challongeMatchId
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function tournamentMatchDemo(Event $event, EventTournament $tournament, int $challongeMatchId, Request $request)
    {

        // maybe reenable, check if possible 
        // $gameserver = EventTournamentMatchServer::getTournamentMatchServer($challongeMatchId);
        // if (!isset($gameserver)) {
        //     return "Error: No GameServer setuped for this match!";
        // }
        if (!isset($tournament->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler);
        }

        // maybe reenable, check if possible 
        // if (!$gamematchapihandler->authorizeserver($request, $gameserver->gameServer)) {
        //     return "Error: Gameserver Secret Key is wrong!";
        // }

        if (!$gamematchapihandler->uploaddemo($request, null, $tournament, $challongeMatchId)) {
            return response('Error: demo uploading failed!', 500);
        }

        return response('uploaded demo successfully!', 200);
    }

    /**
     * tournamentMatchFinalize
     * @param Event $event
     * @param EventTournament $tournament
     * @param int $challongeMatchId
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function tournamentMatchFinalize(Event $event, EventTournament $tournament, int $challongeMatchId, Request $request)
    {
        $gameserver = EventTournamentMatchServer::getTournamentMatchServer($challongeMatchId);
        if (!isset($gameserver)) {
            return "Error: No GameServer setuped for this match!";
        }
        if (!isset($tournament->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $gameserver->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->finalize($request, null, $tournament, $challongeMatchId)) {
            return "Error: Finalizing failed!";
        } else {
            return "Success: Finalized Match!";
        }
    }

    /**
     * tournamentMatchFreeServer
     * @param Event $event
     * @param EventTournament $tournament
     * @param int $challongeMatchId
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function tournamentMatchFreeServer(Event $event, EventTournament $tournament, int $challongeMatchId, Request $request)
    {
        $gameserver = EventTournamentMatchServer::getTournamentMatchServer($challongeMatchId);
        if (!isset($gameserver)) {
            return "Error: No GameServer setuped for this match!";
        }
        if (!isset($tournament->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $gameserver->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->freeserver($request, null, $tournament, $challongeMatchId)) {
            return "Error: Freeing server failed!";
        } else {
            return "Success: Server freed!";
        }
    }


    /**
     * tournamentgMatchFinalizeMap
     * @param Event $event
     * @param EventTournament $tournament
     * @param int $challongeMatchId
     * @param int $mapnumber
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function tournamentMatchFinalizeMap(Event $event, EventTournament $tournament, int $challongeMatchId, int $mapnumber, Request $request)
    {
        $gameserver = EventTournamentMatchServer::getTournamentMatchServer($challongeMatchId);
        if (!isset($gameserver)) {
            return "Error: No GameServer setuped for this match!";
        }

        if (!isset($tournament->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $gameserver->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }
        if (!$gamematchapihandler->finalizemap($request, null, $tournament, $challongeMatchId, $mapnumber)) {
            return "Error: finalizemap failed!";
        }

        return 'Map finalized successfully!';
    }



    /**
     * tournamentMatchUpdateround
     * @param Event $event
     * @param EventTournament $tournament
     * @param int $challongeMatchId
     * @param int $mapnumber
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function tournamentMatchUpdateround(Event $event, EventTournament $tournament, int $challongeMatchId, int $mapnumber, Request $request)
    {
        $gameserver = EventTournamentMatchServer::getTournamentMatchServer($challongeMatchId);
        if (!isset($gameserver)) {
            return "Error: No GameServer setuped for this match!";
        }
        if (!isset($tournament->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $gameserver->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->updateround($request, null, $tournament, $challongeMatchId, $mapnumber)) {
            return "Error: updateround failed!";
        }
        return 'round updated successfully!';
    }


    /**
     * tournamentMatchUpdateplayer
     * @param Event $event
     * @param EventTournament $tournament
     * @param int $challongeMatchId
     * @param int $mapnumber
     * @param string $player
     * @param Request $request
     * @return string
     * @throws Exception
     */
    public function tournamentMatchUpdateplayer(Event $event, EventTournament $tournament, int $challongeMatchId, int $mapnumber, string $player, Request $request)
    {
        $gameserver = EventTournamentMatchServer::getTournamentMatchServer($challongeMatchId);
        if (!isset($gameserver)) {
            return "Error: No GameServer setuped for this match!";
        }
        if (!isset($tournament->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($tournament->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $gameserver->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->updateplayer($request, null, $tournament, $challongeMatchId, $mapnumber, $player)) {
            return "Error: updateplayer failed!";
        }
        return 'player updated successfully!';
    }






    /**
     * matchMakingMatchConfig
     * @return JsonResponse|string
     * @throws Exception
     */
    public function matchMakingMatchConfig(MatchMaking $match, int $nummaps, Request $request)
    {
        if (isset($match->game) && isset($match->game->gamematchapihandler)) {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler);
            $thirdpartyidprop = $gamematchapihandler->getuserthirdpartyrequirements()["thirdpartyid"];
            $thirdpartynameprop = $gamematchapihandler->getuserthirdpartyrequirements()["thirdpartyname"];


            foreach ($match->teams as $team) {
                $gamematchapihandler->addteam($team->name);

                foreach ($team->players as $player) {
                    if (isset($player->user->$thirdpartyidprop) && isset($player->user->$thirdpartynameprop)) {
                        $gamematchapihandler->addplayer($team->name, $player->user->$thirdpartyidprop, $player->user->$thirdpartynameprop, $player->user->id, $player->user->username);
                    }
                }
            }
            if (!isset($match->matchMakingServer->gameServer)) {
                return "Error: Gameserver not selected!";
            }
            if (!$gamematchapihandler->authorizeserver($request, $match->matchMakingServer->gameServer)) {
                return "Error: Gameserver Secret Key is wrong!";
            }
            if (isset($match->matchMakingServer->gameServer->gameserver_secret) && $match->game->matchmaking_autoapi) {
                $apiurl = URL::to('/') . "/api/matchmaking/" . $match->id . "/";
                $result = $gamematchapihandler->getconfig($match->id, $nummaps, $match->team_size, $apiurl, $match->matchMakingServer->gameServer->gameserver_secret);
            } else {
                $result = $gamematchapihandler->getconfig($match->id, $nummaps, $match->team_size, null, null);
            }
        } else {
            return "no gamematchapihandler for match available!";
        }


        return response()->json($result)->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * matchMakingMatchGolive
     * @param Request $request
     * @param MatchMaking $match
     * @param int $mapnumber
     * @return string
     * @throws Exception
     */
    public function matchMakingMatchGolive(Request $request, MatchMaking $match, int $mapnumber)
    {
        if (!isset($match->matchMakingServer->gameServer)) {
            return "Error: No GameServer setuped for this match!";
        }
        if (!isset($match->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $match->matchMakingServer->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if ($mapnumber != 0) {
            return "Error: In matchmaking only one map is possible!";
        }

        if ($match->status != "WAITFORPLAYERS") {
            return "Error: Status is not WAITFORPLAYERS!";
        }

        if (!$gamematchapihandler->golive($request, $match, null, null, $mapnumber)) {
            return "Error: GoLive failed!";
        }

        return 'Match Started successfully!';
    }

    /**
     * matchMakingMatchFinalize
     * @param Request $request
     * @param MatchMaking $match
     * @return string
     * @throws Exception
     */
    public function matchMakingMatchDemo(Request $request, MatchMaking $match)
    {
        // maybe reenable, check if possible 
        // if (!isset($match->matchMakingServer->gameServer)) {
        //     return "Error: No GameServer setuped for this match!";
        // }
        if (!isset($match->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler);
        }
        // maybe reenable, check if possible 
        // if (!$gamematchapihandler->authorizeserver($request, $match->matchMakingServer->gameServer)) {
        //     return "Error: Gameserver Secret Key is wrong!";
        // }

        if (!$gamematchapihandler->uploaddemo($request, $match, null, null)) {
            return response('Error: demo uploading failed!', 500);
        }

        return response('uploaded demo successfully!', 200);
    }

    /**
     * matchMakingMatchFinalize
     * @param Request $request
     * @param MatchMaking $match
     * @return string
     * @throws Exception
     */
    public function matchMakingMatchFinalize(Request $request, MatchMaking $match)
    {
        if (!isset($match->matchMakingServer->gameServer)) {
            return "Error: No GameServer setuped for this match!";
        }
        if (!isset($match->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $match->matchMakingServer->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->finalize($request, $match, null, null)) {
            return "Error: Finalizing failed!";
        } else {
            return "Success: Finalized Match!";
        }
    }

    /**
     * matchMakingMatchFreeServer
     * @param Request $request
     * @param MatchMaking $match
     * @return string
     * @throws Exception
     */
    public function matchMakingMatchFreeServer(Request $request, MatchMaking $match)
    {
        if (!isset($match->matchMakingServer->gameServer)) {
            return "Error: No GameServer setuped for this match!";
        }
        if (!isset($match->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $match->matchMakingServer->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->freeserver($request, $match, null, null)) {
            return "Error: Freeing server failed!";
        } else {
            return "Success: Server freed!";
        }
    }


    /**
     * matchMakingMatchFinalizeMap
     * @param Request $request
     * @param MatchMaking $match
     * @param int $mapnumber
     * @return string
     * @throws Exception
     */
    public function matchMakingMatchFinalizeMap(Request $request, MatchMaking $match, int $mapnumber)
    {
        if (!isset($match->matchMakingServer->gameServer)) {
            return "Error: No GameServer setuped for this match!";
        }
        if ($mapnumber != 0) {
            return "Error: In matchmaking only one map is possible!";
        }
        if (!isset($match->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $match->matchMakingServer->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }
        if (!$gamematchapihandler->finalizemap($request, $match, null, null, $mapnumber)) {
            return "Error: finalizemap failed!";
        }

        return 'Map finalized successfully!';
    }

    /**
     * matchMakingMatchUpdateround
     * @param Request $request
     * @param MatchMaking $match
     * @param int $mapnumber
     * @return string
     * @throws Exception
     */
    public function matchMakingMatchUpdateround(Request $request, MatchMaking $match, int $mapnumber)
    {
        if (!isset($match->matchMakingServer->gameServer)) {
            return "Error: No GameServer setuped for this match!";
        }
        if ($mapnumber != 0) {
            return "Error: In matchmaking only one map is possible!";
        }
        if (!isset($match->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $match->matchMakingServer->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->updateround($request, $match, null, null, $mapnumber)) {
            return "Error: updateround failed!";
        }
        return 'round updated successfully!';
    }

    /**
     * matchMakingMatchUpdateplayer
     * @return string
     * @throws Exception
     */
    public function matchMakingMatchUpdateplayer(Request $request, MatchMaking $match, int $mapnumber, string $player)
    {
        if (!isset($match->matchMakingServer->gameServer)) {
            return "Error: No GameServer setuped for this match!";
        }
        if ($mapnumber != 0) {
            return "Error: In matchmaking only one map is possible!";
        }
        if (!isset($match->game->gamematchapihandler)) {
            return "Error: No gamematchapihandler setuped for this match!";
        } else {
            $gamematchapihandler = (new GameMatchApiHandler())->getGameMatchApiHandler($match->game->gamematchapihandler);
        }

        if (!$gamematchapihandler->authorizeserver($request, $match->matchMakingServer->gameServer)) {
            return "Error: Gameserver Secret Key is wrong!";
        }

        if (!$gamematchapihandler->updateplayer($request, $match, null, null, $mapnumber, $player)) {
            return "Error: updateplayer failed!";
        }
        return 'player updated successfully!';
    }
}
