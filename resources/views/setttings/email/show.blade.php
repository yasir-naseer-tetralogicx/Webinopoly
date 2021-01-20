@extends('layout.index')
@section('content')
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

    .wrap .right{
        text-align: right !important;
    }

    .wrap .left{
        text-align: left !important;
    }

    .wrapper{
        display: flex;
        flex-wrap: wrap;
        /*justify-content: space-between;*/
        width: 100%;
    }

    .product_div{
        margin: 10px 0;
        box-sizing: border-box;
        padding: 10px;
        width: 33.3%;
    }

    .product_div p{
        text-align: left;
    }

    .product_price{
        color: #ff0000db;
        font-weight: bold;

    }

    .product_div .product_img{
        width: 100%;
        height: auto;
    }

    .inner{
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        -webkit-box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
        -moz-box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
        box-shadow: 4px 4px 5px 1px rgba(0,0,0,0.75);
    }

    .product-btn{
        width: 100%;
        background-color: #1f6fb2;
        color: white;
        padding: 15px 0;
        border-radius: 5px;
        border: 1px solid #1f6fb2;
        font-size: 16px;
        display: block;
        text-decoration: none;
    }

    .select2-selection__choice{
        background-color: #7daa40 !important;
        border-radius: 0px;
        border: 1px solid #7daa40 !important;
    }

    @media (max-width: 742px) {
        .product_div {
            width: 50%;
        }
    }
    @media (max-width: 570px) {
        .email_btn
        {
            padding:15px 30px !important;
            font-size:18px !important;
        }

        .product_div {
            width: 100%;
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

<div class="bg-body-light">
    <div class="content content-full pt-2 pb-2">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <h1 class="flex-sm-fill h4 my-2">
                View Email Templates
            </h1>
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item" aria-current="page">
                        <a class="link-fx" href="">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">View Email Templates</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content">
    <div class="block">
        <div class="block-content">
            <div class="email-body" style="padding: 20px;max-width: 80%;margin: auto; font-family: DIN Next,sans-serif;">
                <div class="email-contaner" style="border: 2px solid #7daa40;padding: 25px;">
                    <div class="email-content" style="margin: auto;  text-align: center; ">
                        <div class="email-logo">
                            <img src="https://cdn.shopify.com/s/files/1/0370/7361/7029/files/image_3.png?v=1585895317" alt="Wefullfill" style="width: 35%">
                        </div>
                        @isset($edit)
                            <form action="{{ route('admin.emails.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="email-content-detail" style="margin: 50px 0;">
                                    <input class="email-title " type="text" name="subject" style="margin: 0;margin-bottom: 30px;font-size: 34px; width: 100%" placeholder="subject" value="{{ $template->subject }}">
                                    <br>
                                    <textarea type="text" class="email-message-1" rows="5" style=" margin: 0;margin-bottom: 30px;font-size: 20px;line-height: 1.53; width: 100%" name="body" placeholder="body" >{{ $template->body }}</textarea>
                                    <br>
                                    @if($template->id == '4' || $template->id == '3')
                                        <hr>
                                        <div class="" style="width: 100%">
                                            <div class="wrap">
                                                <div class="left">
                                                    <h2 style="color: #ffffff; margin-right: 5px; margin-top: 7px; margin-bottom: 7px;">Line Items
                                                        @if($order->paid == '0')
                                                            <span class="" style="font-size: small"> (Unpaid </span>
                                                        @elseif($order->paid == '1')
                                                            <span class="" style="font-size: small"> (Paid </span>
                                                        @elseif($order->paid == '2')
                                                            <span class="" style="font-size: small;"> (Refunded, </span>
                                                        @endif

                                                        @if($order->status == 'Paid')
                                                            <span class="" style="font-size: small"> Unfulfilled)</span>
                                                        @elseif($order->status == 'unfulfilled')
                                                            <span class="" style="font-size: small"> {{ucfirst($order->status)}})</span>
                                                        @elseif($order->status == 'partially-shipped')
                                                            <span class="" style="font-size: small;background: darkolivegreen;color: white;"> {{ucfirst($order->status)}})</span>
                                                        @elseif($order->status == 'shipped')
                                                            <span class=" " style="font-size: small;background: orange;color: white;"> {{ucfirst($order->status)}})</span>
                                                        @elseif($order->status == 'delivered')
                                                            <span class=" " style="font-size: small;background: deeppink;color: white;"> {{ucfirst($order->status)}})</span>
                                                        @elseif($order->status == 'completed')
                                                            <span class=" " style="font-size: small;background: darkslategray;color: white;"> {{ucfirst($order->status)}})</span>
                                                        @elseif($order->status == 'new')
                                                            <span class="" style="font-size: small"> Draft) </span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="" style="font-size: small"> {{ucfirst($order->status)}}) </span>
                                                        @else
                                                            <span class="" style="font-size: small">  {{ucfirst($order->status)}}) </span>
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
                                                        <th style="width: 10%">Name</th>
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
                                                                        <span class=""> Unfulfilled</span>
                                                                    @elseif($item->fulfillment_status == 'partially-fulfilled')
                                                                        <span class=""> Partially Fulfilled</span>
                                                                    @else
                                                                        <span class=""> Fulfilled</span>
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
                                                <div class="" style="text-align: right !important; padding: 15px;">
                                                    <div class="" >
                                                        <table class="table table-borderless table-vcenter">
                                                            <thead>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td align="left">Subtotal ({{count($order->line_items)}} items)</td>
                                                                <td align="right">{{number_format($order->cost_to_pay - $order->shipping_price,2)}} USD</td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left">Shipping Price</td>
                                                                <td align="right">{{number_format($order->shipping_price,2)}} USD</td>
                                                            </tr>

                                                            <tr>
                                                                <td align="left">Total Cost</td>
                                                                <td align="right">{{number_format($order->cost_to_pay,2)}} USD</td>
                                                            </tr>
                                                            </tbody>

                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($template->id == '13')
                                        <div class="text-left">
                                            <label for="" style="color: #7daa40 !important;">Select Products</label>
                                        </div>
                                        <select class="@error('type') is-invalid @enderror js-select2 form-control" name="products[]" style="width: 100%; border-radius: 0 !important;" data-placeholder="Select Products.." multiple>
                                           @foreach($products as $product)
                                                @php
                                                    $prods = json_decode($template->products);
                                                @endphp
                                                <option value="{{ $product->id }}"
                                                    @if(in_array($product->id, $prods))
                                                        selected
                                                    @endif
                                                >{{ $product->title }}</option>
                                           @endforeach

                                        </select>
                                        <br><br><br>
                                    @endif

                                    @if($template->id == '20')
                                        <div class="text-left">
                                            <label for="" style="color: #7daa40 !important;">Select Products</label>
                                        </div>
                                        <select class="@error('type') is-invalid @enderror js-select2 form-control" name="products[]" style="width: 100%; border-radius: 0 !important;" data-placeholder="Select Products.." multiple>
                                            @foreach($products as $product)
                                                @php
                                                    $prods = json_decode($template->products);
                                                @endphp
                                                <option value="{{ $product->id }}"
                                                        @if(in_array($product->id, $prods))
                                                        selected
                                                    @endif
                                                >{{ $product->title }}</option>
                                            @endforeach

                                        </select>

                                        <div class="text-left">
                                            <label for="" style="color: #7daa40 !important; margin-top: 10px;">Add Campaign</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" required name="campaign_name" class="form-control" placeholder="Enter campaign name.." required>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" required name="time" placeholder="Select Date/Time" class="js-flatpickr form-control bg-white" id="example-flatpickr-datetime-24" name="example-flatpickr-datetime-24" data-enable-time="true" data-time_24hr="true">
                                            </div>
                                        </div>
                                        <br><br><br>
                                    @endif

                                    @if($template->id == '14')
                                        <div class="text-left">
                                            <label for="" style="color: #7daa40 !important;">Adjust Time</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select name="day" id="" class="form-control">
                                                    <option value="1" @if($template->day==1) selected @endif>Monday</option>
                                                    <option value="2" @if($template->day==2) selected @endif>Tuesday</option>
                                                    <option value="3" @if($template->day==3) selected @endif>Wednesday</option>
                                                    <option value="4" @if($template->day==4) selected @endif>Thursday</option>
                                                    <option value="5" @if($template->day==5) selected @endif>Friday</option>
                                                    <option value="6" @if($template->day==6) selected @endif>Saturday</option>
                                                    <option value="7" @if($template->day==7) selected @endif>Sunday</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="js-flatpickr form-control bg-white" value="{{ $template->time }}" name="time" data-enable-time="true" data-no-calendar="true" data-date-format="H:i" data-time_24hr="true">
                                            </div>
                                        </div>

                                        <br><br><br>
                                    @endif

                                    @if($template->id == '18')
                                        <div class="text-left">
                                            <label for="" style="color: #7daa40 !important;">Add Campaign</label>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" name="campaign_name" class="form-control" placeholder="Enter campaign name.." required>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control form-control-lg" id="formFileLg" type="file" name="banner" required />
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="time" class="js-flatpickr form-control bg-white" id="example-flatpickr-datetime-24" name="example-flatpickr-datetime-24" data-enable-time="true" data-time_24hr="true">
                                            </div>
                                        </div>

                                        <br><br><br>
                                    @endif


                                    @if($template->id == '1' || $template->id == '2')
                                        <a class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">Help Center</a>
                                    @elseif($template->id == '13' ||$template->id == '14')
                                        <a class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">View Products</a>
                                    @elseif($template->id == '18')

                                    @else
                                        <a class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">View Details</a>
                                    @endif
                                </div>
                                <button type="submit" class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none; background-color: #7daa40; color: #ffffff; margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">Update</button>
                            </form>
                        @else
                            <div class="email-content-detail" style="margin: 50px 0;">
                                <h1 class="email-title" style="margin: 0;margin-bottom: 30px;font-size: 34px;">{{ $template->subject }}</h1>
                                <p class="email-message-1" style=" margin: 0;margin-bottom: 30px;font-size: 20px;line-height: 1.53;" >{{ $template->body }} </p>
                                @if($template->id == '4' || $template->id == '3')
                                    <hr>

                                    <div class="" style="width: 100%">
                                        <div class="wrap">
                                            <div class="left">
                                                <h2 style="color: #ffffff; margin-right: 5px; margin-top: 7px; margin-bottom: 7px;">Line Items
                                                    @if($order->paid == '0')
                                                        <span class="" style="font-size: small"> (Unpaid </span>
                                                    @elseif($order->paid == '1')
                                                        <span class="" style="font-size: small"> (Paid </span>
                                                    @elseif($order->paid == '2')
                                                        <span class="" style="font-size: small;"> (Refunded, </span>
                                                    @endif

                                                    @if($order->status == 'Paid')
                                                        <span class="" style="font-size: small"> Unfulfilled)</span>
                                                    @elseif($order->status == 'unfulfilled')
                                                        <span class="" style="font-size: small"> {{ucfirst($order->status)}})</span>
                                                    @elseif($order->status == 'partially-shipped')
                                                        <span class="" style="font-size: small;background: darkolivegreen;color: white;"> {{ucfirst($order->status)}})</span>
                                                    @elseif($order->status == 'shipped')
                                                        <span class=" " style="font-size: small;background: orange;color: white;"> {{ucfirst($order->status)}})</span>
                                                    @elseif($order->status == 'delivered')
                                                        <span class=" " style="font-size: small;background: deeppink;color: white;"> {{ucfirst($order->status)}})</span>
                                                    @elseif($order->status == 'completed')
                                                        <span class=" " style="font-size: small;background: darkslategray;color: white;"> {{ucfirst($order->status)}})</span>
                                                    @elseif($order->status == 'new')
                                                        <span class="" style="font-size: small"> Draft) </span>
                                                    @elseif($order->status == 'cancelled')
                                                        <span class="" style="font-size: small"> {{ucfirst($order->status)}}) </span>
                                                    @else
                                                        <span class="" style="font-size: small">  {{ucfirst($order->status)}}) </span>
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
                                                    <th style="width: 10%">Name</th>
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
                                                                    <span class=""> Unfulfilled</span>
                                                                @elseif($item->fulfillment_status == 'partially-fulfilled')
                                                                    <span class=""> Partially Fulfilled</span>
                                                                @else
                                                                    <span class=""> Fulfilled</span>
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
                                            <div class="" style="text-align: right !important; padding: 15px;">
                                                <div class="" >
                                                    <table class="table table-borderless table-vcenter">
                                                        <thead>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td align="left">Subtotal ({{count($order->line_items)}} items)</td>
                                                            <td align="right">{{number_format($order->cost_to_pay - $order->shipping_price,2)}} USD</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="left">Shipping Price</td>
                                                            <td align="right">{{number_format($order->shipping_price,2)}} USD</td>
                                                        </tr>

                                                        <tr>
                                                            <td align="left">Total Cost</td>
                                                            <td align="right">{{number_format($order->cost_to_pay,2)}} USD</td>
                                                        </tr>
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endif

                                @if($template->id == '13')
                                    <div class="" style="width: 100%">
                                        <div class="wrap">
                                            <div class="left">
                                                <h3 style="color: #ffffff; margin-right: 5px;">Our Top Products</h3>
                                            </div>
                                        </div>

                                        <div class="wrapper">
                                            @foreach($top_products_stores as $product)
                                                @php
                                                    $prods = json_decode($template->products);
                                                @endphp
                                                @if(in_array($product->id, $prods))
                                                    <div class="product_div">
                                                        <div class="inner">
                                                            @foreach($product->has_images()->orderBy('position')->get() as $index => $image)
                                                                @if($index == 0)
                                                                    @if($image->isV == 0)
                                                                        <img class="product_img"  src="{{asset('images')}}/{{$image->image}}">
                                                                    @else
                                                                        <img class="product_img"  src="{{asset('images/variants')}}/{{$image->image}}">
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                            <p><a href="{{route('store.product.wefulfill.show',$product->id)}}" class="title">{{$product->title}}</a></p>
                                                            <p class="product_price">From ${{ $product->price }}</p>
                                                            <a href="{{route('store.product.wefulfill.show',$product->id)}}" class="product-btn">View Product</a>
                                                        </div>

                                                    </div>
                                                @endif
                                            @endforeach

                                        </div>
                                    </div>
                                @endif

                                @if($template->id == '20')
                                    <div class="" style="width: 100%">
                                        <div class="wrap">
                                            <div class="left">
                                                <h3 style="color: #ffffff; margin-right: 5px;">Our Top Products</h3>
                                            </div>
                                        </div>

                                        <div class="wrapper">
                                            @foreach($top_products_stores as $product)
                                                @php
                                                    $prods = json_decode($template->products);
                                                @endphp
                                                @if(in_array($product->id, $prods))
                                                    <div class="product_div">
                                                        <div class="inner">
                                                            @foreach($product->has_images()->orderBy('position')->get() as $index => $image)
                                                                @if($index == 0)
                                                                    @if($image->isV == 0)
                                                                        <img class="product_img"  src="{{asset('images')}}/{{$image->image}}">
                                                                    @else
                                                                        <img class="product_img"  src="{{asset('images/variants')}}/{{$image->image}}">
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                            <p><a href="{{route('store.product.wefulfill.show',$product->id)}}" class="title">{{$product->title}}</a></p>
                                                            <p class="product_price">From ${{ $product->price }}</p>
                                                            <a href="{{route('store.product.wefulfill.show',$product->id)}}" class="product-btn">View Product</a>
                                                        </div>

                                                    </div>
                                                @endif
                                            @endforeach

                                        </div>
                                    </div>
                                @endif

                                @if($template->id == '14')
                                    <div class="" style="width: 100%">
                                        <div class="wrap">
                                            <div class="left">
                                                <h3 style="color: #ffffff; margin-right: 5px; padding-top: 5px;">New Products Template</h3>
                                            </div>
                                        </div>

                                        <div class="wrapper">
                                            @foreach($new_products as $product)
                                                    <div class="product_div">
                                                        <div class="inner">
                                                            @foreach($product->has_images()->orderBy('position')->get() as $index => $image)
                                                                @if($index == 0)
                                                                    @if($image->isV == 0)
                                                                        <img class="product_img"  src="{{asset('images')}}/{{$image->image}}">
                                                                    @else
                                                                        <img class="product_img"  src="{{asset('images/variants')}}/{{$image->image}}">
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                            <p><a href="{{route('store.product.wefulfill.show',$product->id)}}" class="title">{{$product->title}}</a></p>
                                                            <p class="product_price">From ${{ $product->price }}</p>
                                                            <a href="{{route('store.product.wefulfill.show',$product->id)}}" class="product-btn">View Product</a>
                                                        </div>
                                                    </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($template->id == '15' || $template->id == '16' || $template->id == '17')
                                    <div>
                                        @if(count($temp_product->has_images) > 0)
                                            @foreach($temp_product->has_images()->orderBy('position')->get() as $index => $image)
                                                @if($index == 0)
                                                    @if($image->isV == 0)
                                                        <img class="" style="width: 350px; height: auto; border: 1px solid whitesmoke" src="{{asset('images')}}/{{$image->image}}" >
                                                    @else   <img class="" style="width: 350px; height: auto; border: 1px solid whitesmoke" src="{{asset('images/variants')}}/{{$image->image}}" alt="" >
                                                    @endif
                                                @endif
                                            @endforeach
                                        @else
                                            <img class="" style="width: 350px; height: auto; border: 1px solid whitesmoke" src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg" >
                                        @endif

                                        <h3 style="">{{ $temp_product->title }}</h3>
                                    </div>
                                @endif

                                @if($template->id == '18')
                                    <img style="width: 100%; height: auto;" src="{{asset('ticket-attachments')}}/{{$template->banner}}" alt="">
                                @endif

                                <br><br>
                                <hr>

                                @if($template->id == '1' || $template->id == '2')
                                    <a class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">Help Center</a>
                                @elseif($template->id == '13')
                                    <a class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">View Products</a>
                                @elseif($template->id == '18')
                                @else
                                    <br>
                                    <div>
                                        <a class="email_btn" style="padding: 17px 55px; border: 2px solid #7daa40;font-size: 20px;letter-spacing: 1px;text-decoration: none;color: #7daa40;margin-top: 0;FONT-WEIGHT: 600;margin-bottom: 25px;margin-top: 25px">View Details</a>
                                    </div>
                                @endif
                            </div>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
