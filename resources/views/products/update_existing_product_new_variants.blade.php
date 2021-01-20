@extends('layout.index')
@section('content')


    <input type="number"  name="cost" value="{{$product->cost}}" style="display: none">
    <input type="number" name="price" value="{{$product->price}}" style="display: none">
    <input type="text"  name="sku" value="{{$product->sku}}" style="display: none">
    <input type="number"  name="quantity" value="{{$product->quantity}}" style="display: none">

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Update Variant
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item">Update Variant</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="form-horizontal push-30">
        <form action="{{route('product.update.old.variants',$product->id)}}" method="post">
            @csrf
            <input type="hidden" name="type" value="existing-product-update-variants">
            <div class="content">
                <div class="row mb2">
                    <div class="col-sm-6">
                    </div>
                    <div class="col-sm-6 text-right">
                        <div class="content submit-div pr-0">
                            <div class="row ">
                                <div class="col-sm-12 text-right">
                                    <a href="{{ route('product.edit',$product->id) }}" class="btn btn-light btn-square ">Back to Editing</a>
                                    <button class="btn btn-primary btn-square">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="block">
                            <div class="block-header">
                                <h3 class="block-title">Variant</h3>
                            </div>
                            <div class="block-content">
                                <div class="variant_options" style="">
                                    <hr>
                                    <h3 class="font-w300">Options</h3>
                                    <br>
                                    <div class="form-group">
                                        @if(count($product->option1($product))>0)
                                            <div class="col-xs-12 push-10">
                                                <h5>Option 1</h5>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control" placeholder="Attribute Name" name="attribute1" value="{{ $product->attribute1 }}">
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="js-tags-options options-preview form-control" type="text"
                                                               id="product-meta-keywords" name="option1" value="{{ implode(',', $product->option1($product)) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if(count($product->option2($product))>0)
                                            <div class="col-xs-12 push-10">
                                                <h5>Option 2</h5>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control" placeholder="Attribute Name" name="attribute2" value="{{ $product->attribute2 }}">
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="js-tags-options options-preview form-control" type="text"
                                                               id="product-meta-keywords" name="option2" value="{{ implode(',', $product->option2($product)) }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-light btn-square option_btn_2 mt-2">Add another option</button>
                                        @else
                                            <button type="button" class="btn btn-light btn-square option_btn_1 mt-2">Add another option</button>
                                        @endif

                                        @if(count($product->option3($product))>0)
                                            <div class="col-xs-12 push-10">
                                                <h5>Option 3</h5>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control" placeholder="Attribute Name" name="attribute3" value="{{ $product->attribute3 }}">
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="js-tags-options options-preview form-control" type="text"
                                                               id="product-meta-keywords" name="option3" value="{{ implode(',', $product->option3($product)) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="option_2" style="display: none;">
                                        <hr>
                                        <div class="form-group">
                                            <div class="col-xs-12 push-10">
                                                <h5>Option 2</h5>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control" name="attribute2" value="{{ $product->attribute2 }}">
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="js-tags-options options-preview form-control" type="text"
                                                               id="product-meta-keywords" name="option2">
                                                    </div>
                                                </div>
                                                <button type="button"
                                                        class="btn btn-light btn-square option_btn_2 mt-2">Add another
                                                    option
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="option_3" style="display: none;">
                                        <hr>
                                        <div class="form-group">
                                            <div class="col-xs-12 push-10">
                                                <h5>Option 3</h5>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <input type="text" class="form-control" name="attribute3" value="{{ $product->attribute3 }}">
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <input class="js-tags-options options-preview form-control" type="text"
                                                               id="product-meta-keywords" name="option3">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="variants_table" style="display: none;">
                                        <hr>
                                        <h3 class="block-title">Preview</h3>
                                        <br>
                                        <div class="form-group">
                                            <div class="col-xs-12 push-10">
                                                <table class="table table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 20%;">Title</th>
                                                        <th style="width: 15%;">Price</th>
                                                        <th style="width: 17%;">Cost</th>
                                                        <th style="width: 10%;">Quantity</th>
                                                        <th style="width: 20%;">SKU</th>
                                                        <th style="width: 20%;">Barcode</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('input[type="checkbox"][name="variants"]').click(function () {
                $('.submit-div').toggle();
                if ($(this).prop("checked") == true) {
                    $('.variant_options').show();
                } else if ($(this).prop("checked") == false) {
                    $('.variant_options').hide();
                }
            });
            $('.option_btn_1').click(function () {
                if($(this).prev().find('.options-preview').val() !== ''){
                    $('.option_2').show();
                    $('.option_btn_1').hide();
                }
                else{
                    alertify.error('The Option1 must have atleast one option value');
                }

            });
            $('.option_btn_2').click(function () {
                if($(this).prev().find('.options-preview').val() !== ''){
                    $('.option_3').show();
                    $('.option_btn_2').hide();
                }
                else{
                    alertify.error('The Option2 must have atleast one option value');
                }
            });
        });
    </script>
@endsection
