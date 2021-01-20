@extends('layout.single')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                   Payment History
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            Payment History
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
                    <div class="block-content">
                        @if (count($payments) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>

                                    <th>Order</th>
                                    <th style="width: 10%">Payer</th>
                                    <th>Amount</th>
                                    <th>Source</th>
                                    <th>Store</th>
                                    <th>Transaction Date</th>
                                </tr>
                                </thead>

                                @foreach($payments as $index => $payment)
                                    <tbody class="">
                                    <tr>

                                        <td class="font-w600"> @if($payment->has_order)<a href="{{route('store.order.view',$payment->has_order->id)}}">{{ $payment->has_order->name }}</a> @else Order Details Deleted @endif</td>
                                        <td>
                                            {{$payment->name}}
                                        </td>

                                        <td>
                                            {{number_format($payment->amount,2)}} USD
                                        </td>
                                        <td>
                                          @if($payment->card_last_four != null)
                                              <span class="badge badge-warning"> <i class="fa fa-credit-card"></i> CARD </span>
                                              @elseif($payment->paypal_payment_id != null)
                                                <span class="badge badge-success"> <i class="fab fa-paypal"></i> PAYPAL </span>
                                              @else
                                                <span class="badge badge-primary"> <i class="fa fa-wallet"></i> WALLET </span>
                                            @endif

                                        </td>
                                        <td>
                                            <span class="badge badge-primary"> {{$payment->store->shopify_domain}} </span>
                                        </td>
                                        <td>
                                            {{date_create($payment->created_at)->format('d-m-Y') }}
                                        </td>

                                    </tr>
                                    </tbody>

                                @endforeach
                            </table>
                        @else
                            <p>No Payments Founds</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center" style="font-size: 17px">
                {!! $payments->links() !!}
            </div>
        </div>
    </div>

    @endsection
