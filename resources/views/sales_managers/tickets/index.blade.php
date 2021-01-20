@extends('layout.manager')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Tickets
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Tickets</a>
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
                    <input type="search" class="form-control" placeholder="Search by name" value="{{$search}}" name="search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a class="btn btn-danger" href="{{route('sales_managers.tickets')}}"> <i class="fa fa-times"></i> Clear </a>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6  mb2">
                <form class="d-flex text-right" action="" method="get">
                <select name="priority" id="" class="form-control">
                    <option value="" style="display: none">Priority</option>
                    <option @if($priority == 'low') selected @endif value="low">Low</option>
                    <option @if($priority == 'medium') selected @endif value="medium">Medium</option>
                    <option @if($priority == 'high') selected @endif value="high">High</option>
                </select>


                <select name="status" style="margin-left: 10px" class="form-control">
                    <option value="" style="display: none">Status</option>
                    @foreach($statuses as $status)
                        <option @if($selected_status == $status->id) selected @endif value="{{$status->id}}">{{$status->status}}</option>
                    @endforeach
                </select>

                <input type="submit" style="margin-left: 10px" class="btn btn-primary" value="Filter">
                </form>
            </div>
        </div>
        <div class="block">
            <div class="block-content">
                <div class="row">
                    <div class="col-md-12 mb2 table-responsive">
                        @if(count($tickets) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>

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

                                @foreach($tickets as $index => $ticket)
                                    <tbody class="">
                                    <tr>

                                        <td class="font-w600"><a href="{{route('sales_managers.ticket.view',$ticket->id)}}">{{ $ticket->title }}</a></td>
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

                                        <td>{{\Carbon\Carbon::parse($ticket->last_reply_at)->diffForHumans()}}</td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{route('sales_managers.ticket.view',$ticket->id)}}"
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

    </div>

@endsection
