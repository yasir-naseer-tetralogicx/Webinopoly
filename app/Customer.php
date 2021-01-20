<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function has_orders(){
        return $this->hasMany(RetailerOrder::class,'customer_id');
    }
}
