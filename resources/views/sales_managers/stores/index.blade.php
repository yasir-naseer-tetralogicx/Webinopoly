@extends('layout.manager')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                   Stores
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx active" href="">Stores</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="block">
            <div class="block-content">
                @if (count($stores) > 0)
                    <table class="table table-hover table-borderless table-striped table-vcenter">
                        <thead>
                        <tr>

                            <th>Title</th>
                            <th>Shopify Domain</th>
                            <th>Imported Products</th>
                            <th>Orders</th>
                            <th>Tickets</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="">
                        @foreach($stores as $index => $store)
                            <tr>

                                <td class="font-w600"><a href="{{route('sales_managers.stores.view',$store->id)}}">{{ explode('.',$store->shopify_domain)[0]}}</a></td>
                                <td>
                                    <span class="badge badge-primary">{{$store->shopify_domain}}</span>
                                </td>
                                <td>
                                    {{count($store->has_imported)}}
                                </td>
                                <td>
                                    {{count($store->has_orders)}}

                                </td>
                                <td>
                                    {{count($store->has_tickets)}}

                                </td>
                                <td class="text-right">
                                    <div class="btn-group mr-2 mb-2">
                                        <a class="btn btn-xs btn-sm btn-success" type="button" href="{{route('sales_managers.stores.view',$store->id)}}" title="View Store">
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

    @endsection
