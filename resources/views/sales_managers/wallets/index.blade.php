@extends('layout.manager')
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
                        <li class="breadcrumb-item">    Wallets</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div  class="form-horizontal push-30">
    <div class="content">
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
                                    <th>Role</th>
                                    <th>Wallet Number</th>
                                    <th>Available</th>
                                    <th>Requested</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                                </thead>

                                @foreach($users as $index => $user)
                                    <tbody class="">
                                    <tr>
                                        <td class="font-w600">  <a href="{{route('sales_managers.wallets.detail',$user->has_wallet->id)}}"> {{ $user->name }}</a></td>
                                        <td>
                                           {{$user->email}}
                                        </td>
                                        <td>
                                            @if($user->hasRole('non-shopify-users'))
                                                <span class="badge badge-success">Shopify User</span>
                                            @else
                                                <span class="badge badge-warning">Manager</span>
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

                                        <td>
                                            <span class="badge badge-success">Active</span>
                                        </td>
                                         <td>


                                        </td>
                                        <td class="text-center">
                                            <a href="{{route('sales_managers.wallets.detail',$user->has_wallet->id)}}"
                                               class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                               data-original-title="View Wallet"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    </tbody>

                                @endforeach
                            </table>
                        @else
                            <p>No Wallets Found</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
