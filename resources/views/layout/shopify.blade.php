<!DOCTYPE html>
<html>
@include('inc.header')
<body>
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed">
    @include('layout.shopify_sidebar')
    <main id="main-container">
        @include('flash_message.message')
        @if(auth()->check())
            @if(count(auth()->user()->has_shops) == 0)
                <div class="alert alert-info alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>You dont have connected any store yet. For store connection <a href="{{route('system.store.connect')}}">click here</a>.</strong>
                </div>
            @endif
        @endif
        @yield('content')
    </main>
@php
$countries = \App\Country::all();

@endphp

    <div class="modal fade" data-route="{{route('app.questionaire.check')}}" data-user="{{auth()->id()}}" id="questionnaire_modal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Some Basic Information We needed</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option">
                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('app.questionaire.post')}}" method="post">
                        @csrf
                        <input type="hidden" name="user_id" @if(auth()->check()) value="{{auth()->id()}}" @endif>
                        <div class="block-content font-size-sm">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error"> Gender</label>
                                        <select class="form-control " style="width: 100%;"   name="gender" required  >
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error"> Date of Birth</label>
                                        <input required class="form-control" type="date"  name="dob" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Are you new to business or you have your online Online store already?</label>
                                        <input required class="form-control" type="text"  name="new_to_business" value="" >
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error"> What is your target product ranges? </label>
                                        <select class="form-control js-select2" style="width: 100%;" data-placeholder="Choose multiple " name="product_ranges[]" required  multiple="">
                                            <option value="Electronics">Electronics</option>
                                            <option value="Home and Garden">Home and Garden </option>
                                            <option value="Kids and Toy">Kids and Toy</option>
                                            <option value="Health and Beauty">Health and Beauty</option>
                                            <option value="Sports and Outdoor">Sports and Outdoor</option>
                                            <option value="Fashions">Fashions</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Which of the countries you would like to sell to?</label>
                                        <select class="form-control js-select2" style="width: 100%;" data-placeholder="Choose multiple" name="countries[]" required  multiple="">
                                            <option></option>
                                            @foreach($countries as $country)
                                                <option value="{{$country->name}}">{{$country->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">What is your delivery time request for your orders to be delivered ?</label>
                                        <input required class="form-control" type="text"  name="delivery_time" value="" >
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">What is your most concern in our drop shipping service?</label>
                                        <select class="form-control js-select2" style="width: 100%;" data-placeholder="Choose multiple" name="concerns[]" required  multiple="">
                                            <option></option>
                                            <option value="Communication">Communication</option>
                                            <option value="Price">Price</option>
                                            <option value="Product Trends">Product Trends</option>
                                            <option value="Delivery Time">Delivery Time</option>
                                            <option value="Product Quality">Product Quality</option>
                                        </select>
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


    @include('inc.footer')
</div>

</body>
</html>
