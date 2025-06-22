<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchMakingTeamPlayer extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'matchmaking_team_players';

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
    public function team()
    {
        return $this->belongsTo('App\MatchMakingTeam', 'matchmaking_team_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
