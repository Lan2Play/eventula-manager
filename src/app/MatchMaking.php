<?php

namespace App;

use DB;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;

class MatchMaking extends Model
{
    use HasFactory;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'matchmaking';


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

    public function teams()
    {
        return $this->hasMany('App\MatchMakingTeam', 'match_id');
    }


    public function players()
    {
        return $this->hasManyThrough('App\MatchMakingTeamPlayer', 'App\MatchMakingTeam', 'match_id', 'matchmaking_team_id', 'id', 'id');
    }


    public function game()
    {
        return $this->belongsTo('App\Game', 'game_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function matchMakingServer()
    {
        return $this->hasOne('App\MatchMakingServer', 'match_id', 'id');
    }

    public function matchReplays()
    {
        return $this->hasMany('App\MatchReplay', 'matchmaking_id');
    }


    /*
    * oldest team
    */

    public function oldestTeam()
    {
        return $this->hasOne('App\MatchMakingTeam', 'match_id')->oldest();
    }


    /**
     * check MatchTeamPlayer
     * @param  $MatchTeamPlayerId
     * @return EventTournamentParticipant
     */
    public function getMatchTeamPlayer($matchTeamPlayerId)
    {
        return $this->players()->where('user_id', $matchTeamPlayerId)->first();
    }


    /**
     * check MatchTeamOwner
     * @param  $MatchTeamOwnerId
     * @return EventTournamentParticipant
     */
    public function getMatchTeamOwner($matchTeamOwnerId)
    {
        return $this->teams()->where('team_owner_id', $matchTeamOwnerId)->first();
    }




    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }


     /**
     * Set Status
     * @param Boolean
     */
    public function setStatus($status)
    {
        $this->status = $status;
        if (!$this->save()) {
            return false;
        }
        return true;
    }

}
