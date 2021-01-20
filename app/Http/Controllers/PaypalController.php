<?php

namespace App\Http\Controllers;

use App\AdminSetting;
use App\ErrorLog;
use App\Mail\OrderPlaceEmail;
use App\OrderLog;
use App\OrderTransaction;
use App\RetailerOrder;
use App\User;
use App\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\ExpressCheckout;

class PaypalController extends Controller
{
    private $helper;
    private $admin;
    private $inventory;
    private $log;


    /**
     * PaypalController constructor.
     * @param $helper
     */
    public function __construct()
    {
        $this->helper = new HelperController();
        $this->admin = new AdminMaintainerController();
        $this->inventory = new InventoryController();
        $this->log = new ActivityLogController();

    }

    public function paypal_order_payment(Request $request)
    {
     $retailer_order = RetailerOrder::find($request->id);
     $setting = AdminSetting::all()->first();
     if($retailer_order->paid == 0){
         $items = [];
         $order_total = $retailer_order->cost_to_pay ;

         /*adding order-lime-items for paying through paypal*/
         foreach ($retailer_order->line_items as $item){
             array_push($items,[
                 'name' => $item->title .' - '.$item->variant_title,
                 'price' => $item->cost,
                 'qty' =>$item->quantity
             ]);
         }
         if($retailer_order->shipping_price != null){
             array_push($items,[
                 'name' => 'Shipping Price',
                 'price' => $retailer_order->shipping_price,
                 'qty' =>1
             ]);
         }

         if($setting != null){
             if($setting->payment_charge_percentage != null){
                 $order_total = $order_total + (number_format($retailer_order->cost_to_pay*$setting->paypal_percentage/100,2));
                 array_push($items,[
                     'name' => 'WeFullFill Charges('.$setting->paypal_percentage.'%)',
                     'price' => number_format($retailer_order->cost_to_pay*$setting->paypal_percentage/100,2),
                     'qty' =>1
                 ]);
             }
         }
         $data = [];
         $data['items'] = $items;
         $data['invoice_id'] = 'WeFullFill-Invoice'.rand(1,1000);
         $data['invoice_description'] = "Order #".$retailer_order->name." Invoice";
         $data['return_url'] = route('store.order.paypal.pay.success',$retailer_order->id);
         $data['cancel_url'] = route('store.order.paypal.pay.cancel',$retailer_order->id);
         $data['total'] = $order_total;

         $provider = new ExpressCheckout;
         try {
             $response = $provider->setExpressCheckout($data);
             $retailer_order->paypal_token = $response['TOKEN'];
             $retailer_order->save();

             return redirect($response['paypal_link']);
         }
         catch (\Exception $e){
             return redirect()->back()->with('error','System Process Failure');
         }
     }
     else{
         return redirect()->back()->with('error','This order status is paid');
     }

    }


    public function paypal_payment_cancel(Request $request)
    {   $retailer_order = RetailerOrder::find($request->id);
      if($retailer_order != null){
          if($retailer_order->custom == 0){
              return redirect()->route('store.order.view',$retailer_order->id)->with('error','Paypal Transaction Process cancelled successfully');

          }
          else{
              return redirect()->route('users.order.view',$retailer_order->id)->with('error','Paypal Transaction Process cancelled successfully');

          }
      }
      else{
          return redirect()->route('store.orders')->with('error','Paypal Transaction Process cancelled successfully');
      }
    }
    /*Updated Inventory*/


    public function paypal_payment_success(Request $request)
    {
        $retailer_order = RetailerOrder::find($request->id);
        $response = $request->input('response');
        $response = json_decode(json_encode(json_decode($response)));
//        dd($request, $response, $response->payer);

        if ($response->status == 'COMPLETED')
        {
            $retailer_order->paypal_payer_id =$response->payer->payer_id;
            $new_transaction = new OrderTransaction();
            $new_transaction->amount =  $response->purchase_units[0]->amount->value;
            $new_transaction->name = $response->payer->name->given_name.' '.$response->payer->name->surname;
            $new_transaction->retailer_order_id = $retailer_order->id;
            $new_transaction->paypal_payment_id = $response->id;
            $new_transaction->user_id = $retailer_order->user_id;
            $new_transaction->shop_id = $retailer_order->shop_id;
            $new_transaction->save();
            $retailer_order->paid = 1;
            if(count($retailer_order->fulfillments) > 0){
                $retailer_order->status = $retailer_order->getStatus($retailer_order);

            }
            else{
                $retailer_order->status = 'Paid';
            }

            $retailer_order->pay_by = 'Paypal';
            $retailer_order->save();

            /*Order placing email*/
            $user = User::find($retailer_order->user_id);
            $manager_email = null;
            if($user->has_manager()->count() > 0) {
                $manager_email = $user->has_manager->email;
            }
            $users_temp =['info@wefullfill.com',$manager_email];


            foreach($users_temp as $u){
                if($u != null) {
                    try{
                        Mail::to($u)->send(new OrderPlaceEmail($retailer_order));
                    }
                    catch (\Exception $e){
                    }
                }
            }



            /*Maintaining Log*/
            $order_log =  new OrderLog();
            $order_log->message = "An amount of ".$new_transaction->amount." USD paid to WeFullFill through PAYPAL on ".date_create($new_transaction->created_at)->format('d M, Y h:i a')." for further process";
            $order_log->status = "paid";
            $order_log->retailer_order_id = $retailer_order->id;
            $order_log->save();
            $this->admin->sync_order_to_admin_store($retailer_order);
            $this->log->store($retailer_order->user_id, 'Order', $retailer_order->id, $retailer_order->name, 'Order Payment Paid');

            $this->inventory->OrderQuantityUpdate($retailer_order,'new');

            try {
                $this->admin->push_to_mabang($retailer_order->id);
            }
            catch (\Exception $e) {
                $log = new ErrorLog();
                $log->message = "ERP order BUG from Paypal Single: ". $e->getMessage();
                $log->save();
            }


            if($retailer_order->custom == 0){
                return redirect()->route('store.order.view',$retailer_order->id)->with('success','Order Transaction Process Successfully And Will Managed By WeFullFill Administration!');
            }
            else{
                return redirect()->route('users.order.view',$retailer_order->id)->with('success','Order Transaction Process Successfully And Will Managed By WeFullFill Administration!');
            }
        }
        else{
            return redirect()->route('store.orders')->with('error','Order Not Found!');
        }


//        $retailer_order = RetailerOrder::find($request->id);
//        $provider = new ExpressCheckout;
//        $response = $provider->getExpressCheckoutDetails($request->token);
//        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING']) && $retailer_order  != null && $retailer_order->paid == 0)
//        {
//            $retailer_order->paypal_payer_id =$request->PayerID;
//            $new_transaction = new OrderTransaction();
//            $new_transaction->amount =  $response['AMT'];
//            $new_transaction->name = $response['FIRSTNAME'].' '.$response['LASTNAME'];
//            $new_transaction->retailer_order_id = $retailer_order->id;
//            $new_transaction->paypal_payment_id = $request->PayerID;
//            $new_transaction->user_id = $retailer_order->user_id;
//            $new_transaction->shop_id = $retailer_order->shop_id;
//            $new_transaction->save();
//            $retailer_order->paid = 1;
//            if(count($retailer_order->fulfillments) > 0){
//                $retailer_order->status = $retailer_order->getStatus($retailer_order);
//
//            }
//            else{
//                $retailer_order->status = 'Paid';
//            }
//
//            $retailer_order->pay_by = 'Paypal';
//            $retailer_order->save();
//
//            /*Maintaining Log*/
//            $order_log =  new OrderLog();
//            $order_log->message = "An amount of ".$new_transaction->amount." USD paid to WeFullFill through PAYPAL on ".date_create($new_transaction->created_at)->format('d M, Y h:i a')." for further process";
//            $order_log->status = "paid";
//            $order_log->retailer_order_id = $retailer_order->id;
//            $order_log->save();
//            $this->admin->sync_order_to_admin_store($retailer_order);
////            $this->inventory->OrderQuantityUpdate($retailer_order,'new');
//
//            if($retailer_order->custom == 0){
//                return redirect()->route('store.order.view',$retailer_order->id)->with('success','Order Transaction Process Successfully And Will Managed By WeFullFill Administration!');
//            }
//            else{
//                return redirect()->route('users.order.view',$retailer_order->id)->with('success','Order Transaction Process Successfully And Will Managed By WeFullFill Administration!');
//
//            }
//        }
//        else{
//            return redirect()->route('store.orders')->with('error','Order Not Found!');
//        }

    }

    public function test($id){
        $order = RetailerOrder::find($id);
        $this->admin->sync_order_to_admin_store($order);
    }

    public function paypal_bulk_order_payment(Request $request) {

        $orders = json_decode($request->order_ids);
        $setting = AdminSetting::all()->first();

            $items = [];
            $order_total = 0;
            foreach ($orders as $order) {
                $retailer_order = RetailerOrder::find($order->id);


                $order_total = $order_total + $retailer_order->cost_to_pay;

                /*adding order-lime-items for paying through paypal*/
                foreach ($retailer_order->line_items as $item) {
                    array_push($items, [
                        'name' => $item->title . ' - ' . $item->variant_title,
                        'price' => $item->cost,
                        'qty' => $item->quantity
                    ]);
                }
                if ($retailer_order->shipping_price != null) {
                    array_push($items, [
                        'name' => $retailer_order->name . ' Shipping Price',
                        'price' => $retailer_order->shipping_price,
                        'qty' => 1
                    ]);
                }
                if ($setting != null) {
                    if ($setting->payment_charge_percentage != null) {
                        $order_total = $order_total + (number_format($retailer_order->cost_to_pay * $setting->paypal_percentage / 100, 2));
                        array_push($items, [
                            'name' => 'WeFullFill Charges(' . $setting->paypal_percentage . '%)',
                            'price' => number_format($retailer_order->cost_to_pay * $setting->paypal_percentage / 100, 2),
                            'qty' => 1
                        ]);
                    }
                }
            }


            $response = $request->input('response');
            $response = json_decode(json_encode(json_decode($response)));
            if ($response->status == 'COMPLETED') {
                foreach ($orders as $order) {
                    $retailer_order = RetailerOrder::find($order->id);

                    $retailer_order->paypal_token = $response->id;
                    $retailer_order->save();
                }
                $this->bulk_import_order_paypal_success($request->order_ids, $response);
                //$this->log->store($retailer_order->user_id, 'Order', $retailer_order->id, $retailer_order->name, 'Order Payment Paid');

                return redirect(route('store.orders'))->with('success', 'Bulk Payment Processed Successfully!');

            } else {
                return redirect(route('store.orders'))->with('error', 'Payment Failed');
            }

    }

    public function bulk_import_order_paypal_success($id, $response)
    {
        $orders = json_decode($id);

        foreach ($orders as $order) {
            $retailer_order = RetailerOrder::find($order->id);

            $retailer_order->paypal_payer_id = $response->payer->payer_id;
            $new_transaction = new OrderTransaction();
            $new_transaction->amount = $response->purchase_units[0]->amount->value;
            $new_transaction->name = $response->payer->name->given_name.' '.$response->payer->name->surname;
            $new_transaction->retailer_order_id = $retailer_order->id;
            $new_transaction->paypal_payment_id = $response->payer->payer_id;
            $new_transaction->user_id = $retailer_order->user_id;
            $new_transaction->shop_id = $retailer_order->shop_id;
            $new_transaction->save();

            $retailer_order->paid = 1;
            $retailer_order->status = 'Paid';
            $retailer_order->pay_by = 'Paypal';
            $retailer_order->save();

            /*Maintaining Log*/
            $order_log = new OrderLog();
            $order_log->message = "An amount of " . $new_transaction->amount . " USD used to WeFullFill through BULK PAYPAL PAYMENT of " . $response->purchase_units[0]->amount->value . " USD on " . date_create($new_transaction->created_at)->format('d M, Y h:i a') . " for further process";
            $order_log->status = "paid";
            $order_log->retailer_order_id = $retailer_order->id;
            $order_log->save();
            $this->inventory->OrderQuantityUpdate($retailer_order,'new');
            $this->admin->sync_order_to_admin_store($retailer_order);
            try {
                $this->admin->push_to_mabang($retailer_order->id);
            }
            catch (\Exception $e) {
                $log = new ErrorLog();
                $log->message = "ERP order BUG from Paypal Bulk: ". $e->getMessage();
                $log->save();
            }
        }


        return redirect(route('store.orders'))->with('success', 'Bulk Payment Processed Successfully!');


    }
}
