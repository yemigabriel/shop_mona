<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    //
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_name','session_id','product_id','price','qty', 'total_amount', 'option_value_id',
    ];

    public function product() {
        return $this->belongsTo('App\Product') ;
    }

    public function option_value()
    {
        return $this->belongsTo('App\OptionValue','option_value_id');
    }
}
