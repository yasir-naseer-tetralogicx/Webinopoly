@extends('layout.single')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                  Import List
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Import List</a>
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
                                <button type="button" class="import_all_btn btn btn-outline-primary">Import All</button>
                                <button type="button" class="remove_all_btn  btn btn-outline-danger">Remove All</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if(count($products) > 0)
            @foreach($products as $index => $product)

                <div class="block mb-3">
                    <ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs" role="tablist">
                        <li class="nav-item import_checkbox_select">
                            <div class="custom-control custom-checkbox d-inline-block">
                                <input type="checkbox" class="custom-control-input select_one_checkbox" data-remove_url="{{route('store.product.delete',$product->id)}}" data-method="GET" data-url="{{route('retailer.import_to_shopify',$product->id)}}" id="row_{{$product->id}}">
                                <label class="custom-control-label" for="row_{{$product->id}}"></label>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#product_{{$product->id}}_products">Product</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#product_{{$product->id}}_variants">Variants</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#product_{{$product->id}}_images">Images</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#product_{{$product->id}}_description">Description</a>
                        </li>
                        <li class="nav-item ml-auto action_buttons_in_tabs">
                            <div class="block-options pl-3 pr-2">

                                <button  class="btn btn-sm btn-outline-success btn_save_retailer_product" style="vertical-align: bottom" title="Save Product" data-tabs=".product_tab_panes_{{$index}}"><i class="fa fa-save"></i></button>
                                <button  class="btn btn-sm btn-outline-danger" onclick="window.location.href='{{route('store.product.delete',$product->id)}}'" style="vertical-align: bottom" title="Delete Product"><i class="fa fa-trash-alt"></i></button>
                                <button onclick="window.location.href='{{route('retailer.import_to_shopify',$product->id)}}'" class="btn btn-sm btn-primary" style="margin-top:7px" >
                                    <i class="si si-cloud-upload mr-1"></i>
                                    Import to store
                                </button>
                            </div>
                        </li>
                    </ul>
                    <div class="block-content tab-content product_tab_panes_{{$index}}">
                        <div class="tab-pane active" id="product_{{$product->id}}_products" role="tabpanel">
                            <div class="row">
                                <?php
                                if(count($product->has_images) > 0){
                                    $images = $product->has_images()->orderBy('position')->get();
                                }
                                else{
                                    $images = [];
                                }

                                ?>
                                <div class="col-md-3">
                                    <div class="js-gallery">
                                    @if(count($images) > 0)
                                        @if($images[0]->isV == 0)
                                            <a class="img-link img-link-zoom-in img-lightbox" href="{{asset('images')}}/{{$images[0]->image}}">
                                                <img class="img-fluid" src="{{asset('images')}}/{{$images[0]->image}}" alt="">
                                            </a>
                                        @else
                                            <a class="img-link img-link-zoom-in img-lightbox" href="{{asset('images/variants')}}/{{$images[0]->image}}">
                                                <img class="img-fluid" src="{{asset('images/variants')}}/{{$images[0]->image}}" alt="">
                                            </a>
                                        @endif
                                    @endif
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <form action="{{route('store.import_list.product.update',$product->id)}}" method="post">
                                        @csrf
                                        <input type="hidden" name="request_type" value="basic-info">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="title" value="{{$product->title}}">
                                    </div>
                                    <div class="form-group">
                                        <label>Tags</label>
                                        <input class="js-tags-input form-control" type="text"
                                               value="{{$product->tags}}"   name="tags" >
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <input type="text" class="form-control" name="type" value="{{$product->type}}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Vendor</label>
                                                <input type="text" class="form-control" name="vendor" value="{{$product->vendor}}">
                                            </div>
                                        </div>
                                    </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="product_{{$product->id}}_variants" role="tabpanel">
                            <div class="block">
                                <div class="block-content" style="padding-top: 0 !important;">
                                    <table class="table table-vcenter table-hover table-striped table-borderless table-responsive">
                                        <thead>
                                        <tr>
                                            <th style="vertical-align: top">Title</th>
                                            <th style="vertical-align: top">Image</th>
                                            <th style="vertical-align: top">Price</th>
                                            <th style="vertical-align: top">Cost</th>
                                            <th>
                                                <a class="calculate_shipping_btn btn btn-sm text-white btn-primary" data-route="{{route('calculate_shipping')}}" data-product="{{$product->linked_product_id}}" data-toggle="modal" data-target="#shipping_modal_{{$product->id}}">Shipping</a>
                                            </th>
                                            <div class="modal fade" id="shipping_modal_{{$product->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-popout" role="document">
                                                    <div class="modal-content">
                                                        <div class="block block-themed block-transparent mb-0">
                                                            <div class="block-header bg-primary-dark">
                                                                <h3 class="block-title">Calculate Shipping Zone</h3>
                                                                <div class="block-options">
                                                                    <button type="button" class="btn-block-option">
                                                                        <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="block-content font-size-sm">
                                                                <div class="text-center loader-div p-2">
                                                                    <h5>Calculating Shipping Price....</h5>
                                                                    <img src="https://i.ya-webdesign.com/images/shopping-transparent-animated-gif.gif" alt="">
                                                                </div>
                                                                <div class="drop-content">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <th style="vertical-align: top">Quantity</th>
                                            <th style="vertical-align: top">SKU</th>
                                            <th style="vertical-align: top">Barcode</th>
                                        </tr>
                                        </thead>
                                        @if(count($product->hasVariants) > 0)
                                            @foreach($product->hasVariants as $index => $v)
                                                <form action="{{route('store.import_list.product.update',$product->id)}}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="request_type" value="single-variant-update">
                                                    <input type="hidden" name="variant_id" value="{{$v->id}}">
                                                    <tbody class="">
                                                    <tr>
                                                        <td class="variant_title">
                                                            @if($v->option1 != null) {{$v->option1}} @endif    @if($v->option2 != null) / {{$v->option2}} @endif    @if($v->option3 != null) / {{$v->option3}} @endif


                                                        </td>
                                                        <td class="text-center">
                                                            <img class="img-avatar " style="border: 1px solid whitesmoke"  data-input=".varaint_file_input" data-toggle="modal" data-target="#select_image_modal{{$v->id}}"
                                                                 @if($v->has_image == null)  src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg"
                                                                 @else @if($v->has_image->isV == 0) src="{{asset('images')}}/{{$v->has_image->image}}" @else src="{{asset('images/variants')}}/{{$v->has_image->image}}" @endif @endif alt="">
                                                            <div class="modal fade" id="select_image_modal{{ $v->id }}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-popout" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="block block-themed block-transparent mb-0">
                                                                            <div class="block-header bg-primary-dark">
                                                                                <h3 class="block-title">Select Image For Variant</h3>
                                                                                <div class="block-options">
                                                                                    <button type="button" class="btn-block-option">
                                                                                        <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                            <div class="block-content font-size-sm">
                                                                                <div class="row">
                                                                                    @foreach($product->has_images as $image)
                                                                                        <div class="col-md-4">
                                                                                            @if($image->isV == 0)
                                                                                                <img class="img-fluid options-item" src="{{asset('images')}}/{{$image->image}}" alt="">
                                                                                            @else
                                                                                                <img class="img-fluid options-item" src="{{asset('images/variants')}}/{{$image->image}}" alt="">
                                                                                            @endif
                                                                                            <p style="color: #ffffff;cursor: pointer" data-image="{{$image->id}}" data-variant="{{$v->id}}" data-type="retailer" class="rounded-bottom bg-info choose-variant-image text-center">Choose</p>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                                <p class="text-center font-weight-bold">OR</p>
                                                                                <hr>
                                                                                <a class="img-avatar-variant btn btn-sm btn-primary text-white mb2" data-form="#varaint_image_form_{{$index}}{{$v->id}}">Upload New Picture</a>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="price" placeholder="$0.00" value="{{$v->price}}">
                                                        </td>
                                                        <td><input type="text" class="form-control" readonly value="{{$v->cost}}" placeholder="$0.00"></td>
                                                        <td class="drop-shipping text-center">N/A</td>
                                                        <td><input type="text" readonly class="form-control" value="{{$v->quantity}}" name="quantity" placeholder="0"></td>
                                                        <td><input type="text" readonly class="form-control" name="sku" value="{{$v->sku}}"></td>
                                                        <td><input type="text" class="form-control" name="barcode" value="{{$v->barcode}}" placeholder="">
                                                        </td>

                                                    </tr>
                                                    </tbody>
                                                </form>
                                            @endforeach
                                            @else
                                            <form action="{{route('store.import_list.product.update',$product->id)}}" method="post">
                                                @csrf
                                                <input type="hidden" name="request_type" value="default-variant-update">
                                            <tr>
                                                <td class="variant_title">
                                                    Default
                                                </td>
                                                <td class="text-center">
                                                    <img class="img-avatar " style="border: 1px solid whitesmoke" src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="price" placeholder="$0.00" value="{{$product->price}}">
                                                </td>
                                                <td><input type="text" class="form-control" readonly value="{{$product->cost}}" placeholder="$0.00"></td>
                                                <td class="drop-shipping text-center">N/A</td>

                                                <td><input type="text" readonly class="form-control" value="{{$product->quantity}}" name="quantity" placeholder="0"></td>
                                                <td><input type="text" readonly class="form-control" name="sku" value="{{$product->sku}}"></td>
                                                <td><input type="text" class="form-control" name="barcode" value="{{$product->barcode}}" placeholder="">
                                                </td>

                                            </tr>
                                            </form>

                                        @endif
                                    </table>
                                </div>
                                <div class="form-image-src" style="display: none">
                                    @if(count($product->hasVariants) > 0)
                                        @foreach($product->hasVariants as $index => $v)
                                            <form id="varaint_image_form_{{$index}}{{$v->id}}" action="{{route('store.import_list.product.update',$product->id)}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="request_type" value="variant-image-update">
                                                <input type="hidden" name="variant_id" value="{{$v->id}}">
                                                <input type="file" name="varaint_src" class="varaint_file_input" accept="image/*">
                                            </form>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="product_{{$product->id}}_images" role="tabpanel">
                            <div class="block">
                                <div class="block-header">
                                    <h3 class="block-title">Images</h3>
                                </div>
                                <div class="block-content">
                                    @if(count($product->has_images) >0)
                                        <div class="row editable ">

                                            @foreach($product->has_images()->orderBy('position')->get() as $image)
                                                <div class="col-md-4 mb2 preview-image animated fadeIn" >
                                                    <div class="options-container fx-img-zoom-in fx-opt-slide-right">
                                                        @if($image->isV == 0)
                                                            <img class="img-fluid options-item" src="{{asset('images')}}/{{$image->image}}" alt="">
                                                        @else
                                                            <img class="img-fluid options-item" src="{{asset('images/variants')}}/{{$image->image}}" alt="">
                                                        @endif
                                                        <div class="options-overlay bg-black-75">
                                                            <div class="options-overlay-content">
                                                                <a class="btn btn-sm btn-light delete-file" data-type="existing-product-image-delete" data-token="{{csrf_token()}}" data-route="{{route('store.import_list.product.update',$product->id)}}" data-file="{{$image->id}}"><i class="fa fa-times"></i> Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                        <hr>
                                    @endif
                                    <div class="row">
                                        <form class="product-images-form" action="{{route('store.import_list.product.update',$product->id)}}" method="post" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="request_type" value="existing-product-image-add">
                                            <div class="col-md-12" style="padding-bottom: 13px;width: 1006px">
                                                <div class="dropzone dz-clickable">
                                                    <div class="dz-default dz-message"><span>Click here to upload images.</span></div>
                                                    <div class="row preview-drop"></div>
                                                </div>
                                                <input style="display: none" type="file"  name="images[]" accept="image/*" class="push-30-t push-30 dz-clickable images-upload" multiple >
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="product_{{$product->id}}_description" role="tabpanel">
                            <form action="{{route('store.import_list.product.update',$product->id)}}" method="post">
                                @csrf
                                <input type="hidden" name="request_type" value="description">
                            <textarea class="js-summernote" name="description"
                                      placeholder="Please Enter Description here !">{{$product->description}}</textarea>

                            </form>
                        </div>
                    </div>
                </div>


            @endforeach
            @else
                <div class="block">
                    <div class="block-content ">
                        <p class="text-center"> No Products Found in Import List!</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection
