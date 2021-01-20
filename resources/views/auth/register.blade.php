@extends('layout.credential')
@section('content')
    <style>
        .form-group{
            margin-bottom: 0.6rem !important;
        }
        .block-content {
            padding: 1px 1.25rem 1px;
        }
    </style>
    <div class="block">
        <ul class="nav nav-tabs nav-tabs-block nav-justified" data-toggle="tabs" role="tablist">
            <li class="nav-item text-center" >
                <a class="nav-link active" style="font-size: 16px !important;">Register</a>
            </li>
{{--            <li class="nav-item">--}}
{{--                <a class="nav-link active" href="#by_email">By Account</a>--}}
{{--            </li>--}}
{{--            <li class="nav-item">--}}
{{--                <a class="nav-link" href="#by_store">By Store</a>--}}
{{--            </li>--}}
        </ul>
        <div class="block-content tab-content">
            <div class="tab-pane active" id="by_email" role="tabpanel">
                <div class="py-3">
                <form class="" action="{{ route('register') }}" method="POST">
                    @csrf
                        <div class="form-group">
                            <label>Name</label>
                            <input name="name" type="text" required value="{{ old('name') }}" class="form-control form-control-alt form-control-lg">
                            @error('name')
                            <span class="invalid-feedback" role="alert" style="color: red">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input name="email" type="email" required value="{{ old('email') }}" class="form-control form-control-alt form-control-lg">
                            @error('email')
                            <span class="invalid-feedback" role="alert" style="color: red">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control form-control-alt form-control-lg" name="password">

                            @error('password')
                            <span class="invalid-feedback" role="alert" style="color: red">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control form-control-alt form-control-lg" name="password_confirmation">
                            @error('password')
                            <span class="invalid-feedback" role="alert" style="color: red">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>


                    <div class="form-group row">
                        <div class="col-md-7 col-xl-7 submit-column">
                            <button type="submit" class="btn btn-block btn-success">
                                <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Create Account
                            </button>
                        </div>
                    </div>
                </form>

                <a class="btn-block-option font-size-sm" href="{{ route('password.request') }}">Forgot Password?</a>
                <a class="btn-block-option" href="{{route('login')}}" data-toggle="tooltip" data-placement="right" title="Login">
                    <i class="fa fa-users"></i>
                </a>
                </div>
            </div>
            <div class="tab-pane" id="by_store" role="tabpanel">
                <div class="py-3">
                    <form method="POST" action="{{ route('authenticate') }}">
                        @csrf
                        <div class="form-group">
                            <label for="shop">Domain</label>
                            <input id="shop" name="shop" class="form-control form-control-alt form-control-lg"
                                   type="text" placeholder="example.myshopify.com">
                        </div>

                        <div class="form-group row">
                            <div class="col-md-7 col-xl-7 submit-column">
                                <button type="submit" class="btn btn-block btn-success">
                                    <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Create Account
                                </button>
                            </div>
                        </div>
                    </form>
                    <a class="btn-block-option font-size-sm" href="{{ route('password.request') }}">Forgot Password?</a>
                    <a class="btn-block-option" href="{{route('login')}}" data-toggle="tooltip" data-placement="right" title="Login">
                        <i class="fa fa-users"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
