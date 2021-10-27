<form method="POST" action="" id="frmAddStock">
    @csrf
    <div class="form-group">
            <input type="hidden" name="users_id" value="{{auth()->user()->id}}" />
            <input type="hidden" name="product_id" id='product_id' value="{{$product->id}}" />
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-7">
            <!-- text input -->
            <div>
                <span><h5><strong>{{$product->name}}</strong></h5></span>
            </div>
            <div class="form-group">
                <label>Available no. of product</label>
                <input type="number" name="inventory" id="inventory" class="form-control" value="{{$product->getInventory()}}" />
                @error('inventory')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>            
            <!-- text input -->
            <div class="form-group">
                <label>Low Stock <span class="required_field text-danger">*</span></label>
                <input type="number" class="form-control" name="low_stock" value="{{ old('low_stock',(!empty($product) && !empty($product->getInventory()->low_stock))?($product->getInventory())->low_stock:'') }}" placeholder="Low Stock" tabindex="7">
                @error('low_stock')
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