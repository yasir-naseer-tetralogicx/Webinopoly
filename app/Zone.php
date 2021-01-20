<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
   protected $fillable =[
       'name'
   ];
   public function has_countries(){
       return $this->belongsToMany('App\Country','country_zone','zone_id','country_id');
   }
   public function has_rate(){
       return $this->hasMany('App\ShippingRate','zone_id');
   }
   public function courier(){
       return $this->hasOne(Courier::class, 'zone_id');
   }
}
