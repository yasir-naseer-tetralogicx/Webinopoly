<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public function hasCategory(){
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }
    public function has_image(){
        return $this->belongsTo('App\Image', 'image');
    }
    public function linked_product(){
        return $this->belongsTo('App\Product', 'product_id');
    }
    public function has_tiered_prices(){
        return $this->hasMany(TieredPrice::class);
    }
}
