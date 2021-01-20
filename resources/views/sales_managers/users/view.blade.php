@extends('layout.manager')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                   {{$user->name}}
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx active" href="">Users</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx active" href="">{{$user->name}}</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="block">
            <ul class="nav nav-tabs nav-justified nav-tabs-block " data-toggle="tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#orders">Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="#tickets">Tickets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#customers">Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#products">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#payments">Payments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#wallet">Wallet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#settings">Settings</a>
                </li>
            </ul>
            <div class="block-content tab-content">
                <div class="tab-pane active" id="orders" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if (count($user->has_orders) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Source</th>
                                        <th>Order Date</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach($user->has_orders()->orderBy('created_at','DESC')->get() as $index => $order)
                                        <tr>
                                            <td class="font-w600"><a href="{{route('sales_managers.order.view',$order->id)}}">{{ $order->name }}</a></td>
                                            <td>
                                                @if($order->custom == 1)
                                                    <span class="badge badge-primary" style="font-size: 12px"> Custom </span>
                                                @else
                                                    <span class="badge badge-warning" style="font-size: 12px"> Shopify </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{date_create($order->shopify_created_at)->format('D m, Y h:i a') }}
                                            </td>

                                            <td>
                                                {{number_format($order->cost_to_pay,2)}} USD
                                            </td>

                                            <td>
                                                @if($order->status == 'paid')
                                                    <span class="badge badge-primary" style="float: right;font-size: medium"> {{$order->status}}</span>

                                                @elseif($order->status == 'unfulfilled')
                                                    <span class="badge badge-warning" style="font-size: small"> {{$order->status}}</span>
                                                @elseif($order->status == 'partially-shipped')
                                                    <span class="badge " style="font-size: small;background: darkolivegreen;color: white;"> {{$order->status}}</span>
                                                @elseif($order->status == 'shipped')
                                                    <span class="badge " style="font-size: small;background: orange;color: white;"> {{$order->status}}</span>
                                                @elseif($order->status == 'delivered')
                                                    <span class="badge " style="font-size: small;background: deeppink;color: white;"> {{$order->status}}</span>
                                                @elseif($order->status == 'completed')
                                                    <span class="badge " style="font-size: small;background: darkslategray;color: white;"> {{$order->status}}</span>
                                                @else
                                                    <span class="badge badge-success" style="font-size: small"> {{$order->status}}</span>
                                                @endif

                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <a class="btn btn-sm btn-primary text-white" type="button" data-toggle="modal" data-target="#tracking_{{$order->id}}_modal"
                                                       data-original-title="View Tracking"><i class="fa fa-truck"></i></a>
                                                    <a href="{{route('sales_managers.order.view',$order->id)}}"
                                                       class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="View Order"><i class="fa fa-eye"></i></a>
                                                </div>

                                            </td>
                                        </tr>
                                        <div class="modal fade" id="tracking_{{$order->id}}_modal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
                                                <div class="modal-content">
                                                    <div class="block block-themed block-transparent mb-0">
                                                        <div class="block-header bg-primary-dark">
                                                            <h3 class="block-title">{{$order->name}} Tracking Information</h3>
                                                            <div class="block-options">
                                                                <button type="button" class="btn-block-option">
                                                                    <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="block-content font-size-sm">
                                                            @if(count($order->logs) > 0)
                                                                <ul class="timeline timeline-alt">
                                                                    @foreach($order->logs as $log)
                                                                        <li class="timeline-event">
                                                                            @if($log->status == "Newly Synced")
                                                                                <div class="timeline-event-icon bg-warning">
                                                                                    <i class="fa fa-sync"></i>
                                                                                </div>
                                                                            @elseif($log->status == "paid")
                                                                                <div class="timeline-event-icon bg-success">
                                                                                    <i class="fa fa-dollar-sign"></i>
                                                                                </div>
                                                                            @elseif($log->status == "Fulfillment")
                                                                                <div class="timeline-event-icon bg-primary">
                                                                                    <i class="fa fa-star"></i>
                                                                                </div>
                                                                            @elseif($log->status == "Fulfillment Cancelled")
                                                                                <div class="timeline-event-icon bg-danger">
                                                                                    <i class="fa fa-ban"></i>
                                                                                </div>
                                                                            @elseif($log->status == "Tracking Details Added")
                                                                                <div class="timeline-event-icon bg-amethyst">
                                                                                    <i class="fa fa-truck"></i>
                                                                                </div>
                                                                            @elseif($log->status == "Delivered")
                                                                                <div class="timeline-event-icon" style="background: deeppink">
                                                                                    <i class="fa fa-home"></i>
                                                                                </div>
                                                                            @elseif($log->status == "Completed")
                                                                                <div class="timeline-event-icon" style="background: darkslategray">
                                                                                    <i class="fa fa-check"></i>
                                                                                </div>
                                                                            @endif
                                                                            <div class="timeline-event-block block js-appear-enabled animated fadeIn" data-toggle="appear">
                                                                                <div class="block-header block-header-default">
                                                                                    <h3 class="block-title">{{$log->status}}</h3>
                                                                                    <div class="block-options">
                                                                                        <div class="timeline-event-time block-options-item font-size-sm font-w600" style="color: grey">
                                                                                            {{date_create($log->created_at)->format('d M, Y h:i a')}}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="block-content">
                                                                                    <p> {{$log->message}} </p>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <p> No Order Logs Found </p>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                    </tbody>
                                </table>

                            @else
                                <p class="text-center"> No Orders Available</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tickets" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if(count($user->has_tickets) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>

                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Last Reply at</th>
                                        <th style="text-align: right">
                                        </th>
                                    </tr>
                                    </thead>

                                    @foreach($user->has_tickets()->orderBy('updated_at','DESC')->get() as $index => $ticket)
                                        <tbody class="">
                                        <tr>

                                            <td class="font-w600"><a href="">{{ $ticket->title }}</a></td>
                                            <td>
                                                <span class="badge @if($ticket->priority == 'low') badge-primary @elseif($ticket->priority == 'medium') badge-warning @else badge-danger @endif" >{{$ticket->priority}}</span>

                                            </td>
                                            <td>
                                                @if($ticket->category == 'default')
                                                    <span class="badge badge-light">{{$ticket->category}}</span>
                                                @else
                                                    <span class="badge" style="background: {{$ticket->has_category->color}};color: white">{{$ticket->category}}</span>

                                                @endif
                                            </td>
                                            <td>
                                                @if($ticket->has_status != null)
                                                    <span class="badge " style="background: {{$ticket->has_status->color}};color: white;"> {{$ticket->has_status->status}}</span>
                                                @endif
                                            </td>

                                            <td>{{\Carbon\Carbon::parse($ticket->last_reply_at)->diffForHumans()}}</td>
                                            <td class="">
                                                <div class="btn-group">
                                                    <a href="{{route('sales_managers.ticket.view',$ticket->id)}}"
                                                       class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="View Ticket"><i class="fa fa-eye"></i></a>
                                                    <a href=""
                                                       class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="Delete Ticket"><i class="fa fa-times"></i></a>
                                                </div>
                                            </td>

                                        </tr>
                                        </tbody>

                                    @endforeach
                                </table>

                            @else
                                <p class="text-center">No Tickets Found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="customers" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if (count($user->has_customers) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>

                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Total Orders</th>
                                        <th>Total Spends</th>
                                        <th style="text-align: right">
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach($user->has_customers()->orderBy('created_at','DESC')->get() as $index => $customer)
                                        <tr>

                                            <td class="font-w600"><a href="{{route('sales_managers.customer.view',$customer->id)}}">{{ $customer->first_name }} {{$customer->last_name}}</a></td>
                                            <td>
                                                {{$customer->email}}

                                            </td>
                                            <td>
                                                {{count($customer->has_orders)}}
                                            </td>
                                            <td>
                                                {{number_format($customer->total_spent,2)}} USD
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <a href="{{route('sales_managers.customer.view',$customer->id)}}"
                                                       class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="View Customer"><i class="fa fa-eye"></i></a>
                                                </div>

                                            </td>

                                        </tr>


                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center"> No Customers Found </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="products" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if($user->has_stores()->count() > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>Fulfilled By</th>
                                        <th style="text-align: right"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach($user->has_stores()->get() as $store)
                                        @if(count($store->has_products) > 0)
                                            @foreach($store->has_products()->orderBy('created_at','DESC')->get() as $index => $product)
                                                <tr>
                                                    <td>
                                                        <img @if(count($product->has_images) > 0)
                                                             @foreach($product->has_images()->orderBy('position')->get() as $index => $image)
                                                             @if($index == 0)
                                                             @if($image->isV == 0)
                                                             src="{{asset('images')}}/{{$image->image}}"
                                                             @else src="{{asset('images/variants')}}/{{$image->image}}"
                                                             @endif
                                                             @endif
                                                             @endforeach
                                                             @else
                                                             s="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg"
                                                             @endif alt="" class="img-avatar">
                                                    </td>
                                                    <td>
                                                        @if($product->linked_product_id != null)
                                                            <a href="{{route('product.view',$product->linked_product_id)}}">{{$product->title}}</a>
                                                        @else
                                                            {{$product->title}}
                                                        @endif

                                                    </td>

                                                    <td>${{number_format($product->price,2)}}</td>
                                                    <td><span class="mb2 font-size-sm" style="color: grey">@if($product->fulfilled_by == "Fantasy") WeFulfill @else {{$product->fulfilled_by}} @endif</span></td>
                                                    <td class="">
                                                        <div class="btn-group">
                                                            @if($product->linked_product_id != null)
                                                                <a href="{{route('product.view',$product->linked_product_id)}}"
                                                                   class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                                   data-original-title="View Product"><i class="fa fa-eye"></i></a>
                                                            @endif


                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center"> No Products Found !</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="payments" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if (count($user->has_payments) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>

                                        <th>Order</th>
                                        <th style="width: 10%">Payer</th>
                                        <th>Amount</th>
                                        <th>Source</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                    </thead>

                                    @foreach($user->has_payments()->orderBy('created_at','DESC')->get() as $index => $payment)
                                        <tbody class="">
                                        <tr>
                                            <td class="font-w600"> @if($payment->has_order)<a href="{{route('store.order.view',$payment->has_order->id)}}">{{ $payment->has_order->name }}</a> @else Order Details Deleted @endif</td>
                                            <td>
                                                {{$payment->name}}
                                            </td>

                                            <td>
                                                {{number_format($payment->amount,2)}} USD
                                            </td>
                                            <td>
                                                @if($payment->card_last_four != null)
                                                    <span class="badge badge-warning"> <i class="fa fa-credit-card"></i> CARD </span>
                                                @elseif($payment->paypal_payment_id != null)
                                                    <span class="badge badge-success"> <i class="fab fa-paypal"></i> PAYPAL </span>
                                                @else
                                                    <span class="badge badge-primary"> <i class="fa fa-wallet"></i> WALLET </span>
                                                @endif

                                            </td>
                                            <td>
                                                {{date_create($payment->created_at)->format('d-m-Y h:i a') }}
                                            </td>

                                        </tr>
                                        </tbody>

                                    @endforeach
                                </table>
                            @else
                                <div class="block">
                                    <div class="block-content">
                                        <p class="text-center">No Payments Founds</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="wallet" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if($wallet != null)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>
                                        <th style="width: 10%">Wallet Token #</th>
                                        <th>Owner</th>
                                        <th>Available</th>
                                        <th>Pending</th>
                                        <th>Used</th>
                                        <th>Top-up Requests</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{$wallet->wallet_token}}</td>
                                        <td>{{$wallet->owner->name}}</td>

                                        <td>{{number_format($wallet->available,2)}} USD</td>
                                        <td>{{number_format($wallet->pending,2)}} USD</td>
                                        <td>{{number_format($wallet->used,2)}} USD</td>
                                        <td>{{count($wallet->requests)}}</td>
                                        <td class="text-center">
                                            <a href="{{route('sales_managers.wallets.detail',$wallet->id)}}"
                                               class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                               data-original-title="View Wallet"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    </tbody>

                                </table>
                            @else
                                <p class="text-center">No Wallet Information Found!</p>
                            @endif
                        </div>
                    </div>

                </div>

            <div class="tab-pane" id="settings" role="tabpanel">
                <div class="block">
                    <div class="block-content">
                        <p class="text-center"> Coming Soon ... </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
