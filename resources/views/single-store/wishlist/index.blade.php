@extends('layout.single')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Wishlist
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Wishlist</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        @php
            $user = $shop->has_user()->first();
        @endphp
        @if($user->has_manager != null)
            <form class="js-form-icon-search push" action="" method="get">
                <div class="form-group">
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="Search by name" value="" name="search">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                            <a class="btn btn-danger" href="/store/wishlist"> <i class="fa fa-times"></i> Clear </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-end pr-0">
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
                        <div class="col-md-12 mb2">
                            <button style="float: right;margin-bottom: 10px" class="btn btn-sm btn-primary" data-target="#create_new_ticket" data-toggle="modal">Create New Wishlist</button>
                        </div>
                        <div class="col-md-12 mb2">
                            @if(count($wishlist) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>

                                        <th>Product</th>
                                        <th>Cost</th>
                                        <th>Sales</th>
                                        <th>Store</th>
                                        <th style="width: 5%">Markets</th>
                                        <th>Status</th>
                                        <th>Approved Cost</th>
                                        <th style="text-align: right">
                                        </th>
                                    </tr>
                                    </thead>

                                    @foreach($wishlist as $index => $item)
                                        <tbody class="">
                                        <tr>

                                            <td class="font-w600"><a href="{{route('store.wishlist.view',$item->id)}}">{{ $item->product_name }}</a></td>
                                            <td>
                                                {{number_format($item->cost,2)}} USD
                                            </td>
                                            <td>
                                                {{$item->monthly_sales}}
                                            </td>
                                            <td>
                                                @if($item->has_store)
                                                    <span class="badge badge-success">{{ $item->has_store->shopify_domain  }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(count($item->has_market) > 0)
                                                @foreach($item->has_market as $country)
                                                    <span class="badge badge-primary">{{$country->name}}</span>
                                                    @endforeach
                                                @else
                                                    none
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->has_status != null)
                                                    <span class="badge " style="background: {{$item->has_status->color}};color: white;"> {{$item->has_status->name}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->approved_price != null)
                                                    {{number_format($item->approved_price,2)}} USD
                                                    @else
                                                    Not Approved Yet
                                                @endif
                                            </td>

                                            <td class="text-right">
                                                <div class="btn-group">
                                                    <a href="{{route('store.wishlist.view',$item->id)}}"
                                                       class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="View Wishlist"><i class="fa fa-eye"></i></a>
                                                    <a href="{{ route('wishlist.delete', $item->id) }}"
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
            <div class="modal fade" id="create_new_ticket" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-popout" role="document">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">New Wishlist</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option">
                                        <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                    </button>
                                </div>
                            </div>
                            <form action="{{route('wishlist.create')}}" method="post"  enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="manager_id" value="{{$user->sale_manager_id}}">
                                <input type="hidden" name="shop_id" value="{{$shop->id}}">
                                <input type="hidden" name="type" value="shopify-user-wishlist">

                                <div class="block-content font-size-sm">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Product Name <i class="fa fa-question-circle"  title="This is the name of product you want to request in your wishlist"> </i></label>

                                                <input required class="form-control" type="text"  name="product_name"
                                                       placeholder="Enter Title here">
                                            </div>
                                        </div>
                                    </div>
                                    @if($user->has_stores()->count() > 0)
                                        @if($user->has_stores()->count() == 1)
                                            @php
                                                $store = $user->has_stores()->first()
                                            @endphp
                                            <input type="hidden" name="shop_id" value="{{ $store->id }}">
                                        @else
                                            <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <label for="material-error">Shopify Store <i class="fa fa-question-circle"  title="This is the name of the store you want to you want to request for your wishlist"> </i></label>
                                                    <select name="shop_id" id="" class="form-control">
                                                        @foreach($user->has_stores()->get() as $store)
                                                            <option value="{{ $store->id }}"> {{ $store->shopify_domain }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Target Dropshipping Cost <i class="fa fa-question-circle"  title="This is the cost of product you want to request in your wishlist"> </i></label>
                                                <input required class="form-control" type="number" step="any"  name="cost"
                                                       placeholder="Enter Dropshipping Cost here">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Expected Monthly Sales <i class="fa fa-question-circle"  title="This is the expected monthly sales of product you want to request in your wishlist"> </i></label>
                                                <input required class="form-control" type="number" step="any"  name="monthly_sales"
                                                       placeholder="Monthly Sales/Orders">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Reference <i class="fa fa-question-circle"  title="Reference link to product you want to request in your wishlist"> </i></label>
                                                <input  class="form-control" type="url"  name="reference"
                                                        placeholder="Enter Reference here">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Selling Markets <i class="fa fa-question-circle"  title="Countries where product you want to sale. "> </i></label>
                                                <select class="form-control js-select2" style="width: 100%;" data-placeholder="Choose multiple markets.." name="countries[]" required  multiple="">
                                                    <option></option>
                                                    @foreach($countries as $country)
                                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group">
{{--                                        <div class="col-sm-12">--}}
{{--                                            <div class="form-material">--}}
{{--                                                <label for="material-error">Attachments <i class="fa fa-question-circle"  title="Files/Images related to this product (Hold Ctrl for selecting multiple images)"> </i></label>--}}
{{--                                                <input type="file" name="attachments[]" class="form-control" multiple>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

                                        <div class="col-md-12" style="padding-bottom: 13px;">
                                            <div class="dropzone dz-clickable">
                                                <div class="dz-default dz-message"><span>Click here to upload images.</span></div>
                                                <div class="row preview-drop"></div>
                                            </div>

                                            <input style="display: none" accept="image/*"  type="file"  name="attachments[]" class="push-30-t dz-hidden-input push-30 images-upload" multiple>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-material">
                                                <label for="material-error">Description <i class="fa fa-question-circle"  title="Description of product you want to request in wishlist"> </i></label>
                                                <textarea required class="js-summernote" name="description"
                                                          placeholder="Please Enter Description here !"></textarea>
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

        @else
            <div class="block">
                <div class="block-content">
                    <p class="text-center">You can't create wishlist because you are not assigned to any sales manager.</p>
                </div>
            </div>
    @endif


@endsection
