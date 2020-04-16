<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'category_id', 'title', 'alias', 'detail','original_price',
        'discount_price','in_stock', 'status', 'views','image',
    ];

    // protected $with = [
    //     'category'
    // ];

    public function category() {
        return $this->belongsTo('App\Category') ;
    }

    public function product_images() {
        return $this->hasMany('App\ProductImage') ;
    }

}
