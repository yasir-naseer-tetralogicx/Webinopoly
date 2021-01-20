@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    {{ $campaign->name }}
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Campaigns</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="block">
                    <form action="{{ route('email.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="block-content">
                            <div class="form-group">
                                <label for="">Subject</label>
                                <input type="text" name="subject" class="form-control" value="{{ $template->subject }}">
                            </div>

                            <div class="form-group">
                                <label for="">Body</label>
                                <textarea name="body" id="" cols="30" rows="10" class="form-control">{{ $template->body }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="">Campaign Name</label>
                                <input type="text" name="campaign_name" class="form-control" value="{{ $campaign->name }}">
                            </div>

                            <div class="form-group">
                                <label for="">Campaign Time</label>
                                <input type="text" required name="time" value="{{ $campaign->time }}" placeholder="{{ $campaign->time }}" class="js-flatpickr form-control bg-white" id="example-flatpickr-datetime-24" name="example-flatpickr-datetime-24" data-enable-time="true" data-time_24hr="true">
                            </div>

                            @if($template->banner !== null)
                                <div class="text-center">
                                    <img style="width: 50%; height: auto;" src="{{asset('ticket-attachments')}}/{{$template->banner}}" alt="">
                                </div>

                                <div class="form-group">
                                    <label for="">Banner</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input name="banner" type="file" class="custom-file-input" id="inputGroupFile04">
                                            <label class="custom-file-label" for="inputGroupFile04">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($template->products != null)
                                <div class="form-group">
                                    <label for="">Edit Products</label>
                                    @php $products = \App\Product::all(); @endphp
                                    <select class="@error('type') is-invalid @enderror js-select2 form-control" name="products[]" style="width: 100%; border-radius: 0 !important;" data-placeholder="Edit Products.." multiple>
                                        @foreach($products as $product)
                                            @php
                                                $prods = json_decode($template->products);
                                            @endphp
                                            <option value="{{ $product->id }}"
                                                    @if(in_array($product->id, $prods))
                                                    selected
                                                @endif
                                            >{{ $product->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="form-group text-right">
                                <button class="btn btn-success" type="submit">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
