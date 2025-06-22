<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MatchMakingServer extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'matchmaking_server';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'match_id',
        'game_server_id'
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
    public function gameServer()
    {
        return $this->belongsTo('App\GameServer', 'game_server_id', 'id');
    }
    public function match()
    {
        return $this->belongsTo('App\MatchMaking', 'match_id', 'id');
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
     * Get the Server for a match.
     *
     * @return MatchMakingServer
     */
    public static function getMatchMakingServer(int $matchId)
    {
        return MatchMakingServer::where('match_id', $matchId)->first();
    }


}
