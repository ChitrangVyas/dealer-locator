<div class="row">
    <div class="col-sm-12 col-md-12 mb-3 border-bottom">
        <div class="">
            <h5 class="border-bottom"><strong>Customer Detail</strong></h5>
            <ul class="list-unstyled">
                <li><strong>{{ucwords($order->customer->name)}}</strong></li>
                <li><strong>{{ucwords($order->customer->address)}},</strong></li>
                <li><strong>{{ucwords($order->customer->city->name)}},</strong></li>
                <li><strong>{{ucwords($order->customer->state->name)}} - {{$order->customer->zipcode}}</strong></li>
                <li><strong>Mobile: {{$order->customer->mobile}}</strong></li>
                <li><strong>Email: {{$order->customer->email}}</strong></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-12">
    <h5><strong>Order Detail</strong></h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 70%">Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $product)
                <tr>
                    <td>{{$product->name}}</td>
                    <td>{{$product->pivot->qty}}</td>
                    <td>{{ number_format($product->pivot->amount,2)}}</td>
                    <td>{{number_format($product->getSubTotal(),2)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>