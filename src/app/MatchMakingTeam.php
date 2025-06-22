<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchMakingTeam extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'matchmaking_teams';

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
    public function match()
    {
        return $this->belongsTo('App\MatchMaking', 'match_id', 'id');
    }

    public function players()
    {
        return $this->hasMany('App\MatchMakingTeamPlayer', 'matchmaking_team_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'team_owner_id');
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

}
