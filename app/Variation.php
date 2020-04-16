<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
 //    protected $fillable = ['id','sku','product_id','option_id',
	// ];

	protected $fillable = [
        'quantity',
        'price',
        'sale_price',
        'default'
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function optionValues()
    {
        return $this->belongsToMany('App\OptionValue');
    }
}
