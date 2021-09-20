@extends('layouts.adminlte')
@section('title')
{{$title}}
@endsection
@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">{{$action_title}} {{$title}}</h3>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        @if(!empty($brand))
        <form method="POST" action="{{ route('brand.update',$brand->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @else
            <form method="POST" action="{{ route('brand.index') }}" enctype="multipart/form-data">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ (!empty($brand))?$brand->id:'' }}">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Manufacturer <span class="required_field">*</span></label>                            
                            <select class="form-control" id="manufacturer_id" name="manufacturer_id" tabindex="1">
                                <option value="">Please select</option>
                                @foreach($manufacturers as $manufacturer)
                                    <option {{ (!empty($brand) && $brand->manufacturer_id==$manufacturer->id)?"selected='selected'":'' }} value="{{$manufacturer->id}}">{{$manufacturer->name}}</option>
                                @endforeach
                            </select>
                            @error('manufacturer_id')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Name <span class="required_field">*</span></label>
                            <input type="text" class="form-control" name="name"
                                value="{{ old('name',(!empty($brand))?$brand->name:'') }}" placeholder="Full Name" tabindex="2">
                            @error('name')
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
                                    <input type="file" class="custom-file-input" id="logo" name="logo" tabindex="3">
                                    <label class="custom-file-label" for="logo">Choose file</label>
                                </div>
                            </div>
                            @if(!empty($brand->logo))
                            <div class="">
                                <img class='img logo mt-3 mb-4' src="{{ url($brand->logo) }}" width=100 />
                            </div>
                            @endif
                            @error('logo')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Description <span class="required_field">*</span></label>                            
                            <textarea name="description" class="form-control" tabindex="4">{{ old('description',(!empty($brand))?$brand->description:'') }}</textarea>
                            @error('description')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Short Description <span class="required_field">*</span></label>                            
                            <textarea name="short_description" class="form-control" tabindex="5">{{ old('short_description',(!empty($brand))?$brand->short_description:'') }}</textarea>
                            @error('short_description')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary" tabindex="6">Submit</button>
                    </div>
                </div>
            </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection
@section('plugins.Select2', true)
@section('js')
<script>
    jQuery(document).ready(function() {
      $('#manufacturer_id').select2({
        placeholder: "Select",
        allowClear: true,
      });
    });
</script>
@endsection