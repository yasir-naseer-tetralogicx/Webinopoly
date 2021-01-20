@extends('layout.shopify')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Custom Orders
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">My Custom Orders</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content">
        <form class="js-form-icon-search push" action="" method="get">
            <div class="form-group">
                <div class="input-group">
                    <input type="search" class="form-control" placeholder="Search by Order ID" value="{{$search}}" name="search" required >
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a class="btn btn-danger" href=""> <i class="fa fa-times"></i> Clear </a>

                    </div>
                </div>
            </div>
        </form>
        <div class="row" >
            <div class="col-md-12">
                <button style="float: right;margin-bottom: 10px" class="btn btn-sm btn-primary import_button">Import Orders Through CSV</button>
                <a target="_blank" href="{{asset('NewCSVOrderFormat.csv')}}" style="margin-right: 10px;margin-bottom: 10px;float: right" class="btn btn-sm btn-warning">View Sample CSV</a>

                <form id="import-form" action="{{route('order_file_processing')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="import_order_file" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" style="display:none" id="import-file-input">
                </form>
            </div>
            <div class="col-md-12">
                <div class="block">
{{--                    <div class="block-header bulk-div" style="display: none">--}}
{{--                        <div class="btn-group">--}}
{{--                            <button class="btn btn-outline-secondary btn-sm bulk-wallet-btn">Pay With Wallet</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="block-content">
                        @if (count($orders) > 0)
                            <table class="table js-table-checkable table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 70px;">
                                        <div class="custom-control custom-checkbox d-inline-block">
                                            <input type="checkbox" class="custom-control-input check-order-all-user" id="check-all" name="check-all">
                                            <label class="custom-control-label" for="check-all"></label>
                                        </div>
                                    </th>

                                    <th>Name</th>
                                    <th>Order Date</th>
                                    <th>Cost</th>
                                    <th>Payment Status</th>
                                    <th>Status</th>
                                    <th>Stock Status</th>
                                    <th style="text-align: right">
                                        <a href="{{route('users.custom.orders.create')}}"
                                           class="btn btn-sm btn-success" style="font-size: 12px" type="button" data-toggle="tooltip" title=""
                                           data-original-title="Sync Orders"><i class="fa fa-plus"></i> Add New Order</a></th>
                                </tr>
                                </thead>

                                @foreach($orders as $index => $order)
                                    <tbody class="">
                                    <tr>
                                        @if($order->paid == 0)
                                            <td class="text-center">
                                                <div class="custom-control custom-checkbox d-inline-block">
                                                    <input type="checkbox" class="custom-control-input check-order-user" id="row_{{$index}}" name="check_order[]" value="{{$order->id}}">
                                                    <label class="custom-control-label" for="row_{{$index}}"></label>
                                                </div>
                                            </td>
                                        @else
                                            <td class="text-center">

                                            </td>
                                        @endif

                                        <td class="font-w600"><a href="{{route('users.order.view',$order->id)}}">{{ $order->name }}</a></td>
                                        <td>
                                            {{date_create($order->shopify_created_at)->format('d m, Y h:i a') }}
                                        </td>
                                        <td>
                                            {{number_format($order->cost_to_pay,2)}} USD

                                        </td>
                                        <td>
                                            @if($order->paid == '0')
                                                <span class="badge badge-warning" style="font-size: small"> Unpaid </span>
                                            @elseif($order->paid == '1')
                                                <span class="badge badge-success" style="font-size: small"> Paid </span>
                                            @elseif($order->paid == '2')
                                                <span class="badge badge-danger" style="font-size: small;"> Refunded</span>
                                            @endif

                                        </td>
                                        <td>
                                            @if($order->status == 'Paid')
                                                <span class="badge badge-warning" style="font-size: small"> Unfulfilled </span>
                                            @elseif($order->status == 'unfulfilled')
                                                <span class="badge badge-warning" style="font-size: small"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'partially-shipped')
                                                <span class="badge " style="font-size: small;background: darkolivegreen;color: white;"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'shipped')
                                                <span class="badge " style="font-size: small;background: orange;color: white;"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'delivered')
                                                <span class="badge " style="font-size: small;background: deeppink;color: white;"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge " style="font-size: small;background: darkslategray;color: white;"> {{ucfirst($order->status)}}</span>
                                            @elseif($order->status == 'new')
                                                <span class="badge badge-warning" style="font-size: small"> Draft </span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge badge-warning" style="font-size: small"> {{ucfirst($order->status)}} </span>
                                            @else
                                                <span class="badge badge-success" style="font-size: small">  {{ucfirst($order->status)}} </span>
                                            @endif

                                        </td>
                                        <td>
                                                @php
                                                    $out_of_stock = 0;
                                                    foreach($order->line_items()->where('fulfilled_by', 'fantasy')->get() as $item) {
                                                        if($item->linked_real_variant == null && $item->linked_real_product == null)
                                                            $out_of_stock += 1;

                                                        if($item->linked_real_variant && $item->linked_real_variant->quantity == 0) {
                                                            $out_of_stock += 1;
                                                        }
                                                        else if($item->linked_real_product && $item->linked_real_product->quantity == 0){
                                                            $out_of_stock += 1;
                                                        }
                                                    }
                                                @endphp

                                                @if($order->line_items->where('fulfilled_by', 'store')->count() > 0)
                                                    @if($order->line_items()->where('fulfilled_by', 'fantasy')->count() == $out_of_stock)
                                                        <span class="badge badge-danger" style="font-size: small"> Out of Stock </span>
                                                    @else
                                                        <span class="badge badge-warning" style="font-size: small"> Partial Out of Stock </span>
                                                    @endif
                                                @else
                                                    @if($out_of_stock == 0)
                                                        <span class="badge badge-success" style="font-size: small"> In Stock </span>
                                                    @elseif($order->line_items()->count() == $out_of_stock)
                                                        <span class="badge badge-danger" style="font-size: small"> Out of Stock </span>
                                                    @else
                                                        <span class="badge badge-warning" style="font-size: small"> Partial out of Stock </span>
                                                    @endif
                                                @endif
                                            </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{route('users.order.view',$order->id)}}"
                                                   class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="View Order"><i class="fa fa-eye"></i></a>
                                                <a href="{{route('users.order.delete',$order->id)}}"
                                                   class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="Delete Order"><i class="fa fa-times"></i></a>
                                            </div>

                                        </td>

                                    </tr>
                                    </tbody>

                                @endforeach
                            </table>
                            <div class="row">
                                <div class="col-md-12 text-center" style="font-size: 17px">
                                    {!! $orders->links() !!}
                                </div>
                            </div>
                        @else
                            <p>No Orders Found  <a href="{{route('users.custom.orders.create')}}" class="btn btn-sm btn-success" style="font-size: 12px;float: right" type="button" data-toggle="tooltip" title="" data-original-title="Sync Orders"><i class="fa fa-plus"></i> Add New Order</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
