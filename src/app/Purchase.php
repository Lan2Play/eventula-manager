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


    public const STATUS_SUCCESS = 'Success';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_DANGER = 'Danger';


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
    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'purchase_id');
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
        $this->status = self::STATUS_SUCCESS;
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

        if (!$this->tickets->isEmpty())
        {
            foreach ($this->tickets as $ticket)
            {
                if (!$ticket->free && !$ticket->staff)
                {
                    $total += $ticket->ticketType->price;
                }
            }
        }
        return $total; 

    }

    const CONTENT_TYPE_SHOP_ORDER = 'shopOrder';
    const CONTENT_TYPE_EVENT_TICKETS = 'eventTickets';
    const CONTENT_TYPE_NONE = 'none';

    /**
     * Determines the type of content associated with this purchase
     *
     * @return string Returns one of: 'shopOrder', 'eventTickets', or 'none'
     */
    public function getPurchaseContentType(): string
    {
        if ($this->order !== null) {
            return self::CONTENT_TYPE_SHOP_ORDER;
        }

        if (!$this->tickets->isEmpty()) {
            return self::CONTENT_TYPE_EVENT_TICKETS;
        }

        return self::CONTENT_TYPE_NONE;
    }


}
