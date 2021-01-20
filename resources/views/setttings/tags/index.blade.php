@extends('layout.index')
@section('content')
    <div class="bg-body-light">
        <div class="content content-full pt-2 pb-2">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill h4 my-2">
                    Tags
                </h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="link-fx" href="">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">Tags</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-md-12">
                    <div class="block p-2">
                        <form class="form-horizontal" action="{{ route('tags.store') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="form-material">
                                        <label for="material-error">Tags</label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" id="cat" name="name"
                                               placeholder="Enter Tag name here">
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <div class="col-sm-12">
                                    <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
            </div>
            <div class="col-md-12">
                    <div class="block p-3">
                        @if(count($tags) > 0)
                                <h5>All Tags</h5>
                                @foreach($tags as $tag)
                                    <a class="btn btn-sm btn-primary text-white" data-toggle="modal" data-target="#delete_tag_modal_{{$tag->id}}">{{ $tag->name }}</a>
                                    <div class="modal fade" id="delete_tag_modal_{{$tag->id}}" tabindex="-1" role="dialog" aria-labelledby="modal-block-popout" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-popout" role="document">
                                        <div class="modal-content">
                                            <div class="block block-themed block-transparent mb-0">
                                                <div class="block-header bg-primary-dark">
                                                    <h3 class="block-title">{{ $tag->name }}</h3>
                                                    <div class="block-options">
                                                        <button type="button" class="btn-block-option">
                                                            <i class="fa fa-fw fa-times"  data-dismiss="modal" aria-label="Close"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="p-3">
                                                    <h5>Are are about to delete the tag.</h5>
                                                </div>
                                                <div class="block-content block-content-full text-right border-top">

                                                    <form class=" " action="{{route('tags.destroy',$tag->id)}}" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            Delete
                                                        </button>
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                                                            Discard
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                        @endif
                    </div>

            </div>
        </div>

    </div>

@endsection
