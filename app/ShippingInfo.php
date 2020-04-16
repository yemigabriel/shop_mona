<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingInfo extends Model
{
	protected $fillable = ['id', 'location', 'cost', 'duration_days',];
}
