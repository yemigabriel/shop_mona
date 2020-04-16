<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    //
    protected $fillable = [
        'order_identity','order_id','product_id','price','quantity', 'option_value_id',
    ];

    public function product() {
        return $this->belongsTo('App\Product') ;
    }

    public function order() {
        return $this->belongsTo('App\Order') ;
    }

    public function option_value()
    {
        return $this->belongsTo('App\OptionValue','option_value_id');
    }
}
