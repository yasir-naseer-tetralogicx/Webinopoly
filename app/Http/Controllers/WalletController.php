<?php

namespace App\Http\Controllers;


use App\ErrorLog;
use App\Jobs\AutoPaymentForPendingOrders;
use App\Mail\NewUser;
use App\Mail\NewWallet;
use App\Mail\OrderPlaceEmail;
use App\Mail\WalletApproveMail;
use App\Mail\WalletRequestMail;
use App\Mail\WishlistReqeustMail;
use App\OrderLog;
use App\OrderTransaction;
use App\PaypalWalletTransaction;
use App\RetailerOrder;
use App\User;
use App\Wallet;
use App\WalletLog;
use App\WalletRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\ExpressCheckout;

class WalletController extends Controller
{

    private $helper;
    private $admin;
    private $notify;
    private $inventory;
    private $log;

    /**
     * WalletController constructor.
     */
    public function __construct()
    {
        $this->helper = new HelperController();
        $this->admin = new AdminMaintainerController();
        $this->notify = new NotificationController();
        $this->inventory = new InventoryController();
        $this->log = new ActivityLogController();

    }

    public function user_wallet_view()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->has_wallet == null) {
                $wallet = $this->wallet_create(Auth::id());
                try{
                    Mail::to($user->email)->send(new NewWallet($user));

                }catch (\Exception $e){

                }

            } else {
                $wallet = $user->has_wallet;
            }
            return view('non_shopify_users.wallet.index')->with([
                'user' => $user,
                'wallet' => $wallet
            ]);
        } else {
            $shop = $this->helper->getLocalShop();
            if (count($shop->has_user) > 0) {
                if ($shop->has_user[0]->has_wallet == null) {
                    $wallet = $this->wallet_create($shop->has_user[0]->id);
                    try{
                        Mail::to($shop->has_user[0]->email)->send(new NewWallet($shop->has_user[0]));

                    }catch (\Exception $e){

                    }

                } else {
                    $wallet = $shop->has_user[0]->has_wallet;
                }
                return view('single-store.wallet.index')->with([
                    'user' => $shop->has_user[0],
                    'wallet' => $wallet
                ]);
            } else {
                return view('single-store.wallet.index');
            }
        }
    }



    /**
     * @param $id
     * @return mixed
     */
    public function wallet_create($id)
    {
        $wallet = Wallet::create([
            'user_id' => $id,
            'wallet_token' => 'WFF00100' . rand(10000000000, 99999999999),
            'pending' => 0,
            'available' => 0,
            'transferred' => 0,
            'used' => 0,
        ]);
        $wallet_log = new WalletLog();
        $wallet_log->wallet_id = $wallet->id;
        $wallet_log->status = "CREATED";
        $wallet_log->amount = 0;
        $wallet_log->message = 'Wallet ' . $wallet->wallet_token . ' Initiated at ' . now()->format('d M, Y h:i a');
        $wallet_log->save();
        return $wallet;
    }

    public function request_wallet_topup_bank(Request $request)
    {
       $user = User::find($request->input('user_id'));
       $wallet = Wallet::find($request->input('wallet_id'));
       if($user != null && $wallet != null){
         $wallet_request = WalletRequest::create($request->all());
           if ($request->hasFile('attachment')) {
               $image = $request->file('attachment');
               $destinationPath = 'wallet-attachment/';
               $filename = now()->format('YmdHi') . str_replace([' ','(',')'], '-', $image->getClientOriginalName());
               $image->move($destinationPath, $filename);
               $wallet_request->attachment = $filename;
               $wallet_request->save();

           }

           $wallet_log = new WalletLog();
           $wallet_log->wallet_id = $request->input('wallet_id');
           $wallet_log->status = "Top-up Request Through Bank Transfer";
           $wallet_log->amount = $request->input('amount');
           $wallet_log->message = 'A Top-up Request of Amount '.number_format($request->input('amount'),2).' USD Through Bank Transfer Against Wallet ' . $wallet->wallet_token . ' Requested At ' . now()->format('d M, Y h:i a');


           $wallet_log->save();
           $wallet->pending = $wallet->pending + $request->input('amount');
           $wallet->save();

           /*Wallet request email*/
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
               Mail::to($users)->send(new WalletRequestMail($user->email, $wallet));
           }
           catch (\Exception $e){
           }

           $this->log->store($wallet->user_id, 'Wallet', $wallet->id, $wallet->owner->name, 'Top-up Request Submitted to Administration');

          return redirect()->back()->with('success', 'Your Top-up Request Submit Successfully to Administration. Please Wait For Approval!');
       }
       else{
           return redirect()->back()->with('error', 'Something Goes Wrong!');
       }

    }

    public function index(Request $request){
        $admins = User::whereIn('email',['admin@wefullfill.com','super_admin@wefullfill.com'])->pluck('id')->toArray();
        $users  = User::role('non-shopify-users')->whereNotIn('id',$admins)->orderBy('created_at','DESC')->newQuery();
        if($request->has('search')){
            $users->WhereHas('has_shops',function ($q) use ($request){
                $q->where('shopify_domain','LIKE','%'.$request->input('search').'%');
            });
        }


        $users = $users->paginate(30);

        foreach ($users as $user){
            if ($user->has_wallet == null) {
               $this->wallet_create($user->id);
               try{
                   Mail::to($user->email)->send(new NewWallet($user));

               }catch (\Exception $e){

               }

            }
        }
        return view('setttings.wallets.index')->with([
            'users' => $users,
            'search' => $request->input('search')
        ]);
    }

    public function walletRequest(Request $request){

        $wallets = [];

        foreach (Wallet::all() as $wallet) {
            if($wallet->requests()->where('status', 0)->count() > 0) {
                array_push($wallets, $wallet);
            }
        }


        return view('setttings.wallets.requests')->with([
            'wallets' => $wallets
        ]);
    }

    public function wallet_details(Request $request,$id){
        $wallet = Wallet::find($id);
        $user = User::find($wallet->user_id);
        return view('setttings.wallets.wallet_detail')->with([
            'user' => $user,
            'wallet' => $wallet
        ]);
    }

    public function EditWalletDetails($id, Request $request){
            $wallet = WalletRequest::find($id);
            $wallet->cheque_title = $request->input('cheque_title');
            $wallet->cheque = $request->input('cheque');
            $wallet->amount = $request->input('amount');
            $wallet->save();

            // maintain logs..

            $get_wallet = Wallet::find($wallet->wallet_id);
            $wallet_log = new WalletLog();
            $wallet_log->wallet_id =$wallet->wallet_id;
            $wallet_log->status = "Admin Edit Wallet Payment";
            $wallet_log->amount = $request->input('amount');
            $wallet_log->message = 'A Top-up of Amount '.number_format($request->input('amount'),2).' USD edit Against Wallet ' . $get_wallet->wallet_token . ' At ' . now()->format('d M, Y h:i a'). ' By Administration';
            $wallet_log->save();

            $this->log->store(0, 'Wallet', $wallet->id, $wallet->user,'Wallet Request Edited');




        return redirect()->back()->with('success', 'Wallet Request Updated successfully');
    }

    public function approved_bank_statement($id,Request $request){
        $req = WalletRequest::find($id);
        if($req->status == 0){
            $related_wallet = Wallet::find($req->wallet_id);
            if($related_wallet!= null){
                $related_wallet->pending =  $related_wallet->pending - $req->amount;
                $related_wallet->available =   $related_wallet->available + $req->amount;
                $related_wallet->save();
                $req->status = 1;
                $req->save();
                $wallet_log = new WalletLog();
                $wallet_log->wallet_id =$related_wallet->id;
                $wallet_log->status = "Bank Transfer Approved";
                $wallet_log->amount = $req->amount;
                $wallet_log->message = 'A Top-up Request of Amount '.number_format($req->amount,2).' USD Through Bank Transfer Against Wallet ' . $related_wallet->wallet_token . ' Approved At ' . date_create($request->input('date'))->format('d M, Y h:i a') . ' By Administration';
                $wallet_log->timestamps = false;
                $wallet_log->created_at = date_create($request->input('date'))->format('Y-m- H:i:s');
                $wallet_log->save();

                $this->notify->generate('Wallet','Wallet Top-up Request Approved','A Top-up Request of Amount '.number_format($req->amount,2).' USD Through Bank Transfer Against Wallet ' . $related_wallet->wallet_token . ' Approved At ' . date_create($request->input('date'))->format('d M, Y h:i a') . ' By Administration',$related_wallet);

                $user = $related_wallet->owner;

                try{
                    Mail::to($user->email)->send(new WalletApproveMail($user, $related_wallet));
                }
                catch (\Exception $e){
                }

                dispatch(new AutoPaymentForPendingOrders($related_wallet->user_id));

                $this->log->store(0, 'Wallet', $related_wallet->id, $related_wallet->owner->name,'Wallet Request Approved');


                return redirect()->back()->with('success','Top-up Request through Bank Transfer Approved Successfully!');
            }
            else{
                return redirect()->back()->with('error','No wallet found related to this request!');
            }
        }
        else{
            return redirect()->back()->with('error','You cant approve an already approved request!');
        }
    }

    public function topup_wallet_by_admin(Request $request){
        $wallet = Wallet::find($request->input('wallet_id'));
        if($wallet != null){
            if($request->input('amount') > 0){
                $wallet->available =  $wallet->available + $request->input('amount');
                $wallet->save();
                $wallet_log = new WalletLog();
                $wallet_log->wallet_id =$wallet->id;
                $wallet_log->status = "Top-up By Admin";
                $wallet_log->amount = $request->input('amount');
                $wallet_log->message = 'A Top-up of Amount '.number_format($request->input('amount'),2).' USD Added Against Wallet ' . $wallet->wallet_token . ' At ' . now()->format('d M, Y h:i a'). ' By Administration';
                $wallet_log->save();
                $this->notify->generate('Wallet','Wallet Top-up By Admin','A Top-up of Amount '.number_format($request->input('amount'),2).' USD Added Against Wallet ' . $wallet->wallet_token . ' At ' . now()->format('d M, Y h:i a'). ' By Administration',$wallet);

                return redirect()->back()->with('success','Wallet Top-up Successfully!');
            }


        }else{
            return redirect()->back()->with('error','Wallet Not Found!');
        }

    }

    public function editWalletAmount(Request $request){
        $wallet = Wallet::find($request->input('wallet_id'));
        if($wallet != null){
            if($request->input('amount') > 0){
                $wallet->available = $request->input('amount');
                $wallet->save();
                $wallet_log = new WalletLog();
                $wallet_log->wallet_id =$wallet->id;
                $wallet_log->status = "Wallet Amount updated by Admin";
                $wallet_log->amount = $request->input('amount');
                $wallet_log->message = 'An Amount of '.number_format($request->input('amount'),2).' USD Added Against Wallet ' . $wallet->wallet_token . ' At ' . now()->format('d M, Y h:i a'). ' is updated By Administration';
                $wallet_log->save();
                $this->notify->generate('Wallet','Wallet Top-up By Admin','A Top-up of Amount '.number_format($request->input('amount'),2).' USD Added Against Wallet ' . $wallet->wallet_token . ' At ' . now()->format('d M, Y h:i a'). ' is updated By Administration',$wallet);

                return redirect()->back()->with('success','Wallet Amount Updated Successfully!');
            }
            else {
                return redirect()->back()->with('error','Wallet Amount cannot be 0!');
            }


        }else{
            return redirect()->back()->with('error','Wallet Not Found!');
        }

    }


    /*Updated Inventory*/
    public function order_payment_by_wallet(Request $request){
        $retailer_order = RetailerOrder::find($request->id);
        if($retailer_order->paid == 0){
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->has_wallet == null) {
                    return redirect()->back()->with('error','Wallet Does not Exist!');
                } else {
                    $wallet = $user->has_wallet;
                }

            } else {
                $shop = $this->helper->getLocalShop();
                if (count($shop->has_user) > 0) {
                    if ($shop->has_user[0]->has_wallet == null) {
                        return redirect()->back()->with('error','Wallet Does not Exist!');

                    } else {
                        $wallet = $shop->has_user[0]->has_wallet;
                    }

                } else {
                    return redirect()->back()->with('error','Wallet Does not Exist!');

                }
            }
            if($wallet->available >= $retailer_order->cost_to_pay){
                /*Wallet Deduction*/
                $wallet->available =   $wallet->available -  $retailer_order->cost_to_pay;
                $wallet->used =  $wallet->used + $retailer_order->cost_to_pay;
                $wallet->save();
                /*Maintaining Wallet Log*/
                $wallet_log = new WalletLog();
                $wallet_log->wallet_id =$wallet->id;
                $wallet_log->status = "Order Payment";
                $wallet_log->amount = $retailer_order->cost_to_pay;
                $wallet_log->message = 'An Amount '.number_format($retailer_order->cost_to_pay,2).' USD For Order Cost Against Wallet ' . $wallet->wallet_token . ' Deducted At ' . now()->format('d M, Y h:i a');
                $wallet_log->save();
                $this->notify->generate('Wallet','Wallet Order Payment','An Amount '.number_format($retailer_order->cost_to_pay,2).' USD For Order Cost Against Wallet ' . $wallet->wallet_token . ' Deducted At ' . now()->format('d M, Y h:i a'),$wallet);

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



                /*Order Processing*/
                $new_transaction = new OrderTransaction();
                $new_transaction->amount =  $retailer_order->cost_to_pay;
                if($retailer_order->custom == 0){
                    $new_transaction->name = $retailer_order->has_store->shopify_domain;
                }
                else{
                    $new_transaction->name = Auth::user()->email;
                }

                $new_transaction->retailer_order_id = $retailer_order->id;
                $new_transaction->user_id = $retailer_order->user_id;
                $new_transaction->shop_id = $retailer_order->shop_id;
                $new_transaction->save();
                /*Changing Order Status*/
                $retailer_order->paid = 1;
                if(count($retailer_order->fulfillments) > 0){
                    $retailer_order->status = $retailer_order->getStatus($retailer_order);
                }
                else{
                    $retailer_order->status = 'Paid';
                }
                $retailer_order->pay_by = 'Wallet';
                $retailer_order->save();

                /*Maintaining Log*/
                $order_log =  new OrderLog();
                $order_log->message = "An amount of ".$new_transaction->amount." USD paid to WeFullFill through Wallet on ".date_create($new_transaction->created_at)->format('d M, Y h:i a')." for further process";
                $order_log->status = "paid";
                $order_log->retailer_order_id = $retailer_order->id;
                $order_log->save();


                $this->admin->sync_order_to_admin_store($retailer_order);
                $this->inventory->OrderQuantityUpdate($retailer_order,'new');
                try {
                    $this->admin->push_to_mabang($retailer_order->id);
                }
                catch (\Exception $e) {
                    $log = new ErrorLog();
                    $log->message = "ERP order BUG from Wallet Single: ". $e->getMessage();
                    $log->save();
                }

                $this->log->store($retailer_order->user_id, 'Order', $retailer_order->id, $retailer_order->name, 'Order Payment Paid');


                return redirect()->back()->with('success','Order Cost Deducted From Wallet Successfully!');
            }
            else{
                return redirect()->back()->with('error','Wallet Doesnot Have Required Amount!');
            }
        }
        else{
            return redirect()->back()->with('error','Order Cost Already Paid!');
        }
    }

    public function order_bulk_payment_by_wallet(Request $request) {

        foreach ($request->order_ids as $id) {

            $retailer_order = RetailerOrder::find($id);
            if($retailer_order->paid == 0){
                if (Auth::check()) {
                    $user = Auth::user();
                    if ($user->has_wallet == null) {
                        return redirect()->back()->with('error','Wallet Does not Exist!');
                    } else {
                        $wallet = $user->has_wallet;
                    }

                } else {
                    $shop = $this->helper->getLocalShop();
                    if (count($shop->has_user) > 0) {
                        if ($shop->has_user[0]->has_wallet == null) {
                            return redirect(route('store.orders'))->with('error','Wallet Does not Exist!');

                        } else {
                            $wallet = $shop->has_user[0]->has_wallet;
                        }

                    } else {
                        return redirect(route('store.orders'))->with('error','Wallet Does not Exist!');

                    }
                }
                if($wallet->available >= $retailer_order->cost_to_pay){
                    /*Wallet Deduction*/
                    $wallet->available =   $wallet->available -  $retailer_order->cost_to_pay;
                    $wallet->used =  $wallet->used + $retailer_order->cost_to_pay;
                    $wallet->save();
                    /*Maintaining Wallet Log*/
                    $wallet_log = new WalletLog();
                    $wallet_log->wallet_id =$wallet->id;
                    $wallet_log->status = "Order Payment";
                    $wallet_log->amount = $retailer_order->cost_to_pay;
                    $wallet_log->message = 'An Amount '.number_format($retailer_order->cost_to_pay,2).' USD For Order Cost Against Wallet ' . $wallet->wallet_token . ' Deducted At ' . now()->format('d M, Y h:i a');
                    $wallet_log->save();
                    $this->notify->generate('Wallet','Wallet Order Payment','An Amount '.number_format($retailer_order->cost_to_pay,2).' USD For Order Cost Against Wallet ' . $wallet->wallet_token . ' Deducted At ' . now()->format('d M, Y h:i a'),$wallet);

                    /*Order Processing*/
                    $new_transaction = new OrderTransaction();
                    $new_transaction->amount =  $retailer_order->cost_to_pay;
                    if($retailer_order->custom == 0){
                        $new_transaction->name = $retailer_order->has_store->shopify_domain;
                    }
                    else{
                        $new_transaction->name = Auth::user()->email;
                    }

                    $new_transaction->retailer_order_id = $retailer_order->id;
                    $new_transaction->user_id = $retailer_order->user_id;
                    $new_transaction->shop_id = $retailer_order->shop_id;
                    $new_transaction->save();
                    /*Changing Order Status*/
                    $retailer_order->paid = 1;
                    if(count($retailer_order->fulfillments) > 0){
                        $retailer_order->status = $retailer_order->getStatus($retailer_order);

                    }
                    else{
                        $retailer_order->status = 'Paid';
                    }
                    $retailer_order->pay_by = 'Wallet';
                    $retailer_order->save();

                    /*Maintaining Log*/
                    $order_log =  new OrderLog();
                    $order_log->message = "An amount of ".$new_transaction->amount." USD paid to WeFullFill through Wallet on ".date_create($new_transaction->created_at)->format('d M, Y h:i a')." for further process";
                    $order_log->status = "paid";
                    $order_log->retailer_order_id = $retailer_order->id;
                    $order_log->save();


                    $this->admin->sync_order_to_admin_store($retailer_order);
                    $this->inventory->OrderQuantityUpdate($retailer_order,'new');
                    try {
                        $this->admin->push_to_mabang($retailer_order->id);
                    }
                    catch (\Exception $e) {
                        $log = new ErrorLog();
                        $log->message = "ERP order BUG from Wallet Bulk: ". $e->getMessage();
                        $log->save();
                    }


                    $this->log->store($retailer_order->user_id, 'Order', $retailer_order->id, $retailer_order->name, 'Order Payment Paid');


                }
                else{
                    return redirect(route('store.orders'))->with('error','Wallet Doesnot Have Required Amount!');
                }
            }
            else{
                return redirect(route('store.orders'))->with('error','Order Cost Already Paid!');
            }
        }
        return redirect(route('store.orders'))->with('success','Order Cost Deducted From Wallet Successfully!');

    }

    public function paypal_topup_payment(Request $request)
    {
        $wallet = Wallet::find($request->id);
        if($wallet  != null){
            $items = [];
            $order_total = $request->input('amount');

            /*adding order-lime-items for paying through paypal*/

                array_push($items,[
                    'name' => 'Top Up',
                    'price' => $request->input('amount'),
                    'qty' =>1
                ]);

            $data = [];
            $data['items'] = $items;
            $data['invoice_id'] = 'WeFullFill-Wallet-Top-up_'.rand(1,1000);
            $data['invoice_description'] = $data['invoice_id']." Invoice";
            $data['return_url'] = route('store.wallet.paypal.topup.success',$wallet->id);
            $data['cancel_url'] = route('store.wallet.paypal.topup.cancel',$wallet->id);
            $data['total'] = $order_total;

            $provider = new ExpressCheckout;
            $response = $provider->setExpressCheckout($data);

//        dd($response);

            return redirect($response['paypal_link']);
        }
        else{
            return redirect()->back()->with('error','Wallet doesnot exist!');
        }

    }


    public function paypal_topup_payment_cancel(Request $request)
    {   $wallet = Wallet::find($request->id);
        if($wallet != null){
            return redirect()->route('store.user.wallet.show')->with('error','Paypal Transaction Process cancelled successfully');

        }
        else{
            return redirect()->route('store.user.wallet.show')->with('error','Paypal Transaction Process cancelled successfully');
        }
    }

    public function paypal_topup_payment_success(Request $request)
    {

        $wallet = Wallet::find($request->id);
        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING']) && $wallet  != null)
        {
            $wallet->available =  $wallet->available + $response['AMT'];
            $wallet->save();

            $wallet_log = new WalletLog();
            $wallet_log->wallet_id =$wallet->id;
            $wallet_log->status = "Top-up By Paypal";
            $wallet_log->amount = $response['AMT'];
            $wallet_log->message = 'An Amount '.number_format($response['AMT'],2).' USD For Wallet ' . $wallet->wallet_token . ' Top-up At ' . now()->format('d M, Y h:i a');
            $wallet_log->save();

            $paypal_wallet_log  = new PaypalWalletTransaction();
            $paypal_wallet_log->amount = $response['AMT'];
            $paypal_wallet_log->wallet_id = $wallet->id;
            $paypal_wallet_log->reason = 'TOP-UP';
            $paypal_wallet_log->status = 'success';
            $paypal_wallet_log->paypal_payment_id = $request->PayerID;
            $paypal_wallet_log->paypal_token = $request->token;
            $paypal_wallet_log->save();

            return redirect()->route('store.user.wallet.show')->with('success','Wallet Top-up Transaction Process Successfully!');
        }
        else{
            return redirect()->route('store.orders')->with('error','Wallet Doesnot Exist!');
        }

    }

}

