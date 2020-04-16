<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
	protected $fillable = ['id', 'shipping_info_id', 'session_id', 'visitor'];


	public function shipping_info() 
	{
        return $this->belongsTo('App\ShippingInfo', 'shipping_info_id') ;
	}


}
