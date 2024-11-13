<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use Storage;
use Image;
use File;

use App\Game;
use App\GameServer;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class GameServersController extends Controller
{

     /**
     * Show Gameserver Page
     * @return Redirect
     */
    public function show(Game $game, GameServer $gameServer)
    {
        return view('admin.games.gameserver.show')
            ->with('gameServer', $gameServer);
    }

    /**
     * Store GameServer to Database
     * @param  Request $request
     * @return Redirect
     */
    public function store(Game $game, Request $request)
    {
        $rules = [
            'name'              => 'required',
            'address'     => 'required',
            'game_port'      => 'required|integer',
            'type'          =>  'in:Match,Casual'
        ];
        $messages = [
            'name.required'         => 'Game name is required',
            'address.required'     => 'An Address is Required',
            'game_port.required'     => 'A Game Port is Required',
            'game_port.integer'      => 'Game Port must be a number',
            'type.in'                => 'Gameserver Type must be match or casual'
        ];

        $this->validate($request, $rules, $messages);

        $gameServer                 = new GameServer();
        $gameServer->name           = $request->name;
        $gameServer->game_id        = $game->id;
        $gameServer->type           = $request->type;
        $gameServer->ispublic       = ($request->ispublic ? true : false);
        $gameServer->isenabled       = ($request->isenabled ? true : false);
        $gameServer->address        = $request->address;
        $gameServer->game_port      = $request->game_port;
        $gameServer->stream_port      = $request->stream_port;
        $gameServer->game_password  = $request->game_password;
        $gameServer->rcon_address   = $request->rcon_address != "" ? $request->rcon_address : null ;
        $gameServer->rcon_port      = $request->rcon_port;
        $gameServer->rcon_password  = $request->rcon_password;

        if (!$gameServer->save()) {
            Session::flash('alert-danger', 'Could not save GameServer!');
            return Redirect::back();
        }

        $token = $gameServer->createToken("gs_" . Str::random());
        if (!isset($token->plainTextToken) || $token->plainTextToken == "")
        {
            Session::flash('alert-danger', 'Could not create GameServer token!');
            return Redirect::back();
        }
        $gameServer->gameserver_secret = $token->plainTextToken;
        if (!$gameServer->save()) {
            Session::flash('alert-danger', 'Could not save GameServer!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully saved GameServer!');
        return Redirect::to('admin/games/' . $game->slug . '#gameservers');
    }

    /**
     * Update Game
     * @param  GameServer $gameServer
     * @param  Request $request
     * @return Redirect
     */
    public function update(Game $game, GameServer $gameServer, Request $request)
    {
        $rules = [
            'name'           => 'required',
            'address'        => 'required',
            'game_port'      => 'required|integer',
            'type'          =>  'in:Match,Casual'
        ];
        $messages = [
            'name.required'         => 'Game name is required',
            'address.required'     => 'An Address is Required',
            'game_port.required'     => 'A Game Port is Required',
            'game_port.integer'      => 'Game Port must be a number',
            'type.in'                => 'Gameserver Type must be match or casual'
        ];

        $this->validate($request, $rules, $messages);

        $gameServer->name           = $request->name;
        $gameServer->type           = $request->type;
        $gameServer->ispublic       = ($request->ispublic ? true : false);
        $gameServer->isenabled       = ($request->isenabled ? true : false);
        $gameServer->address        = $request->address;
        $gameServer->game_port      = $request->game_port;
        $gameServer->stream_port      = $request->stream_port;
        $gameServer->game_password  = $request->game_password;
        $gameServer->rcon_address   = $request->rcon_address != "" ? $request->rcon_address : null ;
        $gameServer->rcon_port      = $request->rcon_port;
        $gameServer->rcon_password  = $request->rcon_password;

        if (!$gameServer->save()) {
            Session::flash('alert-danger', 'Could not save Game Server!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully saved Game Server!');
        return Redirect::to('admin/games/' . $game->slug . '#gameservers');
    }

    /**
     * Update Gameserver Token
     * @param  GameServer $gameServer
     * @param  Request $request
     * @return Redirect
     */
    public function updatetoken(Game $game, GameServer $gameServer, Request $request)
    {
        $gameServer->tokens()->delete();
        $token = $gameServer->createToken("gs_" . Str::random());
        if (!isset($token->plainTextToken) || $token->plainTextToken == "")
        {
            Session::flash('alert-danger', 'Could not create GameServer token!');
            return Redirect::back();
        }
        $gameServer->gameserver_secret = $token->plainTextToken;
        if (!$gameServer->save()) {
            Session::flash('alert-danger', 'Could not save GameServer!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully saved Game Server token!');
        return Redirect::to('admin/games/' . $game->slug . '#gameservers');
    }

    /**
     * Delete GameSe rver from Database
     * @param  Game  $game
     * @param  GameServer  $gameServer
     * @return Redirect
     */
    public function destroy(Game $game, GameServer $gameServer)
    {
        if (!$gameServer->delete()) {
            Session::flash('alert-danger', 'Cannot delete GameServer!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted GameServer!');
        return Redirect::back();
    }
}
