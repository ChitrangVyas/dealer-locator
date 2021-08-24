@if(!empty($quote))
    <form method="POST" id="saveQuote">
    @method('PUT')
@else
    <form method="POST" id="saveQuote">
@endif
        @csrf
        <input type="hidden" name="from_user" value="{{auth()->user()->id}}" />
        <input type="hidden" name="to_user" value="{{ $to_user }}" />
        <input type="hidden" name="inquiry_id" id="inquiry_id" value="" />
        <input type="hidden" name="type" id="type" value="quote" />
        <input type="hidden" name="order_number" id="order_number" value="{{(!empty($quote_number))?$quote_number:'123'}}" />
        <input type="hidden" name="cart_id" id="modalId" value="{{$id}}" />
            <div class="row">
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
            @php $total=0.00; @endphp
            @foreach ($cart->getContent() as $product)
            <div class="row product-detail" data-product="{{$product->id}}">
            <div class="col-sm-12 col-md-7">
                <!-- text input -->
                <div class="form-group clearfix">
                    <div class="icheck-primary d-inline">
                    <input type="hidden" name="product[]" value="{{ $product->id }}" />
                        <!-- <input type="checkbox" class="mr-3 product" name="product[]" value="{{$product->id}}" checked> -->
                        <label>{{$product->name}}</label>
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
        @php $total += $product->price; @endphp
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