@if(!empty($quote))
    <form method="POST" id="saveQuote">
    @method('PUT')
@else
    <form method="POST" id="saveQuote">
@endif
        @csrf
        <input type="hidden" name="inquiry_id" id="inquiry_id" value="{{(!empty($inquiry_id)) ? $inquiry_id : ''}}" />
        <input type="hidden" name="order_number" id="order_number" value="{{(!empty($order_number)) ? $order_number : ''}}" />
        <input type="hidden" id="type" value="{{(!empty($type)) ? $type : ''}}">
        <input type="hidden" name="is_new_product" id="is_new_product" value="{{(!empty($is_new_product)) ? $is_new_product : ''}}">
        <input type="hidden" name="cart_id" id="modalId" value="{{$id}}" />
            <div class="row">
                @if($is_new_product)
                <div class="col-sm-12 col-md-4 mt-2">
                    <!-- text input -->
                    <label></label>
                    <div class="form-group">
                        <button type="button" id="addNewProductToCart" class="btn btn-primary">Add Product</button>
                    </div>
                </div>
                @else
                <div class="col-sm-12 col-md-4">
                    <!-- text input -->
                    <div class="form-group">
                        <label>Product</label>
                        <select class="form-control select2 select-product" id='selectProduct'>
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 mt-2">
                    <!-- text input -->
                    <label></label>
                    <div class="form-group">
                        <button type="button" id="addProductToCart" class="btn btn-primary">Add</button>
                    </div>
                </div>
                @endif
            </div>
            @if(!$cart->isEmpty())
            <div class="row">
                <div class="col-sm-12 col-md-7">
                    <h5><strong>Product Name</strong></h5>
                </div>
                <div class="col-sm-12 col-md-1">
                    <h5><strong>Qty</strong></h5>
                </div>
                <div class="col-sm-12 col-md-2">
                    <h5><strong>Price</strong></h5>
                </div>
                <div class="col-sm-12 col-md-1">
                    <h5><strong>Amount</strong></h5>
                </div>
            </div>
            @foreach ($cart->getContent() as $product)
            <div class="row product-detail" data-product="{{$product->id}}">
            <div class="col-sm-12 col-md-7">
                <!-- text input -->
                <div class="form-group clearfix">
                    <div class="icheck-primary d-inline">
                        <!-- <input type="checkbox" class="mr-3 product" name="product[]" value="{{$product->id}}" checked> -->
                        @if ($is_new_product)
                        <input type="text" class="form-control product d-inline" value="{{$product->name}}" name="product[{{$product->id}}]" />
                            @else
                            <input type="hidden" name="product[]" value="{{ $product->id }}" />
                            <label>{{$product->name}}</label>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-1">
                <div class="form-group">
                    <!-- <label for="qty">Qty</label> -->
                    <input type="number" class="form-control qty d-inline" id="qty-{{$product->id}}" name="qty[{{$product->id}}]" value="{{$product->quantity}}" min="1" />
                </div>
            </div>
            <div class="col-sm-12 col-md-2">
                <div class="form-group">
                    <!-- <label for="qty">Price</label> -->
                    <input type="text" class="form-control price d-inline" id="price-{{$product->id}}" value="{{$product->price}}" name="amount[{{$product->id}}]" />
                </div>
            </div>
            <div class="col-sm-12 col-md-1">
                <div class="form-group">
                    <span class="col-4 right amount d-inline" id="amount-{{$product->id}}">{{ ($product->quantity * $product->price)}}</span>
                </div>
            </div>
            <div class="col-sm-12 col-md-1">
                <div class="form-group">
                    <span class="col-4 right delete d-inline btn btn-danger" data-product="{{$product->id}}" id="delete-{{$product->id}}"><i class="fas fa-trash"></i></span>
                </div>
            </div>
        </div>
        @endforeach
        
        <div class="row border-top">
            <div class="col-sm-12 offset-md-9 col-md-1 mt-2">
                <div class="form-group">
                    <label for="total">Total</label>
                </div>
            </div>
            <div class="col-sm-12 col-md-1 mt-2">
                <div class="form-group">
                    <span class="col-4 right total d-inline" id="total">{{$cart->getTotal()}}</span>
                </div>
            </div>
        </div>
        <div class="row border-top">
            <div class="col-sm-12 mt-2">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        @else
        <div class="row border-top">
            <div class="col-sm-12 col-md-12 mt-2">
            <div class="jumbotron">
                    <h4 class="text-center text-secondary">Cart is Empty</h4>
                </div>
            </div>
        </div>

        @endif
    </form>