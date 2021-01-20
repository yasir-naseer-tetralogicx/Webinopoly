<?php

namespace App\Http\Controllers;

use App\AdminSetting;
use App\Category;
use App\Country;
use App\Customer;
use App\Exports\ProcessedOrder;
use App\Exports\RetailerOrderExport;
use App\Exports\UnprocessedOrder;
use App\Imports\UsersImport;
use App\Notification;
use App\OrderLog;
use App\OrderTransaction;
use App\Product;
use App\ProductVariant;
use App\Refund;
use App\RetailerOrder;
use App\RetailerOrderLineItem;
use App\RetailerProduct;
use App\RetailerProductVariant;
use App\ShippingRate;
use App\Ticket;
use App\TicketCategory;
use App\User;
use App\UserFile;
use App\UserFileTemp;
use App\WalletLog;
use App\Wishlist;
use App\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Srmklive\PayPal\Services\ExpressCheckout;

class CustomOrderController extends Controller
{
    private $admin;
    private $notify;
    private $inventory;

    /**
     * CustomOrderController constructor.
     * @param $admin
     */
    public function __construct()
    {
        $this->admin = new AdminMaintainerController();
        $this->notify = new NotificationController();
        $this->inventory = new InventoryController();
    }

    public function index(Request $request)
    {
        $orders = RetailerOrder::where('user_id', Auth::id())->where('custom', 1)->newQuery();
        if ($request->has('search')) {
            $orders->where('name', 'LIKE', '%' . $request->input('search') . '%');

        }
        $orders = $orders->orderBy('created_at', 'DESC')->paginate(30);
        return view('non_shopify_users.orders.index')->with([
            'orders' => $orders,
            'search' => $request->input('search')

        ]);
    }

    public function show_create_form()
    {
        $products = Product::query();
//        $products->whereHas('hasVariants',function (){
//
//        });

        $customers = Customer::where('user_id', Auth::id())->get();
        $setting = AdminSetting::all()->first();

        return view('non_shopify_users.orders.create')->with([
            'products' => $products->get(),
            'customers' => $customers,
            'countries' => Country::all(),
            'setting' => $setting
        ]);
    }

    public function getShippingRate(Request $request)
    {
        if ($request->input('variant_selection') != '0') {
            $total_weight = 0;
            $country = $request->input('country');
            if ($request->has('line_items')) {
                foreach ($request->input('line_items') as $index => $item) {
                    $v = ProductVariant::find($item);
                    if ($v->linked_product != null) {
                        $total_weight = $total_weight + ($v->linked_product->weight * $request->input('quantity')[$index]);
                    }
                }
            }

            if ($request->has('single_variant_line_items')) {
                foreach ($request->input('single_variant_line_items') as $index => $item) {
                    $v = Product::find($item);
                    if ($v != null) {
                        $total_weight = $total_weight + ($v->weight * $request->input('single_quantity')[$index]);
                    }
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
                    $rate = $shipping_rates->shipping_price;
                } else {
                    if ($shipping_rates->min > 0) {
                        $ratio = $total_weight / $shipping_rates->min;
                        $rate = $shipping_rates->shipping_price * $ratio;
                    } else {
                        $rate = 0;
                    }
                }

            } else {
                $rate = 0;
            }

            return response()->json([
                'rate' => $rate,
                'message' => 'success'
            ]);

        }
    }

    public function find_products(Request $request)
    {
        $products = Product::query();
        if ($request->has('search')) {
            $products->where('title', 'LIKE', '%' . $request->input('search') . '%');
            $products->orWhereHas('hasVariants', function ($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->input('search') . '%');
                $q->orwhere('sku', 'LIKE', '%' . $request->input('search') . '%');
            });
        }
        $html = view('non_shopify_users.orders.product-browse-section')->with([
            'products' => $products->get(),
        ])->render();

        return response()->json([
            'html' => $html
        ]);

    }

    public function get_selected_variants(Request $request)
    {

        if ($request->has('variants')) {
            $selectedVaraints = ProductVariant::whereIn('id', $request->input('variants'))->get();
        } else {
            $selectedVaraints = [];
        }

        if ($request->has('single_variants')) {
            $selectedSingleVariants = Product::whereIn('id', $request->input('single_variants'))->get();
        } else {
            $selectedSingleVariants = [];

        }


        $total_cost = 0;
        foreach ($selectedVaraints as $varaint) {
            $total_cost = $total_cost + $varaint->price;
        }

        foreach ($selectedSingleVariants as $varaint) {
            $total_cost = $total_cost + $varaint->price;
        }

        $html = view('non_shopify_users.orders.selected-varaint-section')->with([
            'single_variants' => $selectedSingleVariants,
            'variants' => $selectedVaraints,
            'total' => $total_cost
        ])->render();

        return response()->json([
            'html' => $html
        ]);

    }

    public function save_draft_order(Request $request)
    {

        $count = RetailerOrder::all()->count();
        $new = new RetailerOrder();
        $new->email = $request->input('email');
        $count = $count + 1;
        $new->name = '#WF' . $count;

        $new->taxes_included = '0';
        $new->total_tax = '0';
        $new->currency = 'USD';
        $new->total_discounts = '0';


        if (Customer::where('email', $request->input('email'))->exists()) {
            $customer = Customer::where('email', $request->input('email'))->first();
            $new->customer_id = $customer->id;
        } else {
            $customer = new Customer();
            $customer->first_name = $request->input('c_first_name');
            $customer->last_name = $request->input('c_last_name');
            $customer->email = $request->input('email');
            $customer->user_id = Auth::id();
            $customer->save();
            $new->customer_id = $customer->id;
        }

        $new->shipping_address = json_encode($request->except(['line_items', 'quantity', 'c_first_name', 'c_last_name', '_token', 'email']), true);
        $new->status = 'new';
        $new->user_id = Auth::id();
        $new->fulfilled_by = 'fantasy';
        $new->sync_status = 1;

        $new->shopify_created_at = date_create($request->input('order_date'))->format('Y-m-d h:i:s');
        $new->shopify_updated_at = date_create($request->input('order_date'))->format('Y-m-d h:i:s');

        $new->save();

        $cost_to_pay = 0;
        $total_weight = 0;


        if ($request->has('line_items')) {
            foreach ($request->input('line_items') as $index => $item) {
                $variant = ProductVariant::find($item);
                if ($variant != null) {
                    $new_line = new RetailerOrderLineItem();
                    $new_line->retailer_order_id = $new->id;
                    $new_line->shopify_product_id = $variant->linked_product->shopify_id;
                    $new_line->shopify_variant_id = $variant->shopify_id;
                    $new_line->title = $variant->linked_product->title;
                    $new_line->quantity = $request->input('quantity')[$index];
                    $new_line->sku = $variant->sku;
                    $new_line->variant_title = $variant->variant_title;
                    $new_line->title = $variant->title;
                    $new_line->vendor = $variant->linked_product->title;
                    $new_line->price = $variant->price;
                    $new_line->requires_shipping = 'true';
                    $new_line->name = $variant->linked_product->title . ' - ' . $variant->title;
                    $new_line->fulfillable_quantity = $request->input('quantity')[$index];
                    $new_line->fulfilled_by = 'Fantasy';
                    $new_line->cost = $variant->price;
                    $cost_to_pay = $cost_to_pay + $variant->price * $request->input('quantity')[$index];
                    $total_weight = $total_weight + $variant->linked_product->weight;
                    $new_line->save();
                }

            }
        }

        if ($request->has('single_variant_line_items')) {
            foreach ($request->input('single_variant_line_items') as $index => $item) {
                $variant = Product::find($item);
                if ($variant != null) {
                    $new_line = new RetailerOrderLineItem();
                    $new_line->retailer_order_id = $new->id;
                    $new_line->shopify_product_id = $variant->shopify_id;

                    $new_line->title = $variant->title;
                    $new_line->quantity = $request->input('single_quantity')[$index];
                    $new_line->sku = $variant->sku;
                    $new_line->vendor = $variant->vendor;
                    $new_line->price = $variant->price;
                    $new_line->requires_shipping = 'true';
                    $new_line->name = $variant->title;
                    $new_line->fulfillable_quantity = $request->input('single_quantity')[$index];
                    $new_line->fulfilled_by = 'Fantasy';
                    $new_line->cost = $variant->price;
                    $cost_to_pay = $cost_to_pay + $variant->price * $request->input('single_quantity')[$index];
                    $total_weight = $total_weight + $variant->weight;
                    $new_line->save();
                }

            }
        }


        $new->subtotal_price = $cost_to_pay;
        $new->shipping_price = $request->input('shipping_price');
        $total_cost = $cost_to_pay + $request->input('shipping_price');
        $new->total_weight = $total_weight;
        $new->total_price = $total_cost;
        $new->cost_to_pay = $total_cost;
        $new->custom = 1;
        $new->save();


        /*Maintaining Log*/
        $order_log = new OrderLog();
        $order_log->message = "Custom Order Created to WeFullFill on " . date_create($new->created_at)->format('d M, Y h:i a');
        $order_log->status = "Newly Synced";
        $order_log->retailer_order_id = $new->id;
        $order_log->save();

        $settings = AdminSetting::all()->first();

        if ($request->input('payment-option') == 'draft') {
            $response = [
                'status' => 'success',
                'redirect_url' => route('users.order.view', $new->id),
                'payment' => $request->input('payment-option')
            ];
//            return redirect()->route('users.order.view',$new->id)->with('success','Custom Order Created Successfully');
        } elseif ($request->input('payment-option') == 'paypal') {
//            return redirect()->route('store.order.paypal.pay',$new->id);
            $response = [
                'status' => 'success',
                'popup' => view('non_shopify_users.orders.inc_popup')->with([
                    'order' => $new,
                    'settings' => $settings
                ])->render(),
                'form' => view('non_shopify_users.orders.inc_form')->with('order', $new)->render(),
                'cost' => number_format($new->cost_to_pay + ($new->cost_to_pay * $settings->paypal_percentage / 100), 2),
                'redirect_url' => route('users.order.view', $new->id),
                'payment' => $request->input('payment-option')
            ];
        } else {
//            return redirect()->route('store.order.wallet.pay',$new->id);
            $response = [
                'status' => 'success',
                'redirect_url' => route('store.order.wallet.pay', $new->id),
                'payment' => $request->input('payment-option')
            ];
        }

        return response()->json($response);

    }

    public function view_order($id)
    {
        $order = RetailerOrder::find($id);
        $settings = AdminSetting::all()->first();
        $user = User::find(Auth::id());
        if ($order != null) {
            return view('non_shopify_users.orders.view')->with([
                'order' => $order,
                'settings' => $settings,
                'user' => $user
            ]);
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

    public function wefullfill_products(Request $request)
    {
        $country = $this->ip_info($this->getRealIpAddr(), 'Country');

        $categories = Category::all();
        $productQuery = Product::where('status', 1)->newQuery();

        $productQuery->where('global', 0)->whereHas('has_non_shopify_user_preferences', function ($q) {
            return $q->where('user_id', '=', Auth::user()->id);
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
                $productQuery = Product::join('retailer_order_line_items', function ($join) {
                    $join->on('retailer_order_line_items.shopify_product_id', '=', 'products.shopify_id')
                        ->join('retailer_orders', function ($o) {
                            $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
                                ->whereIn('paid', [1, 2]);
                        });
                })->select('products.*', DB::raw('sum(retailer_order_line_items.quantity) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
                    ->groupBy('products.id')
                    ->orderBy('sold', 'DESC');
                $products = $productQuery->paginate(12);
            } else if ($request->input('tag') == 'winning-products') {
                $products = $productQuery->where('tags', 'LIKE', '%' . $request->input('tag') . '%')->paginate(12);

            } else {
                $products = $productQuery->where('processing_time', '24 Hours')->paginate(12);

            }
        }
        if ($request->has('filter')) {
            if ($request->input('filter') == 'most-order') {
                $productQuery = Product::join('retailer_order_line_items', function ($join) {
                    $join->on('retailer_order_line_items.shopify_product_id', '=', 'products.shopify_id')
                        ->join('retailer_orders', function ($o) {
                            $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
                                ->whereIn('paid', [1, 2]);
                        });
                })->select('products.*', DB::raw('sum(retailer_order_line_items.quantity) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
                    ->groupBy('products.id')
                    ->orderBy('sold', 'DESC');
                $products = $productQuery->paginate(12);

            } elseif ($request->input('filter') == 'most-imported') {
                $products = $productQuery->withCount('has_imported')->orderBy('has_imported_count', 'DESC')->paginate(12);
            } elseif ($request->input('filter') == 'new-arrival') {
                $products = $productQuery->orderBy('created_at', 'DESC')->paginate(12);

            }
        } else {
            $products = $productQuery->orderBy('created_at','DESC')->paginate(12);
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

        return view('non_shopify_users.product.wefullfill_products')->with([
            'categories' => $categories,
            'products' => $products,
            'search' => $request->input('search'),
            'filter' => $request->input('filter')
        ]);
    }

    public function view_fantasy_product($id)
    {
        $product = Product::find($id);
        return view('non_shopify_users.product.view_product')->with([
            'product' => $product,
        ]);
    }

    public function process_file(Request $request)
    {
        if ($request->hasFile('import_order_file')) {

            $image = $request->file('import_order_file');
            $destinationPath = 'import-orders/';
            $filename = now()->format('YmdHi') . str_replace([' ', '(', ')'], '-', $image->getClientOriginalName());
            $image->move($destinationPath, $filename);

            $new_file = new UserFile();
            $new_file->file = $filename;
            $new_file->user_id = Auth::id();
            $new_file->save();

            Excel::import(new UsersImport($new_file->id, Auth::id()), 'import-orders/' . $filename);
            $process_data = UserFileTemp::where('user_id', $new_file->user_id)->where('file_id', $new_file->id)->get()->groupBy('order_number');
            foreach ($process_data as $index => $data) {
                $order_name = $index;
                $atleast_one_varaint = false;
                foreach ($data as $item) {
                    if (ProductVariant::where('sku', $item->sku)->exists()) {
                        $atleast_one_varaint = true;
                        break;
                    }
                    if (Product::where('sku', $item->sku)->exists()) {
                        $atleast_one_varaint = true;
                        break;
                    }
                }
                if ($atleast_one_varaint) {
                    $new = new RetailerOrder();
                    $new->name = '#WFI-' . $order_name;
                    $new->taxes_included = '0';
                    $new->total_tax = '0';
                    $new->currency = 'USD';
                    $new->total_discounts = '0';

                    $new->shopify_created_at = $data[0]->created_at;
                    $new->shopify_updated_at = $data[0]->created_at;


                    $new_user = false;
                    foreach ($data as $item) {
                        if (Customer::where('email', $item->email)->exists()) {
                            $customer = Customer::where('email', $item->email)->first();
                            $new->customer_id = $customer->id;
                            $new_user = false;
                            break;
                        } else {
                            $new_user = true;
                        }
                    }
                    $name = explode(' ', $data[0]->name);
                    $first_name = $name[0];
                    if (array_key_exists(1, $name)) {
                        $last_name = '';
                        foreach ($name as $key => $n) {
                            if ($key != 0) {
                                $last_name = $last_name . ' ' . $n;
                            }
                        }
                    } else {
                        $last_name = '';
                    }
                    $address1 = $data[0]->address1;
                    $address2 = $data[0]->address2;
                    $city = $data[0]->city;
                    $postcode = $data[0]->postcode;
                    $country = $data[0]->country;
                    $phone = $data[0]->phone;
                    $email = $data[0]->email;
                    $province = $data[0]->province;

                    if ($new_user) {
                        $customer = new Customer();
                        $customer->first_name = $first_name;
                        $customer->last_name = $last_name;
                        $customer->email = $email;
                        $customer->phone = $phone;
                        $customer->user_id = Auth::id();
                        $customer->save();
                        $new->customer_id = $customer->id;

                    }
                    $shipping_address = [
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "address1" => $address1,
                        "address2" => $address2,
                        "city" => $city,
                        "province" => $province,
                        "zip" => $postcode,
                        "country" => $country,
                        "phone" => $phone

                    ];


                    $new->shipping_address = json_encode($shipping_address, true);

                    $new->email = $email;
                    $new->status = 'new';
                    $new->user_id = Auth::id();
                    $new->fulfilled_by = 'fantasy';
                    $new->sync_status = 1;
                    $new->save();
                    $cost_to_pay = 0;
                    $total_weight = 0;

                    foreach ($data as $item) {
                        $variant = ProductVariant::where('sku', $item->sku)->first();
                        if ($variant != null) {

                            $item->status = 1;
                            $item->order_id = $new->id;
                            $item->save();

                            $new_line = new RetailerOrderLineItem();
                            $new_line->retailer_order_id = $new->id;
                            $new_line->shopify_product_id = $variant->linked_product->shopify_id;
                            $new_line->shopify_variant_id = $variant->shopify_id;
                            $new_line->title = $variant->linked_product->title;
                            $new_line->quantity = $item->quantity;
                            $new_line->sku = $variant->sku;
                            $new_line->variant_title = $variant->variant_title;
                            $new_line->title = $variant->title;
                            $new_line->vendor = $variant->linked_product->title;
                            $new_line->price = $variant->price;
                            $new_line->requires_shipping = 'true';
                            $new_line->name = $variant->linked_product->title . ' - ' . $variant->title;
                            $new_line->fulfillable_quantity = $item->quantity;
                            $new_line->fulfilled_by = 'Fantasy';
                            $new_line->cost = $variant->price;
                            $cost_to_pay = $cost_to_pay + $variant->price * $item->quantity;;
                            $total_weight = $total_weight + $variant->linked_product->weight;
                            $new_line->save();
                        }

                    }

                    foreach ($data as $item) {
                        $variant = Product::where('sku', $item->sku)->first();
                        if ($variant != null) {

                            $item->status = 1;
                            $item->order_id = $new->id;
                            $item->save();

                            $new_line = new RetailerOrderLineItem();
                            $new_line->retailer_order_id = $new->id;
                            $new_line->shopify_product_id = $variant->shopify_id;

                            $new_line->title = $variant->title;
                            $new_line->quantity = $item->quantity;
                            $new_line->sku = $variant->sku;
                            $new_line->vendor = $variant->vendor;
                            $new_line->price = $variant->price;
                            $new_line->requires_shipping = 'true';
                            $new_line->name = $variant->title;
                            $new_line->fulfillable_quantity = $item->quantity;
                            $new_line->fulfilled_by = 'Fantasy';
                            $new_line->cost = $variant->price;
                            $cost_to_pay = $cost_to_pay + $variant->price * $item->quantity;;
                            $total_weight = $total_weight + $variant->weight;
                            $new_line->save();
                        }

                    }

                    $new->subtotal_price = $cost_to_pay;
                    $new->shipping_price = 0;
                    $total_cost = $cost_to_pay;
                    $new->total_weight = $total_weight;
                    $new->total_price = $total_cost;
                    $new->cost_to_pay = $total_cost;
                    $new->custom = 1;
                    $new->save();


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

                    /*Maintaining Log*/
                    $order_log = new OrderLog();
                    $order_log->message = "Custom Order Created to WeFullFill through file import on " . date_create($new->created_at)->format('d M, Y h:i a');
                    $order_log->status = "Newly Synced";
                    $order_log->retailer_order_id = $new->id;
                    $order_log->save();
                }
            }

            $custom_orders = RetailerOrder::where('user_id', Auth::id())->newQuery();
            $custom_orders->whereHas('imported', function ($q) use ($new_file) {
                $q->where('file_id', '=', $new_file->id);
            });

            $temp_data = UserFileTemp::where('user_id', $new_file->user_id)->where('file_id', $new_file->id)->where('status', 0)->get();

            $settings = AdminSetting::all()->first();

            return view('non_shopify_users.orders.processed')->with([
                'orders' => $custom_orders->get(),
                'data' => $temp_data,
                'file' => $new_file,
                'settings' => $settings
            ]);
        } else {
            return redirect()->back();
        }
    }

    public function files(Request $request)
    {
        $files = UserFile::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->get();
        return view('non_shopify_users.orders.import_files')->with([
            'files' => $files,
        ]);
    }

    public function file(Request $request)
    {
        $new_file = UserFile::find($request->id);
        $custom_orders = RetailerOrder::where('user_id', Auth::id())->newQuery();
        $custom_orders->whereHas('imported', function ($q) use ($new_file) {
            $q->where('file_id', '=', $new_file->id);
        });

        $temp_data = UserFileTemp::where('user_id', $new_file->user_id)->where('file_id', $new_file->id)->where('status', 0)->get();
        $settings = AdminSetting::all()->first();
        return view('non_shopify_users.orders.processed')->with([
            'orders' => $custom_orders->get(),
            'data' => $temp_data,
            'file' => $new_file,
            'settings' => $settings
        ]);
    }


    public function download_processed_orders($id)
    {
        $new_file = UserFile::find($id);
        $custom_orders = RetailerOrder::where('user_id', Auth::id())->newQuery();
        $custom_orders->whereHas('imported', function ($q) use ($new_file) {
            $q->where('file_id', '=', $new_file->id);
        });
        $orders = $custom_orders->get();
        return Excel::download(new ProcessedOrder($orders), now() . 'ProcessedOrders' . '.csv');

    }

    public function download_unprocessed_orders($id)
    {
        $new_file = UserFile::find($id);
        $temp_data = UserFileTemp::where('user_id', $new_file->user_id)->where('file_id', $new_file->id)->where('status', 0)->get();

        return Excel::download(new UnprocessedOrder($temp_data), now() . 'UnprocessedFileOrders' . '.csv');

    }

    public function download_order($id)
    {
        $order = RetailerOrder::find($id);
        return Excel::download(new RetailerOrderExport($order), now() . ' ' . $order->name . ' Order' . '.csv');

    }

    public function bulk_import_order_card(Request $request)
    {

        $new_file = UserFile::find($request->id);
        $setting = AdminSetting::all()->first();
        $custom_orders = RetailerOrder::where('user_id', Auth::id())->where('custom', 1)->where('paid', 0)->newQuery();
        $custom_orders->whereHas('imported', function ($q) use ($new_file) {
            $q->where('file_id', '=', $new_file->id);
        });
        $custom_orders = $custom_orders->get();
        $order_total = $custom_orders->sum('cost_to_pay');

        foreach ($custom_orders as $order) {
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
//                $this->inventory->OrderQuantityUpdate($order,'new');
            }

        }
        return redirect()->route('users.files.view', $request->id)->with('success', 'Order Transaction Process Successfully And Will Managed By WeFullFill Administration!');
    }

    public function bulk_import_order_wallet(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->has_wallet == null) {
                return redirect()->route('users.files.view', $request->id)->with('error', 'Wallet Does not Exist!');
            } else {
                $wallet = $user->has_wallet;
            }

        } else {
            $shop = $this->helper->getLocalShop();
            if (count($shop->has_user) > 0) {
                if ($shop->has_user[0]->has_wallet == null) {
                    return redirect()->route('users.files.view', $request->id)->with('error', 'Wallet Does not Exist!');

                } else {
                    $wallet = $shop->has_user[0]->has_wallet;
                }

            } else {
                return redirect()->route('users.files.view', $request->id)->with('error', 'Wallet Does not Exist!');

            }
        }
        $new_file = UserFile::find($request->id);
        $setting = AdminSetting::all()->first();
        $custom_orders = RetailerOrder::where('user_id', Auth::id())->where('custom', 1)->where('paid', 0)->newQuery();
        $custom_orders->whereHas('imported', function ($q) use ($new_file) {
            $q->where('file_id', '=', $new_file->id);
        });
        $custom_orders = $custom_orders->get();
        $order_total = $custom_orders->sum('cost_to_pay');
        if ($wallet->available >= $order_total) {
            foreach ($custom_orders as $retailer_order) {
                /*Wallet Deduction*/
                $wallet->available = $wallet->available - $retailer_order->cost_to_pay;
                $wallet->used = $wallet->used + $retailer_order->cost_to_pay;
                $wallet->save();


                /*Order Processing*/
                $new_transaction = new OrderTransaction();
                $new_transaction->amount = $retailer_order->cost_to_pay;
                if ($retailer_order->custom == 0) {
                    $new_transaction->name = $retailer_order->has_store->shopify_domain;
                } else {
                    $new_transaction->name = Auth::user()->email;
                }

                $new_transaction->retailer_order_id = $retailer_order->id;
                $new_transaction->user_id = $retailer_order->user_id;
                $new_transaction->shop_id = $retailer_order->shop_id;
                $new_transaction->save();
                /*Changing Order Status*/
                $retailer_order->paid = 1;
                $retailer_order->status = 'Paid';
                $retailer_order->pay_by = 'Wallet';
                $retailer_order->save();

                /*Maintaining Log*/
                $order_log = new OrderLog();
                $order_log->message = "An amount of " . $new_transaction->amount . " USD paid to WeFullFill through Wallet on " . date_create($new_transaction->created_at)->format('d M, Y h:i a') . " for further process";
                $order_log->status = "paid";
                $order_log->retailer_order_id = $retailer_order->id;
                $order_log->save();


                $this->admin->sync_order_to_admin_store($retailer_order);
//                $this->inventory->OrderQuantityUpdate($retailer_order,'new');
            }
            /*Maintaining Wallet Log*/
            $wallet_log = new WalletLog();
            $wallet_log->wallet_id = $wallet->id;
            $wallet_log->status = "Order Payment";
            $wallet_log->amount = $order_total;
            $wallet_log->message = 'An Amount ' . number_format($order_total, 2) . ' USD For Order Cost Against Wallet ' . $wallet->wallet_token . ' Deducted At ' . now()->format('d M, Y h:i a');
            $wallet_log->save();
            $this->notify->generate('Wallet', 'Wallet Order Payment', 'An Amount ' . number_format($order_total, 2) . ' USD For Order Cost Against Wallet ' . $wallet->wallet_token . ' Deducted At ' . now()->format('d M, Y h:i a'), $wallet);
            return redirect()->route('users.files.view', $request->id)->with('success', 'Order Cost Deducted From Wallet Successfully!');
        } else {
            return redirect()->route('users.files.view', $request->id)->with('error', 'Wallet Doesnot Have Required Amount!');
        }


    }

    public function bulk_import_order_paypal(Request $request)
    {
        $new_file = UserFile::find($request->id);
        $setting = AdminSetting::all()->first();
        $custom_orders = RetailerOrder::where('user_id', Auth::id())->where('custom', 1)->where('paid', 0)->newQuery();
        $custom_orders->whereHas('imported', function ($q) use ($new_file) {
            $q->where('file_id', '=', $new_file->id);
        });
        $custom_orders = $custom_orders->get();
        if (count($custom_orders) > 0) {
            $items = [];
            $order_total = 0;
            foreach ($custom_orders as $retailer_order) {
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


            $data = [];
            $data['items'] = $items;
            $data['invoice_id'] = 'WeFullFill-Import-Bulk-Pay' . rand(1, 1000);
            $data['invoice_description'] = "WeFullFill-Import-Bulk-Pay-Invoice-" . rand(1, 1000);;
            $data['return_url'] = route('users.orders.bulk.paypal.success', $new_file->id);
            $data['cancel_url'] = route('users.orders.bulk.paypal.cancel', $new_file->id);
            $data['total'] = $order_total;

            $response = $request->input('response');
            $response = json_decode(json_encode(json_decode($response)));
            if ($response->status == 'COMPLETED') {
                foreach ($custom_orders as $retailer_order) {
                    $retailer_order->paypal_token = $response->id;
                    $retailer_order->save();
                }
                $this->bulk_import_order_paypal_success($request->id, $response);
            } else {
                return redirect()->route('users.files.view', $request->id)->with('error', 'Payment Failed');
            }
//            $provider = new ExpressCheckout;
//            try {
//
//                $response = $provider->setExpressCheckout($data);
//
//                foreach ($custom_orders as $retailer_order){
//                    $retailer_order->paypal_token  = $response['TOKEN'];
//                    $retailer_order->save();
//                }
//
//                return redirect($response['paypal_link']);
//            }
//            catch (\Exception $e){
//                return redirect()->route('users.files.view',$request->id)->with('error','System Process Failure');
//            }

        }
    }

    public function bulk_import_order_paypal_cancel(Request $request)
    {
        return redirect()->route('users.files.view', $request->id)->with('error', 'Paypal Transaction Process cancelled successfully');
    }

    public function bulk_import_order_paypal_success($id, $response)
    {
        $file = UserFile::find($id);


//        $provider = new ExpressCheckout;
//        $response = $provider->getExpressCheckoutDetails($request->token);

        $custom_orders = RetailerOrder::where('user_id', Auth::id())->where('custom', 1)
            ->where('paid', 0)
            ->where('paypal_token', $response->id)
            ->newQuery();
        $custom_orders->whereHas('imported', function ($q) use ($file) {
            $q->where('file_id', '=', $file->id);
        });
        $custom_orders = $custom_orders->get();

//        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING']) && $file  != null && count($custom_orders) > 0)
//        {
//        if ($response->status == 'COMPLETED') {
            foreach ($custom_orders as $retailer_order) {
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
                $this->admin->sync_order_to_admin_store($retailer_order);
//                $this->inventory->OrderQuantityUpdate($retailer_order,'new');
            }
            return redirect()->route('users.custom.orders')->with('success', 'Bulk Payment Processed Successfully!');

//        } else {
//            return redirect()->route('users.files.view', $request->id)->with('error', 'Bulk Orders Not Found!');
//        }
    }

    public function helpcenter(Request $request)
    {
        $user = User::find(Auth::id());
        $tickets = Ticket::where('user_id', $user->id)->where('source', 'non-shopify-user')->newQuery();
        $tickets = $tickets->paginate(30);

        return view('non_shopify_users.help-center.index')->with([
            'user' => $user,
            'tickets' => $tickets,
            'categories' => TicketCategory::all(),
        ]);
    }

    public function view_ticket(Request $request)
    {
        $user = User::find(Auth::id());
        $ticket = Ticket::find($request->id);
        return view('non_shopify_users.help-center.view')->with([
            'user' => $user,
            'ticket' => $ticket,
        ]);
    }

    public function wishlist(Request $request)
    {
        $user = User::find(Auth::id());
        $wishlists = Wishlist::where('user_id', $user->id)->newQuery();
        $wishlists = $wishlists->orderBy('created_at', 'DESC')->paginate(30);

        return view('non_shopify_users.wishlist.index')->with([
            'user' => $user,
            'wishlist' => $wishlists,
            'countries' => Country::all(),
        ]);
    }

    public function view_wishlist(Request $request)
    {
        $user = User::find(Auth::id());
        $wishlists = Wishlist::find($request->id);
        return view('non_shopify_users.wishlist.view')->with([
            'user' => $user,
            'wishlist' => $wishlists,
        ]);
    }

    public function delete_wishlist($id)
    {
        Wishlist::find($id)->delete();
        return redirect()->back();
    }

    public function refunds(Request $request)
    {
        $user = User::find(Auth::id());
        $refunds = Refund::where('user_id', $user->id)->newQuery();
        if ($request->has('search')) {
            $refunds->where('order_name', 'LIKE', '%' . $request->input('search') . '%');
        }
        $refunds->whereHas('has_order', function () {

        });
        $orders = RetailerOrder::where('user_id', $user->id)->where('paid', 1)->get();
        return view('non_shopify_users.orders.refunds')->with([
            'refunds' => $refunds->orderBy('created_at')->paginate(20),
            'search' => $request->input('search'),
            'user' => $user,
            'orders' => $orders
        ]);
    }

    public function refund(Request $request)
    {
        $user = User::find(Auth::id());
        $refund = Refund::find($request->id);
        if ($refund->has_order != null) {
            return view('non_shopify_users.orders.view-refund')->with([
                'user' => $user,
                'ticket' => $refund,
            ]);
        } else {
            return redirect()->route('users.refunds')->with('No Refund Found!');

        }

    }


    public function show_notification($id)
    {
        $notification = Notification::find($id);
        if ($notification != null) {
            $notification->read = 1;
            $notification->save();
            if ($notification->type == 'Product') {
                return redirect()->route('users.product.wefulfill.show', $notification->type_id);
            } elseif ($notification->type == 'Order') {
                return redirect()->route('users.order.view', $notification->type_id);

            } elseif ($notification->type == 'Refund') {
                return redirect()->route('users.refund', $notification->type_id);

            } elseif ($notification->type == 'Wish-list') {
                return redirect()->route('users.wishlist.view', $notification->type_id);

            } elseif ($notification->type == 'Ticket') {
                return redirect()->route('help-center.users.ticket.view', $notification->type_id);

            } elseif ($notification->type == 'Wallet') {
                return redirect()->route('store.user.wallet.show');

            }

        }
    }

    public function notifications()
    {
        $query = Notification::query();
        if (Auth::check()) {
            $user = Auth::user();
            $query->whereHas('to_users', function ($q) use ($user) {
                $q->where('email', $user->email);
            });
        }
        $notifications = $query->orderBy('created_at', 'DESC')->paginate(30);

        return view('non_shopify_users.notifications.index')->with([
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


}
