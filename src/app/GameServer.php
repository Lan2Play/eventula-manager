<?php

namespace App;

use Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use Cviebrock\EloquentSluggable\Sluggable;
use Laravel\Sanctum\HasApiTokens;

class GameServer extends Model implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Sluggable, HasApiTokens, Authenticatable, Authorizable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'game_servers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'game',
        'address',
        'game_port',
        'stream_port',
        'game_password',
        'rcon_port',
        'rcon_password'
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

    /*
     * Relationships
     */
    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function eventTournamentMatchServer()
    {
        return $this->hasOne('App\EventTournamentMatchServer', 'game_server_id', 'id');
    }

    public function matchMakingServer()
    {
        return $this->hasOne('App\MatchMakingServer', 'game_server_id', 'id');
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



    public function getAssignedMatchServer()
    {
        $matchmaking = $this->matchMakingServer;
        $eventtournament = $this->eventTournamentMatchServer;

        if (isset($matchmaking) && isset($eventtournament)) {
            return [
                'count' => 2
            ];
        }

        if (!isset($matchmaking) && !isset($eventtournament)) {
            return [
                'count' => 0
            ];
        }

        if (isset($matchmaking)) {
            return [
                'count' => 1,
                'match'  => $matchmaking
            ];
        }

        if (isset($eventtournament)) {
            return [
                'count' => 1,
                'match'  => $eventtournament
            ];
        }
    }
}
