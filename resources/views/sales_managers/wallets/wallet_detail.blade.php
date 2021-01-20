@extends('layout.manager')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Wallet / {{$user->name}}
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Wallet</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">{{$user->name}}</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    @if(!isset($wallet) && !isset($user))
        <div class="block ">
            <div class="block-content ">
                <p class="text-center"> No Account Associated With This Store Found ! <a href="{{route('store.index')}}"> Click Here For Account Association :) </a></p>
            </div>
        </div>
    @else
        @if($wallet != null)
            <div class="content">
                <div class="content-grid">
                    <div class="row mb2">
                        <div class="col-md-8"></div>
                        <div class="col-md-4 ">
                            <button class="btn btn-primary" style="float: right" data-toggle="modal" data-target="#bank_transfer_modal"> Top-up Wallet </button>
                            <div class="modal fade" id="bank_transfer_modal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-popout" role="document">
                                    <div class="modal-content">
                                        <div class="block block-themed block-transparent mb-0">
                                            <div class="block-header bg-primary-dark">
                                                <h3 class="block-title">TOPUP Wallet</h3>
                                                <div class="block-options">
                                                    <button type="button" class="btn-block-option">
                                                        <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <form action="{{route('sales_managers.user.wallet.topup')}}" method="post">
                                                @csrf
                                                <input type="hidden" value="{{$wallet->id}}" name="wallet_id">
                                                <div class="block-content font-size-sm">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <div class="form-material">
                                                                <label for="material-error">Amount</label>
                                                                <input required class="form-control" type="number"  name="amount"
                                                                       value=""   placeholder="Enter Cheque Amount here">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="block-content block-content-full text-right border-top">
                                                    <button type="submit" class="btn btn-sm btn-primary" >Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb2">
                        <div class="col-md-3">
                            <div class="block ">
                                <div class="block-header">
                                    <h3 class="block-title ">Available</h3>
                                </div>
                                <div class="block-content ">
                                    <p class="font-size-h2"> {{number_format($wallet->available,2)}} USD</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="block ">
                                <div class="block-header">
                                    <h3 class="block-title">Pending</h3>
                                </div>
                                <div class="block-content ">
                                    <p class=" font-size-h2"> {{number_format($wallet->pending,2)}} USD</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="block ">
                                <div class="block-header">
                                    <h3 class="block-title">Transferred</h3>
                                </div>
                                <div class="block-content ">
                                    <p class="font-size-h2"> {{number_format($wallet->transferred,2)}} USD</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="block ">
                                <div class="block-header">
                                    <h3 class="block-title">Used</h3>
                                </div>
                                <div class="block-content ">
                                    <p class=" font-size-h2"> {{number_format($wallet->used,2)}} USD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block">
                                <div class="block-header">
                                    <h3 class="block-title ">Wallet ID
                                        <span style="float: right" ><i class="fa fa-info-circle" title="This ID used for wallet-to-wallet transferred"></i> {{$wallet->wallet_token}}</span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block">
                                <div class="block-header">
                                    <div class="block-title">
                                        Bank Transfer Top-up Requests
                                    </div>
                                </div>
                                <div class="block-content">
                                    @if (count($wallet->requests()->where('type','bank transfer')->get()) > 0)
                                        <table class="table table-hover table-borderless table-striped table-vcenter">
                                            <thead>
                                            <tr>
                                                <th>Bank</th>
                                                <th>Cheque</th>
                                                <th>Company/Sender Title</th>
                                                <th>Amount</th>
                                                <th>Bank Proof Copy</th>
                                                <th>Status</th>
                                                <th></th>
                                            </tr>
                                            </thead>

                                            @foreach($wallet->requests()->where('type','bank transfer')->get() as $index => $req)
                                                <tbody class="">
                                                <tr>
                                                    <td class="font-w600">{{ $req->bank_name }}</td>
                                                    <td>
                                                        {{$req->cheque}}
                                                    </td>
                                                    <td>
                                                        {{$req->cheque_title}}
                                                    </td>
                                                    <td>
                                                        {{number_format($req->amount,2)}} USD
                                                    </td>
                                                    <td class="js-gallery">
                                                        @if($req->attachment != null)
                                                            <a class="img-link img-link-zoom-in img-lightbox" href="{{asset('wallet-attachment')}}/{{$req->attachment}}">
                                                                View Proof
                                                            </a>
                                                        @else
                                                            No Proof Provided
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($req->status == 0)
                                                            <span class="badge badge-warning">Pending</span>
                                                        @else
                                                            <span class="badge badge-success">Approved</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        @if($req->status == 0)
                                                            <button class="btn btn-sm btn-success <!--approve-bank-transfer-button-->" data-toggle="modal" data-target="#bank_approve_modal{{$req->id}}" {{--data-route="{{route('sales_managers.wallets.approve.request',$req->id)}}" data-wallet="{{$wallet->wallet_token}}" data-amount="{{number_format($req->amount,2)}} USD" --}} > Approve Request</button>

                                                        @endif
                                                    </td>

                                                </tr>
                                                </tbody>
                                                <div class="modal fade" id="bank_approve_modal{{$req->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-popout" role="document">
                                                        <div class="modal-content">
                                                            <div class="block block-themed block-transparent mb-0">
                                                                <div class="block-header bg-primary-dark">
                                                                    <h3 class="block-title">Approve Top-up</h3>
                                                                    <div class="block-options">
                                                                        <button type="button" class="btn-block-option">
                                                                            <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <form action="{{route('sales_managers.wallets.approve.request',$req->id)}}" method="get">
                                                                    <div class="block-content font-size-sm">
                                                                        <div class="form-group">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-material">
                                                                                    <label for="material-error">Approved Date</label>
                                                                                    <input required class="form-control" type="date"  name="date"
                                                                                           value="" >
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="block-content block-content-full text-right border-top">
                                                                        <button type="submit" class="btn btn-sm btn-primary" >Approved</button>
                                                                    </div>
                                                                </form>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            @endforeach
                                        </table>
                                    @else
                                        <p>No  Bank Transfer Requests Found</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block">
                                <div class="block-header">
                                    <div class="block-title">
                                        AliBaba Top-up Requests
                                    </div>
                                </div>
                                <div class="block-content">
                                    @if (count($wallet->requests()->where('type','alibaba')->get()) > 0)
                                        <table class="table table-hover table-borderless table-striped table-vcenter">
                                            <thead>
                                            <tr>
                                                <th>Company/Sender Title</th>
                                                <th>Alibaba Order Number </th>
                                                <th>Amount</th>
                                                <th>Bank Proof Copy</th>
                                                <th>Notes</th>
                                                <th>Status</th>
                                                <th></th>
                                            </tr>
                                            </thead>

                                            @foreach($wallet->requests()->where('type','alibaba')->get() as $index => $req)
                                                <tbody class="">
                                                <tr>

                                                    <td>
                                                        {{$req->cheque_title}}
                                                    </td>
                                                    <td>
                                                        {{$req->cheque}}
                                                    </td>

                                                    <td>
                                                        {{number_format($req->amount,2)}} USD
                                                    </td>
                                                    <td class="js-gallery">
                                                        @if($req->attachment != null)
                                                            <a class="img-link img-link-zoom-in img-lightbox" href="{{asset('wallet-attachment')}}/{{$req->attachment}}">
                                                                View Proof
                                                            </a>
                                                        @else
                                                            No Proof Provided
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($req->notes != null)
                                                            {{$req->notes}}
                                                        @else
                                                            No Notes
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($req->status == 0)
                                                            <span class="badge badge-warning">Pending</span>
                                                        @else
                                                            <span class="badge badge-success">Approved</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        @if($req->status == 0)
                                                            <button class="btn btn-sm btn-success <!--approve-bank-transfer-button-->" data-toggle="modal" data-target="#bank_approve_modal{{$req->id}}" {{--data-route="{{route('sales_managers.wallets.approve.request',$req->id)}}" data-wallet="{{$wallet->wallet_token}}" data-amount="{{number_format($req->amount,2)}} USD" --}} > Approve Request</button>

                                                        @endif
                                                    </td>

                                                </tr>
                                                </tbody>
                                                <div class="modal fade" id="bank_approve_modal{{$req->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-popout" role="document">
                                                        <div class="modal-content">
                                                            <div class="block block-themed block-transparent mb-0">
                                                                <div class="block-header bg-primary-dark">
                                                                    <h3 class="block-title">Approve Top-up</h3>
                                                                    <div class="block-options">
                                                                        <button type="button" class="btn-block-option">
                                                                            <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                <form action="{{route('sales_managers.wallets.approve.request',$req->id)}}" method="get">
                                                                    <div class="block-content font-size-sm">
                                                                        <div class="form-group">
                                                                            <div class="col-sm-12">
                                                                                <div class="form-material">
                                                                                    <label for="material-error">Approved Date</label>
                                                                                    <input required class="form-control" type="date"  name="date"
                                                                                           value="" >
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="block-content block-content-full text-right border-top">
                                                                        <button type="submit" class="btn btn-sm btn-primary" >Approved</button>
                                                                    </div>
                                                                </form>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            @endforeach
                                        </table>
                                    @else
                                        <p>No AliBaba Top-up Requests Found</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                  @include('inc.wallet_log')
                </div>
            </div>
        @endif
    @endif
@endsection
