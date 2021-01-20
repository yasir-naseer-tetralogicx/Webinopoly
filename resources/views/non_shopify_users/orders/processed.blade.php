@extends('layout.shopify')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Bulk Import Orders
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href=""> Bulk Import Orders</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content">
                        <div class="text-right">
                            <a href="{{route('users.files.download_processed_orders',$file->id)}}" target="_blank"
                               class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" title=""
                               data-original-title="Download Processed Orders Excel File">Processed Orders Export</a>
                        </div>
                        @if (count($orders) > 0)
                            <table
                                class="table js-table-sections table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>
                                    <th></th>

                                    <th>Name</th>
                                    <th>Order Date</th>
                                    <th>Cost</th>
                                    <th>Payment Status</th>
                                    <th>Status</th>
                                    <th style="text-align: right"></th>
                                </tr>
                                </thead>

                                @foreach($orders as $index => $order)
                                    <tbody class="js-table-sections-header">
                                    <tr>
                                        <td class="text-center">
                                            <i class="fa fa-angle-right text-muted"></i>
                                        </td>

                                        <td class="font-w600"><a
                                                href="{{route('users.order.view',$order->id)}}">{{ $order->name }}</a>
                                        </td>
                                        <td>
                                            {{date_create($order->shopify_created_at)->format('d m, Y h:i a') }}
                                        </td>
                                        <td>
                                            {{number_format($order->cost_to_pay,2)}} USD

                                        </td>
                                        <td>
                                            @if($order->paid == '0')
                                                <span class="badge badge-warning"
                                                      style="font-size: small"> Unpaid </span>
                                            @elseif($order->paid == '1')
                                                <span class="badge badge-success" style="font-size: small"> Paid </span>
                                            @elseif($order->paid == '2')
                                                <span class="badge badge-danger"
                                                      style="font-size: small;"> Refunded</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->status == 'Paid')
                                                <span class="badge badge-warning"
                                                      style="font-size: small"> Unfulfilled </span>

                                            @elseif($order->status == 'unfulfilled')
                                                <span class="badge badge-warning"
                                                      style="font-size: small"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'partially-shipped')
                                                <span class="badge "
                                                      style="font-size: small;background: darkolivegreen;color: white;"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'shipped')
                                                <span class="badge "
                                                      style="font-size: small;background: orange;color: white;"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'delivered')
                                                <span class="badge "
                                                      style="font-size: small;background: deeppink;color: white;"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge "
                                                      style="font-size: small;background: darkslategray;color: white;"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'new')
                                                <span class="badge badge-warning"
                                                      style="font-size: small"> Draft </span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge badge-warning"
                                                      style="font-size: small"> {{ucfirst($order->status)}} </span>
                                            @else
                                                <span class="badge badge-success"
                                                      style="font-size: small">  {{ucfirst($order->status)}} </span>
                                            @endif

                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{route('users.order.view',$order->id)}}"
                                                   class="btn btn-sm btn-success" type="button" data-toggle="tooltip"
                                                   title=""
                                                   data-original-title="View Order"><i class="fa fa-eye"></i></a>
                                                <a href="{{route('users.order.delete',$order->id)}}"
                                                   class="btn btn-sm btn-danger" type="button" data-toggle="tooltip"
                                                   title=""
                                                   data-original-title="Delete Order"><i class="fa fa-times"></i></a>
                                            </div>

                                        </td>

                                    </tr>
                                    </tbody>
                                    <tbody>
                                    @foreach($order->line_items as $item)
                                        @if($item->fulfilled_by != 'store')
                                            <tr>
                                                <td class="text-center">

                                                </td>
                                                <td>
                                                    @if($item->linked_real_variant != null)
                                                        <img class="img-avatar"
                                                             @if($item->linked_real_variant->has_image == null)  src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg"
                                                             @else @if($item->linked_real_variant->has_image->isV == 1) src="{{asset('images/variants')}}/{{$item->linked_real_variant->has_image->image}}"
                                                             @else src="{{asset('images')}}/{{$item->linked_real_variant->has_image->image}}"
                                                             @endif @endif alt="">
                                                    @else
                                                        @if($item->linked_real_product != null)
                                                            @if(count($item->linked_real_product->has_images)>0)
                                                                @if($item->linked_real_product->has_images[0]->isV == 1)
                                                                    <img class="img-avatar img-avatar-variant"
                                                                         src="{{asset('images/variants')}}/{{$item->linked_real_product->has_images[0]->image}}">
                                                                @else
                                                                    <img class="img-avatar img-avatar-variant"
                                                                         src="{{asset('images')}}/{{$item->linked_real_product->has_images[0]->image}}">
                                                                @endif
                                                            @else
                                                                <img class="img-avatar img-avatar-variant"
                                                                     src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                                            @endif
                                                        @else
                                                            <img class="img-avatar img-avatar-variant"
                                                                 src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    {{$item->name}}

                                                </td>
                                                <td>
                                                    Fulfilled By:
                                                    @if($item->fulfilled_by == 'store')
                                                        <span class="badge badge-danger"> Store</span>
                                                    @elseif ($item->fulfilled_by == 'Fantasy')
                                                        <span class="badge badge-success"> WeFullFill </span>
                                                    @else
                                                        <span
                                                            class="badge badge-success"> {{$item->fulfilled_by}} </span>
                                                    @endif
                                                </td>
                                                <td>{{number_format($item->cost,2)}} X {{$item->quantity}} USD</td>
                                                <td>
                                                    @if($item->fulfillment_status == null)
                                                        <span class="badge badge-warning"> Unfulfilled</span>
                                                    @elseif($item->fulfillment_status == 'partially-fulfilled')
                                                        <span class="badge badge-danger"> Partially Fulfilled</span>
                                                    @else
                                                        <span class="badge badge-success"> Fulfilled</span>
                                                    @endif
                                                </td>
                                                <td></td>

                                            </tr>
                                        @endif
                                    @endforeach

                                    </tbody>
                                @endforeach
                            </table>
                        @else
                            <p>No Orders Found </p>
                        @endif
                    </div>
                </div>
                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            Summary
                        </h3>
                    </div>
                    <div class="block-content">
                        <table class="table table-borderless table-vcenter">
                            <thead>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    Paid Orders
                                </td>
                                <td align="right">
                                    {{$orders->where('paid',1)->count()}}
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Unpaid Orders
                                </td>
                                <td align="right">
                                    {{$orders->where('paid',0)->count()}}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Cost Paid
                                </td>
                                <td align="right">
                                    {{number_format($orders->where('paid',1)->sum('cost_to_pay'),2)}} USD
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Cost to Pay
                                </td>
                                <td align="right">
                                    {{number_format($orders->where('paid',0)->sum('cost_to_pay'),2)}} USD
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td align="right">
                                    @if($orders->where('paid',0)->count() > 0)
                                        <button class="btn btn-success" data-toggle="modal"
                                                data-target="#payment_modal"><i class="fa fa-credit-card"></i> Credit
                                            Card Pay
                                        </button>
                                        <button class="btn btn-success paypal-pay-button"
                                                data-toggle="modal" data-target="#paypal_pay_trigger"
                                                data-href="{{route('users.orders.bulk.paypal',$file->id)}}"
                                                data-percentage="{{$settings->paypal_percentage}}"
                                                data-fee="{{number_format($orders->where('paid',0)->sum('cost_to_pay')*$settings->paypal_percentage/100,2)}}"
                                                data-subtotal="{{number_format($orders->where('paid',0)->sum('cost_to_pay'),2)}}"
                                                data-pay="{{number_format($orders->where('paid',0)->sum('cost_to_pay')+$orders->where('paid',0)->sum('cost_to_pay')*$settings->paypal_percentage/100,2)}} USD">
                                            <i class="fab fa-paypal"></i> Paypal Pay
                                        </button>
                                        <button class="btn btn-success wallet-pay-button"
                                                data-href="{{route('users.orders.bulk.wallet',$file->id)}}"
                                                data-pay="{{number_format($orders->where('paid',0)->sum('cost_to_pay'),2)}} USD">
                                            <i class="fa fa-wallet"></i> Wallet Pay
                                        </button>

                                        <div class="modal" id="paypal_pay_trigger" tabindex="-1" role="dialog"
                                             aria-labelledby="modal-block-vcenter" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div
                                                        class="block block-rounded block-themed block-transparent mb-0">
                                                        <div
                                                            class="block-content cst_content_wrapper font-size-sm text-center">
                                                            <h2>Are your sure?</h2>
                                                            <div class="text-center"><p>
                                                                    Subtotal: {{number_format($orders->where('paid',0)->sum('cost_to_pay'),2)}}
                                                                    USD
                                                                    <br>
                                                                    WeFullFill Paypal Fee
                                                                    ({{$settings->paypal_percentage}}
                                                                    %): {{number_format($orders->where('paid',0)->sum('cost_to_pay')*$settings->paypal_percentage/100,2)}}
                                                                    USD
                                                                    <br>Total Cost
                                                                    : {{number_format($orders->where('paid',0)->sum('cost_to_pay')+$orders->where('paid',0)->sum('cost_to_pay')*$settings->paypal_percentage/100,2)}} USD
                                                                </p>
                                                            </div>
                                                            <p> A amount
                                                                of {{number_format($orders->where('paid',0)->sum('cost_to_pay')+$orders->where('paid',0)->sum('cost_to_pay')*$settings->paypal_percentage/100,2)}}
                                                                USD will be deducted through your Paypal Account</p>

                                                            <div class="paypal_btn_trigger">
                                                                <div id="paypal-button-container"></div>
                                                            </div>

                                                        </div>
                                                        <div
                                                            class="block-content block-content-full text-center border-top">
                                                            <button type="button" class="btn btn-danger"
                                                                    data-dismiss="modal">Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ajax_paypal_form_submit" style="display: none;">
                                            <form action="{{ route('users.orders.bulk.paypal', $file->id) }}"
                                                  method="POST">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="id" value="{{ $file->id }}">
                                                <textarea name="response"></textarea>
                                            </form>
                                        </div>

                                        <script
                                            src="https://www.paypal.com/sdk/js?client-id=ASxb6_rmf3pte_En7MfEVLPe_KDZQj68bKpzJzl7320mmpV3uDRDLGCY1LaCkyYZ4zNpHdC9oZ73-WFv">
                                        </script>

                                        <script>
                                            paypal.Buttons({
                                                    createOrder: function (data, actions) {
                                                        return actions.order.create({
                                                            purchase_units: [{
                                                                amount: {
                                                                    value: "{{number_format($orders->where('paid',0)->sum('cost_to_pay')+$orders->where('paid',0)->sum('cost_to_pay')*$settings->paypal_percentage/100,2)}}"
                                                                }
                                                            }]
                                                        });
                                                    },
                                                    onApprove: function (data, actions) {
                                                        return actions.order.capture().then(function (details) {
                                                            $('.ajax_paypal_form_submit').find('textarea').val(JSON.stringify(details));
                                                            $('.ajax_paypal_form_submit form').submit();
                                                        });
                                                    }
                                                }).render('#paypal-button-container');
                                            </script>

                                    @endif
                                </td>
                            </tr>

                            </tbody>


                        </table>

                    </div>
                </div>
                <div class="block">
                    <div class="block-header">
                        <h5 class="block-title"> Unprocessed Data </h5>
                        <div class="text-right">
                            <a href="{{route('users.files.download_unprocessed_orders',$file->id)}}" target="_blank"
                               class="btn btn-sm btn-warning" type="button" data-toggle="tooltip" title=""
                               data-original-title="Download Processed Orders Excel File">Unprocessed Orders Export</a>
                        </div>
                    </div>
                    <div class="block-content">

                        @if (count($data) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order Id</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                </tr>
                                </thead>

                                @foreach($data as $index => $item)
                                    <tbody class="">
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$item->order_number}}</td>
                                        <td>{{$item->sku}}</td>
                                        <td>{{$item->quantity}}</td>
                                        <td>{{$item->name}}</td>
                                        <td>{{$item->email}}</td>
                                        <td>{{$item->country}}</td>
                                    </tr>
                                    </tbody>

                                @endforeach
                            </table>
                        @else
                            <p>No Unprocessed Data Found </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Payment for Bulk </h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option">
                                <i class="fa fa-fw fa-times" data-dismiss="modal" aria-label="Close"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('users.orders.bulk.card',$file->id)}}" method="post">
                        @csrf

                        <div class="block-content font-size-sm">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Card Name</label>
                                        <input class="form-control" type="text" required="" name="card_name"
                                               placeholder="Enter Card Title here">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Card Number</label>
                                        <input type="text" required="" name="card_number"
                                               class="form-control js-card js-masked-enabled"
                                               placeholder="9999-9999-9999-9999">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Amount to Pay</label>
                                        <input class="form-control" type="text" readonly
                                               value="{{number_format($orders->where('paid',0)->sum('cost_to_pay'),2)}} USD"
                                               name="amount"
                                               placeholder="Enter 14 Digit Card Number here">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">WeFullFill Charges
                                            ({{$settings->payment_charge_percentage}}%)</label>
                                        <input class="form-control" type="text" readonly
                                               value="{{number_format($orders->where('paid',0)->sum('cost_to_pay')*$settings->payment_charge_percentage/100,2)}} USD"
                                               name="amount"
                                               placeholder="Enter 14 Digit Card Number here">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Total Cost</label>
                                        <input class="form-control" type="text" readonly
                                               value="{{number_format($orders->where('paid',0)->sum('cost_to_pay')+$orders->where('paid',0)->sum('cost_to_pay')*$settings->payment_charge_percentage/100,2)}} USD"
                                               name="amount"
                                               placeholder="Enter 14 Digit Card Number here">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="block-content block-content-full text-right border-top">
                            <button type="submit" class="btn btn-success">Proceed Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



@endsection
