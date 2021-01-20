@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                   Ticket Categories
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Ticket Categories</li>
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
                <button class="btn btn-sm btn-primary text-white" data-toggle="modal" data-target="#create_zone_modal">Create Ticket Category</button>
            </div>
        </div>
        <div class="row" style="margin-top: 10px">
            <div class="col-md-12">
                <div class="block">
                    <div class="block-content">
                        @if (count($categories) > 0)
                            <table class="table table-hover table-borderless table-vcenter">
                                <thead>
                                <tr>

                                    <th >Title</th>
                                    <th>Color</th>
                                    <th></th>
                                </tr>
                                </thead>

                                @foreach($categories as $index => $category)
                                    <tbody class="">
                                    <tr>

                                        <td class="font-w600">{{ $category->name }}</td>
                                        <td>
                                           {{$category->color}}
                                        </td>
                                        <td class="text-right btn-group" style="float: right">
                                            <button class="btn btn-sm btn-warning" type="button" data-toggle="modal"
                                                    data-target="#edit_zone_modal{{$index}}"><i
                                                    class="fa fa-edit"></i>
                                            </button>
                                            <a href="{{ route('ticket.category.delete', $category->id) }}"
                                               class="btn btn-sm btn-danger" type="button" data-toggle="tooltip" title=""
                                               data-original-title="Delete Ticket Category"><i class="fa fa-times"></i></a>
                                        </td>

                                    </tr>
                                    <div class="modal fade" id="edit_zone_modal{{$index}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-popout" role="document">
                                            <div class="modal-content">
                                                <div class="block block-themed block-transparent mb-0">
                                                    <div class="block-header bg-primary-dark">
                                                        <h3 class="block-title">Edit "{{$category->name}}"</h3>
                                                        <div class="block-options">
                                                            <button type="button" class="btn-block-option">
                                                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <form action="{{route('ticket.category.update',$category->id)}}" method="post">
                                                        @csrf
                                                        <input type="hidden" value="{{$category->id}}" name="category_id">
                                                        <div class="block-content font-size-sm">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <div class="form-material">
                                                                        <label for="material-error">Title</label>
                                                                        <input required class="form-control" type="text" name="name"
                                                                               value="{{$category->name}}"   placeholder="Enter Category Title here">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <div class="form-material">
                                                                        <label for="material-error">Color</label>
                                                                        <input required class="form-control" type="color" name="color"
                                                                               value="{{$category->color}}">
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
                                @endforeach
                            </table>
                        @else
                            <p>No Ticket Category Found</p>
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
                        <h3 class="block-title">Ticket Category</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option">
                                <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                            </button>
                        </div>
                    </div>
                    <form action="{{route('ticket.category.create')}}" method="post">
                        @csrf
                        <div class="block-content font-size-sm">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Title</label>
                                        <input required class="form-control" type="text" name="name"
                                               placeholder="Enter Category Title here">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Color</label>
                                        <input required class="form-control" type="color" name="color"
                                             >
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
