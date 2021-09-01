@extends('layouts.adminlte')
@section('title')
    {{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
    <h3 class="card-title">Manufacturer</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">     
        @if(!empty($manufacturer))
            <form method="POST" action="{{ route('manufacturer.update',$manufacturer->id) }}" enctype="multipart/form-data">
            @method('PUT')
        @else
            <form method="POST" action="{{ route('manufacturer.index') }}" enctype="multipart/form-data">
        @endif
        @csrf
        <input type="hidden" name="id" value="{{ (!empty($manufacturer))?$manufacturer->id:'' }}">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <!-- text input -->
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($manufacturer))?$manufacturer->name:'') }}" placeholder="Full Name">
                    @error('name')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <!-- text input -->
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{old('email',(!empty($manufacturer))?$manufacturer->email:'') }}" placeholder="Email" autocomplete="false">
                    @error('email')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <!-- file input -->
                <div class="form-group">
                    <label>Logo</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logo" name="logo">
                        <label class="custom-file-label" for="logo">Choose file</label>
                      </div>
                    </div>
                    @if(!empty($manufacturer->logo))
                    <div class="">
                        <img class='img logo mt-3 mb-4' src="{{ url($manufacturer->logo) }}" width=100 />
                    </div>
                    @endif
                    @error('logo')
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
@section('js')
@endsection
