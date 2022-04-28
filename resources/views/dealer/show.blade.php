<div class="row">
    <form id="discountProducts" class="col-12" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="buyer_id" value="{{ $users->id }}">
        <input type="hidden" name="seller_id" value="{{ (!empty(auth()->user()->parent_id) ? auth()->user()->id : 0) }}">
        <div class="row">
            <div class="col-sm-12 col-md-3">
                <!-- text input -->
                <div class="form-group">
                    <label>Product Name</label>
                   
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <!-- text input -->
                <div class="form-group text-center">
                    <label>MRP</label>
                   
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <!-- text input -->
                <div class="form-group">
                    <label>Discount % </label>                    
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <!-- text input -->
                <div class="form-group text-center">                   
                <label>Final Amount</label>    
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($products as $product)
            <div class="col-sm-12 col-md-3">
                <!-- text input -->
                <div class="form-group">                    
                    <input type="text" class="form-control"  name="product_id[{{ $product->id }}]" value="{{ $product->name }}"  readonly>
                </div>
            </div>
            <div class="col-sm-12 col-md-3 pr-5">
                <!-- text input -->
                <div class="form-group text-right mr-5">                    
                    <input type="hidden" class="form-control" id="price_{{ $product->id }}" name="price[{{ $product->id }}]" value="{{ $product->price }}" readonly >
                    <span><span class="symbol-icon">{!! $currency->symbol !!}</span>{{ Str::number_format($product->price)}}</span>
                </div>
            </div>
            
            <div class="col-sm-12 col-md-3">
                <!-- text input -->
                <div class="form-group">                   
                    <input type="text" class="form-control" data-productid="{{ $product->id }}" id="discount_percentage_{{ $product->id }}" name="discount_percentage[{{ $product->id }}]" value="{{ !empty($product->discount_percentage)?$product->discount_percentage:'' }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-3 pr-5">
                <!-- text input -->
                <div class="form-group text-right mr-5">                   
                    <input type="text" class="form-control text-right" id="final_amount_{{ $product->id }}" name="final_amount[{{ $product->id }}]" value="{{ !empty($product->discount_percentage)?($product->price - (($product->price*$product->discount_percentage)/100)):'' }}" readonly>
                </div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary" tabindex="11">Submit</button>
            </div>
        </div>
    </form>
</div>