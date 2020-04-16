<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
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
        'title','alias','body','image','video','author_id','status','views','meta',
    ];

    public function author() {
        return $this->belongsTo(Encore\Admin\Auth\Database\Administrator::class) ;
    }
}
