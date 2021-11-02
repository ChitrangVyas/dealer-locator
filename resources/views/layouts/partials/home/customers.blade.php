@foreach ($customers as $customer)
<tr>
    <td>{{$customer->name}}</td>
    <td>{{$customer->email}}</td>
    <td>{{$customer->mobile}}</td>
</tr>
@endforeach