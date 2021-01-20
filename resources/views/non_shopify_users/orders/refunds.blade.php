@extends('layout.shopify')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Refunds
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            Refunds
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    <div class="content">
        @if($user->has_manager != null)
            <div class="row" style="margin-bottom: 10px">
                <div class="col-md-12">
                    <form action="" method="GET" class="d-flex">
                        <input type="search" class="form-control d-inline-block" value="{{$search}}" name="search" placeholder="Search By Keyword">
                        <input type="submit" value="Search" class="btn btn-primary btn-sm  d-inline-block" style="margin-left: 10px">
                    </form>
                </div>
            </div>
            <div class="row" style="margin-bottom: 10px">
                <div class="col-md-12 text-right">
                    <button class="btn btn-primary" data-target="#create_refund_modal" data-toggle="modal">Generate Refund</button>
                </div>
            </div>
            <div class="block">
                <div class="block-content">
                    <div class="row">
                        <div class="col-md-12 mb2">
                            @if(count($refunds) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>

                                        <th>Name</th>
                                        <th>Cost</th>
                                        <th>Payment Status</th>
                                        <th>Order Status</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th style="text-align: right">
                                    </tr>
                                    </thead>

                                    @foreach($refunds as $index => $refund)
                                        <tbody class="">
                                        <tr>

                                            <td class="font-w600"><a href="{{route('users.order.view',$refund->has_order->id)}}">{{ $refund->title }}</a></td>
                                            <td>
                                                {{number_format($refund->has_order->cost_to_pay,2)}} USD

                                            </td>
                                            <td>
                                                @if($refund->has_order->paid == '0')
                                                    <span class="badge badge-warning" style="font-size: small"> Unpaid </span>
                                                @elseif($refund->has_order->paid == '1')
                                                    <span class="badge badge-success" style="font-size: small"> Paid </span>
                                                @elseif($refund->has_order->paid == '2')
                                                    <span class="badge badge-danger" style="font-size: small;"> Refunded</span>
                                                @endif

                                            </td>
                                            <td>
                                                @if($refund->has_order->status == 'Paid')
                                                    <span class="badge badge-warning" style="font-size: small"> Unfulfilled</span>
                                                @elseif($refund->has_order->status == 'unfulfilled')
                                                    <span class="badge badge-warning" style="font-size: small"> {{$refund->has_order->status}}</span>
                                                @elseif($refund->has_order->status == 'partially-shipped')
                                                    <span class="badge " style="font-size: small;background: darkolivegreen;color: white;"> {{$refund->has_order->status}}</span>
                                                @elseif($refund->has_order->status == 'shipped')
                                                    <span class="badge " style="font-size: small;background: orange;color: white;"> {{$refund->has_order->status}}</span>
                                                @elseif($refund->has_order->status == 'delivered')
                                                    <span class="badge " style="font-size: small;background: deeppink;color: white;"> {{$refund->has_order->status}}</span>
                                                @elseif($refund->has_order->status == 'completed')
                                                    <span class="badge " style="font-size: small;background: darkslategray;color: white;"> {{$refund->has_order->status}}</span>
                                                @elseif($refund->has_order->status == 'cancelled')
                                                    <span class="badge " style="font-size: small;background: red;color: white;"> {{$refund->has_order->status}}</span>
                                                @else
                                                    <span class="badge badge-success" style="font-size: small"> {{$refund->has_order->status}}</span>
                                                @endif

                                            </td>
                                            <td>
                                                <span class="badge @if($refund->priority == 'low') badge-primary @elseif($refund->priority == 'medium') badge-warning @else badge-danger @endif" >{{$refund->priority}}</span>

                                            </td>
                                            <td>
                                                @if($refund->has_status != null)
                                                    <span class="badge " style="background: {{$refund->has_status->color}};color: white;"> {{$refund->has_status->status}}</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <button onclick="window.location.href='{{route('users.refund',$refund->id)}}'"
                                                            class="btn btn-sm btn-success" type="button"
                                                            data-original-title="View Refund"><i class="fa fa-eye"></i></button>
                                                </div>
                                            </td>

                                        </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                            @else
                                <p>No Refunds Founds</p>
                            @endif
                        </div>


                    </div>
                </div>
            </div>
            <div class="modal fade" id="create_refund_modal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">Generate Refund</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option">
                                        <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                    </button>
                                </div>
                            </div>
                            <form action="{{route('refund.create')}}" method="post"  enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="source" value="non-shopify-user">
                                <input type="hidden" name="manager_id" value="{{$user->sale_manager_id}}">
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                <input type="hidden" name="type" value="user-ticket">

                                <div class="block-content font-size-sm">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Refund Title</label>
                                                <input required class="form-control" type="text"  name="title"
                                                       placeholder="Enter Title here">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Order</label>
                                                <select name="order_id" class="form-control" required>
                                                    <option value="">Select Order for Refund</option>
                                                    @foreach($orders as $order)
                                                        <option value="{{$order->id}}">{{$order->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Priority</label>
                                                <select name="priority" class="form-control" required>
                                                    <option value="low">Low</option>
                                                    <option value="medium">Medium</option>
                                                    <option value="high">High</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Attachments </label>
                                                <input type="file" name="attachments[]" class="form-control" multiple>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Reason</label>
                                                <textarea required class="js-summernote" name="message"
                                                          placeholder="Please Enter Description here !"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="block-content block-content-full text-right border-top">

                                    <button type="submit" class="btn btn-sm btn-primary" >Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="block">
                <div class="block-content">
                    <p class="text-center">You can't generate refunds because you are not assigned to any sales manager.</p>
                </div>
            </div>
    @endif


@endsection
