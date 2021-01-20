@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Shipping Zones
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Shipping Zones</li>
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
                <button class="btn btn-sm btn-primary text-white" data-toggle="modal" data-target="#create_zone_modal">Create Shipping Zone</button>
            </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content">
                        @if (count($zones) > 0)
                            <table class="js-table-sections table table-hover table-borderless table-vcenter">
                                <thead>
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th >Title</th>
                                    <th style="width: 25%;">Countries</th>
                                    <th></th>
                                    <th class="text-center" style="width: 15%;"></th>
                                    <th></th>
                                </tr>
                                </thead>

                                @foreach($zones as $index => $zone)
                                    <tbody class="js-table-sections-header">
                                    <tr>
                                        <td class="text-center">
                                            <i class="fa fa-angle-right"></i>
                                        </td>
                                        <td class="font-w600">{{ $zone->name }}</td>
                                        <td>
                                            @foreach($zone->has_countries as $country)
                                                <span class="badge badge-primary">{{$country->name}}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#create_rate_modal{{$index}}"> Add Rate</button>
                                        </td>
                                        <td></td>
                                        <td class="text-right btn-group" style="float: right">
                                            <button class="btn btn-sm btn-warning" type="button" data-toggle="modal"
                                                    data-target="#edit_zone_modal{{$index}}"><i
                                                    class="fa fa-edit"></i>
                                            </button>
                                            <a href="{{ route('zone.delete', $zone->id) }}"
                                               class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                               data-original-title="Delete Zone"><i class="fa fa-times"></i></a>
                                        </td>

                                    </tr>
                                    <div class="modal fade" id="edit_zone_modal{{$index}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-popout" role="document">
                                            <div class="modal-content">
                                                <div class="block block-themed block-transparent mb-0">
                                                    <div class="block-header bg-primary-dark">
                                                        <h3 class="block-title">Edit "{{$zone->name}}"</h3>
                                                        <div class="block-options">
                                                            <button type="button" class="btn-block-option">
                                                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <form action="{{route('zone.update',$zone->id)}}" method="post">
                                                        @csrf
                                                        <input type="hidden" value="{{$zone->id}}" name="zone_id">
                                                        <div class="block-content font-size-sm">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <div class="form-material">
                                                                        <label for="material-error">Title</label>

                                                                        <input required class="form-control" type="text" id="zone_title" name="name"
                                                                               value="{{$zone->name}}"   placeholder="Enter Zone Title here">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for=""> Select Countries</label>
                                                                </div>
                                                            </div>
                                                            <div class="countries-section">
                                                                @foreach($countries as $country)
                                                                    <div class="col-md-12">
                                                                        <div class="custom-control custom-checkbox d-inline-block">
                                                                            <input type="checkbox" name="countries[]"  @if(in_array($country->id,$zone->has_countries->pluck('id')->toArray())) checked @endif value="{{$country->id}}" class="custom-control-input" id="row_edit_country_{{$zone->id}}_{{$index}}_{{$country->id}}">
                                                                            <label class="custom-control-label"  for="row_edit_country_{{$zone->id}}_{{$index}}_{{$country->id}}">{{$country->name}}</label>
                                                                        </div>
                                                                    </div>

                                                                @endforeach
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
                                    <div class="modal fade" id="create_rate_modal{{$index}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-popout" role="document">
                                            <div class="modal-content">
                                                <div class="block block-themed block-transparent mb-0">
                                                    <div class="block-header bg-primary-dark">
                                                        <h3 class="block-title">Add {{$zone->name}}'s Rate</h3>
                                                        <div class="block-options">
                                                            <button type="button" class="btn-block-option">
                                                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <form action="{{route('zone.rate.create',$zone->id)}}" method="post">
                                                        @csrf
                                                        <input type="hidden" value="{{$zone->id}}" name="zone_id">
                                                        <div class="block-content font-size-sm">
                                                            <div class="form-group row">
                                                                <div class="col-sm-12">
                                                                    <div class="form-material">
                                                                        <label for="material-error">Title</label>
                                                                        <input required class="form-control" type="text"  name="name"
                                                                               placeholder="Enter Zone Title here">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row" style="margin-top: 10px">
                                                                <div class="col-sm-6">
                                                                    <div class="form-material">
                                                                        <label for="material-error">Rate Type</label>
                                                                        <select required class="form-control rate_type_select" name="type">
                                                                            <option value="flat">Flat Rate</option>
                                                                            <option value="order_price">Per Order Price</option>
                                                                            <option value="weight">Per Weight</option>

                                                                        </select>

                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-material">
                                                                        <label for="material-error">Price</label>
                                                                        <input required class="form-control" step="any" type="number" name="shipping_price">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row condition-div" style="display: none">
{{--                                                                <div class="col-sm-6">--}}
{{--                                                                    <div class="form-material">--}}
{{--                                                                        <label for="material-error ">Max Condition</label>--}}
{{--                                                                        <input class="form-control max-condtion" step="any" type="number" name="max">--}}

{{--                                                                    </div>--}}
{{--                                                                </div>--}}
                                                                <div class="col-sm-12">
                                                                    <div class="form-material">
                                                                        <label for="material-error ">Unit Per Kg</label>
                                                                        <input class="form-control min-condtion " step="any" type="number" name="min">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row ">
                                                                <div class="col-sm-6">
                                                                    <div class="form-material">
                                                                        <label for="material-error ">Shipping Time (Days)</label>
                                                                        <input required class="form-control" type="text" name="shipping_time">

                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-material">
                                                                        <label for="material-error ">Processing Time (Days)</label>
                                                                        <input required class="form-control " type="text" name="processing_time">

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
                                    </tbody>
                                    {{--Rates Tables--}}
                                    <tbody>
                                    @if (count($zone->has_rate) > 0)
                                        @foreach($zone->has_rate as $new_index => $rate)
                                            <tr>
                                                <td class="text-center text-success" style="vertical-align: top">
                                                    {{ $rate->name }}
                                                </td>
                                                <td class="font-w600" style="vertical-align: top"> Type: {{ str_replace('_',' ',$rate->type)  }}</td>
                                                <td style="vertical-align: top">
                                                    Condition: @if($rate->type == 'flat') None @elseif($rate->type == 'order_price') {{$rate->min}}  @else  {{$rate->min}}  Kgs @endif
                                                </td>
                                                <td style="width: 25%;vertical-align: top" >
                                                    <p>Shipping Time : {{$rate->shipping_time}}<br>
                                                        Processing Time : {{$rate->processing_time}}
                                                    </p>
                                                </td>
                                                <td
                                                    class="text-success text-center" style="vertical-align: top">${{number_format($rate->shipping_price,2)}}
                                                </td>
                                                <td class="text-center btn-group" style="float: right">
                                                    <button class="btn btn-sm btn-warning" type="button" data-toggle="modal"
                                                            data-target="#edit_rate_modal{{$rate->id}}{{$zone->id}}"><i
                                                            class="fa fa-edit"></i>
                                                    </button>
                                                    <a href="{{ route('zone.rate.delete', $rate->id) }}"
                                                       class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                                       data-original-title="Delete Rate"><i class="fa fa-times"></i></a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="edit_rate_modal{{$rate->id}}{{$zone->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-popout" role="document">
                                                    <div class="modal-content">
                                                        <div class="block block-themed block-transparent mb-0">
                                                            <div class="block-header bg-primary-dark">
                                                                <h3 class="block-title">Add {{$zone->name}}'s Rate</h3>
                                                                <div class="block-options">
                                                                    <button type="button" class="btn-block-option">
                                                                        <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <form action="{{route('zone.rate.update',$rate->id)}}" method="post">
                                                                @csrf
                                                                <input type="hidden" value="{{$zone->id}}" name="zone_id">
                                                                <div class="block-content font-size-sm">
                                                                    <div class="form-group row">
                                                                        <div class="col-sm-12">
                                                                            <div class="form-material">
                                                                                <label for="material-error">Title</label>
                                                                                <input required class="form-control" type="text"  name="name"
                                                                                       value="{{$rate->name}}"     placeholder="Enter Zone Title here">

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row" style="margin-top: 10px">
                                                                        <div class="col-sm-6">
                                                                            <div class="form-material">
                                                                                <label for="material-error">Rate Type</label>

                                                                                <select required class="form-control rate_type_select" name="type">
                                                                                    <option @if($rate->type == 'flat') selected @endif  value="flat">Flat Rate</option>
                                                                                    <option @if($rate->type == 'order_price') selected @endif value="order_price">Per Order Price</option>
                                                                                    <option @if($rate->type == 'weight') selected @endif value="weight">Per Weight</option>

                                                                                </select>

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-material">
                                                                                <label for="material-error">Price</label>
                                                                                <input required class="form-control" type="number" step="any" name="shipping_price" value="{{$rate->shipping_price}}">

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row condition-div" @if($rate->type == 'flat') style="display: none" @endif>
                                                                        <div class="col-sm-12">
                                                                            <div class="form-material">
                                                                                <label for="material-error ">Unit Per Kg</label>
                                                                                <input class="form-control min-condtion " step="any" @if($rate->type != 'flat') required @endif value="{{$rate->min}}" type="number" name="min">

                                                                            </div>
                                                                        </div>
{{--                                                                        <div class="col-sm-6">--}}
{{--                                                                            <div class="form-material">--}}
{{--                                                                                <label for="material-error ">Max Condition</label>--}}
{{--                                                                                <input class="form-control max-condtion" step="any" @if($rate->type != 'flat') required @endif value="{{$rate->max}}" type="number" name="max">--}}

{{--                                                                            </div>--}}
{{--                                                                        </div>--}}
                                                                    </div>
                                                                    <div class="form-group row ">
                                                                        <div class="col-sm-6">
                                                                            <div class="form-material">
                                                                                <label for="material-error ">Shipping Time (Days)</label>
                                                                                <input required class="form-control" type="text" name="shipping_time" value="{{$rate->shipping_time}}">

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-material">
                                                                                <label for="material-error ">Processing Time (Days)</label>
                                                                                <input required class="form-control " type="text" name="processing_time" value="{{$rate->processing_time}}">

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row ">
                                                                        <div class="col-sm-6">
                                                                            <div class="form-material">
                                                                                <label for="material-error ">Courier Title</label>
                                                                                <input required class="form-control @error('title') is-invalid @enderror" type="text" id="zone_title" name="title" placeholder="Enter courier service provider title.." value="@if($zone->courier != null) {{ $zone->courier->title }} @endif">
                                                                                @error('title')
                                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-material">
                                                                                <label for="material-error ">URL</label>
                                                                                <input required class="form-control" type="url" id="zone_title" name="url" placeholder="Enter courier service provider URL.." value="@if($zone->courier != null) {{ $zone->courier->url }} @endif">
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

                                    @else
                                        <tr>
                                            <td>
                                            </td>
                                            <td class="font-w600 text-success">
                                                <button class="btn btn-sm btn-info text-white" data-toggle="modal" data-target="#create_rate_modal{{$index}}"> Add Rate</button>
                                            </td>
                                            <td>
                                                <small></small>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endif

                                    </tbody>

                                @endforeach
                            </table>
                        @else
                            <p>No Shipping Zone Created</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="create_zone_modal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
        <div class="modal-dialog modal-dialog-popout" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Shipping Zone</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option">
                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('zone.create')}}" method="post">
                        @csrf
                        <div class="block-content font-size-sm">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Title</label>
                                        <input required class="form-control" type="text" id="zone_title" name="name"
                                               placeholder="Enter Zone Title here">

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for=""> Select Countries</label>
                                </div>
                            </div>
                            <div class="countries-section">
                                @foreach($countries as $country)
                                    <div class="col-md-12">
                                        <div class="custom-control custom-checkbox d-inline-block">
                                            <input type="checkbox" name="countries[]" value="{{$country->id}}" class="custom-control-input" id="row_country{{$country->id}}">
                                            <label class="custom-control-label" for="row_country{{$country->id}}">{{$country->name}}</label>
                                        </div>
                                    </div>

                                @endforeach
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
