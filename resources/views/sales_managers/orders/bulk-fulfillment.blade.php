@extends('layout.manager')
@section('content')
    <div class="content">
        <div class="row bulk-forms">
            @foreach($orders as $order)
                <form class="fulfilment_process_form col-md-12" action="{{route('sales_managers.order.fulfillment.process',$order->id)}}" method="post">
                    <div class="block">
                            <div class="block-header block-header-default">
                                <h3 class="block-title">
                                    {{$order->name}}'s Fulfillment
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
                                                <td>
                                                    <div class="form-group">
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

                                    </tbody>

                                </table>

                            </div>
                        </div>
                </form>
            @endforeach
            <div class="col-md-8">
            </div>
            <div class="col-md-4">
                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            Summary
                        </h3>
                    </div>
                    <div class="block-content">
                        <p>Fulfilling From WeFullFill Logistics Office</p>
                        <p class="font-weight-bold"><span class="fulfillable_quantity_drop badge badge-pill badge-dark" data-total="{{$fulfillable_quantity}}" style="font-size: 13px">{{$fulfillable_quantity}} of {{$fulfillable_quantity}} </span> Mark as Fulfilled</p>
                        <hr>
                        <div class="row mb2">
                            <div class="col-md-12">
                                <button class="btn bulk_fulfill_items_btn btn-block btn-primary" data-redirect="{{route('sales_managers.orders')}}"> Fulfill Items</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="pre-loader">
        <div class="loader">
        </div>
    </div>
@endsection
