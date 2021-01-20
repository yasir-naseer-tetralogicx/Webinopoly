@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Warehouses
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Warehouses</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row mb2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-sm btn-primary text-white" data-toggle="modal" data-target="#create_warehouse">Create Warehouse</button>
            </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content">
                        @if (count($warehouses) > 0)
                            <table class="js-table-sections table table-hover table-borderless table-vcenter">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Country</th>
                                    <th>Address</th>
                                    <th>State</th>
                                    <th>Zip Code</th>
                                </tr>
                                </thead>
                                <tbody class="">
                                @foreach($warehouses as $index => $warehouse)
                                    <tr>
                                        <td class="font-w600">{{ $warehouse->title }}</td>
                                        <td>
                                            <span class="badge badge-success">{{$warehouse->country->name}}</span>
                                        </td>
                                        <td class="font-w600">{{ $warehouse->address }}</td>
                                        <td class="font-w600">{{ $warehouse->state }}</td>
                                        <td class="font-w600">{{ $warehouse->zip }}</td>
                                        <td class="text-right btn-group" style="float: right">
                                            <button class="btn btn-sm btn-warning" type="button" data-toggle="modal"
                                                    data-target="#edit_warehouse_modal{{$index}}"><i
                                                    class="fa fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('warehouse.destroy', $warehouse->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                        data-original-title="Delete Warehouse"><i class="fa fa-times"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="edit_warehouse_modal{{$index}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-popout" role="document">
                                            <div class="modal-content">
                                                <div class="block block-themed block-transparent mb-0">
                                                    <div class="block-header bg-primary-dark">
                                                        <h3 class="block-title">Edit "{{$warehouse->title}}"</h3>
                                                        <div class="block-options">
                                                            <button type="button" class="btn-block-option">
                                                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <form action="{{route('warehouse.update',$warehouse->id)}}" method="post">
                                                        @csrf
                                                        <input type="hidden" value="{{$warehouse->id}}" name="courier_id">
                                                        <div class="block-content font-size-sm">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="material-error">Title*</label>
                                                                        <input required class="form-control  @error('title') is-invalid @enderror" type="text" id="zone_title" value="{{$warehouse->title}}" name="title" placeholder="Enter Warehouse title..">
                                                                        @error('title')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="material-error">Address*</label>
                                                                        <input required class="form-control  @error('address') is-invalid @enderror" type="text" id="zone_title" value="{{$warehouse->address}}"  name="address" placeholder="Enter Warehouse address..">
                                                                        @error('address')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="material-error">Country*</label>
                                                                        <select name="country_id" id="" class="form-control">
                                                                           @foreach($countries as $country)
                                                                                <option value="{{ $country->id  }}"
                                                                                @if($country->id == $warehouse->country_id) selected @endif>{{ $country->name }}</option>
                                                                           @endforeach
                                                                        </select>
                                                                        @error('title')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="material-error">State</label>
                                                                        <input required class="form-control  @error('state') is-invalid @enderror" type="text" id="zone_title" value="{{$warehouse->state}}"   name="state" placeholder="Enter Warehouse State..">
                                                                        @error('state')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="material-error">Zip*</label>
                                                                        <input required class="form-control  @error('zip') is-invalid @enderror" type="text" id="zone_title" value="{{$warehouse->zip}}"   name="zip" placeholder="Enter courier service provider title..">
                                                                        @error('zip')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
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
                                @endforeach
                            </table>
                        @else
                            <p>No Warehouse Added</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create_warehouse" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Create Warehouse</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option">
                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('warehouse.store')}}" method="post">
                        @csrf
                        <div class="block-content font-size-sm">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="material-error">Title*</label>
                                        <input required class="form-control  @error('title') is-invalid @enderror" type="text" id="zone_title"  name="title" placeholder="Enter Warehouse title..">
                                        @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="material-error">Address*</label>
                                        <input required class="form-control  @error('address') is-invalid @enderror" type="text" id="zone_title"   name="address" placeholder="Enter Warehouse address..">
                                        @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="material-error">Country*</label>
                                        <select name="country_id" id="" class="form-control">
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id  }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="material-error">State</label>
                                        <input required class="form-control  @error('state') is-invalid @enderror" type="text" id="zone_title"   name="state" placeholder="Enter Warehouse State..">
                                        @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="material-error">Zip*</label>
                                        <input required class="form-control  @error('zip') is-invalid @enderror" type="text" id="zone_title"    name="zip" placeholder="Enter courier service provider title..">
                                        @error('zip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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

@endsection
