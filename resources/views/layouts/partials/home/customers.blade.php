@foreach ($customers as $customer)
<tr>
    <td>{{$customer->name}}</td>
    <td>{{$customer->email}}</td>
    <td>{{Str::getMobileWithCountryCode($customer->mobile,$customer->country_id)}}</td>
</tr>
@endforeach