@extends('layout.single')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Help Center
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Help Center</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        @php
            $user = $shop->has_user()->first();
        @endphp
        @if($user->has_manager != null)
            <form class="js-form-icon-search push" action="" method="get">
                <div class="form-group">
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="Search by name" value="" name="search">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                            <a class="btn btn-danger" href=""> <i class="fa fa-times"></i> Clear </a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="block">
                <div class="block-content">
                    <div class="row">
                        <div class="col-md-12 mb2">
                            <button style="float: right;margin-bottom: 10px" class="btn btn-sm btn-primary" data-target="#create_new_ticket" data-toggle="modal">Create New Ticket</button>
                        </div>

                        <div class="col-md-12 mb2">
                            @if(count($tickets) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>

                                        <th>Title</th>
                                        <th>Store</th>
                                        <th>Priority</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Attachments</th>
                                        <th style="text-align: right">
                                        </th>
                                    </tr>
                                    </thead>

                                    @foreach($tickets as $index => $ticket)
                                        <tbody class="">
                                        <tr>

                                            <td class="font-w600"><a href="{{route('help-center.store.ticket.view',$ticket->id)}}">{{ $ticket->title }}</a></td>
                                            <td>
                                              {{$ticket->email}}
                                            </td>
                                            <td>
                                               <span class="badge @if($ticket->priority == 'low') badge-primary @elseif($ticket->priority == 'medium') badge-warning @else badge-danger @endif" >{{$ticket->priority}}</span>

                                            </td>
                                            <td>
                                                @if($ticket->category == 'default')
                                                    <span class="badge badge-light">{{$ticket->category}}</span>
                                                @else
                                                    <span class="badge" style="background: {{$ticket->has_category->color}};color: white">{{$ticket->category}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ticket->has_status != null)
                                                <span class="badge " style="background: {{$ticket->has_status->color}};color: white;"> {{$ticket->has_status->status}}</span>
                                                @endif
                                            </td>
                                            <td>{{count($ticket->has_attachments)}}</td>
                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <a href="{{route('help-center.store.ticket.view',$ticket->id)}}"
                                                       class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="View Ticket"><i class="fa fa-eye"></i></a>
                                                    <a href=""
                                                       class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="Delete Ticket"><i class="fa fa-times"></i></a>
                                                </div>
                                            </td>

                                        </tr>
                                        </tbody>

                                    @endforeach
                                </table>

                                <div class="row">
                                    <div class="col-md-12 text-center" style="font-size: 17px">
                                        {!! $tickets->links() !!}
                                    </div>
                                </div>

                            @else
                                <p class="text-center">No Tickets Found.</p>
                            @endif
                        </div>


                    </div>
                </div>
            </div>
            <div class="modal fade" id="create_new_ticket" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">New Ticket</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option">
                                        <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                    </button>
                                </div>
                            </div>
                            <form action="{{route('help-center.ticket.create')}}" method="post"  enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="source" value="store">
                                <input type="hidden" name="manager_id" value="{{$user->sale_manager_id}}">
                                <input type="hidden" name="shop_id" value="{{$shop->id}}">
                                <input type="hidden" name="type" value="store-ticket">


                                <div class="block-content font-size-sm">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Ticket Subject</label>
                                                <input required class="form-control" type="text"  name="title"
                                                       placeholder="Enter Title here">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Email</label>
                                                <input required class="form-control" type="text"  name="email"
                                                     value="{{$shop->shopify_domain}}"  placeholder="Enter Email here">
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
{{--                                    <div class="form-group">--}}
{{--                                        <div class="col-sm-12">--}}
{{--                                            <div class="form-material">--}}
{{--                                                <label for="material-error">Order #</label>--}}
{{--                                                <select name="order_id" class="form-control" >--}}
{{--                                                    @foreach($orders as $order)--}}
{{--                                                        <option value="{{ $order->id }}">{{ $order->name }}</option>--}}
{{--                                                    @endforeach--}}
{{--                                                </select>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Ticket Category</label>
                                                <select name="category" class="form-control" required>
                                                    <option value="default">Default</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                                    @endforeach
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
                                                <label for="material-error">Message</label>
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
                    <p class="text-center">You can't create tickets because you are not assigned to any sales manager.</p>
                </div>
            </div>
    @endif
    </div>


@endsection
