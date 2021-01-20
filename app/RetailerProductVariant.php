<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailerProductVariant extends Model
{
    public function has_image(){
        return $this->belongsTo('App\RetailerImage', 'image');
    }
    public function linked_product(){
        return $this->belongsTo(RetailerProduct::class, 'product_id');
    }
}
