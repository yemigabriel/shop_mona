<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['id','name',
	];

	public function option_values()
    {
        return $this->hasMany('App\OptionValue');
    }
}
