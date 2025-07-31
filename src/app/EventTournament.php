<?php

namespace App;

use Cache;
use Settings;
use Session;
use Helpers;

use App\EventTournamentParticipant;
use App\EventTournamentMatchServer;
use App\Jobs\GameServerAsign;

use Illuminate\Database\Eloquent\Model;


use GuzzleHttp;
// use Lanops\Challonge\Challonge;
use Reflex\Challonge\Challonge;
use Cviebrock\EloquentSluggable\Sluggable;


class EventTournament extends Model
{
    use sluggable;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'event_tournaments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'challonge_tournament_id',
        'challonge_tournament_url',
        'display_name',
        'nice_name',
        'game',
        'format',
        'bronze',
        'team_size',
        'description',
        'rules',
        'allow_player_teams',
        'status',
        'grand_finals_modifier',
        'randomteams'
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

    protected $casts = [
        'final_history' => 'array',
        'final_ratio' => 'array'
    ];

    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {
            try {
                if ($model->format != 'list') {
                    $http = new GuzzleHttp\Client();
                    $challonge = new Challonge($http, config('challonge.api_key'), false);
                    switch ($model->grand_finals_modifier) {
                        case 'skip':
                            $grand_finals_modifier = 'skip';
                            break;
                        case 'singlematch':
                            $grand_finals_modifier = 'single match';
                            break;
                        case 'doublematch':
                            $grand_finals_modifier = NULL;
                            break;
                    }
                    $params = [
                        'tournament[name]'                    => $model->name,
                        'tournament[tournament_type]'         => strtolower($model->format),
                        'tournament[url]'                     => $model->challonge_tournament_url,
                        'tournament[hold_third_place_match]'  => @($model->allow_bronze ? true : false),
                        'tournament[show_rounds]'             => true,
                        'tournament[grand_finals_modifier]'   => $grand_finals_modifier,
                    ];

                    if (!$response = retry(5, function () use ($challonge, $params) {
                        return $challonge->createTournament($params);
                    }, 100)) {
                        $model->delete();
                        return false;
                    }
                    $model->challonge_tournament_id = $response->id;
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
        self::saved(function ($model) {
            try {
                if ($model->format != 'list' && $model->status != 'LIVE') {
                    // TODO - fire only when name is updated
                    $http = new GuzzleHttp\Client();
                    $challonge = new Challonge($http, config('challonge.api_key'), false);

                    $challongeTournament = retry(5, function () use ($challonge, $model) {
                        return $challonge->fetchTournament($model->challonge_tournament_id);
                    }, 100);

                    $params = [
                        'tournament[name]' => $model->name
                    ];
                    if (!$response = $challongeTournament->update($params)) {
                        return false;
                    }
                }
                if ($model->status == 'COMPLETE' && $model->format != 'list' && !$model->api_complete) {
                    foreach ($model->getStandings('desc', true)->final as $standings) {
                        $http = new GuzzleHttp\Client();
                        $challonge = new Challonge($http, config('challonge.api_key'), false);
                        if (!$challongeParticipants = retry(5, function () use ($challonge, $model) {
                            return $challonge->getParticipants($model->challonge_tournament_id);
                        }, 100)) {
                            return false;
                        }
                        if ($model->team_size == '1v1') {
                            $tournamentParticipant = $model->getParticipantByChallongeId($standings->id);
                            // TODO - Refactor
                            foreach ($challongeParticipants as $challongeParticipant) {
                                if ($challongeParticipant->id == $tournamentParticipant->challonge_participant_id) {
                                    $tournamentParticipant->final_rank = $challongeParticipant->final_rank;
                                }
                            }
                            $tournamentParticipant->final_ratio = serialize([
                                'W' => $standings->win,
                                'L' => $standings->lose,
                                'T' => $standings->tie
                            ]);
                            $tournamentParticipant->final_score = $standings->pts;
                            $finalHistory = array();
                            foreach ($standings->history as $game) {
                                array_push($finalHistory, $game);
                            }
                            $tournamentParticipant->final_History = serialize($finalHistory);
                            $creditAmount = 0;
                            if (Settings::isCreditEnabled() && !$tournamentParticipant->credit_applied) {
                                $creditAmount += Settings::getCreditTournamentParticipation();
                                switch ($tournamentParticipant->final_rank) {
                                    case '1':
                                        $creditAmount += Settings::getCreditTournamentFirst();
                                        break;
                                    case '2':
                                        $creditAmount += Settings::getCreditTournamentSecond();
                                        break;
                                    case '3':
                                        $creditAmount += Settings::getCreditTournamentThird();
                                        break;
                                }
                                $tournamentParticipant->eventTicket->user->editCredit($creditAmount, false, 'Tournament ' . $model->name . ' 1st Place');
                            }
                            $tournamentParticipant->credit_applied = true;
                            $tournamentParticipant->save();
                        }
                        if ($model->team_size != '1v1') {
                            $tournamentTeam = $model->getTeamByChallongeId($standings->id);
                            // TODO - Refactor
                            foreach ($challongeParticipants as $challongeParticipant) {
                                if ($challongeParticipant->id == $tournamentTeam->challonge_participant_id) {
                                    $tournamentTeam->final_rank = $challongeParticipant->final_rank;
                                }
                            }
                            $tournamentTeam->final_ratio = serialize([
                                'W' => $standings->win,
                                'L' => $standings->lose,
                                'T' => $standings->tie
                            ]);
                            $tournamentTeam->final_score = $standings->pts;
                            $finalHistory = array();
                            foreach ($standings->history as $game) {
                                array_push($finalHistory, $game);
                            }
                            $tournamentTeam->final_History = serialize($finalHistory);
                            if (Settings::isCreditEnabled()) {
                                foreach ($tournamentTeam->tournamentParticipants as $tournamentParticipant) {
                                    $creditAmount = 0;
                                    if (!$tournamentParticipant->credit_applied) {
                                        $creditAmount += Settings::getCreditTournamentParticipation();
                                        switch ($tournamentParticipant->final_rank) {
                                            case '1':
                                                $creditAmount += Settings::getCreditTournamentFirst();
                                                break;
                                            case '2':
                                                $creditAmount += Settings::getCreditTournamentSecond();
                                                break;
                                            case '3':
                                                $creditAmount += Settings::getCreditTournamentThird();
                                                break;
                                        }
                                        $tournamentParticipant->eventTicket->user->editCredit($creditAmount, false, 'Tournament ' . $model->name . ' 1st Place');
                                    }
                                    $tournamentParticipant->credit_applied = true;
                                    $tournamentParticipant->save();
                                }
                            }
                            $tournamentTeam->save();
                        }
                        $model->api_complete = true;
                        $model->save();
                    }
                }
                return true;
            } catch (\Throwable $e) {
                Helpers::rethrowIfDebug($e);
                Session::flash('alert-danger', $e->getMessage());
            }
            return false;
        });
        self::deleting(function ($model) {
            try {
                if ($model->format != 'list') {
                    $http = new GuzzleHttp\Client();
                    $challonge = new Challonge($http, config('challonge.api_key'), false);
                    if (isset($model->challonge_tournament_id) && $model->challonge_tournament_id != null) {
                        $matches = retry(5, function () use ($challonge, $model) {
                            return $challonge->getMatches($model->challonge_tournament_id);
                        }, 100);

                        $matchServers = EventTournamentMatchServer::all();
                        foreach ($matchServers as $key => $matchServer) {
                            foreach ($matches as $key => $match) {
                                if ($match->id == $matchServer->challonge_match_id) {
                                    $matchServer->delete();
                                    break;
                                }
                            }
                        }

                        foreach ($matches as $key => $match) {
                            $replays = MatchReplay::where('challonge_match_id', $match->id)->get();
                            foreach ($replays as $matchReplay) {
                                $matchReplay->deleteReplayFile($model->game);
                                $matchReplay->delete();
                            }
                        }

                        $response = retry(5, function () use ($challonge, $model) {
                            return $challonge->fetchTournament($model->challonge_tournament_id);
                        }, 100);

                        if (!$response->delete()) {
                            return false;
                        }
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
    public function event()
    {
        return $this->belongsTo('App\Event')->withoutGlobalScopes();
    }
    public function tournamentParticipants()
    {
        return $this->hasMany('App\EventTournamentParticipant');
    }
    public function tournamentTeams()
    {
        return $this->hasMany('App\EventTournamentTeam');
    }
    public function eventTournamentMatchServer()
    {
        return $this->hasMany('App\EventTournamentMatchServer');
    }
    public function game()
    {
        return $this->belongsTo('App\Game');
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

    /**
     * Set Status
     * @param Boolean
     */
    public function setStatus($status)
    {
        try {
            $http = new GuzzleHttp\Client();
            $challonge = new Challonge($http, config('challonge.api_key'), false);
            if ($status == 'LIVE') {
                if ($this->tournamentTeams) {
                    foreach ($this->tournamentTeams as $team) {
                        if ($team->tournamentParticipants->isEmpty()) {
                            $team->delete();
                        }
                    }
                }
                if ($this->format != 'list') {

                    $tournament = retry(5, function () use ($challonge) {
                        return $challonge->fetchTournament($this->challonge_tournament_id);
                    }, 100);

                    try {
                        $tournament->start();
                    } catch (\Exception $e) {
                        Helpers::rethrowIfDebug($e);
                        Session::flash('alert-danger', $e->getMessage());
                        return false;
                    }
                }
            }
            if ($status == 'COMPLETE') {
                if ($this->format != 'list') {
                    $tournament = retry(5, function () use ($challonge) {
                        return $challonge->fetchTournament($this->challonge_tournament_id);
                    }, 100);

                    try {
                        $tournament->finalize();
                    } catch (\Exception $e) {
                        Helpers::rethrowIfDebug($e);
                        Session::flash('alert-danger', $e->getMessage());
                        return false;
                    }
                }
            }
            $this->status = $status;
            if (!$this->save()) {
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Helpers::rethrowIfDebug($e);
            Session::flash('alert-danger', $e->getMessage());
        }
        return false;
    }

    /**
     * Get Tournament Participant
     * @param  $eventParticipantId
     * @return EventTournamentParticipant
     */
    public function getParticipant($eventParticipantId)
    {
        return $this->tournamentParticipants()->where('ticket_id', $eventParticipantId)->first();
    }

    /**
     * Get Tournament Participant By Challonge ID
     * @param  $challongeParticipantId
     * @return EventTournamentParticipant
     */
    public function getParticipantByChallongeId($challongeParticipantId)
    {
        return $this->tournamentParticipants()->where('challonge_participant_id', $challongeParticipantId)->first();
    }

    /**
     * Get Tournament Team By Challonge ID
     * @param  $challongeParticipantId
     * @return EventTournamentParticipant
     */
    public function getTeamByChallongeId($challongeParticipantId)
    {
        return $this->tournamentTeams()->where('challonge_participant_id', $challongeParticipantId)->first();
    }

    /**
     * Get Teams
     * @param  boolean $obj
     * @return Array|Object
     */
    public function getTeams($obj = false)
    {
        if (!isset($this->tournamentTeams)) {
            return null;
        }
        $return = array();
        foreach ($this->tournamentTeams as $tournamentTeam) {
            $return[$tournamentTeam->id] = $tournamentTeam->name;
        }
        if ($obj) {
            return json_decode(json_encode($return), false);
        }
        return $return;
    }

    /**
     * Get Matches
     * @param  boolean $obj
     * @return Array|Object
     */
    public function getMatches($obj = false)
    {
        try {
            $tournamentMatchesJson = Cache::get($this->challonge_tournament_id . "_matches", function () {
                $http = new GuzzleHttp\Client();
                $challonge = new Challonge($http, config('challonge.api_key'), false);
                $matches = json_encode(retry(5, function () use ($challonge) {
                    return $challonge->getMatches($this->challonge_tournament_id)->toArray();
                }, 100));
                Cache::rememberForever($this->challonge_tournament_id . "_matches", function () use ($matches) {
                    return $matches;
                });
                return $matches;
            });
            $tournamentMatches = json_decode($tournamentMatchesJson);
            $return = array();
            $suggestedplayordercounter = 0;
            foreach ($tournamentMatches as $match) {
                if (!property_exists($match, "optional") || !$match->optional) {
                    $suggestedplayordercounter++;
                    $return[$match->round][$suggestedplayordercounter] = $match;
                }
            }
            if ($obj) {
                return json_decode(json_encode($return), false);
            }
            return $return;
        } catch (\Throwable $e) {
            Helpers::rethrowIfDebug($e);
            Session::flash('alert-danger', $e->getMessage());
        }

        return array();
    }

    /**
     * Get single Match
     * @param int $challongeMatchId
     * @param boolean $obj
     * @return Array|Object
     */
    public function getMatch(int $challongeMatchId, $obj = false)
    {
        try {
            $tournamentMatchesJson = Cache::get($this->challonge_tournament_id . "_matches", function () {
                $http = new GuzzleHttp\Client();
                $challonge = new Challonge($http, config('challonge.api_key'), false);
                $matches = json_encode(retry(5, function () use ($challonge) {
                    return $challonge->getMatches($this->challonge_tournament_id)->toArray();
                }, 100));

                Cache::rememberForever($this->challonge_tournament_id . "_matches", function () use ($matches) {
                    return $matches;
                });
                return $matches;
            });
            $tournamentMatches = json_decode($tournamentMatchesJson);

            foreach ($tournamentMatches as $match) {
                if ($match->id == $challongeMatchId) {
                    if ($obj) {
                        return json_decode(json_encode($match), false);
                    }
                    return $match;
                }
            }
        } catch (\Throwable $e) {
            Helpers::rethrowIfDebug($e);
            Session::flash('alert-danger', $e->getMessage());
        }

        return false;
    }

    /**
     * Get Matches
     * @param int $challongeMatchId
     * @param boolean $obj
     * @return Array|Object
     */
    public function getMatchReplays(int $challongeMatchId, $obj = false)
    {
        return MatchReplay::where('challonge_match_id', $challongeMatchId)->get();
    }


    /**
     * Get Standings
     * @param  string $order
     * @param  boolean $obj
     * @param  boolean $retroactive - For Legacy
     * @return Array|Object
     */
    public function getStandings($order = null, $obj = false, $retroactive = false)
    {
        try {
            $tournamentStandings = Cache::get(
                $this->challonge_tournament_id . "_standings",
                function () use ($retroactive) {
                    if ($this->status == 'COMPLETE' && $this->api_complete && $this->format != 'list') {
                        $standings['progress'] = 100;
                        $standingsArray = array();
                        if ($this->team_size != '1v1') {
                            $participants = $this->tournamentTeams;
                        }
                        if ($this->team_size == '1v1') {
                            $participants = $this->tournamentParticipants;
                        }
                        foreach ($participants as $participant) {
                            $ratio = unserialize($participant->final_ratio);
                            $history = unserialize($participant->final_history);
                            $params = [
                                'win' => $ratio['W'],
                                'lose' => $ratio['L'],
                                'tie' => $ratio['T'],
                                'pts' => $participant->final_score,
                                'history' => $history,
                                'name' => ($this->team_size != '1v1' ?
                                    $participant->name : $participant->eventParticipant->user->username),
                                'id' => $participant->challonge_participant_id
                            ];
                            array_push($standingsArray, $params);
                        }
                        $standings['final'] = collect($standingsArray);
                    }
                    ## Pull LIVE standings from Challonge when tournament is in progress
                    if ($retroactive || ($this->status != 'COMPLETE' && !$this->api_complete && $this->format != 'list')) {
                        $http = new GuzzleHttp\Client();
                        $challonge = new Challonge($http, config('challonge.api_key'), false);
                        $standings = retry(5, function () use ($challonge) {
                            return $challonge->getStandings($this->challonge_tournament_id);
                        }, 100);
                    }

                    Cache::forever($this->challonge_tournament_id . "_standings", $standings);
                    return $standings;
                }
            );
            if ($order == 'asc') {
                $standings = $tournamentStandings['final'];
                $tournamentStandings['final'] = $standings->sortBy('pts');
            }
            if ($order == 'desc') {
                $standings = $tournamentStandings['final'];
                $tournamentStandings['final'] = $standings->sortByDesc('pts');
            }
            if ($obj) {
                return json_decode(json_encode($tournamentStandings), false);
            }
            return $tournamentStandings;
        } catch (\Throwable $e) {
            Helpers::rethrowIfDebug($e);
            Session::flash('alert-danger', $e->getMessage());
        }

        return false;
    }

    /**
     * Get Next Matches
     * @param  integer $limit
     * @param  boolean $obj
     * @return Array|Object
     */
    public function getNextMatches($limit = 0, $obj = false)
    {
        try {
            $tournamentMatchesJson = Cache::get($this->challonge_tournament_id . "_matches", function () {
                $http = new GuzzleHttp\Client();
                $challonge = new Challonge($http, config('challonge.api_key'), false);
                $matches = json_encode(retry(5, function () use ($challonge) {
                    return $challonge->getMatches($this->challonge_tournament_id)->toArray();
                }, 100));

                Cache::rememberForever($this->challonge_tournament_id . "_matches", function () use ($matches) {
                    return $matches;
                });
                return $matches;
            });
            $tournamentMatches = json_decode($tournamentMatchesJson);
            $nextMatches = array();
            foreach ($tournamentMatches as $match) {
                if ($match->state == 'open') {
                    $nextMatches[] = $match;
                }
                if (count($nextMatches) == $limit && $limit != 0) {
                    break;
                }
            }
            if ($obj) {
                return json_decode(json_encode($nextMatches), false);
            }
            return $nextMatches;
        } catch (\Throwable $e) {
            Helpers::rethrowIfDebug($e);
            Session::flash('alert-danger', $e->getMessage());
        }

        return array();
    }

    /**
     * Update Match
     * @param  string $matchId
     * @param  string $player1Score
     * @param  string $player2Score
     * @param  string $player_winner_verify
     * @return Array|Object
     */
    public function updateMatch($matchId, $player1Score, $player2Score, $playerWinnerVerify = null)
    {
        if ($player1Score == 0 && $player2Score == 0) {
            return false;
        }

        try {
            // TODO - add support for multiple sets
            $http = new GuzzleHttp\Client();
            $challonge = new Challonge($http, config('challonge.api_key'), false);
            $match = retry(5, function () use ($challonge, $matchId) {
                return $challonge->getMatch($this->challonge_tournament_id, $matchId);
            }, 100);

            if ($player1Score > $player2Score) {
                $playerWinnerId = $match->player1_id;
            }
            if ($player2Score > $player1Score) {
                $playerWinnerId = $match->player2_id;
            }
            if ($playerWinnerVerify == 'player1') {
                $playerWinnerId = $match->player1_id;
            }
            if ($playerWinnerVerify == 'player2') {
                $playerWinnerId = $match->player2_id;
            }
            $params = [
                'match' => [
                    'scores_csv' => $player1Score . '-' . $player2Score,
                    'winner_id' => $playerWinnerId
                ]
            ];
            if (!$response = $match->update($params)) {
                return false;
            }
            # Update Cache
            Cache::forget($this->challonge_tournament_id . "_matches");
            Cache::forget($this->challonge_tournament_id . "_standings");
            $this->getMatches();
            $this->getStandings();

            # queue next matches
            if (isset($this->game) && $this->match_autostart) {
                $nextmatches = $this->getNextMatches();
                foreach ($nextmatches as $nextmatch) {
                    $matchserver = EventTournamentMatchServer::getTournamentMatchServer($nextmatch->id);
                    if (!isset($matchserver)) {
                        GameServerAsign::dispatch(null, $this, $nextmatch->id)->onQueue('gameserver');
                    }
                }
            }

            return $response;
        } catch (\Throwable $e) {
            Helpers::rethrowIfDebug($e);
            Session::flash('alert-danger', $e->getMessage());
        }

        return false;
    }

    /**
     * Update Match
     * @param  string $matchId
     * @param  string $player1Score
     * @param  string $player2Score
     * @param  string $player_winner_verify
     * @return Array|Object
     */
    public function updateMatchScores($matchId, $player1Score, $player2Score)
    {
        try {
            // TODO - add support for multiple sets
            $http = new GuzzleHttp\Client();
            $challonge = new Challonge($http, config('challonge.api_key'), false);
            $match = retry(5, function () use ($challonge, $matchId) {
                return $challonge->getMatch($this->challonge_tournament_id, $matchId);
            }, 100);


            $params = [
                'match' => [
                    'scores_csv' => $player1Score . '-' . $player2Score
                ]
            ];
            if (!$response = $match->update($params)) {
                return false;
            }
            # Update Cache
            Cache::forget($this->challonge_tournament_id . "_matches");
            Cache::forget($this->challonge_tournament_id . "_standings");
            $this->getMatches();
            $this->getStandings();
            return $response;
        } catch (\Throwable $e) {
            Helpers::rethrowIfDebug($e);
            Session::flash('alert-danger', $e->getMessage());
        }

        return false;
    }

    /**
     * Update all scores Retoractively - This is for Legacy
     * @return Array|Object
     */
    public static function getAllScoresRetroActively()
    {
        $count = 0;
        foreach (EventTournament::all() as $model) {
            if ($model->status == 'COMPLETE' && $model->format != 'list' && !$model->api_complete) {
                foreach ($model->getStandings('desc', true, true)->final as $standings) {
                    $http = new GuzzleHttp\Client();
                    $challonge = new Challonge($http, config('challonge.api_key'), false);




                    if (!$challongeParticipants = retry(5, function () use ($challonge, $model) {
                        return $challonge->getParticipants($model->challonge_tournament_id);
                    }, 100)) {
                        return false;
                    }
                    if ($model->team_size == '1v1') {
                        $tournamentParticipant = $model->getParticipantByChallongeId($standings->id);
                        // TODO - Refactor
                        foreach ($challongeParticipants as $challongeParticipant) {
                            if ($challongeParticipant->id == $tournamentParticipant->challonge_participant_id) {
                                $tournamentParticipant->final_rank = $challongeParticipant->final_rank;
                            }
                        }
                        $tournamentParticipant->final_ratio = serialize([
                            'W' => $standings->win,
                            'L' => $standings->lose,
                            'T' => $standings->tie
                        ]);
                        $tournamentParticipant->final_score = $standings->pts;
                        $finalHistory = array();
                        foreach ($standings->history as $game) {
                            array_push($finalHistory, $game);
                        }
                        $tournamentParticipant->final_history = serialize($finalHistory);
                        $tournamentParticipant->save();
                    }
                    if ($model->team_size != '1v1') {
                        $tournamentTeam = $model->getTeamByChallongeId($standings->id);
                        // TODO - Refactor
                        foreach ($challongeParticipants as $challongeParticipant) {
                            if ($challongeParticipant->id == $tournamentTeam->challonge_participant_id) {
                                $tournamentTeam->final_rank = $challongeParticipant->final_rank;
                            }
                        }
                        $tournamentTeam->final_ratio = serialize([
                            'W' => $standings->win,
                            'L' => $standings->lose,
                            'T' => $standings->tie
                        ]);
                        $tournamentTeam->final_score = $standings->pts;
                        $finalHistory = array();
                        foreach ($standings->history as $game) {
                            array_push($finalHistory, $game);
                        }
                        $tournamentTeam->final_history = serialize($finalHistory);
                        $tournamentTeam->save();
                    }
                    $model->api_complete = true;
                    $model->save();
                }
                $count++;
            }
        }
        dd('DUN');
    }

    /**
     * get bestofnames
     * @return Array
     */
    public static function getBestofnames()
    {
        return array(
            "one" => "Best of one",
            "three" => "Best of three",
            "threefinal" => "Best of three finals",
            "threesemifinalfinal" => "Best of three semifinals + finals",
        );
    }

    /**
     * get grandfinalsmodifiernames
     * @return Array
     */
    public static function getGrandfinalmodifiernames()
    {
        return array(
            "skip" => "no grand finale",
            "singlematch" => "Grand finale with 1 match",
            "doublematch" => "Grand finale with 1 or 2 matches",
        );
    }

    /**
     * get isFinalMatch
     * @return bool
     */
    public function isFinalMatch(int $challongeMatchId)
    {
        $matches = $this->getMatches();
        $matchcount = count($matches);
        $selectedmatchround = 0;

        $maxRound = 1;

        foreach ($matches as $roundkey => $matchround) {
            foreach ($matchround as $match) {

                if ($match->round > $maxRound) {
                    $maxRound = $match->round;
                }

                if ($match->id == $challongeMatchId) {
                    $selectedmatchround = $match->round;
                }
            }
        }

        return $selectedmatchround == $maxRound;
    }

    /**
     * get isSemiFinalMatch
     * @return bool
     */
    public function isSemiFinalMatch(int $challongeMatchId)
    {
        $matches = $this->getMatches();
        $matchcount = count($matches);
        $selectedmatchround = 0;

        $maxRound = 1;

        foreach ($matches as $roundkey => $matchround) {
            foreach ($matchround as $match) {

                if ($match->round > $maxRound) {
                    $maxRound = $match->round;
                }

                if ($match->id == $challongeMatchId) {
                    $selectedmatchround = $match->round;
                }
            }
        }

        return $selectedmatchround == $maxRound - 1;
    }

    /**
     * get isThirdPlaceMatch
     * @return bool
     */
    public function isThirdPlaceMatch(int $challongeMatchId)
    {
        $matches = $this->getMatches();

        foreach ($matches as $roundkey => $matchround) {
            foreach ($matchround as $match) {
                if ($match->id == $challongeMatchId) {
                    if ($match->round == 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * get isFinalMatch
     * @return bool
     */
    public function isLoserBracketMatch(int $challongeMatchId)
    {
        $matches = $this->getMatches();
        $matchcount = count($matches);
        $selectedmatchround = 0;

        foreach ($matches as $roundkey => $matchround) {
            foreach ($matchround as $match) {
                if ($match->id == $challongeMatchId) {
                    return $match->round < 0;
                }
            }
        }

        return false;
    }

    /**
     * get isSemiFinalMatch
     * @return Array
     */
    public function getnummaps(int $challongeMatchId)
    {
        if (!isset($challongeMatchId)) {
            throw new \InvalidArgumentException('Challonge Match Id is empty!');
        }
        if ($this->bestof == "one") {
            return 1;
        }
        if ($this->bestof == "three") {
            return 3;
        }
        if ($this->bestof == "threefinal") {
            return $this->isFinalMatch($challongeMatchId) ? 3 : 1;
        }
        if ($this->bestof == "threesemifinalfinal") {

            return $this->isFinalMatch($challongeMatchId) || $this->isSemiFinalMatch($challongeMatchId) ? 3 : 1;
        }

        throw new \InvalidArgumentException('Wrong bestof:' . $this->bestof);
    }
}
