<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Session;
use Storage;
use Image;
use File;

use App\Game;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class GamesController extends Controller
{
    /**
     * Show Games Index Page
     * @return Redirect
     */
    public function index()
    {
        return view('admin.games.index')
            ->with('games', Game::paginate(20));
    }

    /**
     * Show Game Page
     * @return Redirect
     */
    public function show(Game $game)
    {
        $allcommands= array();
        $allcommands[0] = 'None';
        foreach($game->gameServerMatchCommands as $gameservercommand) {
            $allcommands[$gameservercommand->id] = $gameservercommand->name;
        }

        $matchcounterror = false;
        foreach ($game->gameServers as $gameServer)
        {
            if ($gameServer->getAssignedMatchServer()["count"] > 1)
            {
                $matchcounterror = true;
            }
        }

        return view('admin.games.show')
            ->with('allCommands', $allcommands)
            ->with('game', $game)
            ->with('matchCountError', $matchcounterror);
    }

    /**
     * Store Game to Database
     * @param  Event   $event
     * @param  Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        $rules = [
            'name'              => 'required',
            'image_header'      => 'image',
            'image_thumbnail'   => 'image',
            'matchmaking_enabled'   => 'in:on,off',
            'matchmaking_autostart'   => 'in:on,off',
            'matchmaking_autoapi'   => 'in:on,off',
        ];
        $messages = [
            'name.required'         => 'Game name is required',
            'image_header.image'    => 'Header image must be a Image',
            'image_thumbnail.image' => 'Thumbnail image must be a Image',
            'matchmaking_enabled.in' => 'matchmaking_enabled must be true or false',
            'matchmaking_autostart.in' => 'matchmaking_enabled must be true or false',
            'matchmaking_autoapi.in' => 'matchmaking_enabled must be true or false',
        ];
        $this->validate($request, $rules, $messages);

        if ($request->min_team_count > $request->max_team_count)
        {
            Session::flash('alert-danger', 'minimal team count cannot be bigger than maximal team count');
            return Redirect::back();
        }

        $game               = new Game();
        $game->name         = $request->name;
        $game->description  = @(trim($request->description) == '' ? null : $request->description);
        $game->version      = @(trim($request->version) == '' ? null : $request->version);
        $game->gamecommandhandler = $request->gamecommandhandler;
        $game->gamematchapihandler = $request->gamematchapihandler;
        $game->matchmaking_enabled = ($request->matchmaking_enabled ? true : false);
        $game->matchmaking_autostart = ($request->matchmaking_autostart ? true : false);
        $game->matchmaking_autoapi = ($request->matchmaking_autoapi ? true : false);
        $game->public       = true;
        $game->min_team_count = $request->min_team_count;
        $game->max_team_count = $request->max_team_count;
        $game->connect_game_url = $request->connect_game_url;
        $game->connect_game_command = $request->connect_game_command;
        $game->connect_stream_url = $request->connect_stream_url;


        if (!$game->save()) {
            Session::flash('alert-danger', 'Could not save Game!');
            return Redirect::back();
        }

        $destinationPath = '/storage/images/games/' . $game->slug . '/';

        // TODO - refactor into model
        if (($request->file('image_thumbnail') || $request->file('image_header')) &&
            !File::exists(public_path() . $destinationPath)
        ) {
            File::makeDirectory(public_path() . $destinationPath, 0777, true);
        }

        if ($request->file('image_thumbnail')) {
            $imageName  = 'thumbnail.' . $request->file('image_thumbnail')->getClientOriginalExtension();
            Image::read($request->file('image_thumbnail'))
                ->resize(500, 500)
                ->save(public_path() . $destinationPath . $imageName);
            $game->image_thumbnail_path = $destinationPath . $imageName;
            if (!$game->save()) {
                Session::flash('alert-danger', 'Could not save Game thumbnail!');
                return Redirect::back();
            }
        }

        if ($request->file('image_header')) {
            $imageName  = 'header.' . $request->file('image_header')->getClientOriginalExtension();
            Image::read($request->file('image_header'))
                ->resize(1600, 400)
                ->save(public_path() . $destinationPath . $imageName);
            $game->image_header_path = $destinationPath . $imageName;
            if (!$game->save()) {
                Session::flash('alert-danger', 'Could not save Game Header!');
                return Redirect::back();
            }
        }
        Session::flash('alert-success', 'Successfully saved Game!');
        return Redirect::back();
    }

    /**
     * Update Game
     * @param  Event   $event
     * @param  Request $request
     * @return Redirect
     */
    public function update(Game $game, Request $request)
    {
        $rules = [
            'name'              => 'filled',
            'active'            => 'in:true,false',
            'image_header'      => 'image',
            'image_thumbnail'   => 'image',
            'matchmaking_enabled'   => 'in:on,off',
            'matchmaking_autostart'   => 'in:on,off',
            'matchmaking_autoapi'   => 'in:on,off',
        ];
        $messages = [
            'name.required'         => 'Game name is required',
            'active.filled'         => 'Active must be true or false',
            'image_header.image'    => 'Header image must be a Image',
            'image_thumbnail.image' => 'Thumbnail image must be a Image',
            'matchmaking_enabled.in' => 'matchmaking_enabled must be true or false',
            'matchmaking_autostart.in' => 'matchmaking_enabled must be true or false',
            'matchmaking_autoapi.in' => 'matchmaking_enabled must be true or false',
        ];
        $this->validate($request, $rules, $messages);

        if ($request->min_team_count > $request->max_team_count)
        {
            Session::flash('alert-danger', 'minimal team count cannot be bigger than maximal team count');
            return Redirect::back();
        }

        if ($request->matchstartgameservercommand == 0)
        {
            $matchstartgameservercommand = null;
        }
        else
        {
            $matchstartgameservercommand = $request->matchstartgameservercommand;
        }

        $game->name         = @$request->name;
        $game->description  = @(trim($request->description) == '' ? null : $request->description);
        $game->version      = @(trim($request->version) == '' ? null : $request->version);
        $game->gamecommandhandler = $request->gamecommandhandler;
        $game->gamematchapihandler = $request->gamematchapihandler;
        $game->matchstartgameservercommand = $matchstartgameservercommand;
        $game->matchmaking_enabled = ($request->matchmaking_enabled ? true : false);
        $game->matchmaking_autostart = ($request->matchmaking_autostart ? true : false);
        $game->matchmaking_autoapi = ($request->matchmaking_autoapi ? true : false);
        $game->public       = @($request->public ? true : false);
        $game->connect_game_url = @$request->connect_game_url;
        $game->connect_game_command = @$request->connect_game_command;
        $game->connect_stream_url = @$request->connect_stream_url;
        $game->min_team_count = $request->min_team_count;
        $game->max_team_count = $request->max_team_count;

        if (!$game->save()) {
            Session::flash('alert-danger', 'Could not save Game!');
            return Redirect::back();
        }

        $destinationPath = '/storage/images/games/' . $game->slug . '/';

        if (($request->file('image_thumbnail') || $request->file('image_header')) &&
            !File::exists(public_path() . $destinationPath)
        ) {
            File::makeDirectory(public_path() . $destinationPath, 0777, true);
        }

        if ($request->file('image_thumbnail')) {
            // Check if the image exists in storage before attempting to delete
            if ($game->image_thumbnail_path && Storage::exists($game->image_thumbnail_path)) {
                Storage::delete($game->image_thumbnail_path);
            }
            $imageName  = 'thumbnail.' . $request->file('image_thumbnail')->getClientOriginalExtension();
            Image::read($request->file('image_thumbnail'))
                ->resize(500, 500)
                ->save(public_path() . $destinationPath . $imageName);
            $game->image_thumbnail_path = $destinationPath . $imageName;
            if (!$game->save()) {
                Session::flash('alert-danger', 'Could not save Game thumbnail!');
                return Redirect::back();
            }
        }

        if ($request->file('image_header')) {
            // Check if the image exists in storage before attempting to delete
            if ($game->image_header_path && Storage::exists($game->image_header_path)) {
                Storage::delete($game->image_header_path);
            }
            $imageName  = 'header.' . $request->file('image_header')->getClientOriginalExtension();
            Image::read($request->file('image_header'))
                ->resize(1600, 400)
                ->save(public_path() . $destinationPath . $imageName);
            $game->image_header_path = $destinationPath . $imageName;
            if (!$game->save()) {
                Session::flash('alert-danger', 'Could not save Game Header!');
                return Redirect::back();
            }
        }
        Session::flash('alert-success', 'Successfully saved Game!');
        return Redirect::to('admin/games/' . $game->slug);
    }

    /**
     * Delete Game from Database
     * @param  Game  $game
     * @return Redirect
     */
    public function destroy(Game $game)
    {
        if ($game->eventTournaments && !$game->eventTournaments->isEmpty()) {
            Session::flash('alert-danger', 'Cannot delete game with tournaments!');
            return Redirect::back();
        }
        if ($game->gameServers && !$game->gameServers->isEmpty()) {
            Session::flash('alert-danger', 'Cannot delete game with game servers!');
            return Redirect::back();
        }
        if ($game->gameServerCommands && !$game->gameServerCommands->isEmpty()) {
            Session::flash('alert-danger', 'Cannot delete game with game Server Commands!');
            return Redirect::back();
        }

        if (!$game->delete()) {
            Session::flash('alert-danger', 'Cannot delete Game!');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Successfully deleted Game!');
        return Redirect::to('admin/games/');
    }
}
