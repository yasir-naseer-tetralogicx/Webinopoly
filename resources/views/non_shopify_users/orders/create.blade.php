@extends('layout.shopify')
@section('content')


    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Create Custom Order
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Create Custom</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <form id="custom_order_form" action="{{route('users.custom.orders.create.post')}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <div class="block-header">
                            <h3 class="block-title">
                                Order Details
                            </h3>
                        </div>
                        <div class="block-content pb-2">
                            <div class="text-center">
                                <a class="btn btn-primary text-white" data-toggle="modal" data-target="#browse_product_modal"> Browse Products </a>
                            </div>
                            <hr>
                            <div class="selected-variant-section">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="block">
                        <div class="block-header">
                            <h3 class="block-title">
                                Customer Section
                            </h3>
                        </div>
                        <div class="block-content pb-2">

                            @if(count($customers) > 0)
                                <div class="row mb2">
                                    <div class="col-md-12">
                                        <select name="customer_selection" id="customer_selection_drop" class="form-control">
                                            <option value=""> Select Customer </option>
                                            @foreach($customers as $customer)
                                                <option data-email="{{$customer->email}}" data-first="{{$customer->first_name}}" data-last="{{$customer->last_name}}" value="{{$customer->id}}"> {{$customer->first_name}} {{$customer->last_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <hr>
                            @endif
                            <div class="customer-filling-section row">
                                <div class="col-md-6">
                                    <h5> Customer </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb2">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="c_first_name"
                                                   value=""  placeholder="" required>
                                        </div>
                                        <div class="col-md-6 mb2">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control" name="c_last_name"
                                                   value=""  placeholder="" required>
                                        </div>
                                        <div class="col-md-12 mb2">
                                            <label>Email</label>
                                            <input type="text" class="form-control" name="email"
                                                   value=""  placeholder="" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5> Shipping Details </h5>
                                    <div class="row">
                                        <div class="col-md-6 mb2">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="first_name"
                                                   value=""  placeholder="" required>
                                        </div>
                                        <div class="col-md-6 mb2">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="last_name"
                                                   value=""  placeholder="" required>
                                        </div>
                                        <div class="col-md-12 mb2">
                                            <label>Address</label>
                                            <input type="text" class="form-control" name="address1"
                                                   value=""  placeholder="" required>
                                        </div>
                                        <div class="col-md-12 mb2">
                                            <label>Street (optional)</label>
                                            <input type="text" class="form-control" name="address2"
                                                   value=""  placeholder="" >
                                        </div>
                                        <div class="col-md-4 mb2">
                                            <label>City</label>
                                            <input type="text" class="form-control" name="city"
                                                   value=""  placeholder="" required>
                                        </div>
                                        <div class="col-md-4 mb2">
                                            <label>Province</label>
                                            <input type="text" class="form-control" name="province"
                                                   value=""  placeholder="" required>
                                        </div>
                                        <div class="col-md-4 mb2">
                                            <label>Zip Code</label>
                                            <input type="text" class="form-control" name="zip"
                                                   value=""  placeholder="" required>
                                        </div>
                                        <div class="col-md-12 mb2">
                                            <label>Country</label>
                                            <select name="country" data-route="{{route('users.order.shipping.rate')}}" id="country-selection" required class="form-control">
                                                <option value="">Select Country</option>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->name}}">{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-12 mb2">
                                            <label>Phone</label>
                                            <input type="text" required class="form-control" name="phone"
                                                   value=""  placeholder="" >
                                        </div>

                                        <div class="col-md-12 mb2">
                                            <label>Order Date</label>
                                            <input type="date" required class="form-control" name="order_date"
                                                   value=""  placeholder="" >
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb2">
                <div class="col-md-12">
                    <div class="block">
                        <div class="block-header">
                            <h3 class="block-title">
                            Payment Section
                            </h3>
                        </div>
                        <div class="block-content">
                            <div class="form-group">
                                <div class="custom-control custom-radio custom-control-inline custom-control-lg">
                                    <input type="radio" class="custom-control-input" id="example-rd-custom-inline-lg1" name="payment-option" checked="" value="paypal">
                                    <label class="custom-control-label" for="example-rd-custom-inline-lg1">Pay With PAYPAL</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline custom-control-lg">
                                    <input type="radio" class="custom-control-input" id="example-rd-custom-inline-lg2" name="payment-option" value="wallet">
                                    <label class="custom-control-label" for="example-rd-custom-inline-lg2">Pay with Wallet</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline custom-control-lg">
                                    <input type="radio" class="custom-control-input" id="example-rd-custom-inline-lg3" name="payment-option" value="draft">
                                    <label class="custom-control-label" for="example-rd-custom-inline-lg3">Save as Draft</label>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="row mb2">
                <div class="col-md-12">
                    <input id="shipping_price_input" type="hidden" name="shipping_price" value="0">

                    <input id="variant_selection_check" type="hidden" name="variant_selection" value="0">
                    <div class="text-right">
                        <button class="btn btn-primary custom-order-btn"> Save </button>
                    </div>
                </div>
            </div>
        </form>

    </div>

    <div class="modal fade" id="browse_product_modal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Browse Products</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option">
                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                            </button>
                        </div>
                    </div>
                    <form id="get-selection-form" action="{{route('get_selected_variants')}}" method="get">
                        <div class="block-content font-size-sm">
                            <div class="form-group">
                                <input type="search" id="product-search-field" name="product-search" class="form-control" placeholder="Search by Keyword" style="margin-bottom: 10px">
                                <div class="countries-section" id="product-section">
                                    @include('non_shopify_users.orders.product-browse-section')
                                </div>
                            </div>
                        </div>
                        <div class="block-content block-content-full text-right border-top">
                            <button type="submit" class="btn btn-sm btn-primary " >Add to Order</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="paypal_pay_trigger" tabindex="-1" role="dialog" aria-labelledby="modal-block-vcenter" aria-hidden="true"></div>
    <div class="ajax_paypal_form_submit" style="display: none;"></div>

{{--    <script src="https://www.paypal.com/sdk/js?client-id=AV6qhCigre8RgTt8E6Z0KNesHxr1aDyJ2hmsk2ssQYmlaVxMHm2JFJvqDCsU15FhoCJY0mDzOu-jbFPY&currency=USD"></script>--}}

    <script
        src="https://www.paypal.com/sdk/js?client-id=ASxb6_rmf3pte_En7MfEVLPe_KDZQj68bKpzJzl7320mmpV3uDRDLGCY1LaCkyYZ4zNpHdC9oZ73-WFv">
    </script>

@endsection
