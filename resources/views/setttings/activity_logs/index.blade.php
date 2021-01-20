@extends('layout.index')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Activity Logs
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Activity Logs</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content">
        <form class="js-form-icon-search push" action="" method="get">
            <div class="form-group">
                <div class="input-group">
                    <input type="user_search" class="form-control" placeholder="Search By User Name" value="{{$user_search}}" name="user_search" >
                    <select name="type_search" id="" class="form-control">
                        <option value="" disabled selected>{{ $type_search }}</option>
                        <option value="Product">Product</option>
                        <option value="RetailerProduct">Retailer Product</option>
                        <option value="Order">Order</option>
                        <option value="Ticket">Ticket</option>
                        <option value="Wishlist">Wishlist</option>
                        <option value="Wallet">Wallet</option>
                    </select>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a class="btn btn-danger" href="{{ route('admin.activity.log.index') }}"> <i class="fa fa-times"></i> Clear </a>
                    </div>
                </div>
            </div>
        </form>
        <div class="block">
            <div class="block-content">
                <div class="table-responsive">
                    <table class="table table-borderless table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Item</th>
                            <th>Action</th>
                            <th>Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="font-w600" style="vertical-align: middle">
                                    @if($log->user_id == 0)
                                        WeFullFill(Admin)
                                    @else
                                        {{ $log->user->name }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-success"> {{ $log->model_type }}</span>
                                </td>

                                <td style="vertical-align: middle">
                                    {{ $log->model_name }}
                                </td>
                                <td style="vertical-align: middle">
                                    {{ $log->action }}
                                </td>
                                <td style="vertical-align: middle">
                                    {{ date_format($log->created_at ,"Y/M/d H:i ") }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end">
                        {{ $logs->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
