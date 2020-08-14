<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['id'];

    /**
     * Get the coin that owns the price.
     */
    public function post()
    {
        return $this->belongsTo('App\Coin');
    }
}
