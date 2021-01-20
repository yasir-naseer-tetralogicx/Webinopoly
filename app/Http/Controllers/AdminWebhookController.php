<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use App\FulfillmentLineItem;
use App\OrderFulfillment;
use App\OrderLog;
use App\RetailerOrder;
use App\RetailerOrderLineItem;
use App\User;
use App\WalletLog;
use App\Zone;
use Illuminate\Http\Request;

class AdminWebhookController extends Controller
{
    private $helper;
    private $notify;

    /**
     * AdminWebhookController constructor.
     * @param $helper
     * @param $notify
     */
    public function __construct()
    {
        $this->helper = new HelperController();
        $this->notify = new NotificationController();
    }


    public function set_fulfillments($data){
        $retailer_order = RetailerOrder::where('admin_shopify_id', $data->order_id)->first();
        if ($retailer_order != null && $retailer_order->paid == 1) {
            if ($retailer_order->custom == 1) {

                /*Order Fullfillment Record*/
                $new_fulfillment = new OrderFulfillment();
                $count = count($retailer_order->fulfillments) + 1;
                $new_fulfillment->name = $retailer_order->name . '.F' . $count;
                $new_fulfillment->retailer_order_id = $retailer_order->id;
                $new_fulfillment->status = 'fulfilled';
                $new_fulfillment->save();

                $this->after_fullfiment_process($new_fulfillment, $retailer_order, $data);
            }
            else {

                $shop = $this->helper->getSpecificShop($retailer_order->shop_id);
                $shopify_fulfillment = null;
                if ($shop != null) {
                    $location_response = $shop->api()->rest('GET', '/admin/locations.json');
                    if (!$location_response->errors) {

                        foreach ($location_response->body->locations as $location){
                            if($location->name == "WeFullFill"){
                                $fulfill_data = [
                                    "fulfillment" => [
                                        "location_id" => $location->id,
                                        "tracking_number" => null,
                                        "line_items" => [

                                        ]
                                    ]
                                ];
                            }
                        }

                        if (count($data->tracking_numbers) > 0) {
                            $fulfill_data['fulfillment']['tracking_number'] = $data->tracking_numbers[0];
                        }

                        if($retailer_order->shipping_address)
                        {
                            $shipping = json_decode($retailer_order->shipping_address);
                            $country = $shipping->country;

                            $zoneQuery = Zone::query();
                            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                                $q->where('name','LIKE','%'.$country.'%');
                            });
                            $zoneQuery = $zoneQuery->first();
                            if($zoneQuery->courier != null && $zoneQuery->courier->url != null) {
                                $fulfill_data['fulfillment']['tracking_url'] = $zoneQuery->courier->url;
                                $fulfill_data['fulfillment']['tracking_company'] = $zoneQuery->courier->title;
                            }
                            else if (count($data->tracking_urls) > 0) {
                                $fulfill_data['fulfillment']['tracking_url'] = $data->tracking_urls[0];
                            }
                        }
                        else if (count($data->tracking_urls) > 0) {
                            $fulfill_data['fulfillment']['tracking_url'] = $data->tracking_urls[0];
                        }

                        foreach ($data->line_items as $line_item) {
                            $item = RetailerOrderLineItem::where('sku', $line_item->sku)->where('retailer_order_id',$retailer_order->id)->first();
                            if ($item != null) {
                                $fulfill_quantity =$item->fulfillable_quantity -  $line_item->fulfillable_quantity;

                                array_push($fulfill_data['fulfillment']['line_items'], [
                                    "id" => $item->retailer_product_variant_id,
                                    "quantity" => $fulfill_quantity,
                                ]);
                            }
                        }
                        $response = $shop->api()->rest('POST','/admin/orders/'.$retailer_order->shopify_order_id.'/fulfillments.json',$fulfill_data);
                        if(!$response->errors){

                            /*Order Fullfillment Record*/
                            $new_fulfillment = new OrderFulfillment();
                            $new_fulfillment->fulfillment_shopify_id = $response->body->fulfillment->id;
                            $new_fulfillment->name = $response->body->fulfillment->name;
                            $new_fulfillment->retailer_order_id = $retailer_order->id;
                            $new_fulfillment->status = 'fulfilled';
                            $new_fulfillment->save();
                            /*Order Log*/

                            $shop->api()->rest('POST', '/admin/orders/' . $retailer_order->shopify_order_id . '/fulfillments/' . $response->body->fulfillment->id . '/complete.json');

                            $this->after_fullfiment_process($new_fulfillment, $retailer_order, $data);
                        }
                        else {

                            $log = new ErrorLog();
                            $log->message = "Fulfillment Error Outer: " . json_encode($response->body);
                            $log->save();

                            $response = $shop->api()->rest('GET','/admin/orders/'.$retailer_order->shopify_order_id.'/fulfillments.json',$fulfill_data);
                            if(!$response->errors){

                                /*Order Fullfillment Record*/
                                $new_fulfillment = new OrderFulfillment();
                                $new_fulfillment->fulfillment_shopify_id = $response->body->fulfillments[0]->id;
                                $new_fulfillment->name = $response->body->fulfillments[0]->name;
                                $new_fulfillment->retailer_order_id = $retailer_order->id;
                                $new_fulfillment->status = 'fulfilled';
                                $new_fulfillment->save();
                                /*Order Log*/

                                $shop->api()->rest('POST', '/admin/orders/' . $retailer_order->shopify_order_id . '/fulfillments/' . $response->body->fulfillments[0]->id . '/complete.json');

                                $this->after_fullfiment_process($new_fulfillment, $retailer_order, $data);
                            }else {

                                $log = new ErrorLog();
                                $log->message = "Fulfillment Error Inner: " . json_encode($response->body);
                                $log->save();
                            }

                        }
                    }
                }
            }
        }
    }

    /**
     * @param  $data
     * @param $retailer_order
     * @return array
     */
    public function set_line_item_fullfill_status($data, $retailer_order): array
    {
        foreach ($data->line_items as $item) {
            $line_item = RetailerOrderLineItem::where('sku', $item->sku)->where('retailer_order_id', $retailer_order->id)->first();
            if ($line_item != null) {
                if ($item->fulfillable_quantity == 0) {
                    $line_item->fulfillment_status = 'fulfilled';
                    $line_item->fulfillable_quantity = 0;
                    $line_item->save();
                } else {
                    $line_item->fulfillment_status = 'partially-fulfilled';
                    $line_item->fulfillable_quantity = $item->fulfillable_quantityset_fulfillments;
                    $line_item->save();
                }
            }
        }
        $retailer_order->status = $retailer_order->getStatus($retailer_order);
        $retailer_order->save();
        return array($item, $line_item);
    }

    /**
     * @param OrderFulfillment $new_fulfillment
     * @param $retailer_order
     * @param $data
     */
    public function after_fullfiment_process(OrderFulfillment $new_fulfillment, $retailer_order, $data): void
    {

        /*Order Log*/
        $order_log = new OrderLog();
        $order_log->message = "A fulfillment named " . $new_fulfillment->name . " has been processed successfully on " . date_create($new_fulfillment->created_at)->format('d M, Y h:i a');
        $order_log->status = "Fulfillment";
        $order_log->retailer_order_id = $retailer_order->id;
        $order_log->save();


        /*Fulfillment Line Item Relationship*/
        foreach ($data->line_items as $item) {
            $line_item = RetailerOrderLineItem::where('sku', $item->sku)->where('retailer_order_id', $retailer_order->id)->first();
            if ($line_item != null) {
                $fulfillment_line_item = new FulfillmentLineItem();
                $fulfillment_line_item->fulfilled_quantity = $line_item->fulfillable_quantity - $item->fulfillable_quantity;
                $fulfillment_line_item->order_fulfillment_id = $new_fulfillment->id;
                $fulfillment_line_item->order_line_item_id = $line_item->id;
                $fulfillment_line_item->save();
            }
        }
        /*Each Line Item Fulfillment Status*/
        list($item, $line_item) = $this->set_line_item_fullfill_status($data, $retailer_order);


        /*Notification*/
        $this->notify->generate('Order', 'Order Fulfillment', $retailer_order->name . ' line items fulfilled', $retailer_order);

        /*If Fulfillment has Tracking Information*/
        if (count($data->tracking_numbers) > 0) {
            $new_fulfillment->tracking_number = $data->tracking_numbers[0];
        }

        if($retailer_order->shipping_address)
        {
            $shipping = json_decode($retailer_order->shipping_address);
            $country = $shipping->country;

            $zoneQuery = Zone::query();
            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                $q->where('name','LIKE','%'.$country.'%');
            });
            $zoneQuery = $zoneQuery->first();
            if($zoneQuery->courier != null && $zoneQuery->courier->url != null) {
                $new_fulfillment->tracking_url = $zoneQuery->courier->url;
                $new_fulfillment->courier_id = $zoneQuery->courier->id;
            }
            else if (count($data->tracking_urls) > 0) {
                $new_fulfillment->tracking_url = $data->tracking_urls[0];
            }
        }
        else if (count($data->tracking_urls) > 0) {
            $new_fulfillment->tracking_url = $data->tracking_urls[0];
        }



        $new_fulfillment->admin_fulfillment_shopify_id = $data->id;
        $new_fulfillment->save();
        if (count($data->tracking_numbers) > 0) {
            $count = 0;
            $fulfillment_count = count($retailer_order->fulfillments);
            foreach ($retailer_order->fulfillments as $f) {
                if ($f->tracking_number != null) {
                    $count++;
                }
            }
            if($retailer_order->status == 'fulfilled'){
                if ($count == $fulfillment_count) {
                    $retailer_order->status = 'shipped';
                } else {
                    $retailer_order->status = 'partially-shipped';
                }
            }

            $retailer_order->save();
            $this->notify->generate('Order', 'Order Tracking Details', $retailer_order->name . ' tracking details added successfully!', $retailer_order);
        }
    }

    public function unset_fulfillments($data){
        $retailer_order = RetailerOrder::where('admin_shopify_id', $data->order_id)->first();
        $fulfillment = OrderFulfillment::where('retailer_order_id',$retailer_order->id)->where('admin_fulfillment_shopify_id',$data->id)->first();

        if ($retailer_order != null && $retailer_order->paid == 1 && $fulfillment != null) {
            if ($retailer_order->custom == 1) {
                $this->cancellation_fulfillment_process($fulfillment,$retailer_order);
            }
            else{
                $shop = $this->helper->getSpecificShop($retailer_order->shop_id);
                if($shop != null) {
                    $response = $shop->api()->rest('POST', '/admin/orders/' . $retailer_order->shopify_order_id . '/fulfillments/' . $fulfillment->fulfillment_shopify_id . '/cancel.json');
                    if (!$response->errors) {
                        $this->cancellation_fulfillment_process($fulfillment, $retailer_order);
                    }
                }

            }
        }
    }

    /**
     * @param $fulfillment
     * @param $order
     */
    public function cancellation_fulfillment_process($fulfillment, $order): void
    {
        foreach ($fulfillment->line_items as $item) {
            if ($item->linked_line_item != null) {
                $item->linked_line_item->fulfillable_quantity = $item->linked_line_item->fulfillable_quantity + $item->fulfilled_quantity;
                $item->linked_line_item->save();
                if ($item->linked_line_item->fulfillable_quantity < $item->linked_line_item->quantity) {
                    $item->linked_line_item->fulfillment_status = "partially-fulfilled";
                } else if ($item->linked_line_item->fulfillable_quantity == $item->linked_line_item->quantity) {
                    $item->linked_line_item->fulfillment_status = null;
                }
                $item->linked_line_item->save();
            }
            $item->delete();
        }
        $order_log = new OrderLog();
        $order_log->message = "A fulfillment named " . $fulfillment->name . " has been cancelled successfully on " . now()->format('d M, Y h:i a');

        $this->notify->generate('Order','Order Fulfillment Cancellation',$order->name.' line items fulfillment cancelled',$order);

        $fulfillment->delete();
        $order->status = $order->getStatus($order);
        $order->save();

        /*Maintaining Log*/

        $order_log->status = "Fulfillment Cancelled";
        $order_log->retailer_order_id = $order->id;
        $order_log->save();

    }

    public function set_tracking_details($data)
    {
        $retailer_order = RetailerOrder::where('admin_shopify_id', $data->order_id)->first();
        $fulfillment = OrderFulfillment::where('retailer_order_id', $retailer_order->id)->where('admin_fulfillment_shopify_id', $data->id)->first();
        if ($retailer_order != null && $retailer_order->paid == 1 && $fulfillment != null) {
            if ($retailer_order->custom == 1) {
                $this->tracking_process($data, $fulfillment, $retailer_order);
            }
            else{
                $shop = $this->helper->getSpecificShop($retailer_order->shop_id);
                if ($shop != null) {
                    if ($fulfillment != null) {
                        $tracking = [
                            "fulfillment" => [
                                "tracking_number" => null,
                                "tracking_url" => null,
                                "notify_customer" => false
                            ]
                        ];
                        if (count($data->tracking_numbers) > 0) {
                            $tracking['fulfillment']['tracking_number'] = $data->tracking_numbers[0];
                        }

                        if($retailer_order->shipping_address)
                        {
                            $shipping = json_decode($retailer_order->shipping_address);
                            $country = $shipping->country;

                            $zoneQuery = Zone::query();
                            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                                $q->where('name','LIKE','%'.$country.'%');
                            });
                            $zoneQuery = $zoneQuery->first();
                            if($zoneQuery->courier != null && $zoneQuery->courier->url != null) {
                                $fulfill_data['fulfillment']['tracking_url'] = $zoneQuery->courier->url;
                                $fulfill_data['fulfillment']['tracking_company'] = $zoneQuery->courier->title;
                            }
                            else if (count($data->tracking_urls) > 0) {
                                $fulfill_data['fulfillment']['tracking_url'] = $data->tracking_urls[0];
                            }
                        }
                        else if (count($data->tracking_urls) > 0) {
                            $tracking['fulfillment']['tracking_url'] = $data->tracking_urls[0];
                        }
                        $response = $shop->api()->rest('PUT', '/admin/orders/' . $retailer_order->shopify_order_id . '/fulfillments/' . $fulfillment->fulfillment_shopify_id . '.json', $tracking);

                        if (!$response->errors) {
                            $this->tracking_process($data, $fulfillment, $retailer_order);
                        }

                    }
                }
            }
        }
    }

    /**
     * @param $data
     * @param $fulfillment
     * @param $retailer_order
     */
    public function tracking_process($data, $fulfillment, $retailer_order): void
    {
        if (count($data->tracking_numbers) > 0) {
            $fulfillment->tracking_number = $data->tracking_numbers[0];
        }
        if($retailer_order->shipping_address)
        {
            $shipping = json_decode($retailer_order->shipping_address);
            $country = $shipping->country;

            $zoneQuery = Zone::query();
            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                $q->where('name','LIKE','%'.$country.'%');
            });
            $zoneQuery = $zoneQuery->first();
            if($zoneQuery->courier != null && $zoneQuery->courier->url != null) {
                $fulfillment->tracking_url = $zoneQuery->courier->url;
                $fulfillment->courier_id = $zoneQuery->courier->id;
            }
            else if (count($data->tracking_urls) > 0) {
                $fulfillment->tracking_url = $data->tracking_urls[0];
            }
        }
        else if (count($data->tracking_urls) > 0) {
            $fulfillment->tracking_url = $data->tracking_urls[0];
        }
        $fulfillment->save();

        /*Maintaining Log*/
        $order_log = new OrderLog();
        $order_log->message = "Tracking detailed Updated To Fulfillment named " . $fulfillment->name . "  successfully on " . now()->format('d M, Y h:i a');
        $order_log->status = "Tracking Details Updated";
        $order_log->retailer_order_id = $retailer_order->id;
        $order_log->save();

        if (count($data->tracking_numbers) > 0) {
            $count = 0;
            $fulfillment_count = count($retailer_order->fulfillments);
            foreach ($retailer_order->fulfillments as $f) {
                if ($f->tracking_number != null) {
                    $count++;
                }
            }
            if($retailer_order->status == 'fulfilled'){
                if ($count == $fulfillment_count) {
                    $retailer_order->status = 'shipped';
                } else {
                    $retailer_order->status = 'partially-shipped';
                }
            }

            $retailer_order->save();
            $this->notify->generate('Order', 'Order Tracking Details', $retailer_order->name . ' tracking details updated successfully!', $retailer_order);
        }

    }

    public function cancellation_refund($data){
        $order = RetailerOrder::where('admin_shopify_id',$data->id)->first();
        if($order != null){
            /*Add to Cost to Wallet*/
            $walletController = new WalletController();
            if ($order->has_user != null) {
                $user = User::find($order->has_user->id);
                if ($user->has_wallet == null) {
                    $wallet = $walletController->wallet_create($order->has_user->id);
                } else {
                    $wallet = $user->has_wallet;
                }
            } else {
                $shop = $order->has_store;
                if (count($shop->has_user) > 0) {
                    if ($shop->has_user[0]->has_wallet == null) {
                        $wallet = $walletController->wallet_create($shop->has_user[0]->id);
                    } else {
                        $wallet = $shop->has_user[0]->has_wallet;
                    }
                }
                else{
                    $wallet = null;
                }
            }

            if($wallet != null){
                $wallet->available = $wallet->available + (int)$order->cost_to_pay;
                $wallet->save();
                /*Wallet Log*/
                $wallet_log = new WalletLog();
                $wallet_log->wallet_id = $wallet->id;
                $wallet_log->status = "Top-up through Refund";
                $wallet_log->amount = $order->cost_to_pay;
                $wallet_log->message = 'A Top-up of Amount '.number_format($order->cost_to_pay,2).' USD On Behalf on Refund '.$order->name.' Against Wallet ' . $wallet->wallet_token . ' At ' . now()->format('d M, Y h:i a');
                $wallet_log->save();

                /*Refund Order*/
                $order->status = 'cancelled';
                $order->paid = 2;
                $order->save();
                /*Order Log*/
                $order_log =  new OrderLog();
                $order_log->message = "An amount of ".$order->cost_to_pay." USD refunded to Wallet on ".now()->format('d M, Y h:i a');
                $order_log->status = "refunded";
                $order_log->retailer_order_id = $order->id;
                $order_log->save();
                $this->notify->generate('Order','Order Cancelled and Refund',$order->name.' has been cancelled and refunded',$order);
            }
            else{
                $order->status = 'cancelled';
                $order->save();
                $this->notify->generate('Order','Order Cancelled',$order->name.' has been cancelled',$order);

            }
        }
    }

    public function CompleteFullFillment($orderFullfillment)
    {
        $order = RetailerOrder::where('id', $orderFullfillment->retailer_order_id)->first();
        if ($orderFullfillment->fulfillment_shopify_id) {
            $shop = $this->helper->getSpecificShop($order->shop_id);
            $shop->api()->rest('POST', '/admin/orders/' . $order->shopify_order_id . '/fulfillments/' . $orderFullfillment->fulfillment_shopify_id . '/complete.json');
        }
        if ($orderFullfillment->admin_fulfillment_shopify_id && $order->admin_shopify_id) {
            $admin_shop = $this->helper->getAdminShop();
            $admin_shop->api()->rest('POST', '/admin/orders/' . $order->admin_shopify_id . '/fulfillments/' . $orderFullfillment->admin_fulfillment_shopify_id . '/complete.json');
        }
    }
}
