@extends('layout.manager')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Wishlists
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Wishlists</a>
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
                        <a class="btn btn-danger" href="{{route('sales_managers.wishlist')}}"> <i class="fa fa-times"></i> Clear </a>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6  mb2">
                <form class="d-flex text-right" action="" method="get">
                    <select name="status" style="margin-left: 10px" class="form-control">
                        <option value="" style="display: none">Status</option>
                        @foreach($statuses as $status)
                            <option @if($selected_status == $status->id) selected @endif value="{{$status->id}}">{{$status->name}}</option>
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
                        @if(count($wishlist) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>

                                    <th>Product</th>
                                    <th>COST</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Last Reply</th>
                                    <th style="text-align: right">
                                    </th>
                                </tr>
                                </thead>

                                @foreach($wishlist as $index => $item)
                                    <tbody class="">
                                    <tr>

                                        <td class="font-w600"><a href="{{route('sales_managers.wishlist.view',$item->id)}}">{{ $item->product_name }}</a></td>
                                        <td>{{number_format($item->cost,2)}} USD</td>
                                        <td>
                                            @if($item->has_user != null)
                                                {{$item->has_user->email}}
                                                @elseif($item->has_store != null)
                                                {{explode('.',$item->has_store->shopify_domain)[0]}}
                                                @endif

                                        </td>
                                        <td>
                                            @if($item->has_status != null)
                                                <span class="badge " style="background: {{$item->has_status->color}};color: white;"> {{$item->has_status->name}}</span>
                                            @endif
                                        </td>

                                        <td>{{\Carbon\Carbon::parse($item->updated_at)->diffForHumans()}}</td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a href="{{route('sales_managers.wishlist.view',$item->id)}}"
                                                   class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="View Wishlist"><i class="fa fa-eye"></i></a>
                                                <a href=""
                                                   class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="Delete Wishlist"><i class="fa fa-times"></i></a>
                                            </div>
                                        </td>

                                    </tr>
                                    </tbody>

                                @endforeach
                            </table>

                            <div class="row">
                                <div class="col-md-12 text-center" style="font-size: 17px">
                                    {!! $wishlist->links() !!}
                                </div>
                            </div>

                        @else
                            <p class="text-center">No Wishlist Found.</p>
                        @endif
                    </div>


                </div>
            </div>
        </div>

    </div>

@endsection
