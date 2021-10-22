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
        @if(!empty($product))
        <form method="POST" action="{{ route('product.update',$product->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @else
            <form method="POST" action="{{ route('product.index') }}" enctype="multipart/form-data">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ (!empty($product))?$product->id:'' }}">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Name <span class="required_field">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name',(!empty($product))?$product->name:'') }}" placeholder="Full Name" tabindex="1">
                            @error('name')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Brand <span class="required_field">*</span></label>
                            <select class="form-control" id="brand_id" name="brand_id" tabindex="2">
                                <option value="">Please select</option>
                                @foreach($brands as $brand)
                                <option {{ old('brand_id', (!empty($product) && $product->brand_id == $brand->id))== $brand->id ? "selected='selected'" : '' }} value="{{$brand->id}}">{{$brand->name}}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control categories" multiple="multiple" name="categories[]" tabindex="3">
                                <option value="">Please select</option>
                                @foreach($categories as $category)
                                <option value="{{$category->id}}" {{old('categories[]', (!empty($product_category) && in_array($category->id,$product_category)))?'selected':''}}>{{$category->name}}</option>
                                @endforeach
                            </select>
                            @error('category')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <!-- file input -->
                        <div class="form-group">
                            <label>Image</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" value='' name="image" tabindex="4">
                                    <label class="custom-file-label" for="image">Choose file</label>
                                </div>
                            </div>
                            @if(!empty($product->image))
                            <div class="product_image">
                                <img class='img logo mt-3 mb-4' src="{{ url($product->image) }}" width=100 />
                            </div>
                            @endif
                            @error('image')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <!-- text input -->
                        <div class="form-group summernote-container">
                            <label>Description <span class="required_field">*</span></label>
                            <textarea name="description" class="form-control summernote" tabindex="5">{{ old('description',(!empty($product))?$product->description:'') }}</textarea>
                            @error('description')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Inventory <span class="required_field">*</span></label>
                            <input type="number" class="form-control" name="inventory" value="{{ old('inventory',(!empty($product))?$product->inventory:'') }}" placeholder="inventory" tabindex="6">
                            @error('inventory')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Price <span class="required_field">*</span></label>
                            <input type="number" class="form-control" name="price" value="{{ old('price',(!empty($product))?$product->price:'') }}" placeholder="Price" tabindex="7" min=1 required>
                            @error('price')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Tax</label>
                            <select class="form-control tax_group_id select2" name="tax_group_id" tabindex="8">
                                <option value="">Select Tax Group</option>
                                @foreach($tax_groups as $tax_group)
                                <option value="{{$tax_group->id}}" {{old('tax_group_id', (!empty($product->tax_group_id) && ($tax_group->id == $product->tax_group_id)))?'selected':''}}>{{$tax_group->name}}</option>
                                @endforeach
                            </select>
                            @error('tax_group_id')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>HSN/SAC</label>
                            <input type="text" class="form-control" name="hsn_sac" value="{{ old('hsn_sac',(!empty($product))?$product->hsn_sac:'')}}" placeholder="HSN/SAC" tabindex="9">
                            @error('hsn_sac')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label class="col-12">Status</label>
                            <input type="checkbox" name="status" id="status" class="form-control status" data-bootstrap-switch  data-on-text="Enable" data-off-text="Disable" data-off-color="danger" data-on-color="success" {{((!isset($product)) || (!empty($product->status)))? "checked" :"" }} tabindex="10">
                            @error('status')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <button type="submit" class="btn btn-primary" tabindex="11">Submit</button>
                    </div>
                </div>
            </form>
    </div>
    <!-- /.card-body -->
</div>
@endsection
@section('plugins.Select2', true)
@section('plugins.summernote', true)
@section('plugins.BootstrapSwitch', true)
@section('js')
<script>
    jQuery(document).ready(function() {
        jQuery(".categories").select2({
            tags: true,
            tokenSeparators: [","],
        });
        jQuery(document).ready(function() {
            $("#brand_id").select2();
        });
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ["history", ["undo", "redo"]],
                ["style", ["style"]],
                ["font", ["bold", "italic", "underline", "fontname", "strikethrough", "superscript", "subscript", "clear"]],
                ["color", ["forecolor", "backcolor", "color"]],
                ["paragraph", ["ul", "ol", "paragraph", "height"]],
                ["table", ["table"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["codeview"]],
            ],

        });
    });
</script>
@endsection