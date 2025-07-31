<?php

namespace App;

use Helpers;

use Illuminate\Database\Eloquent\Model;

use GuzzleHttp;
// use Lanops\Challonge\Challonge;
use Reflex\Challonge\Challonge;

class EventTournamentTeam extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_tournament_teams';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_tournament_id',
        'challonge_participant_id',
        'name'
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
        self::created(function ($model) {
            try {
                if ($model->eventTournament->format != 'list') {
                    $http = new GuzzleHttp\Client();
                    $challonge = new Challonge($http, config('challonge.api_key'), false);
                    $tournament = $challonge->fetchTournament($model->eventTournament->challonge_tournament_id);
                    if (!$response = $tournament->addParticipant(['participant[name]' => $model->name])) {
                        $model->delete();
                        return false;
                    }
                    $model->challonge_participant_id = $response->id;
                    $model->save();
                }
                return true;
            } catch (\Throwable $e) {
                Helpers::rethrowIfDebug($e);
                Session::flash('alert-danger', $e->getMessage());
                $model->delete();
            }

            return false;
        });
        self::deleting(function ($model) {
            try {
                if ($model->eventTournament->format != 'list') {
                    $http = new GuzzleHttp\Client();
                    $challonge = new Challonge($http, config('challonge.api_key'), false);
                    $participant = $challonge->getParticipant(
                        $model->eventTournament->challonge_tournament_id,
                        $model->challonge_participant_id
                    );
                    if (!$response = $participant->delete()) {
                        return false;
                    }
                }
                return true;
            } catch (\Throwable $e) {
                Helpers::rethrowIfDebug($e);
                Session::flash('alert-danger', $e->getMessage());
            }

            return false;
        });
    }

    /*
     * Relationships
     */
    public function eventTournament()
    {
        return $this->belongsTo('App\EventTournament');
    }
    public function tournamentParticipants()
    {
        return $this->hasMany('App\EventTournamentParticipant');
    }
}
