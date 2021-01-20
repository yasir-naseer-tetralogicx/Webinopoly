<?php

namespace App\Http\Controllers;

use App\Category;
use App\Country;
use App\Customer;
use App\Mail\NewShopifyUserMail;
use App\Mail\NewUser;
use App\Mail\NewWallet;
use App\Mail\TopShopifyProuctMail;
use App\Notification;
use App\OrderTransaction;
use App\Product;
use App\Refund;
use App\RetailerImage;
use App\RetailerOrder;
use App\RetailerProduct;
use App\ShippingRate;
use App\Shop;
use App\Ticket;
use App\TicketCategory;
use App\User;
use App\WalletSetting;
use App\Wishlist;
use App\WishlistStatus;
use App\Zone;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use function foo\func;

class SingleStoreController extends Controller
{
    private $helper;

    /**
     * SingleStoreController constructor.
     * @param $helper
     */
    public function __construct()
    {
        $this->helper = new HelperController();
    }


    public function index(Request $request)
    {

        $shop = $this->helper->getLocalShop();

        if ($request->has('date-range')) {
            $date_range = explode('-', $request->input('date-range'));
            $start_date = $date_range[0];
            $end_date = $date_range[1];
            $comparing_start_date = Carbon::parse($start_date)->format('Y-m-d');
            $comparing_end_date = Carbon::parse($end_date)->format('Y-m-d');

            $orders = RetailerOrder::whereIN('paid', [1, 2])->where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $sales = RetailerOrder::whereIN('paid', [1, 2])->where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $products = RetailerProduct::where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $profit = RetailerOrder::whereIN('paid', [1])->where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $cost = RetailerOrder::whereIN('paid', [1])->where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');


            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('shop_id', $shop->id)
                ->whereIn('paid', [1, 2])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();


            $ordersQP = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('shop_id', $shop->id)
                ->whereIn('paid', [1])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();

            $productQ = DB::table('retailer_products')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->where('shop_id', $shop->id)
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();


        } else {

            $orders = RetailerOrder::whereIN('paid', [1, 2])->where('shop_id', $shop->id)->count();
            $sales = RetailerOrder::whereIN('paid', [1, 2])->where('shop_id', $shop->id)->sum('cost_to_pay');
            $products = RetailerProduct::where('shop_id', $shop->id)->count();
            $profit = RetailerOrder::whereIN('paid', [1])->where('shop_id', $shop->id)->sum('cost_to_pay');
            $cost = RetailerOrder::whereIN('paid', [1])->where('shop_id', $shop->id)->sum('cost_to_pay');

            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('shop_id', $shop->id)
                ->whereIn('paid', [1, 2])
                ->groupBy('date')
                ->get();


            $ordersQP = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('shop_id', $shop->id)
                ->whereIn('paid', [1])
                ->groupBy('date')
                ->get();

            $productQ = DB::table('retailer_products')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->where('shop_id', $shop->id)
                ->groupBy('date')
                ->get();

        }


        $graph_one_order_dates = $ordersQ->pluck('date')->toArray();
        $graph_one_order_values = $ordersQ->pluck('total')->toArray();
        $graph_two_order_values = $ordersQ->pluck('total_sum')->toArray();

        $graph_three_order_dates = $ordersQP->pluck('date')->toArray();
        $graph_three_order_values = $ordersQP->pluck('total_sum')->toArray();

        $graph_four_order_dates = $productQ->pluck('date')->toArray();
        $graph_four_order_values = $productQ->pluck('total')->toArray();


        $top_products = Product::join('retailer_products', function ($join) use ($shop) {
            $join->on('products.id', '=', 'retailer_products.linked_product_id')
                ->where('retailer_products.shop_id', '=', $shop->id)
                ->join('retailer_order_line_items', function ($j) {
                    $j->on('retailer_order_line_items.shopify_product_id', '=', 'retailer_products.shopify_id')
                        ->join('retailer_orders', function ($o) {
                            $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
                                ->whereIn('paid', [1, 2]);
                        });
                });
        })->select('products.*', DB::raw('sum(retailer_order_line_items.quantity) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
            ->groupBy('products.id')
            ->orderBy('sold', 'DESC')
            ->get()
            ->take(10);


        return view('single-store.dashboard')->with([
            'date_range' => $request->input('date-range'),
            'orders' => $orders,
            'profit' => $profit,
            'sales' => $sales,
            'cost' => $cost,
            'products' => $products,
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

    public function wefullfill_products(Request $request)
    {

        $country = $this->ip_info($this->getRealIpAddr(), 'Country');
        $categories = Category::all();
        $productQuery = Product::with('has_images', 'hasVariants','has_platforms','has_categories','has_subcategories')->where('status', 1)->newQuery();

        $productQuery->where('global', 0)->whereHas('has_preferences', function ($q) {
            return $q->where('shopify_domain', '=', $this->helper->getLocalShop()->shopify_domain);
        });

        $productQuery->orWhere('global', 1)->where('status', 1);

        if ($request->has('category')) {
            $productQuery->whereHas('has_categories', function ($q) use ($request) {
                return $q->where('title', 'LIKE', '%' . $request->input('category') . '%');
            });
        }
        if ($request->has('search')) {
            $productQuery->where('title', 'LIKE', '%' . $request->input('search') . '%')->orWhere('tags', 'LIKE', '%' . $request->input('search') . '%');
        }
        if ($request->has('tag')) {
            if ($request->input('tag') == 'best-seller') {
                $products = $productQuery->where('sortBy', 'Best Seller')->paginate(12);
//                $productQuery =  Product::join('retailer_products',function($join) {
//                    $join->on('retailer_products.linked_product_id', '=', 'products.id')
//                        ->join('retailer_order_line_items', function ($join) {
//                            $join->on('retailer_order_line_items.shopify_product_id', '=', 'retailer_products.shopify_id')
//                                ->join('retailer_orders', function ($o) {
//                                    $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
//                                        ->whereIn('paid', [1, 2]);
//                                });
//                        });
//                })->select('products.*',DB::raw('sum(retailer_order_line_items.quantity) as sold'),DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
//                    ->groupBy('products.id')
//                    ->orderBy('sold','DESC');
//                $products = $productQuery->paginate(12);
            } else if ($request->input('tag') == 'winning-products') {
//                $products = $productQuery->where('tags','LIKE','%'.$request->input('tag').'%')->paginate(12);
                $products = $productQuery->where('sortBy', 'Winning Product')->paginate(12);
            } else {
                $products = $productQuery->where('processing_time', '24 Hours')->paginate(12);
            }
        }
        if ($request->has('filter')) {
            if ($request->input('filter') == 'most-order') {

                $productQuery = Product::join('retailer_products', function ($join) {
                    $join->on('retailer_products.linked_product_id', '=', 'products.id')
                        ->join('retailer_order_line_items', function ($join) {
                            $join->on('retailer_order_line_items.shopify_product_id', '=', 'retailer_products.shopify_id')
                                ->join('retailer_orders', function ($o) {
                                    $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
                                        ->whereIn('paid', [1, 2]);
                                });
                        });
                })->select('products.*', DB::raw('sum(retailer_order_line_items.quantity) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
                    ->groupBy('products.id')
                    ->orderBy('sold', 'DESC');

                $products = $productQuery->paginate(12);

            } elseif ($request->input('filter') == 'most-imported') {
                $products = $productQuery->withCount(['has_imported'])->orderBy('has_imported_count', 'DESC')->paginate(12);
            } elseif ($request->input('filter') == 'new-arrival') {
                $products = $productQuery->orderBy('created_at', 'DESC')->paginate(12);
            }
        } else {
            $products = $productQuery->latest()->paginate(12);
        }


        foreach ($products as $product) {
            $total_weight = $product->weight;
            $zoneQuery = Zone::query();
            $zoneQuery->whereHas('has_countries', function ($q) use ($country) {
                $q->where('name', 'LIKE', '%' . $country . '%');
            });
            $zoneQuery = $zoneQuery->pluck('id')->toArray();

            $shipping_rates = ShippingRate::whereIn('zone_id', $zoneQuery)->newQuery();
            $shipping_rates = $shipping_rates->first();
            if ($shipping_rates != null) {
                if ($shipping_rates->shipping_price > 0) {
                    if ($shipping_rates->type == 'flat') {
                        $product->new_shipping_price = '$' . number_format($shipping_rates->shipping_price, 2);
                    } else {
                        if ($shipping_rates->min > 0) {
                            $ratio = $total_weight / $shipping_rates->min;
                            $product->new_shipping_price = '$' . number_format($shipping_rates->shipping_price * $ratio, 2);
                        } else {
                            $product->new_shipping_price = 'Free Shipping';
                        }
                    }
                } else {
                    $product->new_shipping_price = 'Free Shipping';
                }
            } else {
                $product->new_shipping_price = 'Free Shipping';

            }

        }

        $shop = $this->helper->getLocalShop();
        return view('single-store.products.wefullfill_products')->with([
            'categories' => $categories,
            'products' => $products,
            'shop' => $shop,
            'search' => $request->input('search'),
            'filter' => $request->input('filter')

        ]);
    }

    public function view_fantasy_product($id)
    {
        $product = Product::find($id);
        $shop = $this->helper->getLocalShop();
        return view('single-store.products.view_product')->with([
            'product' => $product,
            'shop' => $shop
        ]);
    }

    public function view_my_product($id)
    {
        $product = RetailerProduct::find($id);
        $shop = $this->helper->getLocalShop();
        return view('single-store.products.view_product')->with([
            'product' => $product,
            'shop' => $shop
        ]);
    }

    public function setting()
    {
        /*Ossiset Shop Model*/
        $shop = ShopifyApp::shop();
        /*Local Shop Model!*/
        $shop = Shop::find($shop->id);
        $user = $shop->has_user->first();

        if (count($shop->has_user) > 0) {
            $associated_user = $shop->has_user[0];
        } else {
            $associated_user = null;
        }
        return view('single-store.index')->with([
            'shop' => $shop,
            'user' => $user,
            'associated_user' => $associated_user,
            'countries' => Country::all()
        ]);
    }

    public function save_personal_info(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if ($user != null) {
            $user->name = $request->input('name');
            $user->save();
            if ($request->hasFile('profile')) {
                $file = $request->file('profile');
                $name = Str::slug($file->getClientOriginalName());
                $profile = date("mmYhisa_") . $name;
                $file->move(public_path() . '/managers-profiles/', $profile);
                $user->profile = $profile;
                $user->save();
            }
            return redirect()->back()->with('success', 'Personal Information Updated Successfully!');
        } else {
            return redirect()->back()->with('error', 'User Not Found!');
        }
    }

    public function save_address(Request $request)
    {
        $user = User::find($request->input('user_id'));
        if ($user != null) {
            $user->address = $request->input('address');
            $user->address2 = $request->input('address2');
            $user->city = $request->input('city');
            $user->state = $request->input('state');
            $user->zip = $request->input('zip');
            $user->country = $request->input('country');
            $user->save();
            return redirect()->back()->with('success', 'Address Updated Successfully!');

        } else {
            return redirect()->back()->with('error', 'Manager Not Found!');
        }
    }


    public function authenticate(Request $request)
    {
        if (Auth::validate($request->except('_token'))) {
            $authenticate = true;
        } else {
            $authenticate = false;
        }
        return response()->json([
            'authenticate' => $authenticate
        ]);
    }

    public function associate(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        $shop = Shop::where('shopify_domain', $request->input('store'))->first();
        if ($user != null && $shop != null) {
            if (!in_array($shop->id, $user->has_shops->pluck('id')->toArray())) {
                $user->has_shops()->attach([$shop->id]);

                // Sending Welcome Email
                try{
                    Mail::to($user->email)->send(new NewShopifyUserMail($user));
                }
                catch (\Exception $e){
                }

                // Sending Top Product Recommendation Email
                try{
                    Mail::to($user->email)->send(new TopShopifyProuctMail($user));
                }
                catch (\Exception $e){
                }

                // Sync To SendGrid WefullFill Members Contact List
                $contacts = [];
                array_push($contacts, [
                    'email' => $user->email,
                    'first_name' => $user->name,
                ]);
                $contacts_payload = [
                    'list_ids' => ["33d743f3-a906-4512-83cd-001f7ba5ab33"],
                    'contacts' => $contacts
                ];

                $payload = json_encode($contacts_payload);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.sendgrid.com/v3/marketing/contacts",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "PUT",
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer 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",
                        "content-type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);



                return response()->json([
                    'status' => 'assigned'
                ]);
            } else {
                return response()->json([
                    'status' => 'already-assigned'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }

    public function de_associate(Request $request)
    {
        $shop = Shop::find($request->id);
        $user = Auth::user();
        $user->has_shops()->detach([$shop->id]);
        return redirect()->back()->with('success', 'Store Removed Successfully!');
    }


    public function customers(Request $request)
    {
        $customersQ = Customer::where('shop_id', $this->helper->getShop()->id)->newQuery();
        $customers = $customersQ->paginate(30);
        return view('single-store.customers.index')->with([
            'customers' => $customers,
        ]);
    }

    public function customer_view($id)
    {
        $customer = Customer::find($id);
        return view('single-store.customers.view')->with([
            'customer' => $customer,
        ]);
    }

    public function getCustomers()
    {
        $shop = $this->helper->getShop();
        $response = $shop->api()->rest('GET', '/admin/api/2019-10/customers.json');
        if ($response->errors) {
            return redirect()->back();
        } else {
            $customers = $response->body->customers;
            foreach ($customers as $index => $customer) {
                if (Customer::where('customer_shopify_id', $customer->id)->exists()) {
                    $new_customer = Customer::where('customer_shopify_id', $customer->id)->first();
                } else {
                    $new_customer = new Customer();
                }
                $new_customer->customer_shopify_id = $customer->id;
                $new_customer->first_name = $customer->first_name;
                $new_customer->last_name = $customer->last_name;
                $new_customer->phone = $customer->phone;
                $new_customer->email = $customer->email;
                $new_customer->total_spent = $customer->total_spent;
                $new_customer->shop_id = $shop->id;
                $local_shop = $this->helper->getLocalShop();
                if (count($local_shop->has_user) > 0) {
                    $new_customer->user_id = $local_shop->has_user[0]->id;
                }
                $new_customer->save();
            }
            return redirect()->back()->with('success', 'Customers Synced Successfully!');
        }

    }

    public function payment_history(Request $request)
    {
        $shop = $this->helper->getLocalShop();
        $payments = OrderTransaction::where('shop_id', $shop->id)->newQuery();
        return view('single-store.orders.payment_history')->with([
            'payments' => $payments->orderBy('created_at')->paginate(20),
        ]);
    }

    public function tracking_info(Request $request)
    {
        $shop = $this->helper->getLocalShop();
        $orders = RetailerOrder::where('shop_id', $shop->id)->newQuery();
        if ($request->has('search')) {
            $orders->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }
        return view('single-store.orders.tracking_info')->with([
            'orders' => $orders->orderBy('created_at')->paginate(20),
            'search' => $request->input('search')

        ]);
    }

    public function helpcenter(Request $request)
    {
        $shop = $this->helper->getLocalShop();
        $user = $shop->has_user()->first();
        $tickets = Ticket::where('user_id', $user->id)->where('source', 'store')->newQuery();
        $orders = $shop->has_orders()->get();
        $tickets = $tickets->paginate(30);

        return view('single-store.help-center.index')->with([
            'shop' => $shop,
            'tickets' => $tickets,
            'orders' => $orders,
            'categories' => TicketCategory::all(),
        ]);
    }

    public function wishlist(Request $request)
    {
        $shop = $this->helper->getLocalShop();
        $user = $shop->has_user()->first();


        if($request->has('search')){
            $wishlist = Wishlist::where('product_name','LIKE','%'.$request->input('search').'%')
                ->orwhere('description','LIKE','%'.$request->input('search').'%')
                ->where('user_id', $user->id)
//                        ->orWhere('shop_id', $shop->id)
                ->orderBy('created_at','DESC')
                ->paginate(30);

            return view('single-store.wishlist.index')->with([
                'shop' => $shop,
                'wishlist' => $wishlist,
                'statuses' => WishlistStatus::all(),
                'selected_status' =>$request->input('status'),
                'countries' => Country::all(),
            ]);

        }

        if($request->has('status')){
            if($request->input('status') != null){
                $wishlist = Wishlist::where('status_id','=',$request->input('status'))
                        ->where('user_id', $user->id)
//                        ->orWhere('shop_id', $shop->id)
                        ->orderBy('created_at','DESC')
                        ->paginate(30);

            }

            return view('single-store.wishlist.index')->with([
                'shop' => $shop,
                'wishlist' => $wishlist,
                'statuses' => WishlistStatus::all(),
                'selected_status' =>$request->input('status'),
                'countries' => Country::all(),
            ]);
        }

        $wishlist = Wishlist::where('user_id', $user->id)
            ->orWhere('shop_id', $shop->id)
            ->orderBy('created_at','DESC')
            ->paginate(30);

        return view('single-store.wishlist.index')->with([
            'shop' => $shop,
            'wishlist' => $wishlist,
            'statuses' => WishlistStatus::all(),
            'selected_status' =>$request->input('status'),
            'countries' => Country::all(),
        ]);
    }

    public function view_wishlist(Request $request)
    {
        $shop = $this->helper->getLocalShop();
        $wishlist = Wishlist::find($request->id);
        return view('single-store.wishlist.view')->with([
            'shop' => $shop,
            'wishlist' => $wishlist,
        ]);
    }

    public function view_ticket(Request $request)
    {
        $shop = $this->helper->getLocalShop();
        $ticket = Ticket::find($request->id);
        return view('single-store.help-center.view')->with([
            'shop' => $shop,
            'ticket' => $ticket,
        ]);
    }

    public function calculate_shipping(Request $request)
    {

        if ($request->has('country')) {
            $country = $request->input('country');
        } else {
            $country = "United States";
        }

        $product = Product::find($request->input('product'));
        if ($product != null) {
            $total_weight = $product->weight;
        } else {
            $total_weight = 0;
        }


        $zoneQuery = Zone::query();
        $zoneQuery->whereHas('has_countries', function ($q) use ($country) {
            $q->where('name', 'LIKE', '%' . $country . '%');
        });
        $zoneQuery = $zoneQuery->pluck('id')->toArray();

        $shipping_rates = ShippingRate::whereIn('zone_id', $zoneQuery)->newQuery();

        $shipping_rates = $shipping_rates->get();

        foreach ($shipping_rates as $shipping_rate) {
            if ($shipping_rate->min > 0) {
                if ($shipping_rate->type == 'flat') {

                } else {
                    $ratio = $total_weight / $shipping_rate->min;
                    $shipping_rate->shipping_price = $shipping_rate->shipping_price * $ratio;
                }

            } else {
                $ratio = 0;
                $shipping_rate->shipping_price = $shipping_rate->shipping_price * $ratio;
            }

        }

        $html = view('inc.calculate_shipping')->with([
            'countries' => Country::all(),
            'selected' => $country,
            'rates' => $shipping_rates,
            'product' => $request->input('product')
        ])->render();

        return response()->json([
            'html' => $html
        ]);


    }

    public function refunds(Request $request)
    {
        $shop = $this->helper->getLocalShop();
        $refunds = Refund::where('shop_id', $shop->id)->newQuery();
        if ($request->has('search')) {
            $refunds->where('order_name', 'LIKE', '%' . $request->input('search') . '%');
        }
        $refunds->whereHas('has_order', function () {

        });
        $orders = RetailerOrder::where('shop_id', $shop->id)->where('paid', 1)->get();
        return view('single-store.orders.refunds')->with([
            'refunds' => $refunds->orderBy('created_at')->paginate(20),
            'search' => $request->input('search'),
            'shop' => $shop,
            'orders' => $orders
        ]);
    }

    public function refund(Request $request)
    {
        $shop = $this->helper->getLocalShop();
        $refund = Refund::find($request->id);
        if ($refund->has_order != null) {
            return view('single-store.orders.view-refund')->with([
                'shop' => $shop,
                'ticket' => $refund,
            ]);
        } else {
            return redirect()->route('store.refunds')->with('No Refund Found!');

        }
    }

    public function show_notification($id)
    {
        $notification = Notification::find($id);
        if ($notification != null) {
            $notification->read = 1;
            $notification->save();
            if ($notification->type == 'Product') {
                return redirect()->route('store.product.wefulfill.show', $notification->type_id);
            } elseif ($notification->type == 'Order') {
                return redirect()->route('store.order.view', $notification->type_id);

            } elseif ($notification->type == 'Refund') {
                return redirect()->route('store.refund', $notification->type_id);

            } elseif ($notification->type == 'Wish-list') {
                return redirect()->route('store.wishlist.view', $notification->type_id);

            } elseif ($notification->type == 'Ticket') {
                return redirect()->route('help-center.store.ticket.view', $notification->type_id);

            } elseif ($notification->type == 'Wallet') {
                return redirect()->route('store.user.wallet.show');

            }

        }
    }

    public function notifications()
    {
        $query = Notification::query();
        $auth_shop = ShopifyApp::shop();
        if ($auth_shop != null) {

            $shop = Shop::find($auth_shop->id);
            $query->whereHas('to_shops', function ($q) use ($shop) {
                $q->where('shopify_domain', $shop->shopify_domain);
            });
            if (count($shop->has_user) > 0) {
                $user = $shop->has_user[0];
                $query->orwhereHas('to_users', function ($q) use ($user) {
                    $q->where('email', $user->email);
                });

            }

        }
        $notifications = $query->orderBy('created_at', 'DESC')->paginate(30);
        return view('single-store.notifications.index')->with([
            'notifications' => $notifications
        ]);

    }

    function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE)
    {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city" => @$ipdat->geoplugin_city,
                            "state" => @$ipdat->geoplugin_regionName,
                            "country" => @$ipdat->geoplugin_countryName,
                            "country_code" => @$ipdat->geoplugin_countryCode,
                            "continent" => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }

    function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // Check IP from internet.
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Check IP is passed from proxy.
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Get IP address from remote address.
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function reports(Request $request) {

        $shop = $this->helper->getLocalShop();
        $shop_user = Shop::find($shop->id);
        $shop_user = $shop_user->has_user()->first();
        $shop_user = $shop_user->name;

        if ($request->has('date-range')) {
            $date_range = explode('-', $request->input('date-range'));
            $start_date = $date_range[0];
            $end_date = $date_range[1];
            $comparing_start_date = Carbon::parse($start_date)->format('Y-m-d');
            $comparing_end_date = Carbon::parse($end_date)->format('Y-m-d');

            $orders = RetailerOrder::whereIN('paid', [1, 2])->where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $sales = RetailerOrder::whereIN('paid', [1, 2])->where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $products = RetailerProduct::where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $profit = RetailerOrder::whereIN('paid', [1])->where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $cost = RetailerOrder::whereIN('paid', [1])->where('shop_id', $shop->id)->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');


            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('shop_id', $shop->id)
                ->whereIn('paid', [1, 2])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();


            $ordersQP = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('shop_id', $shop->id)
                ->whereIn('paid', [1])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();

            $productQ = DB::table('retailer_products')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->where('shop_id', $shop->id)
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();


        } else {

            $orders = RetailerOrder::whereIN('paid', [1, 2])->where('shop_id', $shop->id)->count();
            $sales = RetailerOrder::whereIN('paid', [1, 2])->where('shop_id', $shop->id)->sum('cost_to_pay');
            $products = RetailerProduct::where('shop_id', $shop->id)->count();
            $profit = RetailerOrder::whereIN('paid', [1])->where('shop_id', $shop->id)->sum('cost_to_pay');
            $cost = RetailerOrder::whereIN('paid', [1])->where('shop_id', $shop->id)->sum('cost_to_pay');

            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('shop_id', $shop->id)
                ->whereIn('paid', [1, 2])
                ->groupBy('date')
                ->get();


            $ordersQP = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->where('shop_id', $shop->id)
                ->whereIn('paid', [1])
                ->groupBy('date')
                ->get();

            $productQ = DB::table('retailer_products')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->where('shop_id', $shop->id)
                ->groupBy('date')
                ->get();

        }


        $graph_one_order_dates = $ordersQ->pluck('date')->toArray();
        $graph_one_order_values = $ordersQ->pluck('total')->toArray();
        $graph_two_order_values = $ordersQ->pluck('total_sum')->toArray();

        $graph_three_order_dates = $ordersQP->pluck('date')->toArray();
        $graph_three_order_values = $ordersQP->pluck('total_sum')->toArray();

        $graph_four_order_dates = $productQ->pluck('date')->toArray();
        $graph_four_order_values = $productQ->pluck('total')->toArray();


        $top_products = Product::join('retailer_products', function ($join) use ($shop) {
            $join->on('products.id', '=', 'retailer_products.linked_product_id')
                ->where('retailer_products.shop_id', '=', $shop->id)
                ->join('retailer_order_line_items', function ($j) {
                    $j->on('retailer_order_line_items.shopify_product_id', '=', 'retailer_products.shopify_id')
                        ->join('retailer_orders', function ($o) {
                            $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
                                ->whereIn('paid', [1, 2]);
                        });
                });
        })->select('products.*', DB::raw('sum(retailer_order_line_items.quantity) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
            ->groupBy('products.id')
            ->orderBy('sold', 'DESC')
            ->get()
            ->take(10);

        $range = $request->input('date-range') ? $request->input('date-range') : 'beginning till now';

        return view('single-store.reports')->with([
            'date_range' => $range,
            'orders' => $orders,
            'profit' => $profit,
            'sales' => $sales,
            'cost' => $cost,
            'products' => $products,
            'graph_one_labels' => $graph_one_order_dates,
            'graph_one_values' => $graph_one_order_values,
            'graph_two_values' => $graph_two_order_values,
            'graph_three_labels' => $graph_three_order_dates,
            'graph_three_values' => $graph_three_order_values,
            'graph_four_values' => $graph_four_order_values,
            'graph_four_labels' => $graph_four_order_dates,
            'top_products' => $top_products,
            'shop' => $shop_user,
        ]);

    }

    public function showVideosSection() {
        return view('videos.shopify');
    }

    public function saveWalletSettings(Request $request, $id) {
        WalletSetting::updateOrCreate(
            [ 'user_id' => $id ],
            [ 'enable' => $request->status ]
        );
    }




}
