@extends('layout.shopify')
@section('content')
    <div class="content content-narrow">
        <div class="row mb2">
            <div class="col-md-4">
                <h3 class="font-w700">Settings </h3>
            </div>
            <div class="col-md-8">

                <div class="row">
                    <div class="col-md-12">
                        <div class="block">
                            <div class="block-header">
                                <h3 class="block-title">List of Shopify stores <a href="{{route('system.store.connect')}}" class="btn btn-success btn-sm" style="float: right;margin-left: 10px"> Add Store</a> <a href="{{route('users.stores')}}" class="btn btn-primary btn-sm" style="float: right"> Manage Stores</a></h3>
                            </div>

                            <div class="block-content ">
                                <table class="js-table-sections table table-hover">
                                    <tbody>
                                    <form method="POST" action="{{ route('authenticate') }}" class="shop-login-form">
                                        @csrf
                                        @foreach($associated_user->has_shops as $index => $shop)
                                        <tr>
                                            <td style="vertical-align: middle">{{ $shop->shopify_domain }}</td>
                                            <td class="text-right" style="vertical-align: middle">
                                                <button type="button" class="btn btn-sm btn-success settings-shop-log-btn" >
                                                    <input type="hidden" class="shop-domain-name" value="{{$shop->shopify_domain}}">
                                                    <input type="hidden" name="shop" value="" class="shop-domain-input">
                                                    Switch View
                                                </button>
{{--                                                <a href="{{url('/shop/install?shop='.$shop->shopify_domain)}}" class="">Switch View</a>--}}

                                                <a data-href="{{route('store.user.de-associate',$shop->id)}}" class="de-associate-button btn btn-xs btn-danger text-white"
                                                    title="Remove Store" ><i class="fa fa-trash"></i></a>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </form>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="block">
                            <div class="block-header">
                                <h3 class="block-title">Account Details</h3>
                            </div>
                            <div class="block-content">
                                <form action="{{route('users.save_personal_info')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{$associated_user->id}}">
                                    <div class="image-profile text-center mb2">
                                        <img class="image-drop img-avatar200"
                                             @if($associated_user->profile == null) src="{{asset('assets/media/avatars/avatar0.jpg')}}" @else
                                             src="{{asset('managers-profiles')}}/{{$associated_user->profile}}"
                                             @endif
                                             alt="">
                                    </div>
                                    <div class="image-profile text-center mb2">
                                        <a  class="btn btn-primary text-white upload-manager-profile" style="margin: 10px">Upload Profile</a>
                                        <input type="file" name="profile" class="manager-profile form-control" style="display: none">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <input type="text" name="email"  class="form-control  @error('email') is-invalid @enderror" value="{{$associated_user->email}}">
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="">Username</label>
                                        <input type="text" required name="name" class="form-control" value="{{$associated_user->name}}">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-primary" value="Save">
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="block">
                            <div class="block-header">
                                <h3 class="block-title">Change Password</h3>
                            </div>
                            <div class="block-content">
                                <form id="change_password_manager_form" action="{{route('users.change.password')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{$associated_user->id}}">
                                    <div class="form-group">
                                        <label for="">Current Password</label>
                                        <input type="password" required name="current_password" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">New Password</label>
                                        <input type="password" required name="new_password" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="">New Password (Again)</label>
                                        <input type="password" required name="new_password_again" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-primary" value="Change">
                                    </div>
                                </form>

                            </div>
                        </div>

                        <div class="block">
                            <div class="block-header">
                                <h3 class="block-title">Address Information</h3>
                            </div>
                            <div class="block-content">
                                <form action="{{route('users.save_address')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{$associated_user->id}}">
                                    <div class="form-group">
                                        <label for="">Street Address</label>
                                        <input type="text"  name="address" class="form-control" value="{{$associated_user->address}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="">Address 2</label>
                                        <input type="text"  name="address2" class="form-control" value="{{$associated_user->address2}}">
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="">City</label>
                                            <input type="text"  name="city" class="form-control" value="{{$associated_user->city}}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="">State</label>
                                            <input type="text"  name="state" class="form-control" value="{{$associated_user->state}}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="">Zip</label>
                                            <input type="text"  name="zip" class="form-control" value="{{$associated_user->zip}}">
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label for="">Country</label>
                                        <select name="country" class="form-control">
                                            @foreach($countries as $country)
                                                <option @if($associated_user->country == $country->name) selected @endif value="{{$country->name}}">{{$country->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-primary" value="Save">
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
