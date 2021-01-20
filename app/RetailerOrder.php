<?php

namespace App;

use App\Traits\RetailerOrderTrait;
use Illuminate\Database\Eloquent\Model;

class RetailerOrder extends Model
{
    use RetailerOrderTrait;
    public function line_items(){
        return $this->hasMany('App\RetailerOrderLineItem','retailer_order_id');
    }

    public function has_payment(){
        return $this->hasOne('App\OrderTransaction','retailer_order_id');
    }

    public function has_store(){
        return $this->belongsTo('App\Shop','shop_id');
    }

    public function has_customer(){
        return $this->belongsTo('App\Customer','customer_id');
    }

    public function has_user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function fulfillments(){
        return $this->hasMany('App\OrderFulfillment','retailer_order_id');
    }
    public function logs(){
        return $this->hasMany('App\OrderLog','retailer_order_id');
    }
    public function imported(){
        return $this->hasOne('App\UserFileTemp','order_id');
    }

    public function getCourierNameAttribute() {
        if($this->shipping_address)
        {
            $shipping = json_decode($this->shipping_address);
            $country = $shipping->country;

            $zoneQuery = Zone::query();
            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                $q->where('name','LIKE','%'.$country.'%');
            });
            $zoneQuery = $zoneQuery->first();
            if($zoneQuery->courier == null)
                return '';

            return$zoneQuery->courier->title;
        }
        return '';
    }

    public function getCourierUrlAttribute() {
        if($this->shipping_address)
        {
            $shipping = json_decode($this->shipping_address);
            $country = $shipping->country;

            $zoneQuery = Zone::query();
            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                $q->where('name','LIKE','%'.$country.'%');
            });
            $zoneQuery = $zoneQuery->first();
            if($zoneQuery->courier == null)
                return '';

            return$zoneQuery->courier->url;
        }
        return '';
    }

    public function getCourierIdAttribute() {
        if($this->shipping_address)
        {
            $shipping = json_decode($this->shipping_address);
            $country = $shipping->country;

            $zoneQuery = Zone::query();
            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                $q->where('name','LIKE','%'.$country.'%');
            });
            $zoneQuery = $zoneQuery->first();
            if($zoneQuery->courier == null)
                return '';

            return$zoneQuery->courier->id;
        }
        return '';
    }
}
