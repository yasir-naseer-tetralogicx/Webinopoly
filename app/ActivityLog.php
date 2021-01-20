<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'model_id');
    }

    public function wishlist() {
        return $this->belongsTo(Wishlist::class, 'model_id');
    }

    public function wallet() {
        return $this->belongsTo(Wallet::class, 'model_id');
    }
}
