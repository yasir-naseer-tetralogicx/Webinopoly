<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
   public function has_user(){
       return $this->belongsToMany('App\User','user_shop','shop_id','user_id');
   }
    public function has_imported(){
        return $this->belongsToMany('App\Product','retailer_product_shop','shop_id','product_id');
    }
    public function has_orders(){
        return $this->hasMany('App\RetailerOrder','shop_id');
    }
    public function has_manager(){
        return $this->belongsTo('App\User','sale_manager_id');
    }
    public function has_tickets(){
        return $this->hasMany('App\Ticket','shop_id');
    }
    public function has_products(){
        return $this->hasMany(RetailerProduct::class,'shop_id');
    }
    public function has_customers(){
        return $this->hasMany(Customer::class,'shop_id');
    }
    public function has_payments(){
        return $this->hasMany( OrderTransaction::class,'shop_id');
    }
    public function has_questionnaire(){
        return $this->hasOne(Questionaire::class,'shop_id');
    }
}
