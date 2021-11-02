<!-- info row -->
<div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
        From
        <address>
            @if(!empty($order->addedBy))
                <strong>{{ucwords($order->addedBy->name)}}</strong><br>
                @if(!empty($order->addedBy->city))
                {{ucwords($order->addedBy->address)}},<br>
                {{ucwords($order->addedBy->city->name)}},<br>
                {{ucwords($order->addedBy->state->name)}} - {{$order->addedBy->zipcode}}<br>
                @endif
                @if(!empty($order->addedBy->mobile))
                Phone: {{$order->addedBy->mobile}}<br>
                @endif
                Email: {{$order->addedBy->email}}<br>
                GSTIN: {{ ((!empty($to_user->gstin))?$to_user->gstin:' N/A ') }}
            @else
                <strong>{{ucwords($setting['name'])}}</strong><br>
                {{ucwords($setting['address'])}},<br>
                {{ucwords($setting['city_name'])}},<br>
                {{ucwords($setting['state_name'])}} - {{$setting['zipcode']}}<br>
                Phone: {{$setting['mobile']}}<br>
                Email: {{$setting['email']}}<br>
                GSTIN: {{ ((!empty($setting['gstin']))?$setting['gstin']:' N/A ') }}
            @endif
        </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
        To
        <address>
        @if(!empty($order->customer))
            <strong>{{ucwords($order->customer->name)}}</strong><br>
            {{ucwords($order->customer->address)}},<br>
            {{ucwords($order->customer->city->name)}},<br>
            {{ucwords($order->customer->state->name)}} - {{$order->customer->zipcode}}<br>
            Phone: {{$order->customer->mobile}}<br>
            Email: {{$order->customer->email}}<br>
            GSTIN: {{ ((!empty($to_user->gstin))?$to_user->gstin:' N/A ') }}
        @else
            <strong>{{ucwords($setting['name'])}}</strong><br>
            {{ucwords($setting['address'])}},<br>
            {{ucwords($setting['city_name'])}},<br>
            {{ucwords($setting['state_name'])}} - {{$setting['zipcode']}}<br>
            Phone: {{$setting['mobile']}}<br>
            Email: {{$setting['email']}}<br>
            GSTIN: {{ ((!empty($setting['gstin']))?$setting['gstin']:' N/A ') }}

        @endif
        </address>
    </div>
    <!-- /.col -->
    <div class="col-sm-4 invoice-col">
        <b class="float-right">Date: {{date('d/m/Y',strtotime($order->created_at))}}</b>
        @if ($order->type == 'sale')
        <b>Invoice #{{$order->order_number}}</b><br>
        <br>
        @endif
    </div>
    <!-- /.col -->
</div>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <h5><strong>Order Detail</strong></h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 60%;">Product</th>
                    <th class="text-center">HSN/SAC</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderProduct as $orderProduct)
                <tr>
                    <td style="width: 60%;">{{(!empty($orderProduct->product_name))?$orderProduct->product_name:$orderProduct->product->name}}</td>
                    <td class="text-center">{{ (!empty($orderProduct->product->hsn_sac)) ? $orderProduct->product->hsn_sac : '-' }}</td>
                    <td>{{ number_format($orderProduct->amount,2)}}</td>
                    <td>{{$orderProduct->qty}}</td>
                    <td>{{ number_format($orderProduct->getsubTotal(),2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3"></td>
                    <td><strong>Sub Total</strong></td>
                    <td>{{ number_format($order->getSubTotal()['sub_total'],2) }}</td>
                </tr>
                @foreach ($order->getTotalTax() as $tax)
                <tr>
                    <td colspan="3"></td>
                    <td><strong>{{$tax->name}}</strong></td>
                    <td>{{ number_format($tax->total_tax,2) }}</td>
                </tr>    
                @endforeach
                <tr>
                    <td colspan="3"></td>
                    <td><strong>Total</strong></td>
                    <td>{{ number_format($order->total,2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>