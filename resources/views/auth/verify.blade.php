{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--<div class="container">--}}
{{--    <div class="row justify-content-center">--}}
{{--        <div class="col-md-8">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header">{{ __('Verify Your Email Address') }}</div>--}}

{{--                <div class="card-body">--}}
{{--                    @if (session('resent'))--}}
{{--                        <div class="alert alert-success" role="alert">--}}
{{--                            {{ __('A fresh verification link has been sent to your email address.') }}--}}
{{--                        </div>--}}
{{--                    @endif--}}

{{--                    {{ __('Before proceeding, please check your email for a verification link.') }}--}}
{{--                    {{ __('If you did not receive the email') }},--}}
{{--                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">--}}
{{--                        @csrf--}}
{{--                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--@endsection--}}

@extends('layout.credential')
@section('content')

    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="block block-themed  mb-0">
                <div class="block-header bg-success">
                    <h3 class="block-title">Verify Your Email Address</h3>
                    <div class="block-options">
                        <a class="btn-block-option" href="{{route('login')}}" data-toggle="tooltip" data-placement="left" title="Log In"> <i class="fa fa-backward"></i></a>
                    </div>

                </div>
                <div class="block-content block-content-full block-content-narrow">
                    <p>Before proceeding, please check your email for a verification link. If you did not receive the email</p>
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                <!-- Reminder Form -->
                    <!-- jQuery Validation (.js-validation-reminder class is initialized in js/pages/base_pages_reminder.js) -->
                    <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
                    <form method="POST" action="{{ route('verification.resend') }}" class=" form-horizontal push-30-t push-5">
                        @csrf
                        <div class="form-group">
                            <div class="col-xs-12 col-sm-12 col-md-12 px-0">
                                <button class="btn btn-block btn-success" type="submit">Click here to request another</button>
                            </div>
                        </div>
                    </form>
                    <!-- END Reminder Form -->
                </div>
            </div>
            <!-- END Reminder Block -->
        </div>
    </div>
@endsection

