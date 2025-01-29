<?php

namespace App;

use Auth;

use App\ShopOrderItem;

use Illuminate\Database\Eloquent\Model;

use Cviebrock\EloquentSluggable\Sluggable;

class ShopOrder extends Model
{
    /**
     * The name of the table.
     *
     * @var string
     */
    protected $table = 'shop_orders';

    protected $fillable = [
        'purchase_id',
        'status',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_country',
        'shipping_postcode',
        'shipping_state',
        'deliver_to_event'
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
    public function purchase()
    {
        return $this->belongsTo('App\Purchase', 'purchase_id');
    }
    public function items()
    {
        return $this->hasMany('App\ShopOrderItem', 'shop_order_id');
    }

    /**
     * Update Order
     * @param $item
     * @param $quantity
     * @return Boolean
     */
    public function updateOrder($item)
    {
        $params = [
            'shop_item_id'  => $item->id,
            'quantity'      => $item->quantity,
            'price'         => $item->price,
            'price_credit'  => $item->price_credit,
            'shop_order_id' => $this->id,
        ];
        if (!ShopOrderItem::create($params)) {
            return false;
        }
        return true;
    }

    /**
     * Get New Orders
     * @param $type
     * @return Orders
     */
    public static function getNewOrders($type = 'all')
    {
        if (!$user = Auth::user()) {
            $type = 'all';
        }
        switch ($type) {
            case 'login':
                $orders = self::where('created_at', '>=', $user->last_login)->get();
                break;
            default:
                $orders = self::where('created_at', '>=', date('now - 1 day'))->get();
                break;
        }
        return $orders;
    }

    /**
     * Check if Order has Shipping Details
     * @return Boolean
     */
    public function hasShipping()
    {
        if (
            trim($this->shipping_first_name) == "" &&
            trim($this->shipping_last_name) == "" &&
            trim($this->shipping_address_1) == "" &&
            trim($this->shipping_address_2) == "" &&
            trim($this->shipping_country) == "" &&
            trim($this->shipping_postcode) == "" &&
            trim($this->shipping_state) == ""

        ) {
            return false;
        }
        return true;
    }

    /**
     * Set Order as Processing
     * @param $params
     * @return Orders
     */
    public function setAsProcessing($params)
    {
        $this->status = 'PROCESSING';
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Order as Shipped
     * @param $params
     * @return Orders
     */
    public function setAsShipped($params)
    {
        $this->status = 'SHIPPED';
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Order as Complete
     * @param $params
     * @return Orders
     */
    public function setAsComplete($params)
    {
        $this->status = 'COMPLETE';
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Set Order as Cancelled
     * @param $params
     * @return Orders
     */
    public function setAsCancelled($params)
    {
        $this->status = 'CANCELLED';
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Update Tracking Details
     * @param $params
     * @return Orders
     */
    public function updateTrackingDetails($params)
    {
        $this->shipping_tracking = $params->shipping_tracking;
        $this->shipping_note = $params->shipping_note;
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Get total price of the order
     * @return float
     */
    public function getTotalPrice()
    {
        $total = 0.0;

        foreach ($this->items as $item) {
            $total += $item->price * $item->quantity;
        }
        return $total; 

    }

    /**
     * Get total credit price of the order
     * @return float
     */
    public function getTotalCreditPrice()
    {
        $total = 0.0;

        foreach ($this->items as $item) {
            $total += $item->price_credit * $item->quantity;
        }
        return $total; 

    }

}
