<?php namespace App\Jobs;

use App\Customer;
use App\ErrorLog;
use App\FulfillmentLineItem;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminMaintainerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WebhookController;
use App\Mail\OrderPlaceEmail;
use App\Mail\WalletBalanceMail;
use App\OrderFulfillment;
use App\OrderLog;
use App\OrderTransaction;
use App\ProductVariant;
use App\RetailerOrder;
use App\RetailerOrderLineItem;
use App\RetailerProduct;
use App\RetailerProductVariant;
use App\ShippingRate;
use App\Shop;
use App\User;
use App\WalletLog;
use App\WalletSetting;
use App\Zone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrdersCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;
    private $log;
    private $notify;
    private $admin;
    private $inventory;



    /**
     * Create a new job instance.
     *
     * @param string $shopDomain The shop's myshopify domain
     * @param object $data    The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
        $this->log = new ActivityLogController();
        $this->notify = new NotificationController();
        $this->admin = new AdminMaintainerController();
        $this->inventory = new InventoryController();


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    //    $webhook = new WebhookController();
    //    $webhook->createOrder($this->data, $this->shopDomain);

        $order =  $this->data;
        $shop = Shop::where('shopify_domain', $this->shopDomain)->first();
        $product_ids = [];
        $variant_ids  = [];
        foreach($order->line_items as $item){
            array_push($variant_ids,$item->variant_id);
            array_push($product_ids,$item->product_id);
        }
        if(RetailerProduct::whereIn('shopify_id',$product_ids)->exists()){
            if(!RetailerOrder::where('shopify_order_id',$order->id)->exists()){
                $new = new RetailerOrder();
                $new->shopify_order_id = $order->id;
                $new->email = $order->email;
                $new->phone = $order->phone;
                $new->shopify_created_at = date_create($order->created_at)->format('Y-m-d h:i:s');
                $new->shopify_updated_at =date_create($order->updated_at)->format('Y-m-d h:i:s');
                $new->note = $order->note;
                $new->name = $order->name;
                $new->total_price = $order->total_price;
                $new->subtotal_price = $order->subtotal_price;
                $new->total_weight = $order->total_weight;
                $new->taxes_included = $order->taxes_included;
                $new->total_tax = $order->total_tax;
                $new->currency = $order->currency;
                $new->total_discounts = $order->total_discounts;
                if(isset($order->customer)){
                    if (Customer::where('customer_shopify_id',$order->customer->id)->exists()){
                        $customer = Customer::where('customer_shopify_id',$order->customer->id)->first();
                        $new->customer_id = $customer->id;
                    }
                    else{
                        $customer = new Customer();
                        $customer->customer_shopify_id = $order->customer->id;
                        $customer->first_name = $order->customer->first_name;
                        $customer->last_name = $order->customer->last_name;
                        $customer->phone = $order->customer->phone;
                        $customer->email = $order->customer->email;
                        $customer->total_spent = $order->customer->total_spent;
                        $customer->shop_id = $shop->id;
                        $local_shop = $this->helper->getLocalShop();
                        if(count($local_shop->has_user) > 0){
                            $customer->user_id = $local_shop->has_user[0]->id;
                        }
                        $customer->save();
                        $new->customer_id = $customer->id;
                    }
                    $new->customer = json_encode($order->customer,true);
                }
                if(isset($order->shipping_address)){
                    $new->shipping_address = json_encode($order->shipping_address,true);
                }

                if(isset($order->billing_address)){
                    $new->billing_address = json_encode($order->billing_address,true);
                }

                $new->status = 'new';
                $new->shop_id = $shop->id;

                if(count($shop->has_user) > 0){
                    $new->user_id = $shop->has_user[0]->id;
                }

                $new->fulfilled_by = 'fantasy';
                $new->sync_status = 1;
                $new->save();

                $cost_to_pay = 0;

                foreach ($order->line_items as $item){

                    $new_line = RetailerOrderLineItem::where([
                        'retailer_order_id' => $new->id,
                        'shopify_variant_id' => $item->variant_id,
                        'shopify_product_id' => $item->product_id
                    ])->first();

                    if($new_line === null) {
                        $new_line = new RetailerOrderLineItem();
                    }

                    $new_line->retailer_order_id = $new->id;
                    $new_line->retailer_product_variant_id = $item->id;
                    $new_line->shopify_product_id = $item->product_id;
                    $new_line->shopify_variant_id = $item->variant_id;
                    $new_line->title = $item->title;
                    $new_line->quantity = $item->quantity;
                    $new_line->sku = $item->sku;
                    $new_line->variant_title = $item->variant_title;
                    $new_line->title = $item->title;
                    $new_line->vendor = $item->vendor;
                    $new_line->price = $item->price;
                    $new_line->requires_shipping = $item->requires_shipping;
                    $new_line->taxable = $item->taxable;
                    $new_line->name = $item->name;
                    $new_line->properties = json_encode($item->properties,true);
                    $new_line->fulfillable_quantity = $item->fulfillable_quantity;
                    $new_line->fulfillment_status = $item->fulfillment_status;

                    $retailer_product = RetailerProduct::where('shopify_id',$item->product_id)->first();
                    if($retailer_product != null){
                        $new_line->fulfilled_by = $retailer_product->fulfilled_by;
                    }
                    else{
                        $new_line->fulfilled_by = 'store';
                    }

                    if($retailer_product != null) {
                        $related_variant =  RetailerProductVariant::where('shopify_id',$item->variant_id)->first();
                        if($related_variant != null){
                            $new_line->cost = $related_variant->cost;
                            $cost_to_pay = $cost_to_pay + $related_variant->cost * $item->quantity;
                        }
                        else{
                            $new_line->cost = $retailer_product->cost;
                            $cost_to_pay = $cost_to_pay + $retailer_product->cost * $item->quantity;
                        }
                    }

                    $new_line->save();
                }

                $new->cost_to_pay = $cost_to_pay;
                $new->save();

                if(isset($order->shipping_address)){
                    $total_weight = 0;
                    $country = $order->shipping_address->country;
                    foreach ($new->line_items as $index => $v){
                        if($v->linked_product != null){
                            if($v->linked_product->linked_product != null) {
                                $total_weight = $total_weight + ( $v->linked_product->linked_product->weight *  $v->quantity);
                            }
                        }
                    }
                    $zoneQuery = Zone::query();
                    $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                        $q->where('name','LIKE','%'.$country.'%');
                    });
                    $zoneQuery = $zoneQuery->pluck('id')->toArray();

                    $shipping_rates = ShippingRate::whereIn('zone_id',$zoneQuery)->newQuery();
                    $shipping_rates =  $shipping_rates->first();
                    if($shipping_rates != null){

                        if($shipping_rates->type == 'flat'){
                            $new->shipping_price = $shipping_rates->shipping_price;
                            $new->total_price =  $new->total_price + $shipping_rates->shipping_price;
                            $new->cost_to_pay =  $new->cost_to_pay + $shipping_rates->shipping_price;
                            $new->save();
                        }
                        else{
                            if($shipping_rates->min > 0){
                                $ratio = $total_weight/$shipping_rates->min;
                                $shipping_price =  $shipping_rates->shipping_price*$ratio;
                                $new->shipping_price = $shipping_price;
                                $new->total_price =  $new->total_price + $shipping_price;
                                $new->cost_to_pay =  $new->cost_to_pay + $shipping_price;
                                $new->save();
                            }
                            else{
                                $new->shipping_price = 0;
                                $new->save();
                            }
                        }

                    }
                    else{
                        $new->shipping_price = 0;
                        $new->save();
                    }
                }


                if(count($order->fulfillments) > 0){
                    foreach ($order->fulfillments as $fulfillment){
                            if($fulfillment->status != 'cancelled'){
                                foreach ($fulfillment->line_items as $item){
                                    $line_item = RetailerOrderLineItem::where('retailer_product_variant_id',$item->id)->first();
                                    if($line_item != null){
                                        if($item->fulfillable_quantity == 0){
                                            $line_item->fulfillment_status = 'fulfilled';
                                            $line_item->fulfillable_quantity = 0;
                                            $line_item->save();
                                        }
                                        else{
                                            $line_item->fulfillment_status = 'partially-fulfilled';
                                            $line_item->fulfillable_quantity = $line_item->fulfillable_quantity - $item->fulfillable_quantity;
                                            $line_item->save();
                                        }
                                    }
                                }
                                $new_fulfillment = new OrderFulfillment();
                                $new_fulfillment->fulfillment_shopify_id = $fulfillment->id;
                                $new_fulfillment->name = $fulfillment->name;
                                $new_fulfillment->retailer_order_id = $new->id;
                                $new_fulfillment->status = 'fulfilled';
                                $new_fulfillment->save();

                                $order_log = new OrderLog();
                                $order_log->message = "A fulfillment named " . $new_fulfillment->name . " has been processed successfully on " . date_create($new_fulfillment->created_at)->format('d M, Y h:i a');
                                $order_log->status = "Fulfillment";
                                $order_log->retailer_order_id = $new->id;
                                $order_log->save();
                                foreach ($fulfillment->line_items as $item){
                                    $line_item = RetailerOrderLineItem::where('retailer_product_variant_id',$item->id)->first();
                                    if($line_item != null){
                                        $fulfillment_line_item = new FulfillmentLineItem();
                                        if($item->fulfillable_quantity == 0){
                                            $fulfillment_line_item->fulfilled_quantity =$line_item->quantity;
                                        }
                                        else{
                                            $fulfillment_line_item->fulfilled_quantity =$item->fulfillable_quantity;
                                        }
                                        $fulfillment_line_item->order_fulfillment_id = $new_fulfillment->id;
                                        $fulfillment_line_item->order_line_item_id = $line_item->id;
                                        $fulfillment_line_item->save();
                                    }
                                }

                            }
                        }
                }


                $new->status = $new->getStatus($new);
                $new->save();

                $this->log->store($new->user_id, 'Order', $new->id, $new->name, 'Order Created');

                /*Maintaining Log*/
                $order_log =  new OrderLog();
                $order_log->message = "Order synced to WeFullFill on ".date_create($new->created_at)->format('d M, Y h:i a');
                $order_log->status = "Newly Synced";
                $order_log->retailer_order_id = $new->id;
                $order_log->save();

                /* Auto Order Payment in case user has enabled settings for it (START)*/
                $settings = WalletSetting::where('user_id', $new->user_id)->first();

                DB::beginTransaction();
                try{
                    if($settings && $settings->enable) {

                        if($new->paid == 0){

                            $user = User::find($new->user_id);
                            if ($user && $user->has_wallet != null) {
                                $wallet = $user->has_wallet;
                            }

                            if($wallet && $wallet->available >= $new->cost_to_pay) {

                                /*Wallet Deduction*/
                                $wallet->available =   $wallet->available -  $new->cost_to_pay;
                                $wallet->used =  $wallet->used + $new->cost_to_pay;
                                $wallet->save();
                                /*Maintaining Wallet Log*/
                                $wallet_log = new WalletLog();
                                $wallet_log->wallet_id =$wallet->id;
                                $wallet_log->status = "Order Payment";
                                $wallet_log->amount = $new->cost_to_pay;
                                $wallet_log->message = 'An Amount '.number_format($new->cost_to_pay,2).' USD For Order Cost Against Wallet ' . $wallet->wallet_token . ' Deducted At ' . now()->format('d M, Y h:i a');
                                $wallet_log->save();

                                $this->notify->generate('Wallet','Wallet Order Payment','An Amount '.number_format($new->cost_to_pay,2).' USD For Order Cost Against Wallet ' . $wallet->wallet_token . ' Deducted At ' . now()->format('d M, Y h:i a'),$wallet);


                                /*Order placing email*/
                                $user = User::find($new->user_id);
                                $manager_email = null;
                                if($user->has_manager()->count() > 0) {
                                    $manager_email = $user->has_manager->email;
                                }
                                $users_temp =['info@wefullfill.com',$manager_email];

                                foreach($users_temp as $u){
                                    if($u != null) {
                                        try{
                                            Mail::to($u)->send(new OrderPlaceEmail($new));
                                        }
                                        catch (\Exception $e){
                                        }
                                    }
                                }



                                /*Order Processing*/
                                $new_transaction = new OrderTransaction();
                                $new_transaction->amount =  $new->cost_to_pay;
                                if($new->custom == 0){
                                    $new_transaction->name = $new->has_store->shopify_domain;
                                }
                                else{
                                    $new_transaction->name = $user->email;
                                }

                                $new_transaction->retailer_order_id = $new->id;
                                $new_transaction->user_id = $new->user_id;
                                $new_transaction->shop_id = $new->shop_id;
                                $new_transaction->save();


                                /*Changing Order Status*/
                                $new->paid = 1;
                                if(count($new->fulfillments) > 0){
                                    $new->status = $new->getStatus($new);
                                }
                                else{
                                    $new->status = 'Paid';
                                }
                                $new->pay_by = 'Wallet';
                                $new->save();

                                /*Maintaining Log*/
                                $order_log =  new OrderLog();
                                $order_log->message = "An amount of ".$new_transaction->amount." USD paid to WeFullFill through Wallet on ".date_create($new_transaction->created_at)->format('d M, Y h:i a')." for further process";
                                $order_log->status = "paid";
                                $order_log->retailer_order_id = $new->id;
                                $order_log->save();

                                $this->log->store($new->user_id, 'Order', $new->id, $new->name, 'Order Payment Paid');

                                $this->admin->sync_order_to_admin_store($new);

                                $this->inventory->OrderQuantityUpdate($new,'new');

                                try {
                                    $this->admin->push_to_mabang($new->id);
                                }
                                catch (\Exception $e) {
                                    $log = new ErrorLog();
                                    $log->message = "ERP order BUG from Auto Wallet Payment: ". $e->getMessage();
                                    $log->save();
                                }

                            }
                            else{
                                $this->notify->generate('Wallet','Auto Wallet Order Payment Failure','Your Wallet amount is not enough for making payment for '. $new->name .' kindly top-up your wallet',$wallet);

                                $user = User::find($new->user_id);
                                try{
                                    Mail::to($user->email)->send(new WalletBalanceMail($wallet));
                                }
                                catch (\Exception $e){
                                }
                            }
                        }
                    }
                    DB::commit();
                }
                catch(\Exception $e) {
                    DB::rollBack();
                    $log = new ErrorLog();
                    $log->message = "Payment issue: " .$e->getMessage();
                    $log->save();
                }
                /* Auto Order Payment in case user has enabled settings for it (END)*/


            }
        }
    }
}
