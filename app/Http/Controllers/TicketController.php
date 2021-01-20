<?php

namespace App\Http\Controllers;

use App\Mail\TicketRefundRequst;
use App\Mail\TicketReplyMail;
use App\Mail\WalletApproveMail;
use App\Mail\WishlistReqeustMail;
use App\ManagerLog;
use App\ManagerReview;
use App\Ticket;
use App\TicketAttachment;
use App\TicketCategory;
use App\TicketLog;
use App\TicketThread;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    private $notify;
    private $log;

    /**
     * TicketController constructor.
     * @param $notify
     */
    public function __construct()
    {
        $this->notify = new NotificationController();
        $this->helper = new HelperController();
        $this->log = new ActivityLogController();
    }

    public function create_ticket(Request $request){
        $manager = User::find($request->input('manager_id'));
        if($manager != null){
            $ticket = new Ticket();
            $ticket->title = $request->input('title');
            $ticket->token = $request->input('_token');
            $ticket->email = $request->input('email');
            $ticket->message = $request->input('message');
            $ticket->priority = $request->input('priority');
            if($request->has('order_id')) {
                $ticket->order_id = $request->input('order_id');
            }
            $ticket->status = 'New';
            $ticket->status_id = '1';
            $ticket->source = $request->input('source');
            if($request->input('category') == 'default'){
                $ticket->category = $request->input('category');
            }
            else{
                $category = TicketCategory::find($request->input('category'));
                $ticket->category = $category->name;
                $ticket->category_id = $category->id;
            }
            $ticket->manager_id = $manager->id;
            $user = null;

            if($request->type == 'store-ticket'){
                $shop = $this->helper->getLocalShop();
                $user = $shop->has_user()->first();
                $ticket->user_id = $user->id;
            }
            else{
                $ticket->user_id = Auth::id();
            }

            $ticket->save();

            if($request->hasFile('attachments')){
                $files = $request->file('attachments');
                foreach ($files as $file){
                    $name = Str::slug($file->getClientOriginalName());
                    $attachement = date("mmYhisa_") . $name;
                    $file->move(public_path() . '/ticket-attachments/', $attachement);
                    $ta = new TicketAttachment();
                    $ta->source = $attachement;
                    $ta->ticket_id = $ticket->id;
                    $ta->save();
                }
            }

            /*Ticket request email*/
            $user = User::find($ticket->user_id);

            $manager_email = $manager->email;
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
                Mail::to($users)->send(new TicketRefundRequst($user->email, $ticket));
            }
            catch (\Exception $e){
            }

            $this->log->store($ticket->user_id, 'Ticket', $ticket->id, $ticket->title, 'Ticket Created');



            /*Maintaining Log*/
            $tl = new TicketLog();
            $tl->message = 'A Ticket named "'.$ticket->title.'" against "'.$ticket->email.'" created at '.date_create($ticket->created_at)->format('d M, Y h:i a');
            $tl->status = "Created";
            $tl->ticket_id = $ticket->id;
            $tl->save();
            return redirect()->back()->with('success','Ticket created successfully!');

        }

        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }

    public function create_ticket_thread(Request $request){
        $manager = User::find($request->input('manager_id'));
        $ticket = Ticket::find($request->input('ticket_id'));
        if($manager != null && $ticket != null){
            $thread = new TicketThread();
            $thread->reply = $request->input('reply');
            $thread->source = $request->input('source');
            $thread->manager_id = $manager->id;
            $thread->user_id = $request->input('user_id');
            $thread->shop_id = $request->input('shop_id');
            $thread->ticket_id = $request->input('ticket_id');
            $thread->save();
            $ticket->last_reply_at = now();
            if($request->input('source') == 'manager'){
                $ticket->status_id = '2';
                $ticket->status = 'Replied';
            }
            else{
                $ticket->status_id = '3';
                $ticket->status = 'Waiting For Replied';
            }

            $ticket->save();

            $user = $ticket->has_user;

            try{
                Mail::to($user->email)->send(new TicketReplyMail($user, $ticket));
            }
            catch (\Exception $e){
            }

            if($request->hasFile('attachments')){
                $files = $request->file('attachments');
                foreach ($files as $file){
                    $name = Str::slug($file->getClientOriginalName());
                    $attachement = date("mmYhisa_") . $name;
                    $file->move(public_path() . '/ticket-attachments/', $attachement);
                    $ta = new TicketAttachment();
                    $ta->source = $attachement;
                    $ta->thread_id = $thread->id;
                    $ta->save();
                }
            }


            /*Maintaining Log*/
            $tl = new TicketLog();
            if($request->input('source') == 'manager') {
                $tl->message = 'A Reply Added By Manager on Ticket at ' . date_create($thread->created_at)->format('d M, Y h:i a');
                $tl->status = "Reply From Manager";
            }
            else{
                $tl->message = 'A Reply Added on Ticket at ' . date_create($thread->created_at)->format('d M, Y h:i a');
                $tl->status = "Reply From User";
            }

            $tl->ticket_id = $ticket->id;
            $tl->save();

            if($request->input('source') == 'manager') {
                $tl = new ManagerLog();
                $tl->message = 'A Reply Added By Manager on Ticket at ' . date_create($thread->created_at)->format('d M, Y h:i a');
                $tl->status = "Reply From Manager";
                $tl->manager_id = $manager->id;
                $tl->save();
                $this->notify->generate('Ticket','Ticket Thread','You have a new Message in ticket named '.$ticket->title,$ticket);
            }

            return redirect()->back()->with('success','Reply sent successfully!');

        }

        else{
            return redirect()->back()->with('error','Associated Manager Not Found');
        }
    }

    public function marked_as_completed($id,Request $request){
        $ticket = Ticket::find($id);
        $ticket->status_id = '5';
        $ticket->status = 'Completed';
        $ticket->save();
        $tl = new TicketLog();
        $tl->message = 'Ticket Marked as Completed at ' . date_create(now())->format('d M, Y h:i a');
        $tl->status = "Completed By User";
        $tl->ticket_id = $ticket->id;
        $tl->save();

        $this->log->store(0, 'Ticket', $ticket->id, $ticket->title, 'Ticket Completed');



        return redirect()->back()->with('success','Ticket marked as completed successfully!');
    }

    public function marked_as_closed($id,Request $request){
        $manager = User::role('sales-manager')->find(Auth::id());
        if($manager == null){
            $manager = User::role('sales-manager')->find($request->input('manager_id'));
        }

        if($manager != null){
            $ticket = Ticket::find($id);
            $ticket->status_id = '4';
            $ticket->status = 'Closed';
            $ticket->save();
            $tl = new TicketLog();
            $tl->message = 'Ticket Marked as Closed By Manager at ' . date_create(now())->format('d M, Y h:i a');
            $tl->status = "Closed By Manager";
            $tl->ticket_id = $ticket->id;
            $tl->save();

            $ml = new ManagerLog();
            $ml->message = 'A Ticket Closed By Manager at ' . date_create(now())->format('d M, Y h:i a');
            $ml->status = "Closed By Manager";
            $ml->manager_id = $manager->id;
            $ml->save();
            $this->notify->generate('Ticket','Ticket Marked as Closed',$ticket->title.' has been closed by your manager',$ticket);

            $this->log->store(0, 'Ticket', $ticket->id, $ticket->title, 'Ticket Closed');

            return redirect()->back()->with('success','Ticket marked as completed successfully!');
        }
        else{
            return redirect()->back()->with('error','Manager not Found!');
        }
    }

    public function post_review(Request $request){
        $ticket = Ticket::find($request->input('ticket_id'));
        $manager = User::role('sales-manager')->find($request->input('manager_id'));

        if($ticket !=  null && $manager != null){
            ManagerReview::create($request->all());
            $ticket->review = 1;
            $ticket->save();
            return redirect()->back()->with('success','Ticket Review Added Successfully!');
        }
        else{
            return redirect()->back()->with('error','Invalid Data Passed!');
        }

    }

}
