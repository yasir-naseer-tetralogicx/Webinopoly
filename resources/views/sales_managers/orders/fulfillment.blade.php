@extends('layout.manager')
@section('content')

<div class="bg-body-light">
    <div class="content content-full pt-2 pb-2">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <h1 class="flex-sm-fill h4 my-2">
                {{$order->name}}'s Fulfillment
            </h1>
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item">Dashboard</li>
                    <li class="breadcrumb-item" aria-current="page">
                        All Orders
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        {{$order->name}}
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a class="link-fx active" href=""> Fulfillment</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                       Quantity to Fulfill
                    </h3>
                </div>
                <div class="block-content">
                    <p class="atleast-one-item alert alert-warning" style="display: none"> <i class="fa fa-exclamation-circle"></i> You need to fulfill at least 1 item.</p>
                    <table class="table table-borderless table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Items</th>
                            <th>Price</th>
                            <th style="width: 25%">Quantity</th>

                        </tr>
                        </thead>
                        <tbody>
                        <form id="fulfilment_process_form" action="{{route('sales_managers.order.fulfillment.process',$order->id)}}" method="post">
                            @csrf
                            @foreach($order->line_items as $item)
                            @if($item->fulfilled_by != 'store' && $item->fulfillable_quantity > 0)
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
                                    <p>{{$item->name}} <br> <span class="text-muted">SKU : {{$item->sku}}</span></p>
                                    </td>
                                    <td>  {{number_format($item->cost,2)}} USD</td>
                                    <td><div class="form-group">
                                            <div class="input-group">
                                                <input type="hidden" name="item_id[]" value="{{$item->id}}">
                                                <input type="number" class="form-control fulfill_quantity" min="0" max="{{$item->fulfillable_quantity}}" name="item_fulfill_quantity[]" value="{{$item->fulfillable_quantity}}">
                                                <div class="input-group-append">
                                                <span class="input-group-text">
                                                    of {{$item->fulfillable_quantity}}
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                                @endif
                        @endforeach
                        </form>
                        </tbody>

                    </table>

                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if($order->shipping_address != null)
                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            Shipping Address
                        </h3>

                    </div>
                    @php
                        $shipping = json_decode($order->shipping_address);
                    @endphp
                    <div class="block-content">
                        @if($shipping != null)
                            <p style="font-size: 14px">{{$shipping->first_name}} {{$shipping->last_name}}
                                @if($order->custom == 0)
                                <br> {{$shipping->company}}
                                @endif
                                <br> {{$shipping->address1}}
                                <br> {{$shipping->address2}}
                                <br> {{$shipping->city}}
                                <br> {{$shipping->province}} {{$shipping->zip}}
                                <br> {{$shipping->country}}
                                @if($order->custom == 0)
                                <br> {{$shipping->phone}}
                                    @endif
                            </p>
                            @else
                            <p style="font-size: 14px"> No Shipping Address
                            </p>
                        @endif
                    </div>
                </div>
            @endif
                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            Summary
                        </h3>
                    </div>
                    <div class="block-content">
                        <p>Fulfilling From WeFullFill Logistics Office</p>
                        <p class="font-weight-bold"><span class="fulfillable_quantity_drop badge badge-pill badge-dark" data-total="{{$order->total_fulfillable($order)}}" style="font-size: 13px">{{$order->total_fulfillable($order)}} of {{$order->total_fulfillable($order)}} </span> Mark as Fulfilled</p>
                        <hr>
                        <div class="row mb2">
                            <div class="col-md-12">
                                <button class="btn fulfill_items_btn btn-block btn-primary"> Fulfill Items</button>
                            </div>
                        </div>

                    </div>
                </div>
        </div>
    </div>
</div>

@endsection
