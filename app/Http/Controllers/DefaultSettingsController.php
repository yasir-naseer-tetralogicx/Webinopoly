<?php

namespace App\Http\Controllers;

use App\AdminSetting;
use App\Campaign;
use App\Customer;
use App\DefaultInfo;
use App\EmailTemplate;
use App\Exports\CustomersExport;
use App\GeneralDiscountPreferences;
use App\GeneralFixedPricePreferences;
use App\Jobs\SendNewsEmailJob;
use App\Jobs\SendNewsProductEmailjob;
use App\Product;
use App\Refund;
use App\Shop;
use App\Ticket;
use App\TicketCategory;
use App\TicketStatus;
use App\TieredPricingPrefrences;
use App\User;
use App\WarnedPlatform;
use App\Wishlist;
use App\WishlistStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class DefaultSettingsController extends Controller
{
    private $helper;

    public function __construct()
    {
        $this->helper = new HelperController();
    }

    public function index()
    {
        $info = DefaultInfo::get()->first();
        $platforms = WarnedPlatform::all();
        $admin_settings = AdminSetting::all()->first();
        $shops = \OhMyBrew\ShopifyApp\Models\Shop::whereNotIn('shopify_domain',['wefullfill.myshopify.com'])->get();


        $users = User::role('non-shopify-users')
            ->whereNotIn('email', ['admin@wefullfill.com', 'super_admin@wefullfill.com'])
            ->orderBy('created_at','DESC')
            ->get();

        return view('setttings.default.index')->with([
            'info' => $info,
            'platforms' =>$platforms,
            'settings' =>$admin_settings,
            'shops' => $shops,
            'non_shopify_users' => $users,
        ]);
    }

    public function save_percentage(Request $request)
    {
      AdminSetting::UpdateOrcreate([
          'id' => 1,
      ],[
         'payment_charge_percentage'  => $request->input('payment_charge_percentage'),
          'paypal_percentage' =>$request->input('paypal_percentage'),
      ]);
        return redirect()->back()->with('success', 'Payment Charge Percentage Saved Successfully');
    }

    public function save(Request $request)
    {
        $info = new DefaultInfo();
        $info->ship_info = $request->info;
        $info->processing_time = $request->time;
        $info->ship_price = $request->price;
        $info->warned_platform = $request->warnedplatform;
        $info->save();
        return redirect()->back()->with('success', 'Saved Sucessfully');
    }

    public function update(Request $request, $id)
    {
        $info = DefaultInfo::find($id);
        $info->ship_info = $request->info;
        $info->processing_time = $request->time;
        $info->ship_price = $request->price;
        $info->warned_platform = $request->warnedplatform;
        $info->save();
        return redirect()->back()->with('success', 'Updated Sucessfully');
    }
    public function show_sales_managers(Request $request){
        $sales_managers = User::role('sales-manager')->newQuery();
        if($request->has('search')){
            $sales_managers->where('name','LIKE','%'.$request->input('search').'%');
            $sales_managers->orWhere('email','LIKE','%'.$request->input('search').'%');
        }

       $sales_managers = $sales_managers->orderBy('created_at','DESC')->get();
        return view('setttings.sales-managers.index')->with([
            'sales_managers' => $sales_managers,
            'search' => $request->input('search')
        ]);
    }
    public function show_sales_manager_create(){
        $users = $this->get_non_shopify_users();
        $shops = $this->get_shops_for_managers();
        return view('setttings.sales-managers.create')->with([
            'stores' => $shops,
            'users' => $users
        ]);
    }
    public function show_sales_manager_edit(Request $request){
        $manager = User::find($request->id);
        if($manager != null){
            $users = $this->get_non_shopify_users();
            $shops = $this->get_shops_for_managers();

            return view('setttings.sales-managers.edit')->with([
                'stores' => $shops,
                'users' => $users,
                'manager' => $manager
            ]);
        }
        else{
            return redirect()->route('sales-managers.index')->with('error', 'Manager Not Found!');

        }

    }

    public function search_create_content_sale_manager(Request $request){
        $shops = Shop::query();
        $shops->whereNotIn('shopify_domain', ['wefullfill.myshopify.com', 'fantasy-supplier.myshopify.com']);
        $shops->whereDoesntHave('has_manager', function () {
        });
        $shops->where('shopify_domain','LIKE','%'.$request->input('search').'%');
        $shops = $shops->get();

        $users = User::role('non-shopify-users')->newQuery();
        $users->where('email','LIKE','%'.$request->input('search').'%');
        $users->where('name','LIKE','%'.$request->input('search').'%');
        $users->whereNotIn('email', ['admin@wefullfill.com', 'super_admin@wefullfill.com']);
        $users->whereDoesntHave('has_manager', function () {

        });
        $users = $users->get();

        $html = view('inc.create_content_sale_manager')->with([
            'stores' => $shops,
            'users' => $users,
        ])->render();

        return response()->json([
            'message' => 'success',
            'html' =>$html,
        ]);
    }

    public function search_edit_content_sale_manager(Request $request){
        $manager = User::find($request->input('id'));
        if($manager != null){
            $shops = Shop::query();
            $shops->whereNotIn('shopify_domain', ['wefullfill.myshopify.com', 'fantasy-supplier.myshopify.com']);
            $shops->whereDoesntHave('has_manager', function () {

            });
            $shops->where('shopify_domain','LIKE','%'.$request->input('search').'%');
            $shops = $shops->get();


            $users = User::role('non-shopify-users')->newQuery();
            $users->whereNotIn('email', ['admin@wefullfill.com', 'super_admin@wefullfill.com']);
            $users->where('email','LIKE','%'.$request->input('search').'%');
            $users->where('name','LIKE','%'.$request->input('search').'%');
            $users->whereDoesntHave('has_manager', function () {

            });
            $users = $users->get();

            $html = view('inc.edit_content_sale_manager')->with([
                'stores' => $shops,
                'users' => $users,
                'manager' => $manager
            ])->render();

            return response()->json([
                'message' => 'success',
                'html' =>$html,
            ]);
        }
        else{
            return response()->json([
                'message' => 'error',
            ]);

        }
    }


    public function show_sales_manager(Request $request){
        $manager = User::find($request->id);
        if($manager != null){
            return view('setttings.sales-managers.view')->with([
                'manager' => $manager
            ]);
        }
        else{
            return redirect()->route('sales-managers.index')->with('error', 'Manager Not Found!');

        }

    }

    public function update_manager(Request $request){
        $manager = User::find($request->id);
        if($manager != null){
            $manager->name = $request->input('name');
            $manager->save();
            $this->detach_manager_models($manager);
            $this->attach_manager_models($request, $manager);
            return redirect()->route('sales-managers.index')->with('success', 'Manager Updated Successfully!');

        }
        else{
            return redirect()->route('sales-managers.index')->with('error', 'Manager Not Found!');
        }
    }

    public function create_manager(Request $request){
        $existing_user = User::where('email',$request->input('email'))->first();
        if($existing_user != null){
            if($existing_user->hasRole('non-shopify-users')){
                return redirect()->back()->with('error', 'Email assigned to a non-shopify user and this cant be a sales manager!');
            }
            else{
                if($existing_user->hasRole('sales-manager')){
                    return redirect()->back()->with('success', 'Manager Already Existed!');
                }
                else{
                    return redirect()->back()->with('error', 'Email assigned to a non-shopify user and this cant be a sales manager!');
                }

            }
        }
        else{
            $user =  User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);
            /*Assigning User Role of Non-Shopify-User */
            $user->assignRole('sales-manager');
            $this->attach_manager_models($request, $user);

            return redirect()->route('sales-managers.index')->with('success', 'Manager Created Successfully');
        }
    }
    public function delete_manager(Request $request){
        $user = User::find($request->id);
        if($user != null){
            if($user->hasRole('non-shopify-users')){
                $user->removeRole('sales-manager');
                return redirect()->back()->with('success', 'Manager Deleted Successfully');
            }
            else{
                $user->removeRole('sales-manager');
                $this->detach_manager_models($user);
                $user->delete();
                return redirect()->back()->with('success', 'Manager Deleted Successfully');
            }
        }
        else{
            return redirect()->route('sales-managers.index');
        }
    }
    public function set_manager_as_user(Request $request){
        $user = User::find($request->id);
        $user->assignRole('non-shopify-users');
        return redirect()->back()->with('success', 'Manager Set as Non-Shopify User Successfully');
    }
    public function create_platform(Request $request){
        WarnedPlatform::create($request->all());
        return redirect()->back()->with('success', 'Platform Success Successfully');
    }
    public function update_platform(Request $request){
        WarnedPlatform::find($request->id)->update($request->all());
        return redirect()->back()->with('success', 'Platform Updated Successfully');
    }
    public function delete_platform(Request $request){
        WarnedPlatform::find($request->id)->delete();
        return redirect()->back()->with('success', 'Platform Deleted Successfully');
    }

    /**
     * @return mixed
     */
    public function get_non_shopify_users()
    {
        $users = User::role('non-shopify-users')->newQuery();
        $users->whereNotIn('email', ['admin@wefullfill.com', 'super_admin@wefullfill.com']);
        $users->whereDoesntHave('has_manager', function () {

        });
        $users = $users->get();
        return $users;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get_shops_for_managers()
    {
        $shops = Shop::query();
        $shops->whereNotIn('shopify_domain', ['wefullfill.myshopify.com', 'fantasy-supplier.myshopify.com']);
        $shops->whereDoesntHave('has_manager', function () {

        });
        $shops = $shops->get();
        return $shops;
    }

    /**
     * @param $user
     */
    public function detach_manager_models($user): void
    {
        foreach ($user->has_sales_stores as $shop) {
            $shop->sale_manager_id = null;
            $shop->save();
        }
        foreach ($user->has_users as $non) {
            $non->sale_manager_id = null;
            $non->save();
        }
    }

    /**
     * @param Request $request
     * @param $manager
     */
    public function attach_manager_models(Request $request, $manager): void
    {
        if ($request->has('stores')) {
            foreach ($request->input('stores') as $store) {
                $shop = Shop::find($store);
                $shop->sale_manager_id = $manager->id;
                $shop->save();
            }
        }
        if ($request->has('users')) {
            foreach ($request->input('users') as $u) {
                $non = User::find($u);
                $non->sale_manager_id = $manager->id;
                $non->save();
            }
        }
    }

    public function view_ticket_categories(Request $request){
        $categories = TicketCategory::query();
        return view('setttings.ticket_categories.index')->with([
            'categories' => $categories->orderBy('created_at','DESC')->get(),
        ]);
    }

    public function create_ticket_categories(Request $request){
        TicketCategory::create($request->all());
        return redirect()->back()->with('success','Ticket Category Created Successfully!');
    }
    public function update_ticket_categories(Request $request){
        TicketCategory::find($request->id)->update($request->all());
        return redirect()->back()->with('success','Ticket Category Updated Successfully!');
    }
    public function delete_ticket_categories(Request $request){
        TicketCategory::find($request->id)->delete();
        return redirect()->back()->with('success','Ticket Category Deleted Successfully!');
    }

    public function tickets(Request $request){
        $tickets = Ticket::query();
        if($request->has('search')){
            $tickets->where('title','LIKE','%'.$request->input('search').'%');
            $tickets->orwhere('email','LIKE','%'.$request->input('search').'%');
        }

        if($request->has('status')){
            if($request->has('more_status') && $request->input('more_status') != null)
                $tickets->where('status_id','=',$request->input('status'))->orWhere('status_id', '=',  $request->input('more_status'));
            elseif($request->input('status') != null)
                $tickets->where('status_id','=',$request->input('status'));
        }

        if($request->has('priority')){
            if($request->input('priority') != null) {
                $tickets->where('priority', '=', $request->input('priority'));
            }
        }
        $tickets = $tickets->orderBy('updated_at','DESC')->paginate(30);
        return view('setttings.tickets.index')->with([
            'tickets' => $tickets,
            'search' =>$request->input('search'),
            'statuses' => TicketStatus::all(),
            'selected_status' =>$request->input('status'),
            'priority' =>$request->input('priority'),
        ]);
    }
    public function ticket(Request $request){
        $ticket = Ticket::find($request->id);
        $manager = User::find($ticket->manager_id);
        return view('setttings.tickets.view')->with([
            'manager' => $manager,
            'ticket' => $ticket,
        ]);
    }

    public function wishlist(Request $request){
        $wishlist = Wishlist::query();
        if($request->has('search')){
            $wishlist->where('product_name','LIKE','%'.$request->input('search').'%');
            $wishlist->orwhere('description','LIKE','%'.$request->input('search').'%');
        }
        if($request->has('status')){
            if($request->input('status') != null){
                $wishlist->where('status_id','=',$request->input('status'));

            }
        }
        $wishlist = $wishlist->orderBy('created_at','DESC')->paginate(30);
        return view('setttings.wishlist.index')->with([
            'wishlist' => $wishlist,
            'search' =>$request->input('search'),
            'statuses' => WishlistStatus::all(),
            'selected_status' =>$request->input('status'),
        ]);
    }
    public function view_wishlist(Request $request){
        $wishlist = Wishlist::find($request->id);
        return view('setttings.wishlist.view')->with([
            'wishlist' => $wishlist,
            'products' => Product::all(),
        ]);
    }
//    public function stores(Request $request){
//        $sales_managers = User::role('sales-manager')->orderBy('created_at','DESC')->get();
//        $stores= Shop::query();
//        $stores = $stores->whereNotIn('shopify_domain', ['wefullfill.myshopify.com', 'fantasy-supplier.myshopify.com']);
//        if($request->has('search')){
//            $stores->where('shopify_domain','LIKE','%'.$request->input('search').'%');
//        }
//        $stores =  $stores->orderBy('created_at','DESC')->paginate(30);
//        return view('setttings.stores.index')->with([
//            'stores'=>$stores,
//            'managers' => $sales_managers,
//            'search' => $request->input('search'),
//        ]);
//
//
//    }

    public function stores(Request $request){

        // Non-shopify
        $sales_managers = User::role('sales-manager')->orderBy('created_at','DESC')->get();
        $users = User::role('non-shopify-users')->newQuery();
        $stores= Shop::query();

        $users->whereNotIn('email', ['admin@wefullfill.com', 'super_admin@wefullfill.com']);
        if($request->has('user_search')){

            $users->where('name','LIKE','%'.$request->input('user_search').'%');
            $users->orWhere('email','LIKE','%'.$request->input('user_search').'%');
        }
        if($request->has('status')) {
            if($request->input('status') == 'shopify') {
                $users->has('has_shops');
            }
            else {
                $users->doesnthave('has_shops');
            }
        }
        $users = $users->orderBy('created_at','DESC')->paginate(30);



        return view('setttings.users.new-index')->with([
            'users'=>$users,
            'managers' => $sales_managers,
            'user_search' => $request->input('user_search'),
            'status' => $request->input('status')
        ]);


    }

    public function store(Request $request){
        $store = Shop::find($request->id);
        if (count($store->has_user) > 0) {
            if ($store->has_user[0]->has_wallet == null) {
                $wallet = null;
            } else {
                $wallet = $store->has_user[0]->has_wallet;
            }
        } else {
            $wallet = null;
        }
        return view('setttings.stores.view')->with([
            'store' => $store,
            'wallet' => $wallet
        ]);
    }

    public function download_customer($id)
    {
        $customers = Customer::where('shop_id',$id)->orWhere('user_id',$id)->get();
        return Excel::download(new CustomersExport($customers), now()->format('m-d-y') . ' Customers' . '.csv');

    }

    public function customer_view($id){
        $customer = Customer::find($id);
        return view('setttings.customers.view')->with([
            'customer' => $customer,
        ]);
    }

    public function user(Request $request){
        $user = User::find($request->id);
        if ($user->has_wallet == null) {
            $wallet = null;
        } else {
            $wallet = $user->has_wallet;
        }
        return view('setttings.users.view')->with([
            'user' => $user,
            'wallet' => $wallet
        ]);
    }

    public function users(Request $request){
        $sales_managers = User::role('sales-manager')->orderBy('created_at','DESC')->get();
        $users = User::role('non-shopify-users')->newQuery();

        $users->whereNotIn('email', ['admin@wefullfill.com', 'super_admin@wefullfill.com']);
        if($request->has('search')){
            $users->where('name','LIKE','%'.$request->input('search').'%');
            $users->orWhere('email','LIKE','%'.$request->input('search').'%');
        }
        $users = $users->orderBy('created_at','DESC')->paginate(30);
        return view('setttings.users.index')->with([
            'users'=>$users,
            'managers' => $sales_managers,
            'search' => $request->input('search'),
        ]);
    }

    public function refunds(Request $request){
        $tickets = Refund::query();

        if($request->has('search')){
            $tickets->where('title','LIKE','%'.$request->input('search').'%');
        }
        if($request->has('status')){
            if($request->input('status') != null){
                $tickets->where('status_id','=',$request->input('status'));

            }
        }
        if($request->has('priority')){
            if($request->input('priority') != null) {
                $tickets->where('priority', '=', $request->input('priority'));
            }
        }
        $tickets->whereHas('has_order',function (){

        });

        $tickets = $tickets->paginate(30);
        return view('setttings.refunds.index')->with([
            'tickets' => $tickets,
            'search' =>$request->input('search'),
            'statuses' => TicketStatus::all(),
            'selected_status' =>$request->input('status'),
            'priority' =>$request->input('priority'),
        ]);
    }
    public function view_refund(Request $request){
        $manager = User::find(Auth::id());
        $ticket = Refund::find($request->id);
        if($ticket->has_order != null){
            return view('setttings.refunds.view')->with([
                'manager' => $manager,
                'ticket' => $ticket,
            ]);
        }
        else{
            return redirect()->route('refunds.index')->with('No Refund Found!');
        }

    }

    public function assign_manager(Request $request,$id){
        $manager = User::role('sales-manager')->find($request->input('sale_manager_id'));
        if($manager != null){
            if($request->input('type') == 'user'){
                $user = User::role('non-shopify-users')->find($id);
                $user->sale_manager_id = $manager->id;
                $user->save();

            }
            else{
                $shop = Shop::find($id);
                $shop->sale_manager_id = $manager->id;
                $shop->save();
            }
            return redirect()->back()->with('success','Manager Updated Successfully!');
        }
        else{
           return redirect()->back()->with('error','Manager Not Found!');
        }
    }

    public function save_tiered_pricing_preferences(Request $request) {
        $preferences = TieredPricingPrefrences::first();
        if($request->global == 1) {
            $preferences->global = 1;
        }
        else {
            $preferences->global = 0;
            if($request->shops) {
                $preferences->stores_id = json_encode($request->shops);
            }
            if($request->non_shopify_users) {
                $preferences->users_id = json_encode($request->non_shopify_users);
            }
        }

        $preferences->save();

        return redirect()->back()->with('success', 'Tiered Pricing Preferences Saved Successfully!');
    }

    public function save_general_discount_preferences(Request $request) {
        $preferences = GeneralDiscountPreferences::first();
        if($request->global == 1) {
            $preferences->global = 1;
        }
        else {
            $preferences->global = 0;
            if($request->shops) {
                $preferences->stores_id = json_encode($request->shops);
            }
            if($request->non_shopify_users) {
                $preferences->users_id = json_encode($request->non_shopify_users);
            }
        }

        $preferences->discount_amount = $request->discount;

        $preferences->save();

        return redirect()->back()->with('success', 'General Discount Preferences Saved Successfully!');
    }

    public function save_fixed_discount_preferences(Request $request) {
        $preferences = GeneralFixedPricePreferences::first();
        if($request->global == 1) {
            $preferences->global = 1;
        }
        else {
            $preferences->global = 0;
            if($request->shops) {
                $preferences->stores_id = json_encode($request->shops);
            }
            if($request->non_shopify_users) {
                $preferences->users_id = json_encode($request->non_shopify_users);
            }
        }

        $preferences->fixed_amount = $request->discount;

        $preferences->save();

        return redirect()->back()->with('success', 'General Fixed Price Preferences Saved Successfully!');
    }

    public function getTieredPricingPreferences()
    {
        $shops = \OhMyBrew\ShopifyApp\Models\Shop::whereNotIn('shopify_domain',['wefullfill.myshopify.com'])->get();
        $users = User::role('non-shopify-users')
            ->whereNotIn('email', ['admin@wefullfill.com', 'super_admin@wefullfill.com'])
            ->orderBy('created_at','DESC')
            ->get();

        return view('setttings.discounts.tiered')->with([
            'shops' => $shops,
            'non_shopify_users' => $users,
        ]);
    }

    public function getGeneralDiscountPreferences()
    {
        $shops = \OhMyBrew\ShopifyApp\Models\Shop::whereNotIn('shopify_domain',['wefullfill.myshopify.com'])->get();
        $users = User::role('non-shopify-users')
            ->whereNotIn('email', ['admin@wefullfill.com', 'super_admin@wefullfill.com'])
            ->orderBy('created_at','DESC')
            ->get();

        return view('setttings.discounts.general-discount')->with([
            'shops' => $shops,
            'non_shopify_users' => $users,
        ]);
    }

    public function campaigns() {
        $campaigns = Campaign::orderBy('updated_at', 'DESC')->paginate(20);

        return view('setttings.campaigns.index')->with('campaigns', $campaigns);
    }

    public function getCampaign(Request $request, $id) {

        $campaign = Campaign::find($id);
        $users = $campaign->users()->newQuery();

        if($request->has('status')) {
            if($request->input('status') == 'shopify') {
                $users->has('has_shops');
            }
            else {
                $users->doesnthave('has_shops');
            }
        }
        $users = $users->orderBy('created_at','DESC')->paginate(30);

        return view('setttings.campaigns.show')->with('campaign', $campaign)->with('users', $users)->with('status', $request->input('status'));
    }


    public function deleteCampaign($id) {
        $campaign = Campaign::find($id);
        $campaign->users()->detach();
        $campaign->delete();
        return redirect()->back()->with('success', 'Campaign Deleted Successfully!');
    }

    public function removeUserFromCampaign($id, $user_id) {
        $campaign = Campaign::find($id);
        $campaign->users()->detach($user_id);
        return redirect()->back()->with('success', 'User Removed From Campaign Successfully!');
    }


    public function submitCampaign($id) {
        $campaign = Campaign::find($id);
        $campaign->status = 'Published';
        $campaign->save();

        if($campaign->template_id == '18')
            dispatch(new SendNewsEmailJob($campaign))->delay(Carbon::parse($campaign->time));
        elseif($campaign->template_id == '20')
            dispatch(new SendNewsProductEmailjob($campaign))->delay(Carbon::parse($campaign->time));

        return redirect()->back()->with('success', 'Campaign Published Successfully!');
    }

    public function editCampaign($id) {
        $campaign = Campaign::find($id);
        $template = EmailTemplate::find($campaign->template_id);

        return view('setttings.campaigns.edit')->with('campaign', $campaign)->with('template', $template);
    }

    public function updateCampaign(Request $request, $id) {
        $campaign = Campaign::find($id);
        $template = EmailTemplate::find($campaign->template_id);

        $campaign->name = $request->campaign_name;
        $campaign->time = $request->time;
        $campaign->save();

        $template->subject = $request->subject;
        $template->body = $request->body;

        if($request->hasFile('banner')){
            $file = $request->file('banner');
            $name =now()->format('YmdHi') . str_replace([' ','(',')'], '-', $file->getClientOriginalName());
            $attachement = date("mmYhisa_") . $name;
            $file->move(public_path() . '/ticket-attachments/', $attachement);
            $template->banner = $attachement;
        }

        if($request->products) {
            $template->products = json_encode($request->products);
        }

        $template->save();

        return redirect()->back()->with('success', 'Campaign Updated Successfully!');
    }



}
