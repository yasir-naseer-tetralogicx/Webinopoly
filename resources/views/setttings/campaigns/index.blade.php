@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Campaigns
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Campaigns</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content">
                        @if (count($campaigns) > 0)
                            <table class="js-table-sections table table-hover table-borderless table-vcenter">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Publish Time</th>
                                    <th></th>
                                </tr>
                                </thead>

                                @foreach($campaigns as $campaign)
                                    <tbody class="">
                                    <tr>
                                        <td class="font-w600">{{ $campaign->name }}</td>
                                        <td><span class="badge @if($campaign->status === 'pending') badge-primary @else badge-success @endif">{{ $campaign->status }}</span></td>
                                        <td>{{ $campaign->time }}</td>
                                        <td class="text-right btn-group" style="float: right">
                                            <a href="{{ route('email.campaigns.show', $campaign->id) }}"
                                               class="btn btn-sm btn-primary" type="button" data-toggle="tooltip" title=""
                                               data-original-title="View Campaign"><i class="fa fa-eye"></i></a>
                                            <a href="{{ route('email.campaigns.edit', $campaign->id) }}"
                                               class="btn btn-sm btn-info" type="button" data-toggle="tooltip" title=""
                                               data-original-title="Edit Campaign"><i class="fa fa-pen"></i></a>
                                            <a href="{{ route('email.campaigns.submit', $campaign->id) }}"
                                               class="btn btn-sm btn-success" type="button" data-toggle="tooltip" title=""
                                               data-original-title="View Campaign"><i class="fa fa-check"></i></a>
                                            <a href="{{ route('email.campaigns.delete', $campaign->id) }}"
                                               class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                               data-original-title="Delete Campaign"><i class="fa fa-trash"></i></a>
                                        </td>

                                    </tr>
                                    </tbody>
                                @endforeach
                            </table>
                            <div class="d-flex justify-content-end">
                                {{ $campaigns->links() }}
                            </div>
                        @else
                            <p>No Campaigns</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
