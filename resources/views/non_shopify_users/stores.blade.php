@extends('layout.shopify')
@section('content')
    <style>
        .mb2{
            margin-bottom: 10px !important;
        }
        .swal2-popup {
            font-size: 1.5rem !important;
        }
    </style>
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Stores

                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Stores</a>
                        </li>

                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content" >
        <div class="row mb2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('system.store.connect')}}" class="btn btn-success btn-square ">Add Store</a>
            </div>
        </div>
        <div class="block">
            <div class="block-content">
                @if(count($shops) > 0)
                <table class="js-table-sections table-borderless table table-hover">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Domain</th>
                        <th></th>
                    </tr>
                    </thead>


                    <tbody>
                    @foreach($shops as $index => $shop)
                        <tr>

                            <td class="font-w600" style="vertical-align: middle">
                                {{explode('.',$shop->shopify_domain)[0]}}
                            </td>
                            <td style="vertical-align: middle">{{ $shop->shopify_domain }}</td>
                            <td class="text-right" style="vertical-align: middle">
                                <a href="{{url('/shop/install?shop='.$shop->shopify_domain)}}" class="btn btn-sm btn-success">Switch View</a>

                                <a data-href="{{route('store.user.de-associate',$shop->id)}}" class="de-associate-button btn btn-xs btn-danger text-white"
                                   title="Remove Store" ><i class="fa fa-trash"></i></a>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                    @else
                <p> No Store Added!</p>
                    @endif
            </div>
        </div>
    </div>

@endsection
