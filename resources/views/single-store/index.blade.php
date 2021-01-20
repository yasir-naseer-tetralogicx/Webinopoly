@extends('layout.single')
@section('content')
    <div class="content content-narrow">
        <div class="row mb2">
            <div class="col-md-4">
                <h3 class="font-w700">Settings </h3>
            </div>
            <div class="col-md-8">
                @if($associated_user != null)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block">
                                <div class="block-header">
                                    <h3 class="block-title">Associated Account</h3>
                                </div>
                                <div class="block-content ">
                                    <div class="form-group">
                                        <label class="col-xs-12" for="register1-username">Username</label>
                                        <div class="col-xs-12">
                                            <input class="form-control" readonly value="{{$associated_user->name}}" type="text" id="register1-username" name="register1-username" placeholder="Enter your username..">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12" for="register1-email">Email</label>
                                        <div class="col-xs-12">
                                            <input class="form-control" readonly value="{{$associated_user->email}}" type="email" id="register1-email" name="register1-email" placeholder="Enter your email..">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="block">
                                <div class="block-header">
                                    <h3 class="block-title">List of stores attached</h3>
                                </div>
                                <div class="block-content ">
                                    <table class="js-table-sections table table-hover">
                                        <tbody>
                                        @foreach($associated_user->has_shops as $index => $shop)
                                            <tr>

                                                <td class="font-w600" style="vertical-align: middle">
                                                    {{explode('.',$shop->shopify_domain)[0]}}
                                                </td>
                                                <td style="vertical-align: middle">{{ $shop->shopify_domain }}</td>
{{--                                                <td class="text-right" style="vertical-align: middle">--}}
{{--                                                    <a data-href="{{route('store.user.de-associate',$shop->id)}}" class="de-associate-button btn btn-xs btn-danger"--}}
{{--                                                       type="button" data-toggle="tooltip" title=""--}}
{{--                                                       data-original-title="Remove Store"><i class="fa fa-times"></i></a>--}}

{{--                                                </td>--}}
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>




                            <div class="block">
                                <div class="block-header d-flex justify-content-between">
                                    <h3 class="block-title">Auto Wallet Order Payment Setting</h3>

                                    <div class="custom-control custom-switch custom-control-success mb-1">
                                        <input @if($user->has_wallet_setting && $user->has_wallet_setting->enable)checked="" @endif data-route="{{route('store.save.wallet.settings',$user->id)}}" data-csrf="{{csrf_token()}}" type="checkbox" class="custom-control-input wallet-switch" id="wallet_setting" name="example-sw-success2">
                                        <label class="custom-control-label status-text" for="wallet_setting">@if($user->has_wallet_setting && $user->has_wallet_setting->enable) Enabled @else Disabled @endif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="block">
                                <div class="block-header">
                                    <h3 class="block-title">Address Information</h3>
                                </div>
                                <div class="block-content">
                                    <form action="{{route('store.save_address')}}" method="post">
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
                @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block">
                                <div class="block-header">
                                    <h3 class="block-title">Associate with an account</h3>
                                </div>
                                <form id="authenticate_user_form" data-store="{{$shop->shopify_domain}}" data-token="{{csrf_token()}}" data-route="{{route('store.user.associate')}}" action="{{route('store.user.authenticate')}}" method="post">
                                    @csrf
                                    <div class="block-content ">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <label for="material-error">Email Address</label>
                                                    <input required class="form-control" type="email" id="user-email" name="email"
                                                           value=""   placeholder="Enter Registered Email Address">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="form-material">
                                                    <label for="material-error">Password</label>
                                                    <input required class="form-control" type="password" id="user-password" name="password"
                                                           value=""  placeholder="Enter Password">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="submit" hidden>
                                </form>
                                <div class="block-content block-content-full text-right border-top">
                                    <button type="submit" class="btn btn-sm authenticate_user btn-primary" >Authenticate</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
