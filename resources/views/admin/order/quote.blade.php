@if(!empty($quote))
    <form method="POST" id="saveQuote">
    @method('PUT')
@else
    <form method="POST" id="saveQuote">
@endif
        @csrf
        <input type="hidden" name="from_user" value="{{auth()->user()->id}}" />
        <input type="hidden" name="to_user" value="{{$inquiry->customer->id}}" />
        <input type="hidden" name="inquiry_id" id="inquiry_id" value="" />
        <input type="hidden" name="type" id="type" value="quote" />
        <input type="hidden" name="order_number" id="order_number" value="{{(!empty($quote_number))?$quote_number:''}}" />
        <!-- <input type="hidden" name="id" value="{{ (!empty($inquiry))?$inquiry->id:'' }}" /> -->
        <!-- <input type="hidden" name="type" id="type" value="" /> -->

            <!-- <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <label for="source">Source</label>
                    <input type="text" class="form-control source" id="source" name="source"/>
                </div>
            </div> -->
            <div class="row">
                <!-- <div class="col-sm-13 col-md-6 mb-3 border-bottom">
                    <h4><strong>Quote Number:</strong> #{{$quote_number}}</h4>
                </div> -->
                <div class="col-sm-12 col-md-12 mb-3 border-bottom">
                    <div class="">
                        <h5 class="border-bottom"><strong>Customer Detail</strong></h5>
                        <ul class="list-unstyled">
                            <li><strong>{{ucwords($inquiry->customer->name)}}</strong></li>
                            <li><strong>{{ucwords($inquiry->customer->address)}},</strong></li>
                            <li><strong>{{ucwords($inquiry->customer->city->name)}},</strong></li>
                            <li><strong>{{ucwords($inquiry->customer->state->name)}} - {{$inquiry->customer->zipcode}}</strong></li>
                            <li><strong>Mobile: {{$inquiry->customer->mobile}}</strong></li>
                            <li><strong>Email: {{$inquiry->customer->email}}</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-3">
                    <h5><strong>Product Name</strong></h5>
                </div>
                <div class="col-sm-12 col-md-3">
                    <h5><strong>Qty</strong></h5>
                </div>
                <div class="col-sm-12 col-md-3">
                    <h5><strong>Price</strong></h5>
                </div>
                <div class="col-sm-12 col-md-3">
                    <h5><strong>Amount</strong></h5>
                </div>
            </div>
            @php $total=0.00; @endphp
            @foreach ($inquiry->product as $product)
            <div class="row product-detail" data-product="{{$product->id}}">
            <div class="col-sm-12 col-md-3">
                <!-- text input -->
                <div class="form-group clearfix">
                    <div class="icheck-primary d-inline">
                        <input type="checkbox" class="mr-3 product" name="product[]" value="{{$product->id}}" checked>
                        <label>{{$product->name}}</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <div class="form-group">
                    <!-- <label for="qty">Qty</label> -->
                    <input type="number" class="form-control col-3 qty d-inline" id="qty-{{$product->id}}" name="qty[]" value="1" min="1" />
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <div class="form-group">
                    <!-- <label for="qty">Price</label> -->
                    <input type="text" class="form-control col-6 price d-inline" id="price-{{$product->id}}" value="{{$product->price}}" name="amount[]" />
                </div>
            </div>
            <div class="col-sm-12 col-md-3">
                <div class="form-group">
                    <span class="col-4 right amount d-inline" id="amount-{{$product->id}}">{{$product->price}}</span>
                </div>
            </div>
        </div>
        @php $total += $product->price; @endphp
        @endforeach
        
        <div class="row border-top">
            <div class="col-sm-12 offset-md-8 col-md-1 mt-2">
                <div class="form-group">
                    <label for="total">Total</label>
                </div>
            </div>
            <div class="col-sm-12 col-md-2 mt-2">
                <div class="form-group">
                    <span class="col-4 right total d-inline" id="total">{{$total}}</span>
                </div>
            </div>
        </div>
        <div class="row border-top">
            <div class="col-sm-12 mt-2">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>