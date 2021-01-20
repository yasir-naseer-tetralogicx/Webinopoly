@extends('layout.single')
@section('content')

    @php
        $total_discount = 0;

    @endphp
    <div class="content">
        <form class="row bulk-forms bulk-payment-form" method="post" action="{{ route('store.order.wallet.pay.bulk') }}">
            @csrf
            @foreach($orders as $order)
                <div  class="col-md-12">
                    <input type="hidden" value="{{ $order->id }}" name="order_ids[]">
                    <div class="block">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">
                                {{$order->name}}
                            </h3>
                        </div>
                        <div class="block-content">
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th style="width: 10%">Name</th>
                                    <th>Fulfilled By</th>
                                    <th>Cost</th>
                                    <th>Discount</th>
                                    <th>Price X Quantity</th>
                                    <th>Status</th>
                                    <th>Billing Address</th>
                                    <th>Shipping Address</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $fixed_applied = false;
                                    $discount_applied = true;
                                    $n = $order->line_items->where('fulfilled_by', '!=', 'store')->sum('quantity');
                                    $line_item_count = count($order->line_items);

                                    if($order->line_items->where('fulfilled_by', '!=', 'store')->count() >=2){
                                        $is_general_discount = true;
                                    }
                                    else {
                                        $is_general_discount = false;
                                    }

                                    if(\App\GeneralDiscountPreferences::first()->global == 1) {
                                        $is_applied_for_general_dsiscount = true;
                                    }
                                    else {
                                        $stores = \App\GeneralDiscountPreferences::first()->stores_id;
                                        $store_array= json_decode($stores);
                                        if(in_array($shop->id, $store_array)) { $is_applied_for_general_dsiscount = true; } else { $is_applied_for_general_dsiscount = false; }
                                    }

                                    if(\App\GeneralFixedPricePreferences::first()->global == 1) {
                                        $is_applied_for_general_fixed = true;
                                    }
                                    else {
                                        $stores = \App\GeneralFixedPricePreferences::first()->stores_id;
                                        $store_array= json_decode($stores);
                                        if(in_array($shop->id, $store_array)) { $is_applied_for_general_fixed = true; } else { $is_applied_for_general_fixed = false; }
                                    }

                                    if(\App\TieredPricingPrefrences::first()->global == 1) {
                                        $is_applied = true;
                                    }
                                    else {
                                        $stores = \App\TieredPricingPrefrences::first()->stores_id;
                                        $store_array= json_decode($stores);
                                        if(in_array($shop->id, $store_array)) { $is_applied = true; } else { $is_applied = false; }
                                    }

                                @endphp
                                @foreach($order->line_items as $item)
                                    @if($item->fulfilled_by != 'store')
                                        <tr>
                                            <td>
                                                @if($order->custom == 0)
                                                    @if($item->linked_variant != null)
                                                        <img class="img-avatar"
                                                             @if($item->linked_variant->has_image == null)  src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg"
                                                             @else @if($item->linked_variant->has_image->isV == 1) src="{{asset('images/variants')}}/{{$item->linked_variant->has_image->image}}" @else src="{{asset('images')}}/{{$item->linked_variant->has_image->image}}" @endif @endif alt="">
                                                    @else
                                                        @if($item->linked_product != null)
                                                            @if(count($item->linked_product->has_images)>0)
                                                                @if($item->linked_product->has_images[0]->isV == 1)
                                                                    <img class="img-avatar img-avatar-variant"
                                                                         src="{{asset('images/variants')}}/{{$item->linked_product->has_images[0]->image}}">
                                                                @else
                                                                    <img class="img-avatar img-avatar-variant"
                                                                         src="{{asset('images')}}/{{$item->linked_product->has_images[0]->image}}">
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
                                                @else
                                                    @if($item->linked_real_variant != null)
                                                        <img class="img-avatar"
                                                             @if($item->linked_real_variant->has_image == null)  src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg"
                                                             @else @if($item->linked_real_variant->has_image->isV == 1) src="{{asset('images/variants')}}/{{$item->linked_real_variant->has_image->image}}" @else src="{{asset('images')}}/{{$item->linked_real_variant->has_image->image}}" @endif @endif alt="">
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
                                                @endif
                                            </td>
                                            <td style="width: 30%">
                                                {{$item->name}}

                                            </td>
                                            <td>
                                                @if($item->fulfilled_by == 'store')
                                                    <span class="badge badge-danger"> Store</span>
                                                @elseif ($item->fulfilled_by == 'Fantasy')
                                                    <span class="badge badge-success"> WeFullFill </span>
                                                @else
                                                    <span class="badge badge-success"> {{$item->fulfilled_by}} </span>
                                                @endif
                                            </td>

                                            <td>{{number_format($item->cost,2)}}  X {{$item->quantity}}  USD</td>
                                            <td>
                                                @php
                                                    $variant = $item->linked_variant;
                                                    $real_variant = null;


                                                    if($variant) {
                                                        $real_variant = \App\ProductVariant::where('sku', $variant->sku)->first();
                                                    }
                                                    else{
                                                        $retailer_product = $item->linked_product;
                                                        $real_variant = \App\Product::where('title', $retailer_product->title)->first();
                                                    }
                                                @endphp
                                                @if($real_variant != null && $is_applied && !($is_general_discount))
                                                    @if(count($real_variant->has_tiered_prices) > 0)
                                                        @foreach($real_variant->has_tiered_prices as $var_price)
                                                            @php
                                                                $price = null;

                                                                $qty = (int) $item->quantity;
                                                                if(($var_price->min_qty <= $qty) && ($qty <= $var_price->max_qty)) {
                                                                    if($var_price->type == 'fixed') {
                                                                        $price = $var_price->price * ($qty -1);
                                                                        $price = number_format($price, 2);
                                                                        $total_discount = $total_discount + $price;
                                                                        $price = $price . " USD";
                                                                    }
                                                                    else if($var_price->type == 'discount') {
                                                                        $discount = (double) $var_price->price;
                                                                        $price = $item->cost - ($item->price * $discount / 100);
                                                                        $price = $price * ($qty -1);
                                                                        $price = number_format($price, 2);
                                                                        $total_discount = $total_discount + $price;
                                                                        $price = $price . " USD";
                                                                    }
                                                                }
                                                                else {
                                                                    $price = '';
                                                                }
                                                            @endphp
                                                            {{ ($price) }}
                                                        @endforeach
                                                    @else
                                                        <span></span>
                                                    @endif
                                                @else
                                                    <span></span>
                                                @endif

                                                @if($is_general_discount && $is_applied_for_general_dsiscount)
                                                    @php
                                                        $discount = (double) \App\GeneralDiscountPreferences::first()->discount_amount;
                                                        $price = $order->cost_to_pay - ($order->cost_to_pay * $discount / 100);
                                                        $price = number_format($price, 2);
                                                        if(!$discount_applied) {
                                                            $total_discount = $total_discount + $price;
                                                            $total_discount = $order->cost_to_pay - $total_discount;
                                                            $discount_applied = true;
                                                        }
                                                    @endphp
                                                    {{ \App\GeneralDiscountPreferences::first()->discount_amount }} % on whole order
                                                @endif

                                                @if($is_general_discount && $is_applied_for_general_fixed)
                                                    @php
                                                        if(!$fixed_applied) {
                                                           $total_discount += (double) \App\GeneralFixedPricePreferences::first()->fixed_amount * ($n - 1);
                                                           $fixed_applied = true;
                                                        }
                                                    @endphp
                                                    {{ number_format(\App\GeneralFixedPricePreferences::first()->fixed_amount * ($n - 1), 2) }} $ off on whole order
                                                @endif

                                            </td>
                                            <td>{{$item->price}} X {{$item->quantity}}  USD </td>
                                            <td>
                                                @if($item->fulfillment_status == null)
                                                    <span class="badge badge-warning"> Unfulfilled</span>
                                                @elseif($item->fulfillment_status == 'partially-fulfilled')
                                                    <span class="badge badge-danger"> Partially Fulfilled</span>
                                                @else
                                                    <span class="badge badge-success"> Fulfilled</span>
                                                @endif
                                            </td>
                                            @php
                                                $billing = json_decode($order->billing_address);
                                                $shipping = json_decode($order->shipping_address)
                                            @endphp
                                            <td class="align-middle">
                                                @if(!(is_null($billing)))
                                                    <p style="font-size: 14px">{{$billing->first_name}} {{$billing->last_name}} <br> {{$billing->company}}
                                                        <br> {{$billing->address1}}
                                                        <br> {{$billing->address2}}
                                                        <br> {{$billing->city}}
                                                        <br> {{$billing->province}} {{$billing->zip}}
                                                        <br> {{$billing->country}}
                                                        <br> {{$billing->phone}}
                                                    </p>
                                                @else
                                                    <p class="mb-0">Not Provided!</p>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if(!(is_null($billing)))
                                                    <p style="font-size: 14px">{{$shipping->first_name}} {{$shipping->last_name}}
                                                        <br> {{$shipping->company}}
                                                        <br> {{$shipping->address1}}
                                                        <br> {{$shipping->address2}}
                                                        <br> {{$shipping->city}}
                                                        <br> {{$shipping->province}} {{$shipping->zip}}
                                                        <br> {{$shipping->country}}
                                                        @if(isset($shipping->phone))
                                                            <br>{{$shipping->phone}}
                                                        @endif
                                                    </p>
                                                @else
                                                    <p class="mb-0">Not Provided!</p>
                                                @endif
                                            </td>

                                        </tr>
                                    @endif
                                @endforeach

                                </tbody>


                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
                <div class="col-md-12">
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
                                Subtotal
                            </td>
                            <td align="right">
                                {{number_format($cost_to_pay - $shipping_price,2)}} USD
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Total Discount
                            </td>
                            <td align="right">
                                {{ number_format($total_discount,2) }} USD
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Shipping Price
                            </td>
                            <td align="right">
                                {{number_format($shipping_price,2)}} USD
                            </td>
                        </tr>

                        <tr>
                            <td>
                                Total Cost  to Pay
                            </td>
                            <td align="right">
                                {{number_format($cost_to_pay  - $total_discount,2)}} USD
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td align="right">
                                <button type="button" class="btn btn-success bulk-wallet-pay-button" data-pay=" {{number_format($cost_to_pay  - $total_discount,2)}} USD" data-href="{{route('store.orders')}}" ><i class="fa fa-wallet"></i> Wallet Pay</button>
{{--                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#payment_modal"><i class="fa fa-credit-card"></i> Credit Card Pay</button>--}}
                                <button type="button" class="btn btn-success paypal-pay-button"
                                        data-toggle="modal" data-target="#paypal_pay_trigger">
                                    <i class="fab fa-paypal"></i> Paypal Pay
                                </button>
                            </td>
                        </tr>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        </form>
    </div>

    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout" role="document">
            <div class="modal-content">
                <form action="{{ route('store.order.proceed.bulk.payment') }}" method="POST" class="bulk-card-form">
                    @csrf
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark text-left">
                            <h3 class="block-title">Payment for Orders</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option">
                                    <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                </button>
                            </div>
                        </div>


                        <div class="block-content font-size-sm">
                            <div class="form-group">
                                <div class="col-sm-12 text-left">
                                    <div class="form-material">
                                        <label for="material-error">Card Name</label>
                                        <input  class="form-control" type="text" required=""  name="card_name"
                                                placeholder="Enter Card Title here">
                                        <input type="hidden" name="order_ids" value="{{ $orders }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 text-left">
                                    <div class="form-material">
                                        <label for="material-error">Card Number</label>
                                        <input type="text" required=""  name="card_number"  class="form-control js-card js-masked-enabled"
                                               placeholder="9999-9999-9999-9999">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 text-left">
                                    <div class="form-material">
                                        <label for="material-error">Amount to Pay</label>
                                        <input  class="form-control" type="text" readonly value="{{number_format($cost_to_pay,2)}} USD"  name="amount"
                                                placeholder="Enter 14 Digit Card Number here">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 text-left">
                                    <div class="form-material">
                                        <label for="material-error">WeFullFill Charges ({{$settings->payment_charge_percentage}}%)</label>
                                        <input  class="form-control" type="text" readonly value="{{number_format($cost_to_pay*$settings->payment_charge_percentage/100,2)}} USD"  name="amount"
                                                placeholder="Enter 14 Digit Card Number here">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 text-left">
                                    <div class="form-material">
                                        <label for="material-error">Total Cost</label>
                                        <input  class="form-control" type="text" readonly value="{{number_format($cost_to_pay+$cost_to_pay*$settings->payment_charge_percentage/100,2)}} USD"  name="amount"
                                                placeholder="Enter 14 Digit Card Number here">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="block-content block-content-full text-right border-top">
                            <button type="button" class="btn btn-success bulk-card-btn">Proceed Payment</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                                Subtotal: {{number_format($orders->where('paid',0)->sum('cost_to_pay')  - $total_discount,2)}}
                                USD
                                <br>
                                WeFullFill Paypal Fee
                                ({{$settings->paypal_percentage}}
                                %): {{number_format(($orders->where('paid',0)->sum('cost_to_pay')  - $total_discount)*$settings->paypal_percentage/100,2)}}
                                USD
                                <br>Total Cost
                                : {{number_format(($orders->where('paid',0)->sum('cost_to_pay') - $total_discount)+$orders->where('paid',0)->sum('cost_to_pay')*$settings->paypal_percentage/100,2)}} USD
                            </p>
                        </div>
                        <p> A amount
                            of {{number_format(($orders->where('paid',0)->sum('cost_to_pay')  - $total_discount) +$orders->where('paid',0)->sum('cost_to_pay')*$settings->paypal_percentage/100,2)}}
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
        <form action="{{ route('store.order.paypal.bulk.pay') }}"
              method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="order_ids" value="{{ $orders }}">
            <textarea name="response"></textarea>
        </form>
    </div>

    <script
        src="https://www.paypal.com/sdk/js?client-id=ASxb6_rmf3pte_En7MfEVLPe_KDZQj68bKpzJzl7320mmpV3uDRDLGCY1LaCkyYZ4zNpHdC9oZ73-WFv">
    </script>


{{--    <script src="https://www.paypal.com/sdk/js?client-id=AV6qhCigre8RgTt8E6Z0KNesHxr1aDyJ2hmsk2ssQYmlaVxMHm2JFJvqDCsU15FhoCJY0mDzOu-jbFPY&currency=USD"></script>--}}

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

@endsection
