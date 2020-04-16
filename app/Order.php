<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'order_identity','session_id','transaction_id','stripe_transaction_id',
        'gateway_transaction_id','gross_price','payment_id','status','user_id','name','phone',
        'email','address'
    ];
    //user_id?

    public function user() {
        return $this->belongsTo('App\User') ;
    }

    public function order_items() { //order items
        return $this->hasMany('App\OrderItem') ;
    }

    public function shipping() 
    {
        return $this->hasOne('App\Shipping', 'order_id') ;
    }

}
