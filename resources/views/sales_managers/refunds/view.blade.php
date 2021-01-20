@extends('layout.manager')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Refund
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>

                        <li class="breadcrumb-item">Refunds</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href=""> {{$ticket->has_order->name}} </a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                @if(in_array($ticket->status_id,[1,2,3]))
                    <div class="text-right mb2">
                        <button class="btn btn-success" onclick="window.location.href='{{route('refund.approve',['id' => $ticket->id,'order_id' => $ticket->has_order->id])}}'"> Approve Refund </button>
                    </div>
                @endif
                <div class="block">
                    <div class="block-header">
                        <h5 class="block-title">{{$ticket->title}} <span class="badge @if($ticket->priority == 'low') badge-primary @elseif($ticket->priority == 'medium') badge-warning @else badge-danger @endif" style="float: right;font-size: small"> {{$ticket->priority}}</span></h5>
                    </div>
                    <div class="block-content">
                        <div class="p-2">
                            <p>Ticket-Token: <span class="badge badge-primary" style="font-size: small">{{$ticket->token}} </span></p>
                            <hr>
                            {!! $ticket->message !!}
                            <div class="attachments">
                                @foreach($ticket->has_attachments as $a)
                                    <img style="width: 100%;max-width: 250px" src="{{asset('ticket-attachments')}}/{{$a->source}}" alt="">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @if(count($ticket->has_thread) > 0)
                    <h5> Thread </h5>
                    @foreach($ticket->has_thread as $thread)
                            <div class="block  @if($thread->source == 'manager') bg-muted text-white @endif">


                            <div class="block-header">
                                @if($thread->source == 'manager')
                                    <h5 class="block-title text-white">{{$thread->has_manager->name}} <span class="badge badge-primary " style="float: right;font-size: small"> {{date_create($thread->created_at)->format('m d, Y h:i a')}}</span></h5>
                                @elseif($thread->source == 'store')
                                    <h5 class="block-title">{{explode('.',$ticket->has_store->shopify_domain)[0]}} <span class="badge badge-primary " style="float: right;font-size: small"> {{date_create($thread->created_at)->format('m d, Y h:i a')}}</span></h5>
                                @else
                                    <h5 class="block-title">{{$ticket->has_user->name}} <span class="badge badge-primary " style="float: right;font-size: small"> {{date_create($thread->created_at)->format('m d, Y h:i a')}}</span></h5>

                                @endif
                            </div>
                            <div class="block-content">
                                <div class="p-2">
                                    {!! $thread->reply !!}

                                    <div class="attachments">
                                        @foreach($thread->has_attachments as $a)
                                            <img style="width: 100%;max-width: 250px" src="{{asset('ticket-attachments')}}/{{$a->source}}" alt="">
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="block">
                    <div class="block-header">
                        <h5 class="block-title">Reply</h5>
                    </div>
                    <div class="block-content">
                        <div class="p-2">
                            <form action="{{route('refund.create.thread')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="manager_id" value="{{$ticket->manager_id}}">
                                <input type="hidden" name="source" value="manager">
                                <input type="hidden" name="refund_id" value="{{$ticket->id}}">
                                <div class="form-group">
                                    <div class="form-material">
                                        <label for="material-error">Message</label>
                                        <textarea required class="js-summernote" name="reply"
                                                  placeholder="Please Enter Message here !"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-material">
                                        <label for="material-error">Attachments </label>
                                        <input type="file" name="attachments[]" class="form-control" multiple>
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-primary" value="Save">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="block">
                    <div class="block-header">
                        <div class="block-title">
                            Ticket History
                        </div>
                    </div>
                    <div class="block-content">
                        <ul class="timeline timeline-alt">
                            @foreach($ticket->logs()->orderBy('created_at','DESC')->get() as $log)
                                <li class="timeline-event">
                                    @if($log->status == "Created")
                                        <div class="timeline-event-icon bg-warning">
                                            <i class="fa fa-hourglass-start"></i>
                                        </div>
                                    @elseif($log->status == "Generated")
                                        <div class="timeline-event-icon bg-success">
                                            <i class="fa fa-ticket-alt"></i>
                                        </div>
                                    @elseif($log->status == "Reply From User")
                                        <div class="timeline-event-icon bg-primary">
                                            <i class="fa fa-comment"></i>
                                        </div>
                                    @elseif($log->status == "Reply From Manager")
                                        <div class="timeline-event-icon bg-success">
                                            <i class="fa fa-comment-alt"></i>
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
            </div>
            <div class="col-md-4">
                <div class="block">
                    <div class="block-header">
                        <h5 class="block-title">Refund Details</h5>
                    </div>
                    <div class="block-content">
                        <div class="p-2 font-size-sm">

                            @if($ticket->source == 'store')

                                <span class="font-weight-bold">Store: </span> <span class=" badge badge-primary text-center">{{explode('.',$ticket->has_store->shopify_domain)[0]}}</span>
                                <hr>
                                <span class="font-weight-bold">Domain: </span> <span class="text-center">{{$ticket->has_store->shopify_domain}}</span>
                                <hr>
                            @else

                                <span class="font-weight-bold">Client: </span> <span class="text-center">{{$ticket->has_user->name}}</span>
                                <hr>
                                <span class="font-weight-bold">Email: </span> <span class="text-center">{{$ticket->has_user->email}}</span>
                                <hr>
                            @endif
                                <span class="font-weight-bold">Order: </span> {{$ticket->has_order->name}}
                                <hr>
                                <span class="font-weight-bold">Cost: </span>{{number_format($ticket->has_order->cost_to_pay,2)}} USD
                                <hr>
                                <span class="font-weight-bold">Order Payment Status: </span>
                                @if($ticket->has_order->paid == '0')
                                    <span class="badge badge-warning" style="font-size: small"> Unpaid </span>
                                @elseif($ticket->has_order->paid == '1')
                                    <span class="badge badge-success" style="font-size: small"> Paid </span>
                                @elseif($ticket->has_order->paid == '2')
                                    <span class="badge badge-danger" style="font-size: small;"> Refunded</span>
                                @endif
                                <hr>
                                <span class="font-weight-bold">Order Status: </span>

                                @if($ticket->has_order->status == 'Paid')
                                    <span class="badge badge-warning" style="font-size: small"> Unfulfilled</span>
                                @elseif($ticket->has_order->status == 'unfulfilled')
                                    <span class="badge badge-warning" style="font-size: small"> {{$ticket->has_order->status}}</span>
                                @elseif($ticket->has_order->status == 'partially-shipped')
                                    <span class="badge " style="font-size: small;background: darkolivegreen;color: white;"> {{$ticket->has_order->status}}</span>
                                @elseif($ticket->has_order->status == 'shipped')
                                    <span class="badge " style="font-size: small;background: orange;color: white;"> {{$ticket->has_order->status}}</span>
                                @elseif($ticket->has_order->status == 'delivered')
                                    <span class="badge " style="font-size: small;background: deeppink;color: white;"> {{$ticket->has_order->status}}</span>
                                @elseif($ticket->has_order->status == 'completed')
                                    <span class="badge " style="font-size: small;background: darkslategray;color: white;"> {{$ticket->has_order->status}}</span>
                                @elseif($ticket->has_order->status == 'cancelled')
                                    <span class="badge " style="font-size: small;background: red;color: white;"> {{$ticket->has_order->status}}</span>
                                @else
                                    <span class="badge badge-success" style="font-size: small"> {{$ticket->has_order->status}}</span>
                                @endif
                                <hr>
                                <span class="font-weight-bold">Created at: </span> <span class="text-center">{{date_create($ticket->created_at)->format('m d, Y h:i a')}}</span>
                                <hr>
                                <span class="font-weight-bold">Last Update at: </span> <span class="text-center">{{date_create($ticket->updated_at)->format('m d, Y h:i a')}}</span>
                                <hr>
                                <span class="font-weight-bold">Status: </span>   @if($ticket->has_status != null)
                                    <span class="badge " style="background: {{$ticket->has_status->color}};color: white;"> {{$ticket->has_status->status}}</span>
                                @endif
                                <hr>
                                <span class="font-weight-bold">Ticket Time: </span>  <span class="text-center">{{$ticket->created_at->diffForHumans()}}</span>
                                <hr>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
