@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Settings
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">

        {{--        <div class="block">--}}
        {{--            <div class="block-header">--}}
        {{--                <h3 class="block-title">Shipping Information</h3>--}}
        {{--            </div>--}}
        {{--            <div class="block-content block-content-narrow">--}}
        {{--                <form class="form-horizontal push-10-t"--}}
        {{--                      @if($info)--}}
        {{--                      action="{{ route('default_info.update', $info->id) }}"--}}
        {{--                      @else--}}
        {{--                      action="{{ route('default_info.save') }}"--}}
        {{--                      @endif--}}
        {{--                      method="post">--}}
        {{--                    @csrf--}}
        {{--                    <div class="form-group">--}}
        {{--                        <div class="col-sm-12">--}}
        {{--                            <div class="form-material">--}}
        {{--                                <input class="form-control" type="text" name="info"--}}
        {{--                                       placeholder="Enter Shipping Information here"--}}
        {{--                                       @if($info->ship_info) value="{{ $info->ship_info }}"@endif--}}
        {{--                                >--}}
        {{--                                <label>Shipping Info</label>--}}
        {{--                            </div>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                    <div class="form-group">--}}
        {{--                        <div class="col-sm-12">--}}
        {{--                            <div class="form-material">--}}
        {{--                                <input class="form-control" type="text" name="time"--}}
        {{--                                       placeholder="eg : 6 days"--}}
        {{--                                       @if($info->processing_time) value="{{ $info->processing_time }}"@endif>--}}
        {{--                                <label>Processing Time</label>--}}
        {{--                            </div>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                    <div class="form-group">--}}
        {{--                        <div class="col-sm-12">--}}
        {{--                            <div class="form-material">--}}
        {{--                                <input class="form-control" type="text" name="price"--}}
        {{--                                       placeholder="$0.00"--}}
        {{--                                       @if($info->ship_price) value="{{ $info->ship_price }}"@endif>--}}
        {{--                                <label>Shipping Price</label>--}}
        {{--                            </div>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                    <div class="form-group">--}}
        {{--                        <div class="col-sm-12">--}}
        {{--                            <div class="form-material">--}}
        {{--                                <textarea class="form-control" type="text" name="warnedplatform" rows="3">--}}
        {{--                                    @if($info->warned_platform){{ $info->warned_platform }}@endif</textarea>--}}
        {{--                                <label>Warned Platforms</label>--}}
        {{--                            </div>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}

        {{--                    <div class="form-group">--}}
        {{--                        <div class="col-sm-9">--}}
        {{--                            @if ($info)--}}
        {{--                                <button class="btn btn-sm btn-success" type="submit">Update</button>--}}
        {{--                            @else--}}
        {{--                                <button class="btn btn-sm btn-primary" type="submit">Submit</button>--}}
        {{--                            @endif--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                </form>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        <div class="row">
            <div class="col-md-4">
                <div class="block">
                    <div class="block-header">
                        <h3 class="block-title">Create Platform</h3>
                    </div>
                    <div class="block-content block-content-narrow">
                        <form class="form-horizontal push-10-t"
                              action="{{ route('create_platform') }}"
                              method="post">
                            @csrf
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label>Title</label>
                                        <input class="form-control" type="text" name="name" required
                                               placeholder="Enter Warned Platform Title here">

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="block">
                    <div class="block-header">
                        <h3 class="block-title">Payment Charge Percentage</h3>
                    </div>
                    <div class="block-content block-content-narrow">
                        <form class="form-horizontal push-10-t"
                              action="{{route('payment.charge.save')}}"
                              method="post">
                            @csrf
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label>Credit Card Percentage</label>
                                        <input class="form-control" type="number" step="any" name="payment_charge_percentage" required
                                         @if($settings != null)  value="{{$settings->payment_charge_percentage}}" @endif>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label>Paypal Percentage</label>
                                        <input class="form-control" type="number" step="any" name="paypal_percentage" required
                                               @if($settings != null)  value="{{$settings->paypal_percentage}}" @endif>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="block">
                    <div class="block-header">
                        <h3 class="block-title">Supported Platforms</h3>
                    </div>
                    <div class="block-content block-content-narrow">
                        @if(count($platforms) > 0)
                            <table class="table table-hover table-striped table-borderless table-vcenter">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th class="text-right">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($platforms as $index => $p)
                                    <tr>
                                        <td>{{$p->name}}</td>
                                        <td class="btn-group" style="float: right">
                                            <a class="btn btn-sm btn-warning text-white"
                                               type="button" data-toggle="modal" data-target="#edit_platform_modal{{$index}}"><i class="fa fa-edit"></i></a>
                                            <a href="{{ route('delete_platform', $p->id) }}" class="btn btn-sm btn-danger"
                                               type="button" data-toggle="tooltip" title=""
                                               data-original-title="Delete Plateform"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="edit_platform_modal{{$index}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-popout" role="document">
                                            <div class="modal-content">
                                                <div class="block block-themed block-transparent mb-0">
                                                    <div class="block-header bg-primary-dark">
                                                        <h3 class="block-title">Edit "{{$p->name}}"</h3>
                                                        <div class="block-options">
                                                            <button type="button" class="btn-block-option">
                                                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <form action="{{route('update_platform',$p->id)}}" method="post">
                                                        @csrf
                                                        <div class="block-content font-size-sm">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <div class="form-material">
                                                                        <label for="material-error">Title</label>
                                                                        <input required class="form-control" type="text" id="name" name="name"
                                                                               value="{{$p->name}}">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="block-content block-content-full text-right border-top text-right">
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
                            <p>No Platforms Available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
