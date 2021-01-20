<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    public function has_order(){
        return $this->belongsTo(RetailerOrder::class,'retailer_order_id');
    }
    public function store(){
        return $this->belongsTo(Shop::class,'shop_id');
    }
}
