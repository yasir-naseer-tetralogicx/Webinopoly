@extends('layout.single')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Tracking Information
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            Tracking Info
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row" style="margin-bottom: 10px">
            <div class="col-md-12">
                <form action="" method="GET" class="d-flex">
                    <input type="search" class="form-control d-inline-block" value="{{$search}}" name="search" placeholder="Search By Keyword">
                    <input type="submit" value="Search" class="btn btn-primary btn-sm  d-inline-block" style="margin-left: 10px">
                </form>
            </div>
        </div>
        <div class="row" >

            <div class="col-md-12">

                <div class="block">
                    <div class="block-content">
                        @if(count($orders) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>

                                    <th>Name</th>
                                    <th>Order Date</th>
                                    <th>Price</th>
                                    <th>Cost</th>
                                    <th>Payment Status</th>
                                    <th>Status</th>
                                    <th style="text-align: right">
                                </tr>
                                </thead>

                                @foreach($orders as $index => $order)
                                    <tbody class="">
                                    <tr>

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
                                                <span class="badge badge-warning" style="font-size: small"> Unfulfilled</span>
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
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <button
                                                   class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#tracking_{{$order->id}}_modal"
                                                   data-original-title="View Tracking"><i class="fa fa-eye"></i></button>
                                            </div>

                                        </td>

                                    </tr>
                                    </tbody>
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
                            </table>
                        @else
                            <p>No Order Founds</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center" style="font-size: 17px">
                {!! $orders->links() !!}
            </div>
        </div>
    </div>
@endsection
