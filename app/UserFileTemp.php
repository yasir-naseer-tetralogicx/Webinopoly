<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFileTemp extends Model
{
   protected $fillable = [
       'order_number','quantity','sku','name','address1','address2','city','postcode','country','phone','email','file_id','user_id','province'
   ];
}
