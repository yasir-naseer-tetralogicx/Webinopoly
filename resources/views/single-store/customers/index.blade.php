@extends('layout.single')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Customers
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Customers</a>
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
                    <input type="search" class="form-control" placeholder="Search by name, title of products" value="" name="search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a class="btn btn-danger" href=""> <i class="fa fa-times"></i> Clear </a>

                    </div>
                </div>
            </div>
        </form>


        <div class="row">
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content">
                        @if (count($customers) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>

                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Total Orders</th>
                                    <th>Total Spends</th>

                                    <th style="text-align: right">
                                        <a href="{{route('store.sync.customers')}}"
                                           class="btn btn-sm btn-primary" style="font-size: 12px" type="button" data-toggle="tooltip" title=""
                                           data-original-title="Sync Customers"><i class="fa fa-sync"></i> Sync New Customers</a>
                                    </th>
                                </tr>
                                </thead>

                                @foreach($customers as $index => $customer)
                                    <tbody class="">
                                    <tr>

                                        <td class="font-w600"><a href="{{route('store.customer.view',$customer->id)}}">{{ $customer->first_name }} {{$customer->last_name}}</a></td>
                                        <td>
                                            {{$customer->email}}

                                        </td>
                                        <td>
                                         {{count($customer->has_orders)}}
                                        </td>
                                        <td>
                                            {{number_format($customer->total_spent,2)}} USD
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{route('store.customer.view',$customer->id)}}"
                                                   class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="View Customer"><i class="fa fa-eye"></i></a>
                                            </div>

                                        </td>

                                    </tr>
                                    </tbody>

                                @endforeach
                            </table>
                        @else
                            <p>No Orders Found <a href="{{route('store.sync.customers')}}"
                                                  class="btn btn-sm btn-primary" style="font-size: 12px;float: right" type="button" data-toggle="tooltip" title=""
                                                  data-original-title="Sync Customers"><i class="fa fa-sync"></i> Sync New Customers</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
