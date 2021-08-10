@extends('layouts.adminlte')
@section('title')
{{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">{{$title}}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

        @if(!empty($inquiries_source))
        <form method="POST" action="{{ route('inquiries-source.update',$inquiries_source->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @else
            <form method="POST" action="{{ route('inquiries-source.index') }}" enctype="multipart/form-data">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ (!empty($inquiries_source))?$inquiries_source->id:'' }}">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Source</label>
                            <input type="text" class="form-control" name="source"
                                value="{{ (!empty($inquiries_source))?$inquiries_source->source:'' }}" placeholder="source">
                            @error('name')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>       
                   
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection
@section('plugins.Select2', true)
@section('js')
@endsection
