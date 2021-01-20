@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Courier Service Providers
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Courier Service Providers</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row mb2">
            <div class="col-sm-6">
            </div>
{{--            <div class="col-sm-6 text-right">--}}
{{--                <button class="btn btn-sm btn-primary text-white" data-toggle="modal" data-target="#create_courier">Create Courier Service Providers</button>--}}
{{--            </div>--}}
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content">
                        @if (count($couriers) > 0)
                            <table class="js-table-sections table table-hover table-borderless table-vcenter">
                                <thead>
                                <tr>
                                    <th >Title</th>
                                    <th >URL</th>
                                    <th style="width: 25%;">Country</th>
                                </tr>
                                </thead>
                                <tbody class="">
                                    @foreach($couriers as $index => $courier)
                                    <tr>
                                        <td class="font-w600">{{ $courier->title }}</td>
                                        <td>
                                            <span class="badge badge-primary">{{$courier->url}}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{$courier->zone->name}}</span>
                                        </td>
{{--                                        <td class="text-right btn-group" style="float: right">--}}
{{--                                            <button class="btn btn-sm btn-warning" type="button" data-toggle="modal"--}}
{{--                                                    data-target="#edit_courier_modal{{$index}}"><i--}}
{{--                                                    class="fa fa-edit"></i>--}}
{{--                                            </button>--}}
{{--                                            <form method="POST" action="{{ route('couriers.destroy', $courier->id) }}">--}}
{{--                                                @csrf--}}
{{--                                                @method('DELETE')--}}
{{--                                                <button type="submit" class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""--}}
{{--                                                        data-original-title="Delete Courier"><i class="fa fa-times"></i></button>--}}
{{--                                            </form>--}}
{{--                                        </td>--}}
                                    </tr>
                                    <div class="modal fade" id="edit_courier_modal{{$index}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-popout" role="document">
                                            <div class="modal-content">
                                                <div class="block block-themed block-transparent mb-0">
                                                    <div class="block-header bg-primary-dark">
                                                        <h3 class="block-title">Edit "{{$courier->title}}"</h3>
                                                        <div class="block-options">
                                                            <button type="button" class="btn-block-option">
                                                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <form action="{{route('couriers.update',$courier->id)}}" method="post">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" value="{{$courier->id}}" name="courier_id">
                                                        <div class="block-content font-size-sm">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <div class="form-material">
                                                                        <label for="material-error">Title</label>
                                                                        <input required class="form-control  @error('title') is-invalid @enderror" type="text" id="zone_title" value="{{$courier->title}}"   name="title" placeholder="Enter courier service provider title..">
                                                                        @error('title')
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="form-material">
                                                                        <label for="material-error">URL</label>
                                                                        <input required class="form-control" type="url" id="zone_title" value="{{$courier->url}}" name="url" placeholder="Enter courier service provider URL..">
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
                            <p>No Courier Service Providers Created</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create_courier" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Courier Service Providers</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option">
                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('couriers.store')}}" method="post">
                        @csrf
                        <div class="block-content font-size-sm">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Courier Title</label>
                                        <input required class="form-control @error('title') is-invalid @enderror" type="text" id="zone_title" name="title" placeholder="Enter courier service provider title..">
                                        @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-material">
                                        <label for="material-error">URL</label>
                                        <input required class="form-control" type="url" id="zone_title" name="url" placeholder="Enter courier service provider URL..">
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
