<!DOCTYPE html>
<html>
<head>
    <head>
        <title>New order</title>
    </head>
</head>
<body style="margin: 0; ">
<style>
    .email-body
    {
        color:black;
    }
    .email-content
    {
        /*max-width: 450px;*/
        width : 90%;
    }
    .email-content-detail
    {
        margin: 50px 0;
    }
    .wrap {
        padding-left: 20px;
        background-color: #7daa40 !important;
        color: #ffffff !important;
        padding: 1px 20px
    }

    .custom-badge {
        background: #f3b760;
        color: white;
        padding: 2px 5px;
        border-radius: 5px;
    }

    .wrap .right{
        text-align: right !important;
    }

    tr:nth-child(even) {
        background: #f1f1f1;
    }

    .wrap .left{
        text-align: left !important;
    }
    @media (max-width: 570px) {
        .email_btn
        {
            padding:15px 30px !important;
            font-size:18px !important;
        }
    }
    @media (max-width: 430px) {
        .email_btn {
            padding: 15px 20px !important;
            font-size: 12px !important;
        }
    }
    @media (max-width: 400px) {
        .email_btn {
            padding: 15px 10px !important;
            font-size: 12px !important;
        }
        span
        {
            font-size:18px !important ;
        }
    }
</style>


<div class="email-body" style="padding: 20px;max-width: 80%;margin: auto; font-family: cursive;">
    <div class="email-contaner" style="border: 2px solid #7daa40;padding: 25px;">
        <div class="email-content" style="margin: auto;  text-align: center; ">
            <div class="email-logo">
                <img src="https://cdn.shopify.com/s/files/1/0370/7361/7029/files/image_3.png?v=1585895317" alt="Wefullfill" style="width: 35%">
            </div>

            <div class="email-content-detail" style="margin: 50px 0;">
                <h1 class="email-title" style="margin: 0;margin-bottom: 30px;font-size: 34px;">{{ $template->subject }}</h1>
                <p class="email-message-1" style=" margin: 0;margin-bottom: 30px;font-size: 20px;line-height: 1.53;" >{{ $template->body }} </p>
                <hr>
                <div class="" style="width: 100%">
                    <div class="wrap">
                        <div class="left">
                            <h2 style="color: #ffffff; margin-right: 5px; margin-top: 7px; margin-bottom: 7px;">Line Items
                                @if($order->paid == '0')
                                    <span class="custom-badge" style="font-size: small; margin-right: 5px;"> Unpaid </span>
                                @elseif($order->paid == '1')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px; background-color: #46c37b;"> Paid </span>
                                @elseif($order->paid == '2')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px;"> Refunded </span>
                                @endif

                                @if($order->status == 'Paid')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px;"> Unfulfilled</span>
                                @elseif($order->status == 'unfulfilled')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px;"> {{ucfirst($order->status)}}</span>
                                @elseif($order->status == 'partially-shipped')
                                    <span class="custom-badge" style="font-size: small; margin-right: 5px;"> {{ucfirst($order->status)}}</span>
                                @elseif($order->status == 'shipped')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px;"> {{ucfirst($order->status)}}</span>
                                @elseif($order->status == 'delivered')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px;"> {{ucfirst($order->status)}}</span>
                                @elseif($order->status == 'completed')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px;"> {{ucfirst($order->status)}}</span>
                                @elseif($order->status == 'new')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px;"> Draft </span>
                                @elseif($order->status == 'cancelled')
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px;"> {{ucfirst($order->status)}} </span>
                                @else
                                    <span  class="custom-badge" style="font-size: small; margin-right: 5px; background-color: #46c37b;">  {{ucfirst($order->status)}} </span>
                                @endif

                            </h2>
                        </div>
                    </div>

                    <div class="" style="   padding: 14px;">
                        <table class="table table-borderless table-striped table-vcenter">
                            <thead>
                            <tr>
                                <th>
                                </th>
                                <th style="width: 10%; text-align: left;">Name</th>
                                <th>Fulfilled By</th>
                                <th>Cost</th>
                                <th>Price X Quantity</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->line_items as $item)
                                @if($item->fulfilled_by != 'store')
                                    <tr>
                                        <td>
                                            @if($order->custom == 0)
                                                @if($item->linked_variant != null)
                                                    <img class="" style="width: 40px !important; height: auto;"
                                                         @if($item->linked_variant->has_image == null)  src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg"
                                                         @else @if($item->linked_variant->has_image->isV == 1)
                                                         src="{{asset('images/variants')}}/{{$item->linked_variant->has_image->image}}"
                                                         @else
                                                         src="{{asset('images')}}/{{$item->linked_variant->has_image->image}}"
                                                         @endif
                                                         @endif alt="">
                                                @else
                                                    @if($item->linked_product != null)
                                                        @if(count($item->linked_product->has_images)>0)
                                                            @if($item->linked_product->has_images[0]->isV == 1)
                                                                <img class="" style="width: 40px !important; height: auto;"
                                                                     src="{{asset('images/variants')}}/{{$item->linked_product->has_images[0]->image}}">
                                                            @else
                                                                <img class="" style="width: 40px !important; height: auto;"
                                                                     src="{{asset('images')}}/{{$item->linked_product->has_images[0]->image}}">
                                                            @endif
                                                        @else
                                                            <img class="" style="width: 40px !important; height: auto;"
                                                                 src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                                        @endif
                                                    @else
                                                        <img class="" style="width: 40px !important; height: auto;"
                                                             src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                                    @endif
                                                @endif

                                            @else
                                                @if($item->linked_real_variant != null)
                                                    <img class="" style="width: 40px !important; height: auto;"
                                                         @if($item->linked_real_variant->has_image == null)  src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg"
                                                         @else @if($item->linked_real_variant->has_image->isV == 1) src="{{asset('images/variants')}}/{{$item->linked_real_variant->has_image->image}}" @else src="{{asset('images')}}/{{$item->linked_real_variant->has_image->image}}" @endif @endif alt="">
                                                @else
                                                    @if($item->linked_real_product != null)
                                                        @if(count($item->linked_real_product->has_images)>0)
                                                            @if($item->linked_real_product->has_images[0]->isV == 1)
                                                                <img class="" style="width: 40px !important; height: auto;"
                                                                     src="{{asset('images/variants')}}/{{$item->linked_real_product->has_images[0]->image}}">
                                                            @else
                                                                <img class="" style="width: 40px !important; height: auto;"
                                                                     src="{{asset('images')}}/{{$item->linked_real_product->has_images[0]->image}}">
                                                            @endif
                                                        @else
                                                            <img class="" style="width: 40px !important; height: auto;"
                                                                 src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                                        @endif
                                                    @else
                                                        <img class="" style="width: 40px !important; height: auto;"
                                                             src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                                    @endif
                                                @endif
                                            @endif
                                        </td>

                                        <td style="width: 30%; text-align: left !important;">
                                            {{$item->name}}

                                        </td>
                                        <td>
                                            @if($item->fulfilled_by == 'store')
                                                <span class=""> Store</span>
                                            @elseif ($item->fulfilled_by == 'Fantasy')
                                                <span class=""> WeFullFill </span>
                                            @else
                                                <span class=""> {{$item->fulfilled_by}} </span>
                                            @endif
                                        </td>

                                        <td>{{number_format($item->cost,2)}}  X {{$item->quantity}}  USD</td>
                                        <td>{{$item->price}} X {{$item->quantity}}  USD </td>
                                        <td>
                                            @if($item->fulfillment_status == null)
                                                <span class="custom-badge" style=""> Unfulfilled</span>
                                            @elseif($item->fulfillment_status == 'partially-fulfilled')
                                                <span class="custom-badge" > Partially Fulfilled</span>
                                            @else
                                                <span class="custom-badge" style="background-color: #46c37b"> Fulfilled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="">
                        <div class="wrap">
                            <div class="left">
                                <h2 class="" style="color: #ffffff !important; margin-top: 7px; margin-bottom: 7px;">Summary</h2>
                            </div>
                        </div>
                        <div class="" style=" padding: 15px;">
                            <div class="" style="display: flex; justify-content: space-between" >
                                <div>
                                    <p align="left">Subtotal ({{count($order->line_items)}} items)</p>
                                    <p align="left">Shipping Price</p>
                                    <p align="left">Total Cost</p>
                                </div>
                                <div>
                                    <p align="right">{{number_format($order->cost_to_pay - $order->shipping_price,2)}} USD</p>
                                    <p align="right">{{number_format($order->shipping_price,2)}} USD</p>
                                    <p align="right">{{number_format($order->cost_to_pay,2)}} USD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <br>
                <br>
                <div>
                    <a href="{{ route('store.order.view', $order->id) }}" target="_blank" class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">View Details</a>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="email-footer" style=" padding: 25px 10px; color: white; ">

    <div class="email-footer-caption">
        <ul style=" color: white; list-style: none; padding: 0 ;  margin-top: 25px;text-align: center; ">
            <li class="site-name" style="width: max-content; display: inline-block; margin-right: 15px;padding-right: 15px;border-right: 1px solid white;"><a href="" style="color: white;text-decoration: none;">WeFullFill</a></li>
            <li class="dalls" style="width: max-content; display: inline-block; margin-right: 15px; padding-right: 15px; border-right: 1px solid white;">ROOM 2103 TUNG CHIU COMMERCIAL CENTRE 193,LOCKHART ROAD WAN</li>
            <li class="country" style="width: max-content; display: inline-block;">China</li>
        </ul>
    </div>

</div>

</body>
</html>
