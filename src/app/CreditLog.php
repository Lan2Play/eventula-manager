<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditLog extends Model
{
    use HasFactory;
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'credit_log';

    protected $fillable = ['user_id', 'action', 'amount', 'reason', 'purchase_id', 'admin_id'];

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
    public function admin()
    {
        return $this->belongsTo('App\User', 'admin_id');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function purchase()
    {
        return $this->belongsTo('App\Purchase', 'purchase_id');
    }
}
