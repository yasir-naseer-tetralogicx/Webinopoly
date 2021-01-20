<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function to_shops(){
        return $this->belongsToMany('App\Shop','notification_shop','notification_id','shop_id');
    }
    public function to_users(){
        return $this->belongsToMany('App\User','notification_user','notification_id','user_id');
    }
}
