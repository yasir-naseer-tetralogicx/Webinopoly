<?php

namespace App\Http\Controllers;

use App\ManagerLog;
use App\OrderLog;
use App\Refund;
use App\RefundAttachment;
use App\RefundLog;
use App\RefundThread;
use App\RetailerOrder;
use App\User;
use App\WalletLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RefundController extends Controller
{

    private $helper;
    private $notify;
    private $inventory;
    private $log;

    /**
     * RefundController constructor.
     * @param $helper
     */
    public function __construct()
    {
        $this->helper = new HelperController();
        $this->notify = new NotificationController();
        $this->inventory = new InventoryController();
        $this->log = new ActivityLogController();

    }

    public function create_refund(Request $request){
//        dd($request);
        $manager = User::find($request->input('manager_id'));
        if($manager != null){
            $order =  RetailerOrder::find($request->input('order_id'));
            if($order != null){
                if($order->paid == 0 || $order->paid == 2){
                    return redirect()->back()->with('error','Order cant be refund!');
                }
                else{
                    $ticket = new Refund();
                    $ticket->title = $request->input('title');
                    $ticket->token = $request->input('_token');
                    $ticket->order_number =$order->name;
                    $ticket->order_cost = $order->cost_to_pay;
                    $ticket->reason = $request->input('message');
                    $ticket->priority = $request->input('priority');
                    $ticket->status = 'New';
                    $ticket->status_id = '1';
                    $ticket->source = $request->input('source');
                    $ticket->manager_id = $manager->id;
                    $ticket->order_id = $order->id;
                    if($request->type == 'user-ticket'){
                        $ticket->user_id = Auth::id();
                    }
                    else{
                        $ticket->shop_id = $request->input('shop_id');
                    }
                    $ticket->save();
                    if($request->hasFile('attachments')){
                        $files = $request->file('attachments');
                        foreach ($files as $file){
                            $name =now()->format('YmdHi') . str_replace([' ','(',')'], '-', $file->getClientOriginalName());
                            $attachement = date("mmYhisa_") . $name;
                            $file->move(public_path() . '/ticket-attachments/', $attachement);
                            $ta = new RefundAttachment();
                            $ta->source = $attachement;
                            $ta->refund_id = $ticket->id;
                            $ta->save();
                        }
                    }
                    /*Maintaining Log*/
                    $tl = new RefundLog();
                    $tl->message = 'A Refund named "'.$ticket->title.'" against "'.$order->name.'" created at '.date_create($ticket->created_at)->format('d M, Y h:i a');
                    $tl->status = "Created";
                    $tl->refund_id = $ticket->id;
                    $tl->save();
                    return $this->process_refund($ticket,$order);
                }

            }
            else{
                return redirect()->back()->with('error','Order not found for generating refund!');

            }
        }
        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }

    public function process_refund(Refund $refund,RetailerOrder $order){
        if(in_array($order->status,['Paid','unfulfilled','fulfilled'])){
            return $this->refund_order($refund,$order);
        }

        else{
            return redirect()->back()->with('success','Order Cant Refund Automatically Because Of Its Status');
        }
    }

    public function refund_order(Refund $refund,RetailerOrder $order){

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
            else {
                return redirect()->back()->with('error', 'Order Cant Refund Automatically Because No Wallet Found!');
            }
        }

        $wallet->available = $wallet->available+(double)$order->cost_to_pay;
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

        $refund->status_id = 4;
        $refund->status = 'Closed';
        $refund->save();

        $tl = new RefundLog();
        $tl->message = 'A Refund Generated "'.$refund->title.'" against "'.$order->name.'"  at '.now()->format('d M, Y h:i a');
        $tl->status = "Generated";
        $tl->refund_id = $refund->id;
        $tl->save();

        $this->notify->generate('Refund','Order Refund',$order->name.' Refund Approved',$refund);
        $this->inventory->OrderQuantityUpdate($order,'refund');
        return redirect()->back()->with('success','Order Refunded Successfully!');


    }

    public function create_refund_thread(Request $request){
        $manager = User::find($request->input('manager_id'));
        $ticket = Refund::find($request->input('refund_id'));
        if($manager != null && $ticket != null){
            $thread = new RefundThread();
            $thread->reply = $request->input('reply');
            $thread->source = $request->input('source');
            $thread->manager_id = $manager->id;
            $thread->user_id = $request->input('user_id');
            $thread->shop_id = $request->input('shop_id');
            $thread->refund_id = $request->input('refund_id');
            $thread->save();

            if(!in_array($ticket->status_id,[4,5])){
                if($request->input('source') == 'manager'){
                    $ticket->status_id = '2';
                    $ticket->status = 'Replied';
                }
                else{
                    $ticket->status_id = '3';
                    $ticket->status = 'Waiting For Replied';
                }
            }
            $ticket->save();

            if($request->hasFile('attachments')){
                $files = $request->file('attachments');
                foreach ($files as $file){
                    $name =now()->format('YmdHi') . str_replace([' ','(',')'], '-', $file->getClientOriginalName());
                    $attachement = date("mmYhisa_") . $name;
                    $file->move(public_path() . '/ticket-attachments/', $attachement);
                    $ta = new RefundAttachment();
                    $ta->source = $attachement;
                    $ta->thread_id = $thread->id;
                    $ta->save();
                }
            }


            /*Maintaining Log*/
            $tl = new RefundLog();
            if($request->input('source') == 'manager') {
                $tl->message = 'A Reply Added By Manager on Refund Ticket at ' . date_create($thread->created_at)->format('d M, Y h:i a');
                $tl->status = "Reply From Manager";
            }
            else{
                $tl->message = 'A Reply Added on Refund Ticket Ticket at ' . date_create($thread->created_at)->format('d M, Y h:i a');
                $tl->status = "Reply From User";
            }

            $tl->refund_id = $ticket->id;
            $tl->save();

            if($request->input('source') == 'manager') {
                $tl = new ManagerLog();
                $tl->message = 'A Reply Added By Manager on Refund Ticket at ' . date_create($thread->created_at)->format('d M, Y h:i a');
                $tl->status = "Reply From Manager";
                $tl->manager_id = $manager->id;
                $tl->save();

                $this->notify->generate('Refund','Order Refund Thread',$ticket->order_name.' Refund has a new message in conversation',$ticket);

            }

            return redirect()->back()->with('success','Reply sent successfully!');


        }
        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }

    public function approve_refund($id,$order_id){
        $refund = Refund::find($id);
        $order = RetailerOrder::find($order_id);
        if($refund != null && $order != null){
            return $this->refund_order($refund,$order);
        }
        else{
            return redirect()->back()->with('error','Refund/Order Not Found!');

        }
    }

    public function cancel_order($id){
        $order = RetailerOrder::find($id);
        if($order != null){
            $order->status = 'cancelled';
            $order->save();
            /*Order Log*/
            $order_log =  new OrderLog();
            $order_log->message = "Order cancelled at  ".now()->format('d M, Y h:i a');
            $order_log->status = "cancelled";
            $order_log->retailer_order_id = $order->id;
            $order_log->save();
            $this->notify->generate('Order','Order Cancelled',$order->name.' has been cancelled',$order);
            $this->inventory->OrderQuantityUpdate($order,'refund');
            $this->log->store(0, 'Order', $order->id, $order->name, 'Order Cancelled');
            return redirect()->back()->with('success','Order Cancelled Successfully!');

        }
        else{
            return redirect()->back()->with('error','Order Not Found!');

        }
    }

    public function refund_cancel_order($id){
        $order = RetailerOrder::find($id);
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
                else {
                    return redirect()->back()->with('error', 'Order Cant Refund Automatically Because No Wallet Found!');
                }
            }

            $wallet->available = $wallet->available+(int)$order->cost_to_pay;
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
            $this->log->store(0, 'Order', $order->id, $order->name, 'Order Cancelled & Refunded');

            /*Order Log*/
            $order_log =  new OrderLog();
            $order_log->message = "An amount of ".$order->cost_to_pay." USD refunded to Wallet on ".now()->format('d M, Y h:i a');
            $order_log->status = "refunded";
            $order_log->retailer_order_id = $order->id;
            $order_log->save();
            $this->notify->generate('Order','Order Cancelled and Refund',$order->name.' has been cancelled and refunded',$order);
            $this->inventory->OrderQuantityUpdate($order,'refund');

            return redirect()->back()->with('success','Order Refunded Successfully!');
        }
        else{
            return redirect()->back()->with('error','Order Not Found!');

        }
    }



}
