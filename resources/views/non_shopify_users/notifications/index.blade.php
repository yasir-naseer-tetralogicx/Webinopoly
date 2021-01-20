@extends('layout.shopify')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                   Notifications
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Notifications</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="block">
                <div class="block-content">
                    <div class="row">
                        <div class="col-md-12 mb2">
                            @if(count($notifications) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Notification</th>
                                        <th>Status</th>
                                        <th>Time</th>

                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach($notifications as $index => $notification)
                                        <tr>
                                            <td class="font-w600">{{ $notification->sub_type }}</td>
                                            <td style="width: 50%">
                                                <a href="{{route('users.notification',$notification->id)}}">
                                                    {{$notification->message}}
                                                </a>

                                            </td>
                                            <td>
                                                @if($notification->read == '0')
                                                    <span class="badge badge-success">unread</span>
                                                @else
                                                    <span class="badge badge-danger">read</span>
                                                @endif
                                            </td>
                                            <td>{{$notification->created_at->diffForHumans()}}</td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-md-12 text-center" style="font-size: 17px">
                                        {!! $notifications->links() !!}
                                    </div>
                                </div>

                            @else
                                <p class="text-center">No Notifications Found.</p>
                            @endif
                        </div>


                    </div>
                </div>
            </div>
    </div>

    @endsection
