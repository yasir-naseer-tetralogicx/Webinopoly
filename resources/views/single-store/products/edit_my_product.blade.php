@extends('layout.single')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    {{ \Illuminate\Support\Str::limit($product->title,20,'...') }}

                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">My Products</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">
                                {{ \Illuminate\Support\Str::limit($product->title,20,'...') }}
                            </a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row mb2">
            <div class="col-md-12">
                <button style="float: right;padding: 4px 40px;" class="btn btn-success btn_save_my_product"> Save </button>
            </div>
        </div>
        <div class="block">
            <div class="block-content my_product_form_div">
                <div class="block-header">
                    <h3 class="block-title"> Basic Information</h3>
                </div>
                <div class="row mb2">
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
                                @if($product->import_from_shopify == 1)
                                    <a class="img-link img-link-zoom-in img-lightbox" href="{{$images[0]->image}}">
                                        <img class="img-fluid" src="{{$images[0]->image}}" alt="">
                                    </a>
                                @else
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
                <div class="row mb2">
                    <div class="block-header">
                        <h3 class="block-title"> Description</h3>
                    </div>
                    <div class="col-md-12">
                        <form action="{{route('store.import_list.product.update',$product->id)}}" method="post">
                            @csrf
                            <input type="hidden" name="request_type" value="description">
                            <textarea class="js-summernote" name="description"
                                      placeholder="Please Enter Description here !">{{$product->description}}</textarea>

                        </form>
                    </div>
                </div>
                <div class="row mb2">
                    <div class="block-header">
                        <h3 class="block-title"> Images</h3>
                    </div>
                    <div class="col-md-12">
                        @if(count($product->has_images) >0)
                            <div class="row editable ">

                                @foreach($product->has_images()->orderBy('position')->get() as $image)
                                    <div class="col-md-4 mb2 preview-image animated fadeIn" >
                                        <div class="options-container fx-img-zoom-in fx-opt-slide-right">
                                            @if($product->import_from_shopify == 1)
                                                <img class="img-fluid options-item" src="{{$image->image}}" alt="">
                                            @else
                                                @if($image->isV == 0)
                                                    <img class="img-fluid options-item" src="{{asset('images')}}/{{$image->image}}" alt="">
                                                @else
                                                    <img class="img-fluid options-item" src="{{asset('images/variants')}}/{{$image->image}}" alt="">
                                                @endif
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
                                <div class="col-md-12" style="padding-bottom: 13px;width: 1046px">
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
                <div class="row mb2">
                    <div class="block-header">
                        <h3 class="block-title"> Variants</h3>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-vcenter table-hover table-striped table-borderless table-responsive">
                            <thead>
                            <tr>
                                <th style="vertical-align: top">Title</th>
                                <th style="vertical-align: top">Image</th>
                                <th style="vertical-align: top">Price</th>
                                <th style="vertical-align: top">Cost</th>
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
                                                                    <a class="img-avatar-variant btn btn-sm btn-primary text-white mb2" data-form="#varaint_image_form_{{$index}}">Upload New Picture</a>

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
                                    <tbody class="">
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
                                        <td><input type="text" readonly class="form-control" value="{{$product->quantity}}" name="quantity" placeholder="0"></td>
                                        <td><input type="text" readonly class="form-control" name="sku" value="{{$product->sku}}"></td>
                                        <td><input type="text" class="form-control" name="barcode" value="{{$product->barcode}}" placeholder="">
                                        </td>
                                    </tr>
                                    </tbody>
                                </form>
                            @endif
                        </table>
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
                <div class="row mb2">
                    <div class="col-md-12">
                        <button style="float: right;padding: 4px 40px;" class="btn btn-success btn_save_my_product"> Save </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
