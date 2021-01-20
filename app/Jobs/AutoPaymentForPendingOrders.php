<?php

namespace App\Jobs;

use App\ErrorLog;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminMaintainerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Mail\OrderPlaceEmail;
use App\Mail\WalletBalanceMail;
use App\OrderLog;
use App\OrderTransaction;
use App\RetailerOrder;
use App\User;
use App\WalletLog;
use App\WalletSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AutoPaymentForPendingOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_id;
    private $log;
    private $notify;
    private $admin;
    private $inventory;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
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
        /* Auto Order Payment in case user has enabled settings for it (START)*/
        $settings = WalletSetting::where('user_id', $this->user_id)->first();

        DB::beginTransaction();
        try{
            if($settings && $settings->enable) {

               $orders = RetailerOrder::where('user_id', $this->user_id)->where('paid', 0)->latest()->get();

               foreach ($orders as $new) {
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
                               $log->message = "ERP order BUG from Auto Wallet Payment In Top-up Job: ". $e->getMessage();
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
            }
            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollBack();
            $log = new ErrorLog();
            $log->message = "Payment issue in Top-up Job: " .$e->getMessage();
            $log->save();
        }
        /* Auto Order Payment in case user has enabled settings for it (END)*/
    }
}
