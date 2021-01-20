@extends('layout.manager')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Settings
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx active" href="">Settings</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="block">
                    <div class="block-header">
                        <h3 class="block-title">Personal Information</h3>
                    </div>
                    <div class="block-content">
                        <form action="{{route('sales_managers.save_personal_info')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="manager_id" value="{{$manager->id}}">
                            <div class="image-profile text-center mb2">
                                <img class="image-drop img-avatar200"
                                     @if($manager->profile == null) src="{{asset('assets/media/avatars/avatar0.jpg')}}" @else
                                     src="{{asset('managers-profiles')}}/{{$manager->profile}}"
                                     @endif
                                     alt="">
                            </div>
                            <div class="image-profile text-center mb2">
                                <a  class="btn btn-primary text-white upload-manager-profile" style="margin: 10px">Upload Profile</a>
                                <input type="file" name="profile" class="manager-profile form-control" style="display: none">
                            </div>
                            <div class="form-group">
                                <label for="">Email</label>
                                <input disabled type="text"  class="form-control" value="{{$manager->email}}">
                            </div>
                            <div class="form-group">
                                <label for="">Username</label>
                                <input type="text" required name="name" class="form-control" value="{{$manager->name}}">
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
                        <form id="change_password_manager_form" action="{{route('sales_managers.change_password')}}" method="post">
                            @csrf
                            <input type="hidden" name="manager_id" value="{{$manager->id}}">
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
            </div>
            <div class="col-md-6">
                <div class="block">
                    <div class="block-header">
                        <h3 class="block-title">Address Information</h3>
                    </div>
                    <div class="block-content">
                        <form action="{{route('sales_managers.save_address')}}" method="post">
                            @csrf
                            <input type="hidden" name="manager_id" value="{{$manager->id}}">
                            <div class="form-group">
                                <label for="">Street Address</label>
                                <input type="text"  name="address" class="form-control" value="{{$manager->address}}">
                            </div>
                            <div class="form-group">
                                <label for="">Address 2</label>
                                <input type="text"  name="address2" class="form-control" value="{{$manager->address2}}">
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="">City</label>
                                    <input type="text"  name="city" class="form-control" value="{{$manager->city}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="">State</label>
                                    <input type="text"  name="state" class="form-control" value="{{$manager->state}}">
                                </div>
                                <div class="col-md-4">
                                    <label for="">Zip</label>
                                    <input type="text"  name="zip" class="form-control" value="{{$manager->zip}}">
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="">Country</label>
                                <select name="country" class="form-control">
                                    @foreach($countries as $country)
                                        <option @if($manager->country == $country->name) selected @endif value="{{$country->name}}">{{$country->name}}</option>
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

@endsection
