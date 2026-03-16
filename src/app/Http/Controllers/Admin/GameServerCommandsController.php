<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use Storage;
use Image;
use File;
use Helpers;
use Validator;
use URL;

use App\Game;
use App\GameCommandHandler;
use App\GameServer;
use App\GameServerCommand;
use App\GameServerCommandParameter;
use App\EventTournament;
use App\MatchMaking;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class GameServerCommandsController extends Controller
{
    /**
     * Store GameServer to Database
     * @param  Request $request
     * @return Redirect
     */
    public function store(Game $game, Request $request)
    {
        $rules = [
            'name' => 'required',
            'command' => 'required',
            'scope' => 'required'
        ];
        $messages = [
            'name.required' => 'Command name is required',
            'command.required' => 'Command is required',
            'scope.required' => 'Command scope is required',
        ];

        $this->validate($request, $rules, $messages);

        $gameServerCommand = new GameServerCommand();
        $gameServerCommand->name = $request->name;
        $gameServerCommand->game_id = $game->id;
        $gameServerCommand->command = $request->command;
        $gameServerCommand->verification = (isset($request->verification) && !empty($request->verification)) ? $request->verification : null;
        $gameServerCommand->scope = $request->scope;

        if (!$gameServerCommand->save()) {
            Session::flash('alert-danger', 'Could not save GameServerCommand!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully saved GameServerCommand!');
        return Redirect::to('admin/games/' . $game->slug . '#gameservercommands');
    }

    /**
     * Update Game
     * @param  GameServerCommand $gameServerCommand
     * @param  Request $request
     * @return Redirect
     */
    public function update(Game $game, GameServerCommand $gameServerCommand, Request $request)
    {
        $rules = [
            'name' => 'required',
            'command' => 'required',
            'scope' => 'required'
        ];
        $messages = [
            'name.required' => 'Command name is required',
            'command.required' => 'Command is required',
            'scope.required' => 'Command scope is required',
        ];


        $this->validate($request, $rules, $messages);

        $gameServerCommand->name = $request->name;
        $gameServerCommand->command = $request->command;
        $gameServerCommand->verification = (isset($request->verification) && !empty($request->verification)) ? $request->verification : null;
        $gameServerCommand->scope = $request->scope;

        if (!$gameServerCommand->save()) {
            Session::flash('alert-danger', 'Could not save Game Server Command!');
            return Redirect::back();
        }


        Session::flash('alert-success', 'Successfully saved Game Server Command!');
        return Redirect::to('admin/games/' . $game->slug . '#gameservercommands');
    }

    /**
     * Delete GameServerCommand from Database
     * @param  Game  $game
     * @param  GameServerCommand  $gameServerCommand
     * @return Redirect
     */
    public function destroy(Game $game, GameServerCommand $gameServerCommand)
    {
        // if ($game->eventTournaments && !$game->eventTournaments->isEmpty()) {
        //     Session::flash('alert-danger', 'Cannot delete game with tournaments!');
        //     return Redirect::back();
        // }

        if (!$gameServerCommand->delete()) {
            Session::flash('alert-danger', 'Cannot delete GameServerCommand!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted GameServerCommand!');
        return Redirect::back();
    }

    private function initCommandHandler()
    {

    }

    private function executeCommand(GameServer $gameServer, string $command, ?string $verification)
    {

        $validator = Validator::make(
            ['gameServer' => $gameServer, 'command' => $command],
            [
                'gameServer' => 'required',
                'command' => 'required',
            ],
            [
                'gameServer.required' => 'gameServer is required',
                'command.required' => 'Command is required',
            ]
        );
        if ($validator->fails())
            print_r($validator);

        $result = false;

        try {
            $commandHandler = (new GameCommandHandler())->getGameCommandHandler($gameServer->game->gamecommandhandler);
            $commandHandler->init($gameServer->rcon_address ?? $gameServer->address, $gameServer->rcon_port, $gameServer->rcon_password);
            $result = $commandHandler->execute($command, $verification);
            if ($result == false) {
                $error = "Unexpected Error occured.";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        } catch (Throwable $e) {
            $error = $e->getMessage();
        } catch (TimeoutException $e) {
            $error = $e->getMessage();
        } finally {
            if (isset($commandHandler)) {
                $commandHandler->dispose();
            }
        }

        if (isset($error) || $result == false) {
            Session::flash('alert-danger', 'Error while executing command "' . $command . '" with connector ' . Helpers::getGameCommandHandlerSelectArray()[$gameServer->game->gamecommandhandler] . ' Error:' . var_export($error, true) . ' Result:' . var_export($result, true));
            return false;
        } else {
            Session::flash('alert-success', 'Successfully executed command "' . $command . '" with connector ' . Helpers::getGameCommandHandlerSelectArray()[$gameServer->game->gamecommandhandler] . ' Result:' . var_export($result, true));
            return true;
        }
    }

    /**
     * execute gameServerCommand
     * @param  Game  $game
     * @param  GameServer  $gameServer
     * @param  Request $request
     * @return Redirect
     */
    public function executeGameServerCommand(Game $game, GameServer $gameServer, Request $request)
    {
        $rules = [
            'command' => 'filled'
        ];
        $messages = [
            'command.required' => 'Command is required'
        ];
        $this->validate($request, $rules, $messages);


        $gameServerCommand = GameServerCommand::find($request->command);
        $availableParameters = new \stdClass();
        $availableParameters->game = $game;
        $availableParameters->gameServer = $gameServer;
        $command = Helpers::resolveServerCommandParameters($gameServerCommand->command, $request, $availableParameters);


        $this->executeCommand($gameServer, $command, $gameServerCommand->verification);

        return Redirect::back();
    }



    /**
     * execute gameServerMatchCommand
     * @param  Game  $game
     * @param  GameServer  $gameServer
     * @param  EventTournament $tournament
     * @param  Request $request
     * @return Redirect
     */
    public function executeGameServerTournamentMatchCommand(Game $game, GameServer $gameServer, EventTournament $tournament, Request $request)
    {
        $this->internalExecuteGameServerTournamentMatchCommand($game, $gameServer, $tournament, $request);
        return Redirect::back();
    }
    /**
     * execute gameServerMatchCommand
     * @param  Game  $game
     * @param  GameServer  $gameServer
     * @param  EventTournament $tournament
     * @param  Request $request
     * @return Redirect
     */
    public function internalExecuteGameServerTournamentMatchCommand(Game $game, GameServer $gameServer, EventTournament $tournament, Request $request)
    {
        $rules = [
            'command' => 'filled'
        ];
        $messages = [
            'command.required' => 'Command is required'
        ];
        $this->validate($request, $rules, $messages);

        $challongeMatch = $tournament->getMatch($request->challonge_match_id);


        $gameServerCommand = GameServerCommand::find($request->command);
        $availableParameters = new \stdClass();
        $availableParameters->game = $game;
        $availableParameters->event = $tournament->event;
        $availableParameters->tournament = $tournament;
        $availableParameters->gameServer = $gameServer;
        $availableParameters->match = $challongeMatch;
        $availableParameters->gamematchapiurl = new \stdClass();
        $availableParameters->gamematchapiurl->matchconfigapi = URL::to('/') . "/api/events/" . $tournament->event->slug . "/tournaments/" . $tournament->slug . "/" . $challongeMatch->id . "/configure/" . $tournament->getnummaps($request->challonge_match_id);
        $availableParameters->gamematchapiurl->matchapibase = URL::to('/') . "/api/events/" . $tournament->event->slug . "/tournaments/" . $tournament->slug . "/" . $challongeMatch->id;


        $command = Helpers::resolveServerCommandParameters($gameServerCommand->command, $request, $availableParameters);

        return $this->executeCommand($gameServer, $command, $gameServerCommand->verification);

    }





    /**
     * execute executeGameServerTournamentMatchMakingCommand
     * @param  Game  $game
     * @param  GameServer  $gameServer
     * @param  MatchMaking $match
     * @param  Request $request
     * @return Redirect
     */
    public function executeGameServerMatchMakingCommand(Game $game, GameServer $gameServer, MatchMaking $match, Request $request)
    {
        $this->internalExecuteGameServerMatchMakingCommand($game, $gameServer, $match, $request);

        return Redirect::back();
    }

    /**
     * execute executeGameServerTournamentMatchMakingCommand
     * @param  Game  $game
     * @param  GameServer  $gameServer
     * @param  MatchMaking $match
     * @param  Request $request
     * @return Redirect
     */
    public function internalExecuteGameServerMatchMakingCommand(Game $game, GameServer $gameServer, MatchMaking $match, Request $request)
    {
        $rules = [
            'command' => 'filled'
        ];
        $messages = [
            'command.required' => 'Command is required'
        ];
        $this->validate($request, $rules, $messages);

        $gameServerCommand = GameServerCommand::find($request->command);
        $availableParameters = new \stdClass();
        $availableParameters->game = $game;
        $availableParameters->gameServer = $gameServer;
        $availableParameters->match = $match;
        $availableParameters->gamematchapiurl = new \stdClass();
        $availableParameters->gamematchapiurl->matchconfigapi = URL::to('/') . "/api/matchmaking/" . $match->id . "/configure/1";
        $availableParameters->gamematchapiurl->matchapibase = URL::to('/') . "/api/matchmaking/" . $match->id;


        $command = Helpers::resolveServerCommandParameters($gameServerCommand->command, $request, $availableParameters);

        return $this->executeCommand($gameServer, $command, $gameServerCommand->verification);

        //return Redirect::back();
    }

}
