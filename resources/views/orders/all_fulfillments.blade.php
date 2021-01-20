@extends('layout.index')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Fulfillment & Tracking
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Fulfillment & Tracking</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row" >
            <div class="col-md-12">
                <div class="block">
                    <div class="block-header bulk-div" style="display: none">
                        <button class="btn btn-outline-secondary btn-sm bulk-fulfill-btn">Fulfill Orders</button>
                    </div>
                    <div class="block-content">
                        @if ($count > 0)
                            <table class="table  table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>
                                    <th>Total Pending</th>
                                    <th>Export All Unfulfilled</th>
                                    <th>Import Tracking CSV File</th>

                                </tr>
                                </thead>
                                <tbody class="">
                                <tr>

                                    <td class="font-w600">{{$count}} Orders</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="window.location.href='{{route('orders.bulk.tracking.download')}}'"> Export</button>
                                    </td>
                                    <td>
                                        <form action="{{route('orders.bulk.tracking.import')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input style="padding: 4px;" type="file" class="form-control" accept="text/csv" name="import_tracking" id="import-tracking">
                                        </form>


                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        @else
                            <p>No Orders For Export</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
