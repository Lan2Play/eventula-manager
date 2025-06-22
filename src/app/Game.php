<?php

namespace App;

use Storage;
use Settings;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use GuzzleHttp\Client;
use Lanops\Challonge\Challonge;

use Cviebrock\EloquentSluggable\Sluggable;

class Game extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'games';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'name',
        'active',
        'gamecommandhandler',
        'gamematchapihandler',
        'image_header_path',
        'image_thumbnail_path',
        'connect_game_url',
        'connect_game_command',
        'connect_stream_url'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

    public static function boot()
    {
        parent::boot();
        self::deleted(function ($model) {
            if ($model->image_thumbnail_path || $model->image_header_path) {
                // TODO - redo storage and image calls like this
                Storage::deleteDirectory('public/images/games/' . $model->slug . '/');
            }
        });
    }

    /*
     * Relationships
     */
    public function eventTournaments()
    {
        return $this->hasMany('App\EventTournament');
    }

    public function matchMakings()
    {
        return $this->hasMany('App\MatchMaking', 'game_id', 'id');
    }

    public function gameServers()
    {
        return $this->hasMany('App\GameServer');
    }

    public function gameServerCommands()
    {
        return $this->hasMany('App\GameServerCommand');
    }

    public function gameServerCommandParameters()
    {
        return $this->hasMany('App\GameServerCommandParameter');
    }

    public function matchStartGameServerCommand()
    {
        return $this->hasOne('App\GameServerCommand', 'id', 'matchStartgameServerCommand');
    }

    public function gameServerMatchCommands()
    {
        return $this->hasMany('App\GameServerCommand')->where("scope", 1);
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getGameServerCommands()
    {
        $return = array();
        foreach (GameServerCommand::where(['game_id' => $this->id, 'scope' => 0])->get() as $gameServerCommand) {
            $return[] = $gameServerCommand;
        }

        return $return;
    }

    public function getMatchCommands()
    {
        $return = array();
        foreach (GameServerCommand::where(['game_id' => $this->id, 'scope' => 1])->get() as $gameServerCommand) {
            $return[] = $gameServerCommand;
        }

        return $return;
    }

    public function getGameServerSelectArray()
    {
        $return = array();
        foreach (GameServer::where(['game_id' => $this->id, 'type' => 'Match', 'isenabled' => true])->get() as $gameServer) {

            if (!isset($gameServer->eventTournamentMatchServer) && !isset($gameServer->matchMakingServer)) {
                $return[$gameServer->id] = $gameServer->name;
            }
        }
        return $return;
    }

    public static function getGameSelectArray($publicOnly = true)
    {
        $return[0] = 'None';
        foreach (Game::where('public', $publicOnly)->orderBy('name', 'ASC')->get() as $game) {
            $return[$game->id] = $game->name;
        }
        return $return;
    }

    public static function getMatchmakingGameSelectArray($publicOnly = true)
    {
        $return = [];
        if( Settings::isSystemsMatchMakingNonegameEnabled() || Auth::user()->getAdmin() )
        {
            $return[0] = 'None';
        }

        foreach (Game::where(['public' => $publicOnly, 'matchmaking_enabled' => true])->orderBy('name', 'ASC')->get() as $game) {
            $return[$game->id] = $game->name;
        }
        return $return;
    }
}
