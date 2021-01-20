<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function generate($type,$subtype,$message,$object){
        $notification = new Notification();
        $notification->type = $type;
        $notification->sub_type = $subtype;
        $notification->type_id = $object->id;
        $notification->message = $message;
        $notification->save();

        if($type == 'Product'){
           $shops = $object->has_retailer_products->pluck('shop_id')->toArray();
           $notification->to_shops()->attach($shops);
        }

        if(in_array($type,['Refund','Ticket','Order','Wish-list'])){
            $shop = $object->shop_id;
            $notification->to_shops()->attach($shop);
            $user = $object->user_id;
            $notification->to_users()->attach($user);
        }
        if(in_array($type,['Wallet'])){
            $user = $object->user_id;
            $notification->to_users()->attach($user);
        }
    }



}
