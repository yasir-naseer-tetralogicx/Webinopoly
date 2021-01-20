<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FulfillmentLineItem extends Model
{
   public function linked_line_item(){
     return  $this->belongsTo(RetailerOrderLineItem::class,'order_line_item_id');
   }
}
