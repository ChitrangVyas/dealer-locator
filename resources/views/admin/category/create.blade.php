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
        @if(!empty($category))
        <form method="POST" action="{{ route('category.update',$category->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @else
            <form method="POST" action="{{ route('category.index') }}" enctype="multipart/form-data">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ (!empty($category))?$category->id:'' }}">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Name <span class="required_field">*</span></label>
                            <input type="text" class="form-control" name="name"
                                value="{{ old('name',(!empty($category))?$category->name:'') }}" placeholder="Full Name" tabindex="1">
                            @error('name')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>       
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Parent category</label>
                            <select class="form-control select2" id='parent_category' name='parent_category' tabindex="2">
                                <option value="0">Select</option>
                                @foreach($parent_category as $tc)
                                <option value="{{$tc->id}}" {{ (!empty($category) && $category->parent_category == $tc->id ) ? 'selected' :''}} >{{$tc->name}}</option>
                                @endforeach
                                
                            </select>
                            @error('parent_category')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- file input -->
                        <div class="form-group">
                            <label>Image <span class="required_field">*</span></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image" tabindex="3">
                                    <label class="custom-file-label" for="image">Choose file</label>
                                </div>
                            </div>
                            @if(!empty($category->image))
                            <div class="">
                                <img class='img logo mt-3 mb-4' src="{{ url($category->image) }}" width=100 />
                            </div>
                            @endif
                            @error('image')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> 
                    <div class="col-sm-12 col-md-12">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Description</label>                            
                            <textarea name="description" class="form-control summernote" tabindex="4">{{ old('description',(!empty($category))?$category->description:'') }}</textarea>
                            @error('description')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary" tabindex="5">Submit</button>
                    </div>
                </div>
            </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection
@section('plugins.Select2', true)
@section('plugins.summernote', true)
@section('js')
<script>
    jQuery(document).ready(function() {
      $('#parent_category').select2({
        placeholder: "Select",
        allowClear: true,
      });
      $('.summernote').summernote({
            height: 200
    });
    });
</script>
@endsection
