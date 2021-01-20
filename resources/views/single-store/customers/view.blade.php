@extends('layout.single')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    {{$customer->first_name}} {{$customer->last_name}}
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            Customers
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx active" href="">  {{$customer->first_name}} {{$customer->last_name}}</a>
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
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            Personal Information
                        </h3>

                    </div>
                    <div class="block-content">
                        <table class="table table-hover table-borderless table-striped table-vcenter">
                            <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Total Orders</th>
                                <th>Total Spent</th>

                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>  {{$customer->first_name}} {{$customer->last_name}} </td>
                                <td>  {{$customer->email}} </td>
                                <td> @if($customer->phone != null) {{$customer->phone}} @else No Phone Number @endif  </td>
                                <td>  {{count($customer->has_orders)}} Orders </td>
                                <td>  {{number_format($customer->total_spent,2)}} USD </td>

                            </tr>

                            </tbody>


                        </table>

                    </div>
                </div>
                <div class="block">
                    <div class="block-content">
                        @if (count($customer->has_orders) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Order Date</th>
                                    <th>Price</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                                </thead>

                                @foreach($customer->has_orders as $index => $order)
                                    <tbody class="">
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td class="font-w600"><a href="{{route('store.order.view',$order->id)}}">{{ $order->name }}</a></td>
                                        <td>
                                            {{date_create($order->shopify_created_at)->format('D m, Y h:i a') }}
                                        </td>

                                        <td>
                                            {{number_format($order->total_price,2)}} USD
                                        </td>
                                        <td>
                                            {{number_format($order->cost_to_pay,2)}} USD

                                        </td>
                                        <td>
                                            @if($order->status == 'new')
                                                <span class="badge badge-warning" style="font-size: small">
                                                {{$order->status}}
                                                </span>
                                            @elseif($order->status == 'paid')
                                                <span class="badge badge-primary" style="font-size: small"> Ordered</span>

                                            @elseif($order->status == 'unfulfilled')
                                                <span class="badge badge-warning" style="font-size: small"> {{$order->status}}</span>

                                            @elseif($order->status == 'shipped')
                                                <span class="badge" style="font-size: small;background: orange;color: white;"> {{$order->status}}</span>
                                            @else
                                                <span class="badge badge-success" style="font-size: small"> {{$order->status}}</span>
                                            @endif

                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{route('store.order.view',$order->id)}}"
                                                   class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="View Order"><i class="fa fa-eye"></i></a>
                                                <a href="{{route('store.order.delete',$order->id)}}"
                                                   class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="Delete Order"><i class="fa fa-times"></i></a>
                                            </div>

                                        </td>

                                    </tr>
                                    </tbody>

                                @endforeach
                            </table>
                        @else
                            <p>No Orders Found </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
