@extends('layout.shopify')
@section('content')

    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Import Files
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Import Files</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row" >
            <div class="col-md-12">
                <div class="block">
                    <div class="block-header">
                        <h5 class="block-title"> Imported Files </h5>
                    </div>
                    <div class="block-content">
                        @if (count($files) > 0)
                            <table class="table table-hover table-borderless table-striped table-vcenter">
                                <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Date</th>
                                    <th>Download</th>
                                    <th></th>
                                </tr>
                                </thead>

                                @foreach($files as $index => $file)
                                    <tbody class="">
                                    <tr>
                                        <td>{{$file->file}}</td>
                                        <td> {{$file->created_at->diffForHumans()}}</td>
                                        <td>
                                            <a href="{{route('users.files.download_processed_orders',$file->id)}}" target="_blank"
                                               class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" title=""
                                               data-original-title="Download Processed Orders Excel File">Processed Orders Export</a>
                                            <a href="{{route('users.files.download_unprocessed_orders',$file->id)}}" target="_blank"
                                               class="btn btn-sm btn-warning" type="button" data-toggle="tooltip" title=""
                                               data-original-title="Download Unrocessed Orders Excel File">Unprocessed Orders Export</a>

                                        </td>
                                        <td align="right">
                                            <div class="btn-group">
                                                <a href="{{asset('import-orders')}}/{{$file->file}}" target="_blank"
                                                   class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="Download File"><i class="fa fa-download"></i></a>
                                                <a href="{{route('users.files.view',$file->id)}}"
                                                   class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                                   data-original-title="View File"><i class="fa fa-eye"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>

                                @endforeach
                            </table>
                        @else
                            <p>No Files Found </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
