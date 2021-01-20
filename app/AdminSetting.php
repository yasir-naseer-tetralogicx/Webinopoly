<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
   protected $fillable = [
       'payment_charge_percentage','paypal_percentage'
   ];
}
