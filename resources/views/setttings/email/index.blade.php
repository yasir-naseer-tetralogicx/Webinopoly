@extends('layout.index')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Email Templates
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Email Templates</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content">
{{--        <div class="row mb-3">--}}
{{--            <div class="col-sm-3 text-right">--}}
{{--                <a href="{{ route('product.create') }}" class="btn btn-success btn-square ">Add New Product</a>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="block">
            <div class="block-content">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped table-vcenter">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($templates as $template)
                                    <tr>
                                        <td class="font-w600" style="vertical-align: middle">
                                            <a href="{{ route('admin.emails.show', $template->id) }}">{{ $template->title }}</a>
                                        </td>
                                        <td style="vertical-align: middle">
                                            <div class="custom-control custom-switch custom-control-success mb-1">
                                                <input @if($template->status ==1)checked="" @endif data-route="{{route('admin.emails.status',$template->id)}}" data-template="{{ $template->id }}" data-csrf="{{csrf_token()}}" type="checkbox" class="custom-control-input template-status-switch" id="status_template_{{ $template->id }}" name="example-sw-success2">
                                                <label class="custom-control-label status-text_{{ $template->id }}" for="status_template_{{ $template->id }}">@if($template->status ==1) Published @else Draft @endif</label>
                                            </div>
                                        </td>
                                        <td class="text-right" style="vertical-align: middle">

                                            <div class="btn-group mr-2 mb-2" role="group" aria-label="Alternate Primary First group">
                                                <a class="btn btn-xs btn-sm btn-success" href="{{ route('admin.emails.show', $template->id) }}" type="button"  title="View Template">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a  class="btn btn-sm btn-warning" href="{{ route('admin.emails.edit', $template->id) }}"
                                                    type="button" data-toggle="tooltip" title=""
                                                    data-original-title="Edit Tempalte"><i
                                                        class="fa fa-edit"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
{{--                <div class="row">--}}
{{--                    <div class="col-md-12 text-center" style="font-size: 17px">--}}
{{--                        {!! $products->links() !!}--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
@endsection
