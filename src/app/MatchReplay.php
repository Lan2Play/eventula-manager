<?php

namespace App;

use DB;
use Auth;
use File;
use Storage;

use Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

use Cviebrock\EloquentSluggable\Sluggable;

class MatchReplay extends Model
{
    use HasFactory;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'matchreplay';


    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    protected $hidden = array(
        'created_at',
        'updated_at'
    );

   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'challonge_match_id',
        'matchmaking_id',
    ];


    /*
    * Relationships
    */

    public function matchMakingMatch()
    {
        return $this->belongsTo('App\MatchMaking', 'matchmaking_id');
    }


    /*
    * MatchReplay features
    */

    public static function createReplayPath(Game $game, String $demoname)
    {
        $destinationPath = '/storage' . MatchReplay::getDestinationPathFiles($game);
        if (!File::exists(public_path() . $destinationPath)) {
            if (!File::makeDirectory(public_path() . $destinationPath, 0777, true))
            {
                return false;
            }
        }
        return MatchReplay::getReplayPath($game,$demoname);

    }

    public static function getReplayPath(Game $game, String $demoname)
    {
        return MatchReplay::getDestinationPathFiles($game).$demoname;
    }



    public static function getDestinationPathFiles(Game $game)
    {
        return '/demos/' . $game->slug . '/';
    }

    public static function getReplaySize(Game $game,$demoname)
    {
        return Helpers::bytesToHuman(Storage::disk('public')->size(MatchReplay::getReplayPath($game, $demoname)));
    }

    public function deleteReplayFile(?Game $game = null)
    {
        if(isset($this->matchMakingMatch))
        {
            if(!Storage::disk('public')->delete(MatchReplay::getReplayPath($this->matchMakingMatch->game, $this->name)))
            {
                return false;
            }
            return true;
        }
        else
        {
            if (!isset($game))
            {
                return false;
            }
            if(!Storage::disk('public')->delete(MatchReplay::getReplayPath($game, $this->name)))
            {
                return false;
            }
            return true;
        }

    }


}
