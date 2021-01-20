<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    public function has_market(){
       return $this->belongsToMany(Country::class,'wishlist_countries','wishlist_id','country_id');
    }
    public function has_status()
    {
        return $this->belongsTo(WishlistStatus::class,'status_id');
    }
    public function has_attachments()
    {
        return $this->hasMany(WishlistAttachment::class,'wishlist_id');
    }
    public function has_thread()
    {
        return $this->hasMany(WishlistThread::class,'wishlist_id');
    }
    public function has_store()
    {
        return $this->belongsTo(Shop::class,'shop_id');
    }
    public function has_manager()
    {
        return $this->belongsTo(User::class,'manager_id');
    }
    public function has_user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function has_product()
    {
        return $this->belongsTo(Product::class,'related_product_id');
    }

    public function has_retailer_product()
    {
        return $this->belongsTo(RetailerProduct::class,'related_product_id');
    }
}
