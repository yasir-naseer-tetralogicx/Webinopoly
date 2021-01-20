@extends('layout.single')
@section('content')
<style>
    .marketing_video iframe{
        width: 100%;
    }
    .push-20 a{
        margin: 3px;
    }
</style>

<div class="bg-body-light">
    <div class="content content-full pt-2 pb-2">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
            <h1 class="flex-sm-fill h4 my-2">
                WeFullFill Products
            </h1>
            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item">Dashboard</li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a class="link-fx" href="">WeFullFill Products</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content">
    <form class="js-form-icon-search push" action="{{route('store.product.wefulfill')}}" method="get">
        <div class="form-group">
            <div class="input-group">
                <input type="search" class="form-control" placeholder="Search by title, tags keyword" value="{{$search}}" name="search">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                    <a class="btn btn-danger" href="{{route('store.product.wefulfill')}}"> <i class="fa fa-times"></i> Clear </a>
                </div>
            </div>
        </div>
    </form>

    <div class="row mb-2" style="padding: 0 14px;">
        @foreach($categories as $index => $category)
            @if($index < 11)
                <div class="col-md-3 p-0">
                    <a href="{{route('store.product.wefulfill')}}?category={{$category->title}}">
                        <div class="block pointer m-0">
                            <div class="block-content p-3 text-center">
                                <p class="m-0" style="font-size:14px;font-weight: 600;"> @if($category->icon != null) <img class="img-avatar" src="{{asset('categories-icons')}}/{{$category->icon}}" alt=""> @endif {{$category->title}}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if($index == 11)
                <div class="col-md-3 p-0 see-more-block">
                    <div class="block pointer  m-0">
                        <div class="block-content p-3 text-center">
                            <p  class="m-0" style="font-size:14px;">See More ....</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($index >= 11)
                <div class="col-md-3 p-0 after12" style="display: none">
                    <a href="{{route('store.product.wefulfill')}}?category={{$category->title}}">
                        <div class="block pointer m-0">
                            <div class="block-content p-3 text-center">
                                <p class="m-0" style="font-size:14px;font-weight: 600;"> @if($category->icon != null) <img class="img-avatar" src="{{asset('categories-icons')}}/{{$category->icon}}" alt=""> @endif {{$category->title}}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if($index == count($categories)-1)
                <div class="col-md-3 p-0 after12 see-less-block" style="display: none">
                    <div class="block pointer m-0">
                        <div class="block-content p-3 text-center">
                            <p class="m-0" style="font-size:14px;">See Less ....</p>
                        </div>
                    </div>
                </div>
            @endif


        @endforeach
    </div>
    <div class="row mb-2" style="padding: 0 14px;">
        <div class="col-md-4 p-0 " >
            <a href="{{route('store.product.wefulfill')}}?tag=winning-products">
                <div class="block pointer m-0" style="background-color:#edfb79;">


                    <div class="block-content p-3 text-center">
                        <p class="m-0" style="font-size:14px;font-weight: 600;"><img class="img-avatar" src="{{asset('winning.png')}}" alt=""> Winning Products </p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 p-0">
            <a href="{{route('store.product.wefulfill')}}?tag=24-hours-dispatch">
                <div class="block pointer m-0" style="background-color:#94a5ff;">

                    <div class="block-content p-3 text-center">
                        <p class="m-0" style="font-size:14px;font-weight: 600;"> <img class="img-avatar" src="https://image.flaticon.com/icons/svg/46/46016.svg" alt="" style="margin-right: 10px"> 24 Hours Dispatch</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 p-0 ">
            <a href="{{route('store.product.wefulfill')}}?tag=best-seller">
                <div class="block pointer m-0" style="background-color:#83ff83;">


                    <div class="block-content p-3 text-center">
                        <p class="m-0" style="font-size:14px;font-weight: 600;"> <img class="img-avatar" src="{{asset('best.png')}}" alt=""> Best Sellers</p>
                    </div>
                </div>
            </a>
        </div>



    </div>
    <div class="row mb-2">
        <div class="col-md-6 text-right">
        </div>
        <div class="col-md-6 text-right">
            <form action="" method="get">
                <div class="d-flex">
                    <select name="filter" class="form-control" required>
                        <option value="">Filter By</option>
                        <option @if($filter == 'most-order') selected @endif value="most-order">Most Order</option>
                        <option @if($filter == 'most-imported') selected @endif value="most-imported">Most Imported</option>
                        <option @if($filter == 'new-arrival') selected @endif value="new-arrival">New Arrival</option>
                    </select>
                    <input type="submit" style="margin-left: 10px" class="btn btn-sm btn-primary" value="Filter">
                </div>
            </form>


        </div>
    </div>
    <div class="row" style="margin-top: 20px">
        @if(count($products) > 0)
            @foreach($products as $index => $product)
                <div class="col-sm-4 col-lg-3">
                    <div class="block">
                        <div class="options-container">
                            <a href="{{route('store.product.wefulfill.show',$product->id)}}">
                                @if(count($product->has_images) > 0)
                                    @foreach($product->has_images()->orderBy('position')->cursor() as $index => $image)
                                        @if($index == 0)
                                            @if($image->isV == 0)
                                                <img class="img-fluid options-item" src="{{asset('images')}}/{{$image->image}}">
                                            @else   <img class="img-fluid options-item" src="{{asset('images/variants')}}/{{$image->image}}" alt="">
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <img class="img-fluid options-item" src="https://wfpl.org/wp-content/plugins/lightbox/images/No-image-found.jpg">
                                @endif

                            </a>
                            <div class="options-overlay bg-black-75">
                                <div class="options-overlay-content">
                                    <div class="push-20">

                                        <a class="btn btn-sm btn-primary" href="{{route('store.product.wefulfill.show',$product->id)}}">View</a>
                                        @if($product->marketing_video != null)
                                            <a class="btn btn-sm btn-success text-white" data-toggle="modal" data-target="#video_modal{{$product->id}}"> View Video</a>
                                        @endif

                                        @if(!in_array($product->id,$shop->has_imported->pluck('id')->toArray()))
                                            <a class="btn btn-sm btn-success" href="{{route('store.product.wefulfill.add-to-import-list',$product->id)}}">
                                                <i class="fa fa-plus"></i> Add to Import List
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="block-content" style="padding-bottom: 10px">
                            <div class="push-10">
                                <a class="h6" style="font-size: 0.9rem" href="{{route('store.product.wefulfill.show',$product->id)}}">{{$product->title}}</a>
                                <div class="d-flex">
                                    <div class="font-w600 text-success mt-1">
                                        From.
                                        @if(count($product->hasVariants) > 0)
                                            ${{ number_format($product->hasVariants->min('price'), 2) }}
                                        @else
                                            ${{ number_format($product->price, 2) }}
                                        @endif
                                    </div>
                                    <div class="font-400 text-primary mt-1 push-10-l" style="margin-left: auto">{{$product->new_shipping_price}}</div>
                                </div>

                            </div>

                            @if($product->processing_time != null)
                                <hr>
                                <p class="text-muted font-size-sm">  Dispatch Within {{$product->processing_time}} </p>

                            @endif
                            <hr>
                            @if(!in_array($product->id,$shop->has_imported->pluck('id')->toArray()))
                                <button onclick="window.location.href='{{ route('store.product.wefulfill.add-to-import-list',$product->id)}}'" class="btn btn-primary btn-block mb2"><i class="fa fa-plus"></i> Add to Import List</button>
                            @else
                                <button disabled class="btn btn-success btn-block mb2"><i class="fa fa-check-circle-o"></i> Added to Import List</button>
                            @endif
                            @if($product->isUpdated($shop))
                                <button onclick="window.location.href='{{ route('store.product.wefulfill.updated-product',$product->id)}}'" class="btn btn-danger btn-block mb2">View Updated Variants</button>
                            @endif
                            <span class="mb2 font-size-sm" style="color: grey">Fulfilled By WeFullFill</span>
                        </div>
                    </div>
                </div>
                @if($product->marketing_video != null)
                    <div class="modal fade" id="video_modal{{$product->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-popout" role="document">
                            <div class="modal-content">
                                <div class="block block-themed block-transparent mb-0">
                                    <div class="block-header bg-primary-dark">
                                        <h3 class="block-title">{{ucfirst($product->title)}} Video</h3>
                                        <div class="block-options">
                                            <button type="button" class="btn-block-option">
                                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="block-content font-size-sm marketing_video">
                                        {!! $product->marketing_video !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content ">
                        <p class="text-center"> No Product Found !</p>
                    </div>
                </div>
            </div>

        @endif

    </div>
    <div class="row">
        <div class="col-md-12 text-center" style="font-size: 17px">
            {!! $products->links() !!}
        </div>
    </div>
</div>


@endsection
