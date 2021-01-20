@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                  {{$manager->email}}
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Sales Managers </li>
                        <li class="breadcrumb-item">Edit </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="block">
            <div class="block-content">
                <form id="create_manager_form" action="{{route('sales-managers.update',$manager->id)}}" method="post">
                    @csrf
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="form-material">
                                <label for="material-error">Name</label>
                                <input required class="form-control" type="text" id="manager_name" name="name"
                                       value="{{$manager->name}}"
                                       placeholder="Enter Sales Manager Title here">

                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="form-material">
                                <label for="material-error">Email</label>
                                <input readonly class="form-control" type="email" id="manager_email" name="email" value="{{$manager->email}}"
                                       placeholder="Enter Sales Manager Email here">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="form-material">
                                <input  class="form-control" data-route="{{route('sales-managers.edit.search')}}" data-manager="{{$manager->id}}" type="search" id="search-edit-input-stores-users" name="search"
                                       placeholder="Search by Keyword in Users/Stores">
                            </div>
                        </div>
                    </div>
                    <div class="drop-content">
                    <div @if(count($stores) > 5 || count($manager->has_sales_stores) >5) class="sales-stores-section"  @else class="mb2" @endif >
                        <label style="margin-left: 15px" for="material-error">Stores</label>
                        @if(count($stores) > 0 || count($manager->has_sales_stores) >0)
                            @foreach($manager->has_sales_stores as $store)
                                <div class="col-md-12">
                                    <div class="custom-control custom-checkbox d-inline-block">
                                        <input type="checkbox" name="stores[]" checked value="{{$store->id}}" class="custom-control-input " id="sales_store_{{$store->id}}">
                                        <label class="custom-control-label"  for="sales_store_{{$store->id}}">{{explode('.',$store->shopify_domain)[0]}} ({{$store->shopify_domain}})</label>
                                    </div>
                                </div>
                            @endforeach
                            @foreach($stores as $store)
                                <div class="col-md-12">
                                    <div class="custom-control custom-checkbox d-inline-block">
                                        <input type="checkbox" name="stores[]" value="{{$store->id}}" class="custom-control-input checkbox-to-check" id="store_{{$store->id}}">
                                        <label class="custom-control-label"  for="store_{{$store->id}}">{{explode('.',$store->shopify_domain)[0]}} ({{$store->shopify_domain}})</label>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-12">
                                <p> No Store Available</p>
                            </div>
                        @endif
                    </div>
                    <div    @if(count($users) > 5 || count($manager->has_users) >5) class="sales-stores-section" @else class="mb2" @endif>
                        <label style="margin-left: 15px" for="material-error">Non-Shopify Users</label>
                        @if(count($users) > 0 || count($manager->has_users) >0)

                        @foreach($manager->has_users as $user)
                                <div class="col-md-12">
                                    <div class="custom-control custom-checkbox d-inline-block">
                                        <input type="checkbox" name="users[]" checked value="{{$user->id}}" class="custom-control-input" id="selected_user_{{$user->id}}">
                                        <label class="custom-control-label"  for="selected_user_{{$user->id}}">{{$user->name}} ({{$user->email}})</label>
                                    </div>
                                </div>
                            @endforeach
                            @foreach($users as $user)
                                <div class="col-md-12">
                                    <div class="custom-control custom-checkbox d-inline-block">
                                        <input type="checkbox" name="users[]" value="{{$user->id}}" class="custom-control-input checkbox-to-check" id="user_{{$user->id}}">
                                        <label class="custom-control-label"  for="user_{{$user->id}}">{{$user->name}} ({{$user->email}})</label>
                                    </div>
                                </div>
                            @endforeach
                        @else  <div class="col-md-12">
                            <p> No User Available</p>
                        </div>
                        @endif
                    </div>
                    </div>

                    <div class="block-content block-content-full text-right border-top">

                        <button type="submit" class="btn btn-sm btn-primary" >Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
