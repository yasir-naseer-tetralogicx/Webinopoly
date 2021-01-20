@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Wallets
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Wallets</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div  class="form-horizontal push-30">
    <div class="content">

        <form class="js-form-icon-search push" action="" method="get">
            <div class="form-group">
                <div class="input-group">
                    <input type="search" class="form-control" placeholder="Search by Store and Email" value="@isset($search) {{$search}} @endif" name="search" required >
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a class="btn btn-danger" href="{{route('admin.wallets')}}"> <i class="fa fa-times"></i> Clear </a>

                    </div>
                </div>
            </div>
        </form>

        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content">
                        @if (count($users) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Email</th>
                                    <th>Shops</th>
                                    <th>Wallet Number</th>
                                    <th>Available</th>
                                    <th>Requested</th>

                                    <th></th>
                                </tr>
                                </thead>
                                <tbody class="">
                                @foreach($users as $index => $user)
                                    <tr>
                                        <td class="font-w600">{{ $user->name }}</td>
                                        <td>
                                           {{$user->email}}
                                        </td>
                                        <td>
                                            @if(count($user->has_shops) > 0)
                                                @foreach($user->has_shops as $shop)
                                                    <span class="badge badge-success">{{explode('.',$shop->shopify_domain)[0]}}</span>
                                                    @endforeach
                                                @else
                                                <span class="badge badge-primary">No Shop Attached</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{$user->has_wallet->wallet_token}}
                                        </td>
                                        <td>
                                            {{number_format($user->has_wallet->available,2)}} USD
                                        </td>
                                        <td>
                                            {{number_format($user->has_wallet->pending,2)}}
                                        </td>

                                        <td class="text-center">
                                            <a href="{{route('admin.wallets.detail',$user->has_wallet->id)}}"
                                               class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                               data-original-title="View Wallet"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No Wallets Found</p>
                        @endif
                            <div class="row">
                                <div class="col-md-12 text-center" style="font-size: 17px">
                                    {!! $users->links() !!}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
