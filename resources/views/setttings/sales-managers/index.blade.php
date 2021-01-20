@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Sales Managers
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Sales Managers</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div  class="form-horizontal push-30">
        <div class="content">
            <div class="row mb2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6 text-right">
                    <a class="btn btn-sm btn-primary text-white" href="{{route('sales-managers.create.form')}}">Add New Manager</a>
                </div>
            </div>
            <form class="js-form-icon-search push" action="" method="get">
                <div class="form-group">
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="Search by name" value="{{$search}}" name="search">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                            <a class="btn btn-danger" href="{{route('sales-managers.index')}}"> <i class="fa fa-times"></i> Clear </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row" style="margin-top: 10px">
                <div class="col-md-12">
                    <div class="block">
                        <div class="block-content">
                            @if (count($sales_managers) > 0)
                                <table class="table table-hover table-borderless table-striped table-vcenter">
                                    <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Stores</th>
                                        <th>Non-Shopify Users</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach($sales_managers as $index => $manager)
                                        <tr>
                                            <td class="font-w600 d-flex">
                                                <img class="img-avatar" @if($manager->profile == null) src="{{ asset('assets/media/avatars/avatar10.jpg') }}" @else  src="{{asset('managers-profiles')}}/{{$manager->profile}}" @endif alt="">
                                                <span style="margin-left: 10px;">{{ $manager->name }}</span></td>
                                            <td>
                                                {{$manager->email}}
                                            </td>

                                            <td>
                                                <span class="label label-success">Active</span>
                                            </td>
                                            <td>
                                                @if(count($manager->has_sales_stores) > 0)
                                                    @foreach($manager->has_sales_stores as $shop)
                                                        <span class="badge badge-primary">{{explode('.',$shop->shopify_domain)[0]}}</span>
                                                    @endforeach
                                                @else
                                                    <span class="badge badge-warning">NONE</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(count($manager->has_users) > 0)
                                                    @foreach($manager->has_users as $user)
                                                        <span class="badge badge-primary">{{$user->email}}</span>
                                                    @endforeach
                                                @else
                                                    <span class="badge badge-warning">NONE</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="btn-group mr-2 mb-2">
                                                    <a class="btn btn-xs btn-sm btn-success" type="button" href="{{route('sales-managers.view',$manager->id)}}" title="View Manager">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('sales-managers.edit.form', $manager->id) }}" class="btn btn-sm btn-warning"
                                                       type="button" data-toggle="tooltip" title=""
                                                       data-original-title="Edit Manager"><i
                                                            class="fa fa-edit"></i></a>
                                                    <a href="{{ route('sales-managers.delete', $manager->id) }}"
                                                       class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="Delete Manager"><i class="fa fa-times"></i></a>
                                                </div>

                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No Sales Manager Found</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
