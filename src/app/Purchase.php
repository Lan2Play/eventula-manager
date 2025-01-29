<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'purchases';

    protected $fillable = [
        'user_id',
        'type',
        'transaction_id',
        'token',
        'status',
        'paypal_email'
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
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function participants()
    {
        return $this->hasMany('App\EventParticipant', 'purchase_id');
    }
    public function order()
    {
        return $this->hasOne('App\ShopOrder', 'purchase_id');
    }
    public function creditLog()
    {
        return $this->hasOne('App\CreditLog', 'purchase_id');
    }


    /**
     * Get Purchase Type
     * @param String
     */
    public function getPurchaseType()
    {
        switch (strtolower($this->type)) {
            case 'stripe':
                return 'Card';
                break;
            case 'paypal express':
                return 'Paypal';
                break;
            case 'free':
                return 'free';
                break;           
            case 'system':
                return 'System (Free)';
                break;            
            case 'onsite':
                return 'onsite';
                break;
            default:
                return $this->type;
                break;
        }
    }


    /**
     * Set Purchase Success
     * @return boolean
     */
    public function setSuccess()
    {
        $this->status = "Success";
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Get total price of the orders Tickets
     * @return float
     */
    public function getTotalTicketPrice()
    {
        $total = 0.0;

        if (!$this->participants->isEmpty())
        {
            foreach ($this->participants as $participant)
            {
                if (!$participant->free && !$participant->staff)
                {
                    $total += $participant->ticket->price;
                }
            }
        }
        return $total; 

    }



}
