@extends('layout.single')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    My Products
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">My Products</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="content-grid">
            <form action="">
                <div class="row mb2">

                    <div class="col-md-7">
                        <input type="search" value="{{$search}}" name="search" placeholder="Search By Keyword" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <select name="source" class="form-control">
                            <option @if($source == 'all') selected @endif value="all">All Sources</option>
                            <option @if($source == 'Fantasy') selected @endif value="Fantasy">WeFullFill</option>
                            <option @if($source == 'AliExpress') selected @endif value="AliExpress">AliExpress</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-block btn-primary"><i class="fa fa-search" style="margin-right: 5px"></i>Search</button>
                    </div>

                </div>
            </form>
            <div class="block mb2">
                <div class="block-content" style="padding: 1.25rem 0.75rem 1px !important;">
                    <div class="p-2">
                        <div class="custom-control custom-checkbox d-inline-block">
                            <input type="checkbox" class="custom-control-input select_all_checkbox" id="row_selected_all" >
                            <label class="custom-control-label" for="row_selected_all"></label>
                        </div>
                        <div class="product-count d-inline-block">
                            <p style="font-size: 13px;font-weight: 600">  Showing  {{count($products)}} products </p>
                        </div>
                        <div class="selected-product-count d-none" >
                            <p style="font-size: 13px;font-weight: 600">  Selected  products </p>
                        </div>
                        <div class="mb-3 checkbox_selection_options d-none ml-1" >
                            <div class="btn-group btn-group-sm" role="group" aria-label="Small Outline Primary">
                                <button type="button" class="remove_all_btn  btn btn-outline-danger">Remove All</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($products) > 0)
                <div class="row">
                    @foreach($products as $index => $product)
                        <div class="col-md-4">
                            <div class="block mb-3">
                                <ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs" role="tablist">
                                    <li class="nav-item import_checkbox_select">
                                        <div class="custom-control custom-checkbox d-inline-block">
                                            <input type="checkbox" class="custom-control-input select_one_checkbox" data-remove_url="{{route('store.product.delete',$product->id)}}" data-method="GET"  id="row_{{$product->id}}">
                                            <label class="custom-control-label" for="row_{{$product->id}}"></label>
                                        </div>
                                    </li>

                                </ul>
                                <div class="block-content tab-content product_tab_panes_{{$index}}">
                                    <div class="tab-pane active" id="product_{{$product->id}}_products" role="tabpanel">
                                        <div class="block">
                                            <div class="options-container">
                                                <a href="{{route('store.my_product.wefulfill.show',$product->id)}}">
                                                    @if(count($product->has_images) > 0)
                                                        @foreach($product->has_images()->orderBy('position')->cursor() as $index => $image)
                                                            @if($index == 0)
                                                                @if($product->import_from_shopify == 1)
                                                                    <img class="img-fluid options-item" src="{{$image->image}}">
                                                                @else
                                                                    @if($image->isV == 0)
                                                                        <img class="img-fluid options-item" src="{{asset('images')}}/{{$image->image}}">
                                                                    @else   <img class="img-fluid options-item" src="{{asset('images/variants')}}/{{$image->image}}" alt="">
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <img class="img-fluid options-item" src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                                    @endif

                                                </a>
                                            </div>

                                            <div class="block-content" style="padding-bottom: 10px">
                                                <div class="push-10">
                                                    <a class="h6" href="{{route('store.my_product.wefulfill.show',$product->id)}}">{{$product->title}}</a>
                                                    <div class="font-w600 text-success mt-1 push-10-l">${{number_format($product->price,2)}}  <span class="mb2 font-size-sm" style="float: right;color: grey">@if($product->fulfilled_by == "Fantasy") WeFulfill @else {{$product->fulfilled_by}} @endif </span></div>
                                                </div>
                                                <hr>
                                                <div class="btn-group" role="group">
                                                    <button type="button"  class="btn btn-outline-secondary" onclick="window.location.href='{{route('store.my_product.wefulfill.show',$product->id)}}'" title="View Product"><i class="fa fa-eye"></i></button>
                                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{route('store.my_product.edit',$product->id)}}'" title="Edit Product"><i class="fa fa-pencil-alt"></i></button>
                                                    <button  class="btn btn-outline-secondary" onclick="window.location.href='{{route('store.product.delete',$product->id)}}'" style="vertical-align: bottom" title="Delete Product"><i class="fa fa-trash-alt"></i></button>
{{--                                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{route('store.product.sync',$product->id)}}'"  title="Sync"><i class="fa fa-sync"></i></button>--}}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-end">
                    {{ $products->links() }}
                </div>
            @else
                <div class="block">
                    <div class="block-content ">
                        <p class="text-center"> No Product Found !</p>
                    </div>
                </div>
            @endif



        </div>
    </div>

@endsection
