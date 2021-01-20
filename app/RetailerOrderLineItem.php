<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetailerOrderLineItem extends Model
{
    public function linked_product(){
        return $this->hasOne( RetailerProduct::class,'shopify_id','shopify_product_id');
    }
    public function linked_variant(){
        return $this->hasOne(RetailerProductVariant::class,'shopify_id','shopify_variant_id');
    }

    public function linked_real_product(){
        return $this->hasOne( Product::class,'shopify_id','shopify_product_id');
    }
    public function linked_real_variant(){
        return $this->hasOne(ProductVariant::class,'shopify_id','shopify_variant_id');
    }
}
