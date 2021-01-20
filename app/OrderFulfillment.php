<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderFulfillment extends Model
{
    public function line_items(){
        return $this->hasMany(FulfillmentLineItem::class,'order_fulfillment_id');
    }

    public function courier() {
        return $this->belongsTo(Courier::class);
    }

}
