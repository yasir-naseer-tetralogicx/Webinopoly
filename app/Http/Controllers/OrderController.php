<?php

namespace App\Http\Controllers;

use App\AdminSetting;
use App\Customer;
use App\ErrorLog;
use App\FulfillmentLineItem;
use App\Mail\OrderPlaceEmail;
use App\Mail\WalletBalanceMail;
use App\OrderFulfillment;
use App\OrderLog;
use App\OrderTransaction;
use App\RetailerOrder;
use App\RetailerOrderLineItem;
use App\RetailerProduct;
use App\RetailerProductVariant;
use App\ShippingRate;
use App\User;
use App\WalletLog;
use App\WalletSetting;
use App\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use function Psy\sh;

class OrderController extends Controller
{
    private $helper;
    private $admin;
    private $inventory;
    private $log;


    /**
     * OrderController constructor.
     * @param $helper
     */
    public function __construct()
    {
        $this->helper = new HelperController();
        $this->admin = new AdminMaintainerController();
        $this->inventory = new InventoryController();
        $this->log = new ActivityLogController();
    }

    public function index(Request $request)
    {
//        $shop = $current_shop = \OhMyBrew\ShopifyApp\Facades\ShopifyApp::shop();
//        $response = $shop->api()->rest('GET', '/admin/webhooks.json');
//        dd($response);

        $orders = RetailerOrder::where('shop_id', $this->helper->getShop()->id)->where('custom', 0)->newQuery();
        if ($request->has('search')) {
            $orders->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }
        $orders = $orders->orderBy('name', 'DESC')->paginate(30);
        return view('single-store.orders.index')->with([
            'orders' => $orders,
            'search' => $request->input('search')
        ]);
    }


    public function view_order($id)
    {
        $shop = $this->helper->getLocalShop();
        $order = RetailerOrder::find($id);
        $settings = AdminSetting::all()->first();
        if ($order != null) {
            return view('single-store.orders.view')->with([
                'order' => $order,
                'settings' => $settings,
                'shop' => $shop
            ]);
        }
    }

    /*Updated Inventory*/
    public function proceed_payment(Request $request)
    {
        $order = RetailerOrder::find($request->input('order_id'));
        $settings = AdminSetting::all()->first();
        if ($order != null && $order->paid == 0) {
            $last_four = substr($request->input('card_number'), 0, 3);
            $new_transaction = new OrderTransaction();
            $new_transaction->note = $request->input('note');
            $new_transaction->amount = $order->cost_to_pay + ($order->cost_to_pay * $settings->payment_charge_percentage / 100);
            $new_transaction->name = $request->input('card_name');
            $new_transaction->card_last_four = $last_four;
            $new_transaction->retailer_order_id = $order->id;
            $new_transaction->user_id = $order->user_id;
            $new_transaction->shop_id = $order->shop_id;
            $new_transaction->save();

            $order->paid = 1;
            if (count($order->fulfillments) > 0) {
                $order->status = $order->getStatus($order);

            } else {
                $order->status = 'Paid';

            }

            $order->save();

            /*Order placing email*/
            $user = User::find($order->user_id);
            $manager_email = null;
            if($user->has_manager()->count() > 0) {
                $manager_email = $user->has_manager->email;
            }

            $users_temp =['info@wefullfill.com',$manager_email];
            $users = [];

            foreach($users_temp as $key => $ut){
                if($ut != null) {
                    $ua = [];

                    $ua['email'] = $ut;

                    $ua['name'] = 'test';

                    $users[$key] = (object)$ua;
                }
            }

            try{
                Mail::to($users)->send(new OrderPlaceEmail($user->email, $order));
            }
            catch (\Exception $e){

            }

            /*Maintaining Log*/
            $order_log = new OrderLog();
            $order_log->message = "An amount of " . $new_transaction->amount . " USD paid to WeFullFill on " . date_create($new_transaction->created_at)->format('d M, Y h:i a') . " for further process";
            $order_log->status = "paid";
            $order_log->retailer_order_id = $order->id;
            $order_log->save();
            $this->admin->sync_order_to_admin_store($order);

//            $this->inventory->OrderQuantityUpdate($order,'new');
            $this->log->store($order->user_id, 'Order', $order->id, $order->name, 'Order Payment Paid');


            return redirect()->back()->with('success', 'Order Transaction Process Successfully And Will Managed By WeFullFill Administration!');
        } else {
            return redirect()->back();
        }
    }

    public function delete($id)
    {
        $r = RetailerOrder::find($id);
        foreach ($r->line_items as $i) {
            $i->delete();
        }
        foreach ($r->fulfillments as $f) {
            foreach ($f->line_items as $item) {
                $item->delete();
            }
            $f->delete();
        }
        $r->delete();
        return redirect()->back()->with('success', 'Order Deleted Successfully!');
    }

    public function syncAllOrders($id) {

        $user = User::find($id);
        $shops = $user->has_stores()->get();


        foreach ($shops as $s) {
            $shop = $this->helper->getSpecificShop($s->id);
            $response = $shop->api()->rest('GET', '/admin/api/2019-10/orders.json', ['status' => 'any']);


            if (!$response->errors) {
                $orders = $response->body->orders;
                foreach ($orders as $index => $order) {
                    $product_ids = [];
                    $variant_ids = [];
                    foreach ($order->line_items as $item) {
                        array_push($variant_ids, $item->variant_id);
                        array_push($product_ids, $item->product_id);
                    }
                    if (RetailerProduct::whereIn('shopify_id', $product_ids)->exists()) {
                        if (!RetailerOrder::where('shopify_order_id', $order->id)->exists()) {
                            $new = new RetailerOrder();
                            $new->shopify_order_id = $order->id;
                            $new->email = $order->email;
                            $new->phone = $order->phone;
                            $new->shopify_created_at = date_create($order->created_at)->format('Y-m-d h:i:s');
                            $new->shopify_updated_at = date_create($order->updated_at)->format('Y-m-d h:i:s');
                            $new->note = $order->note;
                            $new->name = $order->name;
                            $new->total_price = $order->total_price;
                            $new->subtotal_price = $order->subtotal_price;
                            $new->total_weight = $order->total_weight;
                            $new->taxes_included = $order->taxes_included;
                            $new->total_tax = $order->total_tax;
                            $new->currency = $order->currency;
                            $new->total_discounts = $order->total_discounts;
                            if (isset($order->customer)) {
                                if (Customer::where('customer_shopify_id', $order->customer->id)->exists()) {
                                    $customer = Customer::where('customer_shopify_id', $order->customer->id)->first();
                                    $new->customer_id = $customer->id;
                                } else {
                                    $customer = new Customer();
                                    $customer->customer_shopify_id = $order->customer->id;
                                    $customer->first_name = $order->customer->first_name;
                                    $customer->last_name = $order->customer->last_name;
                                    $customer->phone = $order->customer->phone;
                                    $customer->email = $order->customer->email;
                                    $customer->total_spent = $order->customer->total_spent;
                                    $customer->shop_id = $shop->id;
                                    $local_shop = $s;
                                    if ($local_shop->has_user->count() > 0) {
                                        $customer->user_id = $local_shop->has_user[0]->id;
                                    }
                                    $customer->save();
                                    $new->customer_id = $customer->id;
                                }
                                $new->customer = json_encode($order->customer, true);
                            }
                            if (isset($order->shipping_address)) {
                                $new->shipping_address = json_encode($order->shipping_address, true);
                            }
                            if (isset($order->billing_address)) {
                                $new->billing_address = json_encode($order->billing_address, true);
                            }

                            $new->status = 'new';
                            $new->shop_id = $shop->id;
                            $local_shop = $s;
                            if ($local_shop->has_user->count() > 0) {
                                $new->user_id = $local_shop->has_user[0]->id;
                            }
                            $new->fulfilled_by = 'fantasy';
                            $new->sync_status = 1;
                            $new->save();
                            $cost_to_pay = 0;

                            foreach ($order->line_items as $item) {
                                $new_line = new RetailerOrderLineItem();
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
                                $new_line->properties = json_encode($item->properties, true);
                                $new_line->fulfillable_quantity = $item->fulfillable_quantity;
                                $new_line->fulfillment_status = $item->fulfillment_status;

                                $retailer_product = RetailerProduct::where('shopify_id', $item->product_id)->first();
                                if ($retailer_product != null) {
                                    $new_line->fulfilled_by = $retailer_product->fulfilled_by;
                                } else {
                                    $new_line->fulfilled_by = 'store';
                                }

                                if ($retailer_product != null) {
                                    $related_variant = RetailerProductVariant::where('shopify_id', $item->variant_id)->first();
                                    if ($related_variant != null) {
                                        $new_line->cost = $related_variant->cost;
                                        $cost_to_pay = $cost_to_pay + $related_variant->cost * $item->quantity;
                                    } else {
                                        $new_line->cost = $retailer_product->cost;
                                        $cost_to_pay = $cost_to_pay + $retailer_product->cost * $item->quantity;
                                    }
                                }

                                $new_line->save();
                            }
                            $new->cost_to_pay = $cost_to_pay;
                            $new->save();

                            if (isset($order->shipping_address)) {
                                $total_weight = 0;
                                $country = $order->shipping_address->country;
                                foreach ($new->line_items as $v) {
                                    if ($v->linked_product != null) {
                                        $total_weight = $total_weight + ($v->linked_product->weight * $v->quantity);
                                    }
                                }

                                $zoneQuery = Zone::query();
                                $zoneQuery->whereHas('has_countries', function ($q) use ($country) {
                                    $q->where('name', 'LIKE', '%' . $country . '%');
                                });
                                $zoneQuery = $zoneQuery->pluck('id')->toArray();

                                $shipping_rates = ShippingRate::whereIn('zone_id', $zoneQuery)->newQuery();
                                $shipping_rates = $shipping_rates->first();
                                if ($shipping_rates != null) {
                                    if ($shipping_rates->type == 'flat') {
                                        $new->shipping_price = $shipping_rates->shipping_price;
                                        $new->total_price = $new->total_price + $shipping_rates->shipping_price;
                                        $new->cost_to_pay = $new->cost_to_pay + $shipping_rates->shipping_price;
                                        $new->save();
                                    } else {
                                        if ($shipping_rates->min > 0) {
                                            $ratio = $total_weight / $shipping_rates->min;
                                            $shipping_price = $shipping_rates->shipping_price * $ratio;
                                            $new->shipping_price = $shipping_price;
                                            $new->total_price = $new->total_price + $shipping_price;
                                            $new->cost_to_pay = $new->cost_to_pay + $shipping_price;
                                            $new->save();
                                        } else {
                                            $new->shipping_price = 0;
                                            $new->save();
                                        }
                                    }

                                } else {
                                    $new->shipping_price = 0;
                                    $new->save();
                                }
                            }

                            if (count($order->fulfillments) > 0) {
                                foreach ($order->fulfillments as $fulfillment) {
                                    if ($fulfillment->status != 'cancelled') {
                                        foreach ($fulfillment->line_items as $item) {
                                            $line_item = RetailerOrderLineItem::where('retailer_product_variant_id', $item->id)->first();
                                            if ($line_item != null) {
                                                if ($item->fulfillable_quantity == 0) {
                                                    $line_item->fulfillment_status = 'fulfilled';
                                                    $line_item->fulfillable_quantity = 0;
                                                    $line_item->save();
                                                } else {
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
                                        foreach ($fulfillment->line_items as $item) {
                                            $line_item = RetailerOrderLineItem::where('retailer_product_variant_id', $item->id)->first();
                                            if ($line_item != null) {
                                                $fulfillment_line_item = new FulfillmentLineItem();
                                                if ($item->fulfillable_quantity == 0) {
                                                    $fulfillment_line_item->fulfilled_quantity = $line_item->quantity;
                                                } else {
                                                    $fulfillment_line_item->fulfilled_quantity = $item->fulfillable_quantity;
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

                            /*Maintaining Log*/
                            $order_log = new OrderLog();
                            $order_log->message = "Order synced to WeFullFill on " . date_create($new->created_at)->format('d M, Y h:i a');
                            $order_log->status = "Newly Synced";
                            $order_log->retailer_order_id = $new->id;
                            $order_log->save();
                        }
                    }

                }
            }
        }

        return redirect()->back()->with('success', 'Orders Synced Successfully');

    }

    public function getOrders()
    {
        $shop = $this->helper->getShop();
        $response = $shop->api()->rest('GET', '/admin/api/2019-10/orders.json', ['status' => 'any']);

        if (!$response->errors) {
            $orders = $response->body->orders;
            foreach ($orders as $index => $order) {
                $product_ids = [];
                $variant_ids = [];
                foreach ($order->line_items as $item) {
                    array_push($variant_ids, $item->variant_id);
                    array_push($product_ids, $item->product_id);
                }
                if (RetailerProduct::whereIn('shopify_id', $product_ids)->exists()) {
                    if(!RetailerOrder::where('shopify_order_id', $order->id)->exists()) {

                        $new = new RetailerOrder();
                        $new->shopify_order_id = $order->id;
                        $new->email = $order->email;
                        $new->phone = $order->phone;
                        $new->shopify_created_at = date_create($order->created_at)->format('Y-m-d h:i:s');
                        $new->shopify_updated_at = date_create($order->updated_at)->format('Y-m-d h:i:s');
                        $new->note = $order->note;
                        $new->name = $order->name;
                        $new->total_price = $order->total_price;
                        $new->subtotal_price = $order->subtotal_price;
                        $new->total_weight = $order->total_weight;
                        $new->taxes_included = $order->taxes_included;
                        $new->total_tax = $order->total_tax;
                        $new->currency = $order->currency;
                        $new->total_discounts = $order->total_discounts;
                        if (isset($order->customer)) {
                            if (Customer::where('customer_shopify_id', $order->customer->id)->exists()) {
                                $customer = Customer::where('customer_shopify_id', $order->customer->id)->first();
                                $new->customer_id = $customer->id;
                            } else {
                                $customer = new Customer();
                                $customer->customer_shopify_id = $order->customer->id;
                                $customer->first_name = $order->customer->first_name;
                                $customer->last_name = $order->customer->last_name;
                                $customer->phone = $order->customer->phone;
                                $customer->email = $order->customer->email;
                                $customer->total_spent = $order->customer->total_spent;
                                $customer->shop_id = $shop->id;
                                $local_shop = $this->helper->getLocalShop();
                                if (count($local_shop->has_user) > 0) {
                                    $customer->user_id = $local_shop->has_user[0]->id;
                                }
                                $customer->save();
                                $new->customer_id = $customer->id;
                            }
                            $new->customer = json_encode($order->customer, true);
                        }
                        if (isset($order->shipping_address)) {
                            $new->shipping_address = json_encode($order->shipping_address, true);
                        }
                        if (isset($order->billing_address)) {
                            $new->billing_address = json_encode($order->billing_address, true);
                        }

                        $new->status = 'new';
                        $new->shop_id = $shop->id;
                        $local_shop = $this->helper->getLocalShop();
                        if (count($local_shop->has_user) > 0) {
                            $new->user_id = $local_shop->has_user[0]->id;
                        }
                        $new->fulfilled_by = 'fantasy';
                        $new->sync_status = 1;
                        $new->save();
                        $cost_to_pay = 0;

                        foreach ($order->line_items as $item) {
                            $new_line = new RetailerOrderLineItem();
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
                            $new_line->properties = json_encode($item->properties, true);
                            $new_line->fulfillable_quantity = $item->fulfillable_quantity;
                            $new_line->fulfillment_status = $item->fulfillment_status;

                            $retailer_product = RetailerProduct::where('shopify_id', $item->product_id)->first();
                            if ($retailer_product != null) {
                                $new_line->fulfilled_by = $retailer_product->fulfilled_by;
                            } else {
                                $new_line->fulfilled_by = 'store';
                            }

                            if ($retailer_product != null) {
                                $related_variant = RetailerProductVariant::where('shopify_id', $item->variant_id)->first();
                                if ($related_variant != null) {
                                    $new_line->cost = $related_variant->cost;
                                    $cost_to_pay = $cost_to_pay + $related_variant->cost * $item->quantity;
                                } else {
                                    $new_line->cost = $retailer_product->cost;
                                    $cost_to_pay = $cost_to_pay + $retailer_product->cost * $item->quantity;
                                }
                            }

                            $new_line->save();
                        }
                        $new->cost_to_pay = $cost_to_pay;
                        $new->save();

                        if (isset($order->shipping_address)) {
                            $total_weight = 0;
                            $country = $order->shipping_address->country;
                            foreach ($new->line_items as $v) {
                                if ($v->linked_product != null) {
                                    $total_weight = $total_weight + ($v->linked_product->weight * $v->quantity);
                                }
                            }

                            $zoneQuery = Zone::query();
                            $zoneQuery->whereHas('has_countries', function ($q) use ($country) {
                                $q->where('name', 'LIKE', '%' . $country . '%');
                            });
                            $zoneQuery = $zoneQuery->pluck('id')->toArray();

                            $shipping_rates = ShippingRate::whereIn('zone_id', $zoneQuery)->newQuery();
                            $shipping_rates = $shipping_rates->first();
                            if ($shipping_rates != null) {
                                if ($shipping_rates->type == 'flat') {
                                    $new->shipping_price = $shipping_rates->shipping_price;
                                    $new->total_price = $new->total_price + $shipping_rates->shipping_price;
                                    $new->cost_to_pay = $new->cost_to_pay + $shipping_rates->shipping_price;
                                    $new->save();
                                } else {
                                    if ($shipping_rates->min > 0) {
                                        $ratio = $total_weight / $shipping_rates->min;
                                        $shipping_price = $shipping_rates->shipping_price * $ratio;
                                        $new->shipping_price = $shipping_price;
                                        $new->total_price = $new->total_price + $shipping_price;
                                        $new->cost_to_pay = $new->cost_to_pay + $shipping_price;
                                        $new->save();
                                    } else {
                                        $new->shipping_price = 0;
                                        $new->save();
                                    }
                                }

                            } else {
                                $new->shipping_price = 0;
                                $new->save();
                            }
                        }

                        if (count($order->fulfillments) > 0) {
                            foreach ($order->fulfillments as $fulfillment) {
                                if ($fulfillment->status != 'cancelled') {
                                    foreach ($fulfillment->line_items as $item) {
                                        $line_item = RetailerOrderLineItem::where('retailer_product_variant_id', $item->id)->first();
                                        if ($line_item != null) {
                                            if ($item->fulfillable_quantity == 0) {
                                                $line_item->fulfillment_status = 'fulfilled';
                                                $line_item->fulfillable_quantity = 0;
                                                $line_item->save();
                                            } else {
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
                                    foreach ($fulfillment->line_items as $item) {
                                        $line_item = RetailerOrderLineItem::where('retailer_product_variant_id', $item->id)->first();
                                        if ($line_item != null) {
                                            $fulfillment_line_item = new FulfillmentLineItem();
                                            if ($item->fulfillable_quantity == 0) {
                                                $fulfillment_line_item->fulfilled_quantity = $line_item->quantity;
                                            } else {
                                                $fulfillment_line_item->fulfilled_quantity = $item->fulfillable_quantity;
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

                        /*Maintaining Log*/
                        $order_log = new OrderLog();
                        $order_log->message = "Order synced to WeFullFill on " . date_create($new->created_at)->format('d M, Y h:i a');
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
        return redirect()->route('store.orders')->with('success', 'Orders Synced Successfully');
    }

    public function show_bulk_payments(Request $request)
    {
        $orders_array = explode(',', $request->input('orders'));
        if (count($orders_array) > 0) {
            $orders = RetailerOrder::whereIn('id', $orders_array)->newQuery();

            $orders->whereHas('line_items', function ($q) {
                $q->where('fulfillable_quantity', '>', 0);
            });
            $orders = $orders->get();
            $shipping_price = $orders->sum('shipping_price');
            $cost_to_pay = $orders->sum('cost_to_pay');
            $settings = AdminSetting::all()->first();
            $shop = $this->helper->getLocalShop();




            return view('single-store.orders.bulk-payment')->with([
                'orders' => $orders,
                'shipping_price' => $shipping_price,
                'cost_to_pay' => $cost_to_pay,
                'settings' => $settings,
                'shop' => $shop
            ]);

        }
        else {
            return redirect()->back();
        }
    }

    public function proceed_bulk_payment(Request $request) {
        $orders = json_decode($request->order_ids);

        foreach ($orders as $o) {
            $order = RetailerOrder::find($o->id);

            $settings = AdminSetting::all()->first();
            if ($order != null && $order->paid == 0) {
                $last_four = substr($request->input('card_number'), 0, 3);
                $new_transaction = new OrderTransaction();
                $new_transaction->note = $request->input('note');
                $new_transaction->amount = $order->cost_to_pay + ($order->cost_to_pay * $settings->payment_charge_percentage / 100);
                $new_transaction->name = $request->input('card_name');
                $new_transaction->card_last_four = $last_four;
                $new_transaction->retailer_order_id = $order->id;
                $new_transaction->user_id = $order->user_id;
                $new_transaction->shop_id = $order->shop_id;
                $new_transaction->save();

                $order->paid = 1;
                $order->status = 'Paid';
                $order->save();

                /*Maintaining Log*/
                $order_log = new OrderLog();
                $order_log->message = "An amount of " . $new_transaction->amount . " USD paid to WeFullFill on " . date_create($new_transaction->created_at)->format('d M, Y h:i a') . " for further process";
                $order_log->status = "paid";
                $order_log->retailer_order_id = $order->id;
                $order_log->save();
                $this->admin->sync_order_to_admin_store($order);

            }

        }
        return redirect(route('store.orders'))->with('success', 'Order Transaction Process Successfully And Will Managed By WeFullFill Administration!');

    }

    public function manuallyGetOrders()
    {
        $shop = $this->helper->getSpecificShop(55);
        $response = $shop->api()->rest('GET', '/admin/api/2019-10/orders.json', ['status' => 'any']);
        if (!$response->errors) {
            $orders = $response->body->orders;
            foreach ($orders as $index => $order) {
                $order = $orders[0];

                $product_ids = [];
                $variant_ids = [];
                foreach ($order->line_items as $item) {
                    $item->variant_id = 36677116428443;
                    $item->product_id = 5776107307163;
                    array_push($variant_ids, $item->variant_id);
                    array_push($product_ids, $item->product_id);
                }



                if (RetailerProduct::whereIn('shopify_id', $product_ids)->exists()) {
                    if (!RetailerOrder::where('shopify_order_id', $order->id)->exists()) {
                        $new = new RetailerOrder();
                        $new->shopify_order_id = $order->id;
                        $new->email = $order->email;
                        $new->phone = $order->phone;
                        $new->shopify_created_at = date_create($order->created_at)->format('Y-m-d h:i:s');
                        $new->shopify_updated_at = date_create($order->updated_at)->format('Y-m-d h:i:s');
                        $new->note = $order->note;
                        $new->name = $order->name;
                        $new->total_price = $order->total_price;
                        $new->subtotal_price = $order->subtotal_price;
                        $new->total_weight = $order->total_weight;
                        $new->taxes_included = $order->taxes_included;
                        $new->total_tax = $order->total_tax;
                        $new->currency = $order->currency;
                        $new->total_discounts = $order->total_discounts;
                        if (isset($order->customer)) {
                            if (Customer::where('customer_shopify_id', $order->customer->id)->exists()) {
                                $customer = Customer::where('customer_shopify_id', $order->customer->id)->first();
                                $new->customer_id = $customer->id;
                            } else {
                                $customer = new Customer();
                                $customer->customer_shopify_id = $order->customer->id;
                                $customer->first_name = $order->customer->first_name;
                                $customer->last_name = $order->customer->last_name;
                                $customer->phone = $order->customer->phone;
                                $customer->email = $order->customer->email;
                                $customer->total_spent = $order->customer->total_spent;
                                $customer->shop_id = $shop->id;
                                $local_shop = $this->helper->getLocalShop();
                                if (count($local_shop->has_user) > 0) {
                                    $customer->user_id = $local_shop->has_user[0]->id;
                                }
                                $customer->save();
                                $new->customer_id = $customer->id;
                            }
                            $new->customer = json_encode($order->customer, true);
                        }
                        if (isset($order->shipping_address)) {
                            $new->shipping_address = json_encode($order->shipping_address, true);
                        }
                        if (isset($order->billing_address)) {
                            $new->billing_address = json_encode($order->billing_address, true);
                        }

                        $new->status = 'new';
                        $new->shop_id = $shop->id;
                        $local_shop = $this->helper->getLocalShop();
                        if (count($local_shop->has_user) > 0) {
                            $new->user_id = $local_shop->has_user[0]->id;
                        }
                        $new->fulfilled_by = 'fantasy';
                        $new->sync_status = 1;
                        $new->save();
                        $cost_to_pay = 0;

                        foreach ($order->line_items as $item) {
                            $new_line = new RetailerOrderLineItem();
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
                            $new_line->properties = json_encode($item->properties, true);
                            $new_line->fulfillable_quantity = $item->fulfillable_quantity;
                            $new_line->fulfillment_status = $item->fulfillment_status;

                            $retailer_product = RetailerProduct::where('shopify_id', $item->product_id)->first();
                            if ($retailer_product != null) {
                                $new_line->fulfilled_by = $retailer_product->fulfilled_by;
                            } else {
                                $new_line->fulfilled_by = 'store';
                            }

                            if ($retailer_product != null) {
                                $related_variant = RetailerProductVariant::where('shopify_id', $item->variant_id)->first();
                                if ($related_variant != null) {
                                    $new_line->cost = $related_variant->cost;
                                    $cost_to_pay = $cost_to_pay + $related_variant->cost * $item->quantity;
                                } else {
                                    $new_line->cost = $retailer_product->cost;
                                    $cost_to_pay = $cost_to_pay + $retailer_product->cost * $item->quantity;
                                }
                            }

                            $new_line->save();
                        }
                        $new->cost_to_pay = $cost_to_pay;
                        $new->save();

                        if (isset($order->shipping_address)) {
                            $total_weight = 0;
                            $country = $order->shipping_address->country;
                            foreach ($new->line_items as $v) {
                                if ($v->linked_product != null) {
                                    $total_weight = $total_weight + ($v->linked_product->weight * $v->quantity);
                                }
                            }

                            $zoneQuery = Zone::query();
                            $zoneQuery->whereHas('has_countries', function ($q) use ($country) {
                                $q->where('name', 'LIKE', '%' . $country . '%');
                            });
                            $zoneQuery = $zoneQuery->pluck('id')->toArray();

                            $shipping_rates = ShippingRate::whereIn('zone_id', $zoneQuery)->newQuery();
                            $shipping_rates = $shipping_rates->first();
                            if ($shipping_rates != null) {
                                if ($shipping_rates->type == 'flat') {
                                    $new->shipping_price = $shipping_rates->shipping_price;
                                    $new->total_price = $new->total_price + $shipping_rates->shipping_price;
                                    $new->cost_to_pay = $new->cost_to_pay + $shipping_rates->shipping_price;
                                    $new->save();
                                } else {
                                    if ($shipping_rates->min > 0) {
                                        $ratio = $total_weight / $shipping_rates->min;
                                        $shipping_price = $shipping_rates->shipping_price * $ratio;
                                        $new->shipping_price = $shipping_price;
                                        $new->total_price = $new->total_price + $shipping_price;
                                        $new->cost_to_pay = $new->cost_to_pay + $shipping_price;
                                        $new->save();
                                    } else {
                                        $new->shipping_price = 0;
                                        $new->save();
                                    }
                                }

                            } else {
                                $new->shipping_price = 0;
                                $new->save();
                            }
                        }

                        if (count($order->fulfillments) > 0) {
                            foreach ($order->fulfillments as $fulfillment) {
                                if ($fulfillment->status != 'cancelled') {
                                    foreach ($fulfillment->line_items as $item) {
                                        $line_item = RetailerOrderLineItem::where('retailer_product_variant_id', $item->id)->first();
                                        if ($line_item != null) {
                                            if ($item->fulfillable_quantity == 0) {
                                                $line_item->fulfillment_status = 'fulfilled';
                                                $line_item->fulfillable_quantity = 0;
                                                $line_item->save();
                                            } else {
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
                                    foreach ($fulfillment->line_items as $item) {
                                        $line_item = RetailerOrderLineItem::where('retailer_product_variant_id', $item->id)->first();
                                        if ($line_item != null) {
                                            $fulfillment_line_item = new FulfillmentLineItem();
                                            if ($item->fulfillable_quantity == 0) {
                                                $fulfillment_line_item->fulfilled_quantity = $line_item->quantity;
                                            } else {
                                                $fulfillment_line_item->fulfilled_quantity = $item->fulfillable_quantity;
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

                        /*Maintaining Log*/
                        $order_log = new OrderLog();
                        $order_log->message = "Order synced to WeFullFill on " . date_create($new->created_at)->format('d M, Y h:i a');
                        $order_log->status = "Newly Synced";
                        $order_log->retailer_order_id = $new->id;
                        $order_log->save();
                    }
                }
            }

            dd('done');
        }
        return redirect()->route('store.orders')->with('success', 'Orders Synced Successfully');
    }


}
