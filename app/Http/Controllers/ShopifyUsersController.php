<?php

namespace App\Http\Controllers;

use App\Country;
use App\Product;
use App\RetailerOrder;
use App\RetailerProduct;
use App\Shop;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;

class ShopifyUsersController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();

        if ($request->has('date-range')) {
            $date_range = explode('-',$request->input('date-range'));
            $start_date = $date_range[0];
            $end_date = $date_range[1];
            $comparing_start_date = Carbon::parse($start_date)->format('Y-m-d');
            $comparing_end_date = Carbon::parse($end_date)->format('Y-m-d');

            $orders = RetailerOrder::whereIN('paid',[1,2])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $sales = RetailerOrder::whereIN('paid',[1,2])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $refunds =  RetailerOrder::whereIN('paid',[2])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $profit = RetailerOrder::whereIN('paid',[1])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $cost = RetailerOrder::whereIN('paid',[1])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');


            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[1,2])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();


            $ordersQP = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[1])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();

            $ordersQR = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[2])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();





        } else {

            $orders = RetailerOrder::whereIN('paid',[1,2])->where('user_id',$user->id)->count();
            $sales = RetailerOrder::whereIN('paid',[1,2])->where('user_id',$user->id)->sum('cost_to_pay');
            $refunds = RetailerOrder::whereIN('paid',[2])->where('user_id',$user->id)->sum('cost_to_pay');
            $profit = RetailerOrder::whereIN('paid',[1])->where('user_id',$user->id)->sum('cost_to_pay');
            $cost = RetailerOrder::whereIN('paid',[1])->where('user_id',$user->id)->sum('cost_to_pay');

            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[1,2])
                ->groupBy('date')
                ->get();


            $ordersQP = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[1])
                ->groupBy('date')
                ->get();

            $ordersQR = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[2])
                ->groupBy('date')
                ->get();

        }


        $graph_one_order_dates = $ordersQ->pluck('date')->toArray();
        $graph_one_order_values = $ordersQ->pluck('total')->toArray();
        $graph_two_order_values = $ordersQ->pluck('total_sum')->toArray();

        $graph_four_order_dates = $ordersQP->pluck('date')->toArray();
        $graph_four_order_values = $ordersQP->pluck('total_sum')->toArray();

        $graph_three_order_dates = $ordersQR->pluck('date')->toArray();
        $graph_three_order_values = $ordersQR->pluck('total_sum')->toArray();


        $top_products =  Product::join('retailer_order_line_items',function($join) use ($user){
            $join->on('retailer_order_line_items.shopify_product_id','=','products.shopify_id')
                ->join('retailer_orders',function($o) use ($user){
                    $o->on('retailer_order_line_items.retailer_order_id','=','retailer_orders.id')
                        ->whereIn('paid',[1,2])
                    ->where('user_id',$user->id);
                });
        })->select('products.*',DB::raw('sum(retailer_order_line_items.quantity) as sold'),DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
            ->groupBy('products.id')
            ->orderBy('sold','DESC')
            ->get()
            ->take(10);



        return view('non_shopify_users.index')->with([
            'date_range' => $request->input('date-range'),
            'orders' => $orders,
            'profit' => $profit,
            'sales' =>$sales,
            'cost' =>$cost,
            'refunds' => $refunds,
            'graph_one_labels' => $graph_one_order_dates,
            'graph_one_values' => $graph_one_order_values,
            'graph_two_values' => $graph_two_order_values,
            'graph_three_labels' => $graph_three_order_dates,
            'graph_three_values' => $graph_three_order_values,
            'graph_four_values' => $graph_four_order_values,
            'graph_four_labels' => $graph_four_order_dates,
            'top_products' => $top_products,
        ]);

    }
    public function stores(){
        $shops = auth()->user()->has_shops;
        return view('non_shopify_users.stores')->with([
            'shops' => $shops
        ]);
    }

    public function setting(){

        $associated_user = Auth::user();
        return view('non_shopify_users.settings.index')->with([
            'associated_user' =>$associated_user,
            'countries' => Country::all()
        ]);
    }

    public function save_personal_info(Request $request){
        $user = User::find($request->input('user_id'));
        if($user != null){
            $user->name =  $request->input('name');
            $user->save();

            $this->validate($request, [
                'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            ]);

            if($request->hasFile('profile')){
                $file = $request->file('profile');
                $name = Str::slug($file->getClientOriginalName());
                $profile = date("mmYhisa_") . $name;
                $file->move(public_path() . '/managers-profiles/', $profile);
                $user->profile = $profile;
                $user->save();
            }
            return redirect()->back()->with('success','Personal Information Updated Successfully!');
        }
        else{
            return redirect()->back()->with('error','User Not Found!');
        }
    }
    public function save_address(Request $request){
        $user = User::find($request->input('user_id'));
        if($user != null){
            $user->address =  $request->input('address');
            $user->address2 =  $request->input('address2');
            $user->city =  $request->input('city');
            $user->state =  $request->input('state');
            $user->zip =  $request->input('zip');
            $user->country =  $request->input('country');
            $user->save();
            return redirect()->back()->with('success','Address Updated Successfully!');

        }
        else{
            return redirect()->back()->with('error','Manager Not Found!');
        }
    }

    public function change_password(Request $request){
        $manager = User::find($request->input('user_id'));
        if($manager != null){
            $array_to_check = [
                'email' => $manager->email,
                'password' =>$request->input('current_password')
            ];
            if(Auth::validate($array_to_check)){
                if($request->input('new_password') == $request->input('new_password_again')){
                    $manager->password = Hash::make($request->input('new_password'));
                    $manager->save();

                    return redirect()->back()->with('success','Password Changed Successfully!');

                }
                else{
                    return redirect()->back()->with('error','New Password Mismatched!');
                }
            }
            else{
                return redirect()->back()->with('error','Current Password is Invalid!');
            }

        }
        else{
            return redirect()->back()->with('error','User Not Found!');
        }
    }

    public function reports(Request $request){
        $user = Auth::user();

        if ($request->has('date-range')) {
            $date_range = explode('-',$request->input('date-range'));
            $start_date = $date_range[0];
            $end_date = $date_range[1];
            $comparing_start_date = Carbon::parse($start_date)->format('Y-m-d');
            $comparing_end_date = Carbon::parse($end_date)->format('Y-m-d');

            $orders = RetailerOrder::whereIN('paid',[1,2])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $sales = RetailerOrder::whereIN('paid',[1,2])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $refunds =  RetailerOrder::whereIN('paid',[2])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $profit = RetailerOrder::whereIN('paid',[1])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $cost = RetailerOrder::whereIN('paid',[1])->where('user_id',$user->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');


            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[1,2])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();


            $ordersQP = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[1])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();

            $ordersQR = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[2])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();





        } else {

            $orders = RetailerOrder::whereIN('paid',[1,2])->where('user_id',$user->id)->count();
            $sales = RetailerOrder::whereIN('paid',[1,2])->where('user_id',$user->id)->sum('cost_to_pay');
            $refunds = RetailerOrder::whereIN('paid',[2])->where('user_id',$user->id)->sum('cost_to_pay');
            $profit = RetailerOrder::whereIN('paid',[1])->where('user_id',$user->id)->sum('cost_to_pay');
            $cost = RetailerOrder::whereIN('paid',[1])->where('user_id',$user->id)->sum('cost_to_pay');

            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[1,2])
                ->groupBy('date')
                ->get();


            $ordersQP = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[1])
                ->groupBy('date')
                ->get();

            $ordersQR = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('user_id',$user->id)
                ->whereIn('paid',[2])
                ->groupBy('date')
                ->get();

        }


        $graph_one_order_dates = $ordersQ->pluck('date')->toArray();
        $graph_one_order_values = $ordersQ->pluck('total')->toArray();
        $graph_two_order_values = $ordersQ->pluck('total_sum')->toArray();

        $graph_four_order_dates = $ordersQP->pluck('date')->toArray();
        $graph_four_order_values = $ordersQP->pluck('total_sum')->toArray();

        $graph_three_order_dates = $ordersQR->pluck('date')->toArray();
        $graph_three_order_values = $ordersQR->pluck('total_sum')->toArray();


        $top_products =  Product::join('retailer_order_line_items',function($join) use ($user){
            $join->on('retailer_order_line_items.shopify_product_id','=','products.shopify_id')
                ->join('retailer_orders',function($o) use ($user){
                    $o->on('retailer_order_line_items.retailer_order_id','=','retailer_orders.id')
                        ->whereIn('paid',[1,2])
                        ->where('user_id',$user->id);
                });
        })->select('products.*',DB::raw('sum(retailer_order_line_items.quantity) as sold'),DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
            ->groupBy('products.id')
            ->orderBy('sold','DESC')
            ->get()
            ->take(10);


        $range = $request->input('date-range') ? $request->input('date-range') : 'beginning till now';

        return view('non_shopify_users.reports')->with([
            'date_range' => $range,
            'orders' => $orders,
            'profit' => $profit,
            'sales' =>$sales,
            'cost' =>$cost,
            'refunds' => $refunds,
            'graph_one_labels' => $graph_one_order_dates,
            'graph_one_values' => $graph_one_order_values,
            'graph_two_values' => $graph_two_order_values,
            'graph_three_labels' => $graph_three_order_dates,
            'graph_three_values' => $graph_three_order_values,
            'graph_four_values' => $graph_four_order_values,
            'graph_four_labels' => $graph_four_order_dates,
            'top_products' => $top_products,
            'user' => $user
        ]);

    }

    public function showVideosSection() {
        return view('videos.non-shopify');
    }

}
