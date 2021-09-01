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

        @if(!empty($product))
        <form method="POST" action="{{ route('product.update',$product->id) }}" enctype="multipart/form-data">
            @method('PUT')
            @else
            <form method="POST" action="{{ route('product.index') }}" enctype="multipart/form-data">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ (!empty($product))?$product->id:'' }}">
                <div class="row">
<!-- - name
- brand_id
- description
- image
- inventory(manage by master)
 -->
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name"
                                value="{{ old('name',(!empty($product))?$product->name:'') }}" placeholder="Full Name">
                            @error('name')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Brand</label>                            
                            <select class="form-control" id="brand_id" name="brand_id">
                                <option value="">Please select</option>
                                @foreach($brands as $brand)
                                    <option {{ old('brand_id', (!empty($product) && $product->brand_id))==$brand->id?"selected='selected'":'' }} value="{{$brand->id}}">{{$brand->name}}</option>
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
                            <select class="form-control categories" multiple="multiple" name="categories[]">
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
                                    <input type="file" class="custom-file-input" id="image" value='' name="image">
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
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Description</label>                            
                            <textarea name="description" class="form-control" >{{ old('description',(!empty($product))?$product->description:'') }}</textarea>
                            @error('description')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Inventory</label>
                            <input type="number" class="form-control" name="inventory"
                                value="{{ old('inventory',(!empty($product))?$product->inventory:'') }}" placeholder="inventory">
                            @error('inventory')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" class="form-control" name="price"
                                value="{{ old('price',(!empty($product))?$product->price:'') }}" placeholder="Price" min=1 required>
                            @error('price')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
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
<script>
jQuery(document).ready(function(){
    jQuery(".categories").select2({
        tags: true,
        tokenSeparators: [","],
    });
    jQuery(document).ready(function() {
      $("#brand_id").select2();
    });
});
</script>
@endsection