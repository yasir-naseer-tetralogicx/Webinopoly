@extends('layout.index')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Users
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx active" href="">Users</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <form class="js-form-icon-search push" action="" method="get">
            <div class="form-group">
                <div class="input-group">
                    <input type="search" class="form-control" placeholder="Search by name" value="{{$user_search}}" name="user_search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                        <a class="btn btn-danger" href="{{route('stores.index')}}"> <i class="fa fa-times"></i> Clear </a>
                    </div>
                </div>
            </div>
        </form>
        <div class="col-md-12 px-0">
            <div class="bg-white p-3 push">
                <!-- Navigation -->
                <div id="horizontal-navigation-hover-normal" class="d-none d-lg-block mt-2 mt-lg-0">
                    <ul class="nav-main nav-main-horizontal nav-main-hover">
                        <li class="nav-main-item">
                            <a class="nav-main-link @if($status == 'shopify') active @endif " href="?status=shopify">
                                <i class="nav-main-link-icon si si-home"></i>
                                <span class="nav-main-link-name">Shopify Users</span>
                            </a>
                        </li>
                        <li class="nav-main-item">
                            <a class="nav-main-link @if($status == 'non_shopify') active @endif " href="?status=non_shopify">
                                <i class="nav-main-link-icon si si-users"></i>
                                <span class="nav-main-link-name">Non-Shopify Users</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- END Navigation -->
            </div>
        </div>
        <div class="block">
            <div class="block-content">
                @if (count($users) > 0)
                    <table class="table table-hover table-borderless table-striped table-vcenter">
                        <thead>
                        <tr>

                            <th>Title</th>
                            <th>Email</th>
                            <th>Stores</th>
                            <th>Manager</th>
                            <th>Products</th>
                            <th>Orders</th>
                            <th>Outcome</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="">
                        @foreach($users as $index => $user)
                            <tr>
                                <td class="font-w600"><a href="{{route('users.view',$user->id)}}">{{$user->name}}</a></td>
                                <td>
                                    <span class="badge badge-primary">{{$user->email}}</span>
                                </td>
                                <td>
                                    @if($user->has_shops()->count() > 0)
                                        <span class="badge badge-success">Shopify User</span>
                                    @else
                                        <span class="badge badge-info">Non-Shopify User</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->has_manager != null)
                                        <a href="{{route('sales-managers.view',$user->has_manager->id)}}">
                                        <img class="img-avatar-rounded" @if($user->has_manager->profile == null) src="{{ asset('assets/media/avatars/avatar10.jpg') }}" @else  src="{{asset('managers-profiles')}}/{{$user->has_manager->profile}}" @endif alt="">
                                        <span style="margin: auto 0px auto 5px;">{{$user->has_manager->name}}</span>
                                        </a>
                                    @else
                                        Manager Deleted
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $products_count = 0;
                                        if($user->has_stores()->count() > 0) {
                                          foreach($user->has_stores()->get() as $store){
                                                if($store->has_imported !== null) {
                                                    $products_count += $store->has_imported()->count();
                                                }
                                          }
                                        }

                                    @endphp
                                    {{$products_count}}
                                </td>
                                <td>
                                    {{count($user->has_orders)}}
                                </td>
                                <td>
                                    @php
                                        $sum =0;
                                         if($user->has_orders()->where('paid', 1)->count() > 0) {
                                             foreach($user->has_orders()->where('paid', 1)->get() as $order){
                                                 $sum += $order->line_items()->sum('cost');
                                             }
                                         }
                                    @endphp
                                    $ {{number_format($sum, 2)}}
                                </td>

                                <td class="text-right">
                                    <div class="btn-group mr-2 mb-2">
                                        <a class="btn btn-primary btn-xs btn-sm text-white" data-toggle="modal" data-target="#assign_manager_{{$user->id}}" type="button" title="Assign Sales Manager">  <i class="fa fa-user"></i></a>
                                        <a class="btn btn-xs btn-sm btn-success" type="button" href="{{route('users.view',$user->id)}}" title="View User">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade" id="assign_manager_{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-popout" role="document">
                                    <div class="modal-content">
                                        <div class="block block-themed block-transparent mb-0">
                                            <div class="block-header bg-primary-dark">
                                                <h3 class="block-title">Manage Sales Manager</h3>
                                                <div class="block-options">
                                                    <button type="button" class="btn-block-option">
                                                        <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <form action="{{route('assign_manager',$user->id)}}" method="post">
                                                @csrf
                                                <input type="hidden" name="type" value="user">
                                                <div class="block-content font-size-sm">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label for=""> Managers</label>
                                                            <select required name="sale_manager_id" class="form-control">
                                                                @foreach($managers as $manager)
                                                                    <option  @if($user->has_manager != null) @if($user->has_manager->id == $manager->if) selected @endif @endif value="{{$manager->id}}"> {{$manager->name}} {{$manager->last_name}} ({{$manager->email}}) </option>
                                                                @endforeach
                                                            </select>
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

                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-center"> No User Available</p>
                @endif
                    <div class="row">
                        <div class="col-md-12 text-center" style="font-size: 17px">
                            {!! $users->appends(request()->input())->links() !!}
                        </div>
                    </div>
            </div>
        </div>
    </div>
    @endsection
