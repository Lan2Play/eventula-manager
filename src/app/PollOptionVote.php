<?php

namespace App;

use DB;
use Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class PollOptionVote extends Model
{
    use HasFactory;

    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'poll_option_votes';
    
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
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function pollOption()
    {
        return $this->belongsTo('App\PollOption', 'poll_option_id');
    }

    /**
     * Get New Votes
     * @param $type
     * @return PollOptionVote
     */
    public static function getNewVotes($type = 'all')
    {
        if (!$user = Auth::user()) {
            $type = 'all';
        }
        switch ($type) {
            case 'login':
                $votes = self::where('created_at', '>=', $user->last_login)->get();
                break;
            default:
                $votes = self::where('created_at', '>=', date('now - 1 day'))->get();
                break;
        }
        return $votes;
    }
}
