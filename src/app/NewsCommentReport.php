<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsCommentReport extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'news_feed_comment_reports';

    protected $fillable = ['news_feed_comment_id', 'user_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array(
        'created_at',
    );

    /*
     * Relationships
     */
    public function newsComment()
    {
        return $this->belongsTo('App\NewsComment', 'news_feed_comment_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
