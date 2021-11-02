@foreach ($orders as $order)
<tr>
    <td>{{$order->order_number}}</td>
    @php $order_products =''; $order_products = implode(', ',$order->orderProduct->pluck('product_name')->toArray()) @endphp
    <td>{{ $order_products }}</td>
    @if($order->type =='sale')
    <td><span class="badge badge-info">{{(!empty($order->getUserOrderStatus()))?$order->getInvoiceStatus(($order->getUserOrderStatus())->status_id):'N/A'}}</span></td>
    @endif
    <td>{{$order->updated_at}}</td>
</tr>
@endforeach