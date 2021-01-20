<?php


namespace App\Http\Controllers;

use App\AdminFile;
use App\AdminFileTemp;
use App\Courier;
use App\Customer;
use App\ERPOrderFulfillment;
use App\ErrorLog;
use App\Exports\CustomersExport;
use App\Exports\OrdersExport;
use App\FulfillmentLineItem;
use App\Imports\BulkTrackingImport;
use App\Mail\NewShopifyUserMail;
use App\Mail\OrderStatusMail;
use App\Notification;
use App\OrderFulfillment;
use App\OrderLog;
use App\Product;
use App\Refund;
use App\RetailerOrder;
use App\RetailerOrderLineItem;
use App\RetailerProduct;
use App\Shop;
use App\User;
use App\WalletRequest;
use App\WishlistStatus;
use App\Zone;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use function foo\func;

class AdminOrderController extends Controller
{
    private $helper;
    private $admin_maintainer;
    private $notify;
    private $log;


    /**
     * AdminOrderController constructor.
     * @param $helper
     */
    public function __construct()
    {
        $this->helper = new HelperController();
        $this->admin_maintainer = new AdminMaintainerController();
        $this->notify = new NotificationController();
        $this->log = new ActivityLogController();

    }



    public function index(Request $request)
    {
        $orders = RetailerOrder::whereIn('paid', [1, 2])->newQuery();
        if ($request->has('search')) {
            $orders->where('name', 'LIKE', '%' . $request->input('search') . '%')
                   ->orWhere('id', 'LIKE', '%' . $request->input('search') . '%')
                   ->orWhere('admin_shopify_name', 'LIKE', '%' . $request->input('search') . '%');
        }
        if ($request->has('status')) {
            if ($request->input('status') == 'unfulfilled') {
                $orders->whereIN('status', ['Paid', 'unfulfilled']);
            } else {
                $orders->where('status', $request->input('status'));
            }

        }
        $all_orders = RetailerOrder::whereIn('paid', [1, 2])->get();
        $orders = $orders->orderBy('created_at', 'DESC')->paginate(30);

        return view('orders.index')->with([
            'all_orders' => $all_orders,
            'orders' => $orders,
            'search' => $request->input('search'),
            'status' => $request->input('status')
        ]);
    }

    public function view_order($id)
    {
        $order = RetailerOrder::find($id);
        $couriers = Courier::all();
//        $fullfillment = OrderFulfillment::where('retailer_order_id', $id)->first();
        if ($order != null) {
            return view('orders.view')->with([
                'order' => $order,
                'couriers' => $couriers
            ]);
        }
    }

    public function fulfill_order($id)
    {
        $order = RetailerOrder::find($id);
        if ($order != null) {
            if ($order->paid == 1) {
                return view('orders.fulfillment')->with([
                    'order' => $order
                ]);
            } else {
                return redirect() - back()->with('error', 'Refunded Order Cant Be Processed Fulfillment');
            }

        }
    }

    public function fulfillment_order(Request $request, $id)
    {
        $order = RetailerOrder::find($id);
        if ($order != null) {
            if ($order->paid == 1) {
                $fulfillable_quantities = $request->input('item_fulfill_quantity');
                if ($order->custom == 0) {
                    $shop = $this->helper->getSpecificShop($order->shop_id);
                    $shopify_fulfillment = null;
                    if ($shop != null) {
                        $location_response = $shop->api()->rest('GET', '/admin/locations.json');
                        if (!$location_response->errors) {

                            foreach ($location_response->body->locations as $location) {
                                if ($location->name == "WeFullFill") {
                                    $data = [
                                        "fulfillment" => [
                                            "location_id" => $location->id,
                                            "tracking_number" => null,
                                            "line_items" => [
                                            ]
                                        ]
                                    ];
                                }
                            }

                            foreach ($request->input('item_id') as $index => $item) {
                                $line_item = RetailerOrderLineItem::find($item);
                                if ($line_item != null && $fulfillable_quantities[$index] > 0) {
                                    array_push($data['fulfillment']['line_items'], [
                                        "id" => $line_item->retailer_product_variant_id,
                                        "quantity" => $fulfillable_quantities[$index],
                                    ]);
                                }
                            }

                            $response = $shop->api()->rest('POST', '/admin/orders/' . $order->shopify_order_id . '/fulfillments.json', $data);

                            if ($response->errors) {
                                if(strpos($response->body->base[0], "already fulfilled") !== false){
                                    $res = $shop->api()->rest('GET', '/admin/orders/' . $order->shopify_order_id . '/fulfillments.json');
                                    return $this->set_fulfilments_for_already_fulfilled_order($request, $id, $fulfillable_quantities, $order, $res);
                                }

                                dd($response);
                                return redirect()->back()->with('error', 'Cant Fulfill Items of Order in Related Store!');
                            } else {
                                return $this->set_fulfilments($request, $id, $fulfillable_quantities, $order, $response);
                            }
                        } else {
                            return redirect()->back()->with('error', 'Cant Fulfill Item Cause Related Store Dont have Location Stored!');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Order Related Store Not Found');
                    }
                } else {
                    return $this->set_fulfilments($request, $id, $fulfillable_quantities, $order, '');
                }
            } else {
                return redirect() - back()->with('error', 'Refunded Order Cant Be Processed Fulfillment');
            }
        } else {
            return redirect()->route('admin.order')->with('error', 'Order Not Found To Process Fulfillment');
        }

    }

    public function fulfillment_cancel_order(Request $request)
    {
        $order = RetailerOrder::find($request->id);
        $fulfillment = OrderFulfillment::find($request->fulfillment_id);
        if ($order != null && $fulfillment != null) {
            if ($order->paid == 1) {
                if ($order->custom == 0) {
                    $shop = $this->helper->getSpecificShop($order->shop_id);
                    if ($shop != null) {
                        $response = $shop->api()->rest('POST', '/admin/orders/' . $order->shopify_order_id . '/fulfillments/' . $fulfillment->fulfillment_shopify_id . '/cancel.json');
                        if ($response->errors) {
                            return redirect()->back()->with('error', 'Order Fulfillment Cancellation Failed!');
                        } else {
                            return $this->unset_fullfilment($fulfillment, $order);
                        }
                    } else {
                        return redirect()->back()->with('error', 'Order Related Store Not Found');

                    }
                } else {
                    return $this->unset_fullfilment($fulfillment, $order);
                }
            } else {
                return redirect() - back()->with('error', 'Refunded Order Cant Be Processed Fulfillment');
            }

        } else {
            return redirect()->route('admin.order')->with('error', 'Order Not Found To Cancel Fulfillment');
        }
    }

    public function fulfillment_add_tracking(Request $request)
    {

        $order = RetailerOrder::find($request->id);
        if ($order != null) {
            if ($order->paid == 1) {
                $fulfillments = $request->input('fulfillment');
                $tracking_numbers = $request->input('tracking_number');
                $tracking_urls = $request->input('tracking_url');
                $tracking_notes = $request->input('tracking_notes');
                $courier_id = $request->input('courier_id');

                if ($order->custom == 0) {
                    $shop = $this->helper->getSpecificShop($order->shop_id);
                    if ($shop != null) {
                        foreach ($fulfillments as $index => $f) {
                            $current = OrderFulfillment::find($f);
                            if ($current != null) {
                                $data = [
                                    "fulfillment" => [
                                        "tracking_number" => $tracking_numbers[$index],
                                        "tracking_url" => $tracking_urls[$index],
                                    ]
                                ];
                                if($courier_id[$index] !== null){
                                    $courier = Courier::find($courier_id[$index]);
                                    $data['fulfillment']['tracking_company'] = $courier->title;
                                }


                                $response = $shop->api()->rest('PUT', '/admin/orders/' . $order->shopify_order_id . '/fulfillments/' . $current->fulfillment_shopify_id . '.json', $data);
                                if ($order->admin_shopify_id != null) {
                                    $this->admin_maintainer->admin_order_fulfillment_add_tracking($order, $current, $data);
                                }
                                if (!$response->errors) {
                                    $current->tracking_number = $tracking_numbers[$index];
                                    $current->tracking_url = $tracking_urls[$index];
                                    $current->tracking_notes = $tracking_notes[$index];
                                    if($courier_id[$index] !== null)
                                        $current->courier_id = $courier_id[$index];
                                    $current->save();
                                    $this->CompleteFullFillment($current);
                                    /*Maintaining Log*/
                                    $order_log = new OrderLog();
                                    $order_log->message = "Tracking detailed added to fulfillment named " . $current->name . "  successfully on " . now()->format('d M, Y h:i a');
                                    $order_log->status = "Tracking Details Added";
                                    $order_log->retailer_order_id = $order->id;
                                    $order_log->save();
                                }

                            }
                        }
                    } else {
                        return redirect()->back()->with('error', 'Order Related Store Not Found');
                    }
                } else {
                    foreach ($fulfillments as $index => $f) {
                        $current = OrderFulfillment::find($f);
                        if ($current != null) {
                            $current->tracking_number = $tracking_numbers[$index];
                            $current->tracking_url = $tracking_urls[$index];
                            $current->tracking_notes = $tracking_notes[$index];
                            if($courier_id[$index] !== null)
                                $current->courier_id = $courier_id[$index];
                            $current->save();

                            if ($order->admin_shopify_id != null) {
                                $data = [
                                    "fulfillment" => [
                                        "tracking_number" => $tracking_numbers[$index],
                                        "tracking_url" => $tracking_urls[$index],
                                    ]
                                ];

                                if($courier_id[$index] !== null){
                                    $courier = Courier::find($courier_id[$index]);
                                    $data['fulfillment']['tracking_company'] = $courier->title;
                                }

                                $this->admin_maintainer->admin_order_fulfillment_add_tracking($order, $current, $data);
                                $this->CompleteFullFillment($current);
                            }
                            /*Maintaining Log*/
                            $order_log = new OrderLog();
                            $order_log->message = "Tracking detailed added to fulfillment named " . $current->name . "  successfully on " . now()->format('d M, Y h:i a');
                            $order_log->status = "Tracking Details Added";
                            $order_log->retailer_order_id = $order->id;
                            $order_log->save();
                        }
                    }
                }
                $count = 0;
                $fulfillment_count = count($order->fulfillments);
                foreach ($order->fulfillments as $f) {
                    if ($f->tracking_number != null) {
                        $count++;
                    }
                }
                if ($count == $fulfillment_count) {
                    $order->status = 'shipped';
                } else {
                    $order->status = 'partially-shipped';
                }
                $order->save();
                $this->log->store(0, 'Order', $order->id, $order->name, 'Order Tracking Added');
                $this->notify->generate('Order', 'Order Tracking Details', $order->name . ' tracking details added successfully!', $order);
                return redirect()->back()->with('success', 'Tracking Details Added To Fulfillment Successfully!');
            } else {
                return redirect() - back()->with('error', 'Refunded Order Cant Be Processed Fulfillment');
            }
        } else {
            return redirect()->route('admin.order')->with('error', 'Order Not Found To Add Tracking In Fulfillment');

        }
    }

    public function fulfillment_edit_tracking(Request $request, $id, $fulfillment_id) {
        $order = RetailerOrder::find($id);
        if ($order != null) {
            if ($order->paid == 1) {
                $tracking_number = $request->input('tracking_number');
                $tracking_url = $request->input('tracking_url');
                $tracking_note = $request->input('tracking_notes');
                $courier_id = $request->input('courier_id');

                if ($order->custom == 0) {
                    $shop = $this->helper->getSpecificShop($order->shop_id);
                    if ($shop != null) {
                            $current = OrderFulfillment::find($fulfillment_id);
                            if ($current != null) {
                                $current->tracking_number = $tracking_number;
                                $current->tracking_url = $tracking_url;
                                $current->tracking_notes = $tracking_note;
                                if($courier_id !== null)
                                    $current->courier_id = $courier_id;
                                $current->save();

                                //$this->CompleteFullFillment($current);
                                /*Maintaining Log*/
                                $order_log = new OrderLog();
                                $order_log->message = "Tracking detailed of fulfillment named " . $current->name . "  updated successfully on " . now()->format('d M, Y h:i a');
                                $order_log->status = "Tracking Details Updated";
                                $order_log->retailer_order_id = $order->id;
                                $order_log->save();

                                $data = [
                                    "fulfillment" => [
                                        "tracking_number" => $tracking_number,
                                        "tracking_url" => $tracking_url,
                                        "notify_customer" => false
                                    ]
                                ];
                                if($courier_id !== null){
                                    $courier = Courier::find($courier_id);
                                    $data['fulfillment']['tracking_company'] = $courier->title;
                                }

                                $response = $shop->api()->rest('PUT', '/admin/orders/' . $order->shopify_order_id . '/fulfillments/' . $current->fulfillment_shopify_id . '.json', $data);

                                if ($order->admin_shopify_id != null) {
                                    $this->admin_maintainer->admin_order_fulfillment_edit_tracking($order, $current, $data);
                                }
                            }

                    } else {
                        return redirect()->back()->with('error', 'Order Related Store Not Found');
                    }
                } else {
                        $current = OrderFulfillment::find($fulfillment_id);
                        if ($current != null) {
                            $current->tracking_number = $tracking_number;
                            $current->tracking_url = $tracking_url;
                            $current->tracking_notes = $tracking_note;
                            if($courier_id !== null)
                                $current->courier_id = $courier_id;
                            $current->save();

                            if ($order->admin_shopify_id != null) {
                                $data = [
                                    "fulfillment" => [
                                        "tracking_number" => $tracking_number,
                                        "tracking_url" => $tracking_url,
                                        "notify_customer" => false
                                    ]
                                ];

                                if($courier_id !== null){
                                    $courier = Courier::find($courier_id);
                                    $data['fulfillment']['tracking_company'] = $courier->title;
                                }

                                $this->admin_maintainer->admin_order_fulfillment_edit_tracking($order, $current, $data);
                                //$this->CompleteFullFillment($current);
                            }
                            /*Maintaining Log*/
                            $order_log = new OrderLog();
                            $order_log->message = "Tracking detailed of fulfillment named " . $current->name . " updated successfully on " . now()->format('d M, Y h:i a');
                            $order_log->status = "Tracking Details Updated";
                            $order_log->retailer_order_id = $order->id;
                            $order_log->save();
                        }

                }


                $this->log->store(0, 'Order', $order->id, $order->name, 'Order Tracking Updated');
                $this->notify->generate('Order', 'Order Tracking Details', $order->name . ' tracking details updated successfully!', $order);
                return redirect()->back()->with('success', 'Tracking Details Updated Successfully!');
            } else {
                return redirect() - back()->with('error', 'Refunded Order Cant Be Processed Fulfillment');
            }
        } else {
            return redirect()->route('admin.order')->with('error', 'Order Not Found To Edit Tracking In Fulfillment');

        }
    }

    public function mark_as_delivered(Request $request)
    {
        $order = RetailerOrder::find($request->id);
        if ($order != null) {
            if ($order->paid == 1) {
                $order->status = 'delivered';
                $order->save();
                $this->log->store(0, 'Order', $order->id, $order->name, 'Order Marked As Delivered');


                /*Maintaining Log*/
                $order_log = new OrderLog();
                $order_log->message = "Order marked as delivered successfully on " . now()->format('d M, Y h:i a');
                $order_log->status = "Delivered";
                $order_log->retailer_order_id = $order->id;
                $order_log->save();
                $this->notify->generate('Order', 'Order Marked as Delivered', $order->name . ' marked as delivered successfully!', $order);


                return redirect()->back()->with('success', 'Order Marked as Delivered Successfully');
            } else {
                return redirect() - back()->with('error', 'Refunded Order Cant Be Processed Fulfillment');
            }
        } else {
            return redirect()->back()->with('error', 'Order Marked as Delivered Failed');

        }

    }

    public function mark_as_completed(Request $request)
    {
        $order = RetailerOrder::find($request->id);
        if ($order != null) {
            if ($order->paid == 1) {
                $order->status = 'completed';
                $order->save();

                $this->log->store(0, 'Order', $order->id, $order->name, 'Order Marked As Completed');


                $order_log = new OrderLog();
                $order_log->message = "Order marked as completed successfully on " . now()->format('d M, Y h:i a');
                $order_log->status = "Completed";
                $order_log->retailer_order_id = $order->id;
                $order_log->save();
                $this->notify->generate('Order', 'Order Marked as Completed', $order->name . ' marked as completed successfully!', $order);


                return redirect()->back()->with('success', 'Order Marked as Completed Successfully');
            } else {
                return redirect() - back()->with('error', 'Refunded Order Cant Be Processed Fulfillment');
            }
        } else {
            return redirect()->back()->with('error', 'Order Marked as Completed Failed');

        }

    }

    /**
     * @param Request $request
     * @param $id
     * @param $fulfillable_quantities
     * @param $order
     * @param $response
     * @return RedirectResponse
     */
    public function set_fulfilments(Request $request, $id, $fulfillable_quantities, $order, $response): RedirectResponse
    {
        foreach ($request->input('item_id') as $index => $item) {
            $line_item = RetailerOrderLineItem::find($item);
            if ($line_item != null && $fulfillable_quantities[$index] > 0) {
                if ($fulfillable_quantities[$index] == $line_item->fulfillable_quantity) {
                    $line_item->fulfillment_status = 'fulfilled';

                } else if ($fulfillable_quantities[$index] < $line_item->fulfillable_quantity) {
                    $line_item->fulfillment_status = 'partially-fulfilled';
                }
                $line_item->fulfillable_quantity = $line_item->fulfillable_quantity - $fulfillable_quantities[$index];
            }
            $line_item->save();
        }
        $order->status = $order->getStatus($order);
        $order->save();

        $fulfillment = new OrderFulfillment();
        if ($order->custom == 0) {
            $fulfillment->fulfillment_shopify_id = $response->body->fulfillment->id;
            $fulfillment->name = $response->body->fulfillment->name;
        } else {
            $count = count($order->fulfillments) + 1;
            $fulfillment->name = $order->name . '.F' . $count;
        }
        $fulfillment->retailer_order_id = $order->id;
        $fulfillment->status = 'fulfilled';
        $fulfillment->save();

        /*Maintaining Log*/
        $order_log = new OrderLog();
        $order_log->message = "A fulfillment named " . $fulfillment->name . " has been processed successfully on " . date_create($fulfillment->created_at)->format('d M, Y h:i a');
        $order_log->status = "Fulfillment";
        $order_log->retailer_order_id = $order->id;
        $order_log->save();

        foreach ($request->input('item_id') as $index => $item) {
            if ($fulfillable_quantities[$index] > 0) {
                $fulfillment_line_item = new FulfillmentLineItem();
                $fulfillment_line_item->fulfilled_quantity = $fulfillable_quantities[$index];
                $fulfillment_line_item->order_fulfillment_id = $fulfillment->id;
                $fulfillment_line_item->order_line_item_id = $item;
                $fulfillment_line_item->save();

            }
        }
        if ($order->admin_shopify_id != null) {
            $this->admin_maintainer->admin_order_fullfillment($order, $request, $fulfillment);
        }

        $user = $order->has_user;
        try{
            Mail::to($user->email)->send(new OrderStatusMail($user, $order));
        }
        catch (\Exception $e){
        }

        $this->log->store(0, 'Order', $order->id, $order->name, 'Order Line Items Fulfilled');
        $this->notify->generate('Order', 'Order Fulfillment', $order->name . ' line items fulfilled', $order);
        return redirect()->route('admin.order.view', $id)->with('success', 'Order Line Items Marked as Fulfilled Successfully!');
    }

    public function set_fulfilments_for_already_fulfilled_order(Request $request, $id, $fulfillable_quantities, $order, $response): RedirectResponse
    {
        foreach ($request->input('item_id') as $index => $item) {
            $line_item = RetailerOrderLineItem::find($item);
            if ($line_item != null && $fulfillable_quantities[$index] > 0) {
                if ($fulfillable_quantities[$index] == $line_item->fulfillable_quantity) {
                    $line_item->fulfillment_status = 'fulfilled';

                } else if ($fulfillable_quantities[$index] < $line_item->fulfillable_quantity) {
                    $line_item->fulfillment_status = 'partially-fulfilled';
                }
                $line_item->fulfillable_quantity = $line_item->fulfillable_quantity - $fulfillable_quantities[$index];
            }
            $line_item->save();
        }
        $order->status = 'fulfilled';
        $order->save();


        $fulfillment = new OrderFulfillment();
        if ($order->custom == 0) {
            $fulfillment->fulfillment_shopify_id = $response->body->fulfillments[0]->id;
            $fulfillment->name = $response->body->fulfillments[0]->name;
        } else {
            $count = count($order->fulfillments) + 1;
            $fulfillment->name = $order->name . '.F' . $count;
        }
        $fulfillment->retailer_order_id = $order->id;
        $fulfillment->status = 'fulfilled';
        $fulfillment->save();

        /*Maintaining Log*/
        $order_log = new OrderLog();
        $order_log->message = "A fulfillment named " . $fulfillment->name . " has been processed successfully on " . date_create($fulfillment->created_at)->format('d M, Y h:i a');
        $order_log->status = "Fulfillment";
        $order_log->retailer_order_id = $order->id;
        $order_log->save();

        foreach ($request->input('item_id') as $index => $item) {
            if ($fulfillable_quantities[$index] > 0) {
                $fulfillment_line_item = new FulfillmentLineItem();
                $fulfillment_line_item->fulfilled_quantity = $fulfillable_quantities[$index];
                $fulfillment_line_item->order_fulfillment_id = $fulfillment->id;
                $fulfillment_line_item->order_line_item_id = $item;
                $fulfillment_line_item->save();

            }
        }
        if ($order->admin_shopify_id != null) {
            $this->admin_maintainer->admin_order_fullfillment($order, $request, $fulfillment);
        }

        $user = $order->has_user;
        try{
            Mail::to($user->email)->send(new OrderStatusMail($user, $order));
        }
        catch (\Exception $e){
        }

        $this->log->store(0, 'Order', $order->id, $order->name, 'Order Line Items Fulfilled');
        $this->notify->generate('Order', 'Order Fulfillment', $order->name . ' line items fulfilled', $order);
        return redirect()->route('admin.order.view', $id)->with('success', 'Order Line Items Marked as Fulfilled Manually Successfully!');
    }

    /**
     * @param $fulfillment
     * @param $order
     * @return RedirectResponse
     */
    public function unset_fullfilment($fulfillment, $order): RedirectResponse
    {
        foreach ($fulfillment->line_items as $item) {
            if ($item->linked_line_item != null) {
                $item->linked_line_item->fulfillable_quantity = $item->linked_line_item->fulfillable_quantity + $item->fulfilled_00quantity;
                $item->linked_line_item->save();
                if ($item->linked_line_item->fulfillable_quantity < $item->linked_line_item->quantity) {
                    $item->linked_line_item->fulfillment_status = "partially-fulfilled";
                } else if ($item->linked_line_item->fulfillable_quantity == $item->linked_line_item->quantity) {
                    $item->linked_line_item->fulfillment_status = null;
                }
                $item->linked_line_item->save();
            }
            $item->delete();
        }
        $order_log = new OrderLog();
        $order_log->message = "A fulfillment named " . $fulfillment->name . " has been cancelled successfully on " . now()->format('d M, Y h:i a');
        if ($order->admin_shopify_id != null) {
            $this->admin_maintainer->admin_order_fulfillment_cancel($order, $fulfillment);
        }
        $this->notify->generate('Order', 'Order Fulfillment Cancellation', $order->name . ' line items fulfillment cancelled', $order);

        $fulfillment->delete();
        $order->status = $order->getStatus($order);
        $order->save();
        $this->log->store(0, 'Order', $order->id, $order->name, 'Order Fulfillment Cancelled');



        /*Maintaining Log*/

        $order_log->status = "Fulfillment Cancelled";
        $order_log->retailer_order_id = $order->id;
        $order_log->save();

        return redirect()->back()->with('success', 'Order Fulfillment Cancelled Successfully');
    }

    public function dashboard(Request $request)
    {
        if ($request->has('date-range')) {
            $date_range = explode('-', $request->input('date-range'));
            $start_date = $date_range[0];
            $end_date = $date_range[1];
            $comparing_start_date = Carbon::parse($start_date)->format('Y-m-d');
            $comparing_end_date = Carbon::parse($end_date)->format('Y-m-d');

            $orders = RetailerOrder::whereIN('paid', [1, 2])->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();
            $sales = RetailerOrder::whereIN('paid', [1, 2])->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $refund = RetailerOrder::whereIN('paid', [2])->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->sum('cost_to_pay');
            $stores = Shop::whereNotIn('shopify_domain', ['wefullfill.myshopify.com'])->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])->count();


            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->whereIn('paid', [1, 2])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();


            $ordersQR = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->whereIn('paid', [2])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();

            $shopQ = DB::table('shops')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->whereNotIn('shopify_domain', ['wefullfill.myshopify.com'])
                ->whereBetween('created_at', [$comparing_start_date, $comparing_end_date])
                ->groupBy('date')
                ->get();


        } else {

            $orders = RetailerOrder::whereIN('paid', [1, 2])->count();
            $sales = RetailerOrder::whereIN('paid', [1, 2])->sum('cost_to_pay');
            $refund = RetailerOrder::whereIN('paid', [2])->sum('cost_to_pay');
            $stores = Shop::whereNotIn('shopify_domain', ['wefullfill.myshopify.com'])->count();

            $ordersQ = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->whereIn('paid', [1, 2])
                ->groupBy('date')
                ->get();


            $ordersQR = DB::table('retailer_orders')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total, sum(cost_to_pay) as total_sum'))
                ->whereIn('paid', [2])
                ->groupBy('date')
                ->get();

            $shopQ = DB::table('shops')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->whereNotIn('shopify_domain', ['wefullfill.myshopify.com'])
                ->groupBy('date')
                ->get();
        }


        $graph_one_order_dates = $ordersQ->pluck('date')->toArray();
        $graph_one_order_values = $ordersQ->pluck('total')->toArray();
        $graph_two_order_values = $ordersQ->pluck('total_sum')->toArray();

        $graph_three_order_dates = $ordersQR->pluck('date')->toArray();
        $graph_three_order_values = $ordersQR->pluck('total_sum')->toArray();

        $graph_four_order_dates = $shopQ->pluck('date')->toArray();
        $graph_four_order_values = $shopQ->pluck('total')->toArray();


        $top_products_users = Product::join('retailer_order_line_items', function ($join) {
            $join->on('retailer_order_line_items.shopify_product_id', '=', 'products.shopify_id')
                ->join('retailer_orders', function ($o) {
                    $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
                        ->where('retailer_orders.paid', '>=', 1);
                });
        })->select('products.*', DB::raw('sum(retailer_order_line_items.quantity) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
            ->groupBy('products.id')
            ->orderBy('sold', 'DESC')
            ->get()
            ->take(5);

        $top_products_stores = Product::join('retailer_products', function ($join) {
            $join->on('retailer_products.linked_product_id', '=', 'products.id')
                ->join('retailer_order_line_items', function ($join) {
                    $join->on('retailer_order_line_items.shopify_product_id', '=', 'retailer_products.shopify_id')
                        ->join('retailer_orders', function ($o) {
                            $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
                                ->where('retailer_orders.paid', '>=', 1);
                        });
                });
        })->select('products.*', DB::raw('sum(retailer_order_line_items.quantity) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
            ->groupBy('products.id')
            ->orderBy('sold', 'DESC')
            ->get()
            ->take(5);

//        dd($top_products_stores,$top_products_users);


        $top_stores = Shop::whereNotIn('shopify_domain', ['wefullfill.myshopify.com'])
            ->join('retailer_products', function ($join) {
                $join->on('retailer_products.shop_id', '=', 'shops.id')
                    ->join('retailer_order_line_items', function ($j) {
                        $j->on('retailer_order_line_items.shopify_product_id', '=', 'retailer_products.shopify_id')
                            ->join('retailer_orders', function ($o) {
                                $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
                                    ->where('retailer_orders.paid', '>=', 1);
                            });
                    });

            })
            ->select('shops.*', DB::raw('COUNT(retailer_orders.id) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
            ->groupBy('shops.id')
            ->orderBy('sold', 'DESC')
            ->get()
            ->take(10);

        $top_users = User::role('non-shopify-users')->join('retailer_orders', function ($o) {
            $o->on('retailer_orders.user_id', '=', 'users.id');
        })->where('retailer_orders.paid', '>=', 1)
            ->where('retailer_orders.custom', '=', 1)
            ->select('users.*', DB::raw('COUNT(retailer_orders.cost_to_pay) as sold'), DB::raw('sum(retailer_orders.cost_to_pay) as selling_cost'))
            ->groupBy('users.id')
            ->orderBy('sold', 'DESC')
            ->get()
            ->take(10);

//        $top_users = User::role('non-shopify-users')->join('retailer_products', function ($join) {
//            $join->on('retailer_products.user_id', '=', 'users.id')
//                ->join('retailer_order_line_items', function ($j) {
//                    $j->join('products', function ($p) {
//                        $p->on('retailer_order_line_items.shopify_product_id', '=', 'products.shopify_id');
//                    });
//                    $j->join('retailer_orders', function ($o) {
//                        $o->on('retailer_order_line_items.retailer_order_id', '=', 'retailer_orders.id')
//                            ->whereIn('paid', [1, 2]);
//                    });
//                });
//        })->select('users.*', DB::raw('sum(retailer_order_line_items.quantity) as sold'), DB::raw('sum(retailer_order_line_items.cost) as selling_cost'))
//            ->groupBy('users.id')
//            ->orderBy('sold', 'DESC')
//            ->get()
//            ->take(10);

//        dd($top_products);





        return view('welcome')->with([
            'date_range' => $request->input('date-range'),
            'orders' => $orders,
            'refunds' => $refund,
            'sales' => $sales,
            'stores' => $stores,
            'graph_one_labels' => $graph_one_order_dates,
            'graph_one_values' => $graph_one_order_values,
            'graph_two_values' => $graph_two_order_values,
            'graph_three_labels' => $graph_three_order_dates,
            'graph_three_values' => $graph_three_order_values,
            'graph_four_values' => $graph_four_order_values,
            'graph_four_labels' => $graph_four_order_dates,
            'top_products_stores' => $top_products_stores,
            'top_products_users' => $top_products_users,
            'top_stores' => $top_stores,
            'top_users' => $top_users,
        ]);
    }

    public function show_bulk_fulfillments(Request $request)
    {
        $orders_array = explode(',', $request->input('orders'));
        if (count($orders_array) > 0) {
            $orders = RetailerOrder::whereIn('id', $orders_array)->newQuery();


            $orders->whereHas('line_items', function ($q) {
                $q->where('fulfillable_quantity', '>', 0);
            });
            $orders = $orders->get();

            $total_quantity = 0;
            $fulfillable_quantity = 0;
            foreach ($orders as $order) {
                $total_quantity = $total_quantity + $order->line_items->whereIn('fulfilled_by', ['Fantasy', 'AliExpress'])->sum('quantity');
                $fulfillable_quantity = $fulfillable_quantity + $order->line_items->whereIn('fulfilled_by', ['Fantasy', 'AliExpress'])->sum('fulfillable_quantity');

            }

            if (!Auth::check()) {
                return view('orders.bulk-fulfillment')->with([
                    'orders' => $orders,
                    'total_quantity' => $total_quantity,
                    'fulfillable_quantity' => $fulfillable_quantity
                ]);
            } else {
                return view('sales_managers.orders.bulk-fulfillment')->with([
                    'orders' => $orders,
                    'total_quantity' => $total_quantity,
                    'fulfillable_quantity' => $fulfillable_quantity
                ]);
            }


        } else {
            return redirect()->back();
        }
    }

    public function show_import_data()
    {
        $order = RetailerOrder::whereIn('status', ['Paid', 'unfulfilled'])->where('paid', 1)->count();
        return view('orders.all_fulfillments')->with([
            'count' => $order
        ]);
    }

    public function download_orders()
    {
        $orders = RetailerOrder::whereIn('status', ['Paid', 'unfulfilled'])->where('paid', 1)->get();
        return Excel::download(new OrdersExport($orders), now()->format('m-d-y') . ' Unfulfillment Orders' . '.csv');
    }


    public function import_bulk_tracking(Request $request)
    {
        if ($request->hasFile('import_tracking')) {
            $image = $request->file('import_tracking');
            $destinationPath = 'admin/import-tracking/';
            $filename = now()->format('YmdHi') . str_replace([' ', '(', ')'], '-', $image->getClientOriginalName());
            $image->move($destinationPath, $filename);

            $new_file = new AdminFile();
            $new_file->file = $destinationPath . $filename;
            $new_file->save();

            Excel::import(new BulkTrackingImport($new_file), $destinationPath . $filename);
            $data = AdminFileTemp::where('file_id', $new_file->id)->get();
            foreach ($data as $d) {
                $retailer_order = RetailerOrder::find($d->order_id);
                if ($retailer_order != null && $retailer_order->paid == 1 && in_array($retailer_order->status, ['Paid', 'unfulfilled'])) {
                    if ($retailer_order->custom == 1) {
                        $fulfillment = new OrderFulfillment();
                        $count = count($retailer_order->fulfillments) + 1;
                        $fulfillment->name = $retailer_order->name . '.F' . $count;
                        $fulfillment->retailer_order_id = $retailer_order->id;
                        $fulfillment->status = 'fulfilled';
                        $fulfillment->save();

                        /*Maintaining Log*/
                        $this->fulfillment_tracking_process($fulfillment, $retailer_order, $d);
                        if ($retailer_order->admin_shopify_id != null) {
                            $this->admin_fulfillment_tracking_process($retailer_order, $d, $fulfillment);
                        }
                    } else {
                        $shop = $this->helper->getSpecificShop($retailer_order->shop_id);
                        if ($shop != null) {
                            $location_response = $shop->api()->rest('GET', '/admin/locations.json');

                            if (!$location_response->errors) {
                                $fulfill_data = [
                                    "fulfillment" => [
                                        "location_id" => $location_response->body->locations[0]->id,
                                        "tracking_number" => $d->tracking_number,
                                        "tracking_company" => $d->tracking_company,
                                        "tracking_url" => $d->tracking_url,
                                        "line_items" => [

                                        ]
                                    ]
                                ];
                                foreach ($retailer_order->line_items as $index => $item) {
                                    if ($item->fulfillable_quantity > 0) {
                                        array_push($fulfill_data['fulfillment']['line_items'], [
                                            "id" => $item->retailer_product_variant_id,
                                            "quantity" => $item->fulfillable_quantity,
                                        ]);
                                    }
                                }
                                $response = $shop->api()->rest('POST', '/admin/orders/' . $retailer_order->shopify_order_id . '/fulfillments.json', $fulfill_data);
                                if (!$response->errors) {

                                    $fulfillment = new OrderFulfillment();
                                    $fulfillment->fulfillment_shopify_id = $response->body->fulfillment->id;
                                    $fulfillment->name = $response->body->fulfillment->name;
                                    $fulfillment->retailer_order_id = $retailer_order->id;
                                    $fulfillment->status = 'fulfilled';
                                    $fulfillment->save();
                                    $this->fulfillment_tracking_process($fulfillment, $retailer_order, $d);
                                    if ($retailer_order->admin_shopify_id != null) {
                                        $this->admin_fulfillment_tracking_process($retailer_order, $d, $fulfillment);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return redirect()->route('admin.orders')->with('success', 'Related Orders Fulfillments Process Successfully!');

        } else {
            return redirect()->back()->with('error', 'No File Found!');
        }
    }

    /**
     * @param OrderFulfillment $fulfillment
     * @param $retailer_order
     * @param $data
     */
    public function fulfillment_tracking_process(OrderFulfillment $fulfillment, $retailer_order, $data): void
    {
        /*Maintaining Log*/
        $order_log = new OrderLog();
        $order_log->message = "A fulfillment named " . $fulfillment->name . " has been processed successfully on " . date_create($fulfillment->created_at)->format('d M, Y h:i a');
        $order_log->status = "Fulfillment";
        $order_log->retailer_order_id = $retailer_order->id;
        $order_log->save();

        foreach ($retailer_order->line_items as $index => $item) {
            if ($item->fulfillable_quantity > 0) {
                $fulfillment_line_item = new FulfillmentLineItem();
                $fulfillment_line_item->fulfilled_quantity = $item->fulfillable_quantity;
                $fulfillment_line_item->order_fulfillment_id = $fulfillment->id;
                $fulfillment_line_item->order_line_item_id = $item->id;
                $fulfillment_line_item->save();

                $item->fulfillable_quantity = 0;
                $item->fulfillment_status = 'fulfilled';
                $item->save();

            }
        }

        $this->notify->generate('Order', 'Order Fulfillment', $retailer_order->name . ' line items fulfilled', $retailer_order);

        $fulfillment->tracking_number = $data->tracking_number;
        $fulfillment->tracking_url = $data->tracking_url;
        $fulfillment->tracking_notes = $data->tracking_notes;
        $fulfillment->tracking_company = $data->tracking_company;
        $fulfillment->save();

        $retailer_order->status = 'shipped';
        $retailer_order->save();

        $this->notify->generate('Order', 'Order Tracking Details', $retailer_order->name . ' tracking details added successfully!', $retailer_order);
    }


    /**
     * @param $retailer_order
     * @param $data
     * @param OrderFulfillment $fulfillment
     */
    public function admin_fulfillment_tracking_process($retailer_order, $data, OrderFulfillment $fulfillment): void
    {
        $admin_shop = $this->helper->getAdminShop();
        /*Location and Admin Order Fetch!*/
        $location_response = $admin_shop->api()->rest('GET', '/admin/locations.json');
        $admin_order_response = $admin_shop->api()->rest('GET', '/admin/orders/' . $retailer_order->admin_shopify_id . '.json');

        if (!$location_response->errors && !$admin_order_response->errors) {
            $admin_fulfill_data = [
                "fulfillment" => [
                    "location_id" => $location_response->body->locations[0]->id,
                    "tracking_number" => $data->tracking_number,
                    "tracking_company" => $data->tracking_company,
                    "tracking_url" => $data->tracking_url,
                    "line_items" => [

                    ]
                ]
            ];
            $admin_variants = $admin_order_response->body->order->line_items;
            foreach ($admin_variants as $variant) {
                if ($variant->fulfillable_quantity > 0) {
                    array_push($admin_fulfill_data['fulfillment']['line_items'], [
                        "id" => $variant->id,
                        "quantity" => $variant->fulfillable_quantity,
                    ]);
                }
            }

            if (count($admin_fulfill_data['fulfillment']['line_items']) > 0) {
                $response = $admin_shop->api()->rest('POST', '/admin/orders/' . $retailer_order->admin_shopify_id . '/fulfillments.json', $admin_fulfill_data);
                if (!$response->errors) {
                    $fulfillment->admin_fulfillment_shopify_id = $response->body->fulfillment->id;
                    $fulfillment->save();
                }
            }

        }
    }

    public function manualSyncfulfillment(Request $request, $id)
    {
        $shop = $this->helper->getAdminShop();
        $order = RetailerOrder::find($id);

        $response = $shop->api()->rest('GET','admin/orders/'. $order->admin_shopify_id .'/fulfillments.json');
        $data = $response->body->fulfillments[0];
        $fulfillment = OrderFulfillment::where('admin_fulfillment_shopify_id',$data->id)->first();
        $webhook = new AdminWebhookController();

        if($fulfillment == null){
            $webhook->set_fulfillments($data);
        }
        else {
            return redirect()->back()->with('error', 'Order is not fulfilled in Wefullfill');
        }

        return redirect()->back()->with('success', 'Order Fulfillment Synced Successfully!');
    }

    public function CompleteFullFillment($orderFullfillment)
    {
        $order = RetailerOrder::where('id', $orderFullfillment->retailer_order_id)->first();
        if ($orderFullfillment->fulfillment_shopify_id) {
            $shop = $this->helper->getSpecificShop($order->shop_id);
            $shop->api()->rest('POST', '/admin/orders/' . $order->shopify_order_id . '/fulfillments/' . $orderFullfillment->fulfillment_shopify_id . '/complete.json');
        }
        if ($orderFullfillment->admin_fulfillment_shopify_id && $order->admin_shopify_id) {
            $admin_shop = $this->helper->getAdminShop();
            $admin_shop->api()->rest('POST', '/admin/orders/' . $order->admin_shopify_id . '/fulfillments/' . $orderFullfillment->admin_fulfillment_shopify_id . '/complete.json');
        }
    }

    public function changeFulfillmentServiceUrl() {
        $shop = $this->helper->getSpecificShop(71);
        $response = $shop->api()->rest('GET', '/admin/fulfillment_services.json');
        dd(123, $response);



        $ids = [55,56,57,58,59,63,67,71,75,78,81,83,84,85,90,92];
        for($i =0 ; $i < count($ids); $i++) {
            $shop = $this->helper->getSpecificShop($ids[$i]);

            $response = $shop->api()->rest('GET', '/admin/fulfillment_services.json');

            if($response->errors) {
                continue;
            }

            $service_ids = [];
            if(count($response->body->fulfillment_services) > 0){
                foreach ($response->body->fulfillment_services as $service) {
                    array_push($service_ids, $service->id);
                }

                foreach ($service_ids as $id) {
                    $data = [
                        'fulfillment_service' => [
                            'callback_url' => 'https://app.wefullfill.com',
                        ]
                    ];

                    $resp =  $shop->api()->rest('PUT', '/admin/api/2020-04/fulfillment_services/'.$id.'.json',$data);

                    if($response->errors) {
                        dd($response);
                    }
                }
            }
        }

        dd(567);
    }


    public function testWebhook() {

        $shop = $this->helper->getSpecificShop(71);

        $response = $shop->api()->rest('GET', '/admin/webhooks.json');

        dd(123, $response);

        $ids = [67,71,75,78,81,83];
        for($i =0 ; $i < count($ids); $i++) {
            $shop = $this->helper->getSpecificShop($ids[$i]);

            $response = $shop->api()->rest('GET', '/admin/webhooks.json');

            if($response->errors) {
                continue;
            }

            $webhook_ids = [];
            if(count($response->body->webhooks) > 0){
                foreach ($response->body->webhooks as $webhook) {
                    array_push($webhook_ids, $webhook->id);
                }

                foreach ($webhook_ids as $id) {
                    $shop->api()->rest('DELETE', '/admin/webhooks/'.$id.'.json');
                }
            }

            $data = [
                "webhook" => [
                    "topic" => "orders/create",
                    "address" => "https://app.wefullfill.com/webhook/orders-create",
                    "format" => "json"
                ]
            ];
            $shop->api()->rest('POST', '/admin/webhooks.json', $data);
            $data = [];

            $data = [
                "webhook" => [
                    "topic" => "customers/create",
                    "address" => "https://app.wefullfill.com/webhook/customers-create",
                    "format" => "json"
                ]
            ];
            $shop->api()->rest('POST', '/admin/webhooks.json', $data);
            $data = [];

            $data = [
                "webhook" => [
                    "topic" => "fulfillments/create",
                    "address" => "https://app.wefullfill.com/webhook/fulfillments-create",
                    "format" => "json"
                ]
            ];
            $shop->api()->rest('POST', '/admin/webhooks.json', $data);
            $data = [];

            $data = [
                "webhook" => [
                    "topic" => "fulfillments/update",
                    "address" => "https://app.wefullfill.com/webhook/fulfillments-update",
                    "format" => "json"
                ]
            ];
            $shop->api()->rest('POST', '/admin/webhooks.json', $data);
            $data = [];

            $data = [
                "webhook" => [
                    "topic" => "orders/cancelled",
                    "address" => "https://app.wefullfill.com/webhook/orders-cancelled",
                    "format" => "json"
                ]
            ];
            $shop->api()->rest('POST', '/admin/webhooks.json', $data);
            $data = [];

            $data = [
                "webhook" => [
                    "topic" => "products/delete",
                    "address" => "https://app.wefullfill.com/webhook/products-delete",
                    "format" => "json"
                ]
            ];
            $shop->api()->rest('POST', '/admin/webhooks.json', $data);
            $data = [];

        }

        dd(123);
    }


    public function manual_push_order($id) {
        $retailer_order = RetailerOrder::find($id);

        $this->admin_maintainer->sync_order_to_admin_store($retailer_order);

        return redirect()->back()->with('success','Order Synced to Wefulfill Successfully');

    }

    public function sendOrderStatusEmail($id) {
        $order = RetailerOrder::find($id);
        $user = $order->has_user;
        try{
            Mail::to($user->email)->send(new OrderStatusMail($user, $order));
        }
        catch (\Exception $e){
            return redirect()->back()->with('error',"Order Status Email Cant be Send To User");
        }

        return redirect()->back()->with('success','Order Status Email Send to User Successfully');
    }

    public function getFulfillmentFromErp(Request $request) {

        $order_id = $request->platformOrderId;
        $logistics_code = $request->logisticsCode;
        $track_number = $request->trackNumber;
        $logistics_name = $request->logisticsName;
        $track_url = $request->trackUrl;
        $item_list = $request->itemList;


        $order = RetailerOrder::find($order_id);
        if($order_id !== null && $order) {

            try{
                // Save fulfillment
                $flag = true;
                if(ERPOrderFulfillment::where('retailer_order_id', $order->id)->exists()) {
                    $fulfillment = ERPOrderFulfillment::where('retailer_order_id', $order->id)->first();
                    if($fulfillment->track_number == $track_number) {
                        $flag = false;
                    }
                }
                else {
                    $fulfillment = new ERPOrderFulfillment();
                }

                $fulfillment->retailer_order_id = $order->id;
                $fulfillment->logistic_code = $logistics_code;
                $fulfillment->track_number = $track_number;
                $fulfillment->logistic_name = $logistics_name;
                $fulfillment->track_url = $track_url;
                $fulfillment->erp_order_id = $order_id;
                $fulfillment->line_items = json_encode($item_list);
                $fulfillment->save();

                $order->pushed_to_erp = 1;
                $order->save();

                if($flag) {
                    $this->set_erp_order_fulfillment($fulfillment, $order);
                }
                else {
                    $this->update_erp_order_fulfillment($fulfillment, $order);
                }

            }
            catch(\Exception $e) {
                $log = new ErrorLog();
                $log->message = "Mabang Error: ". $e->getMessage();
                $log->save();
            }

            // Send Success Response
            return response()->json(["code" => 0, "message" => ""]);
        }
        else {
            // Send Error Response
            return response()->json(["code" => 999, "message" => "错误描述"]);
        }

    }

    public function set_erp_order_fulfillment($data, $order) {
        $retailer_order = RetailerOrder::find($order->id);
        if ($retailer_order != null && $retailer_order->paid == 1) {
            if ($retailer_order->custom == 1) {

                /*Order Fullfillment Record*/
                $new_fulfillment = new OrderFulfillment();
                $count = count($retailer_order->fulfillments) + 1;
                $new_fulfillment->name = $retailer_order->name . '.F' . $count;
                $new_fulfillment->retailer_order_id = $retailer_order->id;
                $new_fulfillment->status = 'fulfilled';
                $new_fulfillment->save();

                $this->after_fullfiment_process($new_fulfillment, $retailer_order, $data);
            }
            else {

                $shop = $this->helper->getSpecificShop($retailer_order->shop_id);
                $shopify_fulfillment = null;
                if ($shop != null) {
                    $location_response = $shop->api()->rest('GET', '/admin/locations.json');
                    if (!$location_response->errors) {

                        foreach ($location_response->body->locations as $location){
                            if($location->name == "WeFullFill"){
                                $fulfill_data = [
                                    "fulfillment" => [
                                        "location_id" => $location->id,
                                        "tracking_number" => null,
                                        "line_items" => [

                                        ]
                                    ]
                                ];
                            }
                        }

                        if ($data->track_number) {
                            $fulfill_data['fulfillment']['tracking_number'] = $data->track_number;
                        }

                        if($retailer_order->shipping_address)
                        {
                            $shipping = json_decode($retailer_order->shipping_address);
                            $country = $shipping->country;

                            $zoneQuery = Zone::query();
                            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                                $q->where('name','LIKE','%'.$country.'%');
                            });
                            $zoneQuery = $zoneQuery->first();
                            if($zoneQuery->courier != null && $zoneQuery->courier->url != null) {
                                $fulfill_data['fulfillment']['tracking_url'] = $zoneQuery->courier->url;
                                $fulfill_data['fulfillment']['tracking_company'] = $zoneQuery->courier->title;
                            }
                            else if ($data->track_url) {
                                $fulfill_data['fulfillment']['tracking_url'] = $data->track_url;
                            }
                        }
                        else if ($data->track_url) {
                            $fulfill_data['fulfillment']['tracking_url'] = $data->track_url;
                        }

                        $line_items = json_decode($data->line_items);
                        foreach ($line_items as $line_item) {
                            $item = RetailerOrderLineItem::where('sku', $line_item->platformSku)->where('retailer_order_id',$retailer_order->id)->first();
                            if ($item != null) {
                                $fulfill_quantity =$item->fulfillable_quantity -  0;
                                array_push($fulfill_data['fulfillment']['line_items'], [
                                    "id" => $item->retailer_product_variant_id,
                                    "quantity" => $fulfill_quantity,
                                ]);
                            }
                        }
                        $response = $shop->api()->rest('POST','/admin/orders/'.$retailer_order->shopify_order_id.'/fulfillments.json',$fulfill_data);
                        if(!$response->errors){
                            /*Order Fullfillment Record*/
                            $new_fulfillment = new OrderFulfillment();
                            $new_fulfillment->fulfillment_shopify_id = $response->body->fulfillment->id;
                            $new_fulfillment->name = $response->body->fulfillment->name;
                            $new_fulfillment->retailer_order_id = $retailer_order->id;
                            $new_fulfillment->status = 'fulfilled';
                            $new_fulfillment->save();
                            /*Order Log*/

                            $shop->api()->rest('POST', '/admin/orders/' . $retailer_order->shopify_order_id . '/fulfillments/' . $response->body->fulfillment->id . '/complete.json');

                            $this->after_fullfiment_process($new_fulfillment, $retailer_order, $data);
                        }
                        else {
                            $log = new ErrorLog();
                            $log->message = "Fulfillment Error Outer From Manbang: " . json_encode($response->body);
                            $log->save();
                        }
                    }
                }
            }
        }
    }

    public function after_fullfiment_process(OrderFulfillment $new_fulfillment, $retailer_order, $data): void
    {
        /*Order Log*/
        $order_log = new OrderLog();
        $order_log->message = "A fulfillment named " . $new_fulfillment->name . " has been processed successfully on " . date_create($new_fulfillment->created_at)->format('d M, Y h:i a');
        $order_log->status = "Fulfillment";
        $order_log->retailer_order_id = $retailer_order->id;
        $order_log->save();


        /*Fulfillment Line Item Relationship*/
        $line_items = json_decode($data->line_items);
        foreach ($line_items as $item) {
            $line_item = RetailerOrderLineItem::where('sku', $item->platformSku)->where('retailer_order_id', $retailer_order->id)->first();
            if ($line_item != null) {
                $fulfillment_line_item = new FulfillmentLineItem();
                $fulfillment_line_item->fulfilled_quantity = $line_item->fulfillable_quantity - 0;
                $fulfillment_line_item->order_fulfillment_id = $new_fulfillment->id;
                $fulfillment_line_item->order_line_item_id = $line_item->id;
                $fulfillment_line_item->save();
            }
        }
        /*Each Line Item Fulfillment Status*/
        list($item, $line_item) = $this->set_line_item_fullfill_status($data, $retailer_order);


        /*Notification*/
        $this->notify->generate('Order', 'Order Fulfillment', $retailer_order->name . ' line items fulfilled', $retailer_order);

        /*If Fulfillment has Tracking Information*/
        if ($data->track_number) {
            $new_fulfillment->tracking_number = $data->track_number;
        }

        if($retailer_order->shipping_address)
        {
            $shipping = json_decode($retailer_order->shipping_address);
            $country = $shipping->country;

            $zoneQuery = Zone::query();
            $zoneQuery->whereHas('has_countries',function ($q) use ($country){
                $q->where('name','LIKE','%'.$country.'%');
            });
            $zoneQuery = $zoneQuery->first();
            if($zoneQuery->courier != null && $zoneQuery->courier->url != null) {
                $new_fulfillment->tracking_url = $zoneQuery->courier->url;
                $new_fulfillment->courier_id = $zoneQuery->courier->id;
            }
            else if ($data->track_url) {
                $new_fulfillment->tracking_url = $data->track_url;
            }
        }
        else if ($data->track_url) {
            $new_fulfillment->tracking_url = $data->track_url;
        }



        $new_fulfillment->admin_fulfillment_shopify_id = $data->erp_order_id;
        $new_fulfillment->save();
        if ($data->track_number) {
            $count = 0;
            $fulfillment_count = count($retailer_order->fulfillments);
            foreach ($retailer_order->fulfillments as $f) {
                if ($f->tracking_number != null) {
                    $count++;
                }
            }
            if($retailer_order->status == 'fulfilled'){
                if ($count == $fulfillment_count) {
                    $retailer_order->status = 'shipped';
                } else {
                    $retailer_order->status = 'partially-shipped';
                }
            }

            $retailer_order->save();
            $this->notify->generate('Order', 'Order Tracking Details', $retailer_order->name . ' tracking details added successfully!', $retailer_order);
        }
    }

    public function set_line_item_fullfill_status($data, $retailer_order): array
    {
        $line_items = json_decode($data->line_items);
        foreach ($line_items as $item) {
            $line_item = RetailerOrderLineItem::where('sku', $item->platformSku)->where('retailer_order_id', $retailer_order->id)->first();
            if ($line_item != null) {
//                if ($item->fulfillable_quantity == 0) {
                if (true) {
                    $line_item->fulfillment_status = 'fulfilled';
                    $line_item->fulfillable_quantity = 0;
                    $line_item->save();
                } else {
                    $line_item->fulfillment_status = 'partially-fulfilled';
                    $line_item->fulfillable_quantity = $item->quantity;
                    $line_item->save();
                }
            }
        }
        $retailer_order->status = $retailer_order->getStatus($retailer_order);
        $retailer_order->save();
        return array($item, $line_item);
    }

}



