<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    protected $fillable = ['id','option_id','value',
	];

	public function option()
    {
        return $this->belongsTo('App\Option');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function variation()
    {
        return $this->belongsToMany('App\Variation');
    }
}
