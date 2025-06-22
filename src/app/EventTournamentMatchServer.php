<?php

namespace App;

use Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Cviebrock\EloquentSluggable\Sluggable;

class EventTournamentMatchServer extends Model
{
    use HasFactory;
    use Sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_tournament_match_server';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'challonge_match_id',
        'event_tournament_id',
        'game_server'
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
        return $this->belongsTo('App\GameServer');
    }

    public function eventTournament()
    {
        return $this->belongsTo('App\EventTournament', 'event_tournament_id', 'id');
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
                'source' => 'challonge_match_id'
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

    /**
     * Get the Server for a match.
     *
     * @return EventTournamentMatchServer
     */
    public static function getTournamentMatchServer(int $challongeMatchId)
    {
        return EventTournamentMatchServer::where('challonge_match_id', $challongeMatchId)->first();
    }
}
