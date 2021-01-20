@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    {{$manager->email}}
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Sales Managers </li>
                        <li class="breadcrumb-item">Edit </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="block">
            <ul class="nav nav-tabs nav-justified nav-tabs-block " data-toggle="tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#stores"> Stores </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#users">Non-Shopify Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tickets">Tickets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#settings">Personal Information</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#manager_log">Log</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#manager_reviews">Reviews</a>
                </li>
            </ul>
            <div class="block-content tab-content">
                <div class="tab-pane active" id="stores" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if (count($manager->has_sales_stores) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Shopify Domain</th>
                                        <th>Imported Products</th>
                                        <th>Orders</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach($manager->has_sales_stores as $index => $store)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td class="font-w600">{{ explode('.',$store->shopify_domain)[0]}}</td>
                                            <td>
                                                <span class="badge badge-primary">{{$store->shopify_domain}}</span>
                                            </td>
                                            <td>
                                                {{count($store->has_imported)}}
                                            </td>
                                            <td>
                                                {{count($store->has_orders)}}

                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group mr-2 mb-2">
                                                    <a class="btn btn-xs btn-sm btn-success" type="button" href="" title="View Store">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center"> No Store Available</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="users" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if (count($manager->has_users) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Email</th>
                                        <th>Imported Files</th>
                                        <th>Orders</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach($manager->has_users as $index => $user)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td class="font-w600">{{$user->name}}</td>
                                            <td>
                                                <span class="badge badge-primary">{{$user->email}}</span>
                                            </td>
                                            <td>
                                                {{count($user->has_files)}}
                                            </td>
                                            <td>
                                                {{count($user->has_orders)}}

                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group mr-2 mb-2">
                                                    <a class="btn btn-xs btn-sm btn-success" type="button" href="" title="View User">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center"> No User Available</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tickets" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            <div class="table-responsive">
                                @if(count($manager->has_manager_tickets) > 0)
                                    <table class="table table-hover table-borderless table-striped table-vcenter">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Source</th>
                                            <th>Priority</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Last Reply</th>
                                            <th style="text-align: right">
                                            </th>
                                        </tr>

                                        </thead>
                                        <tbody class="">
                                        @foreach($manager->has_manager_tickets()->orderBy('updated_at','DESC')->get() as $index => $ticket)

                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td class="font-w600"><a href="">{{ $ticket->title }}</a></td>
                                                <td>
                                                    @if($ticket->source == 'store') {{explode('.',$ticket->email)[0]}} @else {{$ticket->has_user->name}}  @endif   <span class="badge badge-primary">{{$ticket->source}}</span>
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

                                                <td>{{\Carbon\Carbon::parse($ticket->last_reply_at)->diffForHumans()}}</td>
                                                <td class="">
                                                    <div class="btn-group">
                                                        <a href="{{route('tickets.view',$ticket->id)}}"
                                                           class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                           data-original-title="View Ticket"><i class="fa fa-eye"></i></a>
                                                        <a href=""
                                                           class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                           data-original-title="Delete Ticket"><i class="fa fa-times"></i></a>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>

                                @else
                                    <p class="text-center">No Tickets Found.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="settings" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            <div class="image-profile text-center mb2">
                                <h5>Profile Photo</h5>
                                <img class="image-drop img-avatar200"
                                     @if($manager->profile == null) src="{{asset('assets/media/avatars/avatar0.jpg')}}" @else
                                     src="{{asset('managers-profiles')}}/{{$manager->profile}}"
                                     @endif
                                     alt="">
                            </div>

                            <div class="form-group">
                                <label for="">Email</label>
                                <input disabled type="text"  class="form-control" value="{{$manager->email}}">
                            </div>
                            <div class="form-group">
                                <label for="">Username</label>
                                <input disabled type="text"  name="name" class="form-control" value="{{$manager->name}}">
                            </div>
                            <div class="form-group">
                                <label for="">Street Address</label>
                                <input disabled type="text"  name="address" class="form-control" value="{{$manager->address}}">
                            </div>
                            <div class="form-group">
                                <label for="">Address 2</label>
                                <input disabled type="text"  name="address2" class="form-control" value="{{$manager->address2}}">
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="">City</label>
                                    <input disabled type="text"  name="city" class="form-control" value="{{$manager->city}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="">State</label>
                                    <input disabled type="text"  name="state" class="form-control" value="{{$manager->state}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="">Zip</label>
                                    <input disabled type="text"  name="zip" class="form-control" value="{{$manager->zip}}">
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="">Country</label>
                                <input disabled type="text"  name="zip" class="form-control" value="{{$manager->country}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="manager_log" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if(count($manager->has_manager_logs) > 0)
                                <div class="block">
                                    <div class="block-content">
                                        <ul class="timeline timeline-alt">
                                            @foreach($manager->has_manager_logs()->orderBy('created_at','DESC')->get() as $log)
                                                <li class="timeline-event">
                                                    @if($log->status == "Top-up Request Approval")
                                                        <div class="timeline-event-icon bg-success">
                                                            <i class="fa fa-dollar-sign"></i>
                                                        </div>
                                                    @elseif($log->status == "Top-up By Manager")
                                                        <div class="timeline-event-icon bg-success">
                                                            <i class="fa fa-dollar-sign"></i>
                                                        </div>
                                                    @elseif($log->status == "Order Fulfillment")
                                                        <div class="timeline-event-icon bg-primary">
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    @elseif($log->status == "Reply From Manager")
                                                        <div class="timeline-event-icon bg-primary">
                                                            <i class="fa fa-comment-alt"></i>
                                                        </div>
                                                    @elseif($log->status == "Order Fulfillment Cancelled")
                                                        <div class="timeline-event-icon bg-danger">
                                                            <i class="fa fa-ban"></i>
                                                        </div>
                                                    @elseif($log->status == "Add Tracking in Order's Fulfillment")
                                                        <div class="timeline-event-icon bg-amethyst">
                                                            <i class="fa fa-truck"></i>
                                                        </div>
                                                    @elseif($log->status == "Order Marked as Delivered")
                                                        <div class="timeline-event-icon" style="background: deeppink">
                                                            <i class="fa fa-home"></i>
                                                        </div>
                                                    @elseif($log->status == "Order Marked as Completed")
                                                        <div class="timeline-event-icon" style="background: darkslategray">
                                                            <i class="fa fa-check"></i>
                                                        </div>
                                                    @endif
                                                    <div class="timeline-event-block block js-appear-enabled animated fadeIn" data-toggle="appear">
                                                        <div class="block-header block-header-default">
                                                            <h3 class="block-title">{{$log->status}}</h3>
                                                            <div class="block-options">
                                                                <div class="timeline-event-time block-options-item font-size-sm font-w600">
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
                                    </div>
                                </div>
                            @else
                                <p class="text-center"> No Manager Logs Found </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="manager_reviews" role="tabpanel">
                    <div class="block">
                        <div class="block-content">
                            @if(count($manager->has_reviews) > 0)
                                <div class="block">
                                    <div class="block-content">
                                        @foreach($manager->has_reviews as $review)
                                            <div class="d-flex">
                                                <input type="hidden" name="rating" value="{{$review->rating}}">
                                                <div class='rating-stars disabled'>
                                                    <ul id='stars' style="margin-bottom: 5px">
                                                        <li class='star' title='Poor' data-value='1'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star' title='Fair' data-value='2'>
                                                            <i class='fa fa-star fa-fw '></i>
                                                        </li>
                                                        <li class='star' title='Good' data-value='3'>
                                                            <i class='fa fa-star fa-fw '></i>
                                                        </li>
                                                        <li class='star' title='Excellent' data-value='4'>
                                                            <i class='fa fa-star fa-fw '></i>
                                                        </li>
                                                        <li class='star' title='WOW!!!' data-value='5'>
                                                            <i class='fa fa-star fa-fw '></i>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div style="margin-left: auto">
                                                    <span class="badge badge-primary">{{$review->created_at->diffForHumans()}}</span>
                                                </div>
                                            </div>
                                            <p>By {{$review->name}} ({{$review->email}}) on Ticket # <a href="{{route('tickets.view',$review->has_ticket->id)}}">{{$review->has_ticket->token}}</a></p>
                                            <p>{!! $review->review !!}</p>
                                            <hr>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="text-center"> No Reviews Found </p>
                            @endif
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection
