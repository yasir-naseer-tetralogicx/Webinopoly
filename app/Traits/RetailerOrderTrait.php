<?php


namespace App\Traits;


trait RetailerOrderTrait
{
    public function getStatus($order){
        $quanity = $order->line_items->whereIn('fulfilled_by',['Fantasy','AliExpress'])->sum('quantity');
        $fulfillable_quanity = $order->line_items->whereIn('fulfilled_by',['Fantasy','AliExpress'])->sum('fulfillable_quantity');
        if($fulfillable_quanity == 0){
            return 'fulfilled';
        }
        else if($fulfillable_quanity == $quanity || $fulfillable_quanity < $quanity){
            return 'unfulfilled';
        }


    }

    public function checkStoreItem($order){
        return $order->line_items()->where('fulfilled_by','store')->count();
    }
    public function total_quantity($order){
        return $order->line_items->whereIn('fulfilled_by',['Fantasy','AliExpress'])->sum('quantity');
    }
    public function total_fulfillable($order){
        return $order->line_items->whereIn('fulfilled_by',['Fantasy','AliExpress'])->sum('fulfillable_quantity');
    }


}
