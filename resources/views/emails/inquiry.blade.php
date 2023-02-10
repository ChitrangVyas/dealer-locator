@extends('emails.template')
@section('body')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="col-sm-4 invoice-col" colspan="2">
            <p><strong>Dear Customer,</strong></p>
        </td>
    </tr>
    @if (!empty($inquiry->dealer))
    <tr>
        <td class="col-sm-4 invoice-col" colspan="2">
            <p class="col-sm-4 invoice-col">
                <hr>
                <strong>Dealer Details</strong>
                <br>
                <address>
                    <strong>{{ucwords($inquiry->dealer->name)}}</strong><br>
                    {{ucwords($inquiry->dealer->address)}},<br>
                    {{ucwords($inquiry->dealer->city->name)}},<br>
                    {{ucwords($inquiry->dealer->state->name)}} - {{$inquiry->dealer->zipcode}}<br>
                    Phone: {{$inquiry->dealer->mobile}}<br>
                    Email: {{$inquiry->dealer->email}}<br>
                    GSTIN: {{ ((!empty($inquiry->dealer->gstin))?$inquiry->dealer->gstin:' N/A ') }}
                </address>
            </p>
        </td>
    </tr>
    @endif
    @if (!empty($inquiry->retailer))
    <tr>
        <td class="col-sm-4 invoice-col" colspan="2">
            <hr>
            <p class="col-sm-4 invoice-col">
                <strong>Retailer Details</strong>
                <br>
                <address>
                    <strong>{{ucwords($inquiry->retailer->name)}}</strong><br>
                    {{ucwords($inquiry->retailer->address)}},<br>
                    {{ucwords($inquiry->retailer->city->name)}},<br>
                    {{ucwords($inquiry->retailer->state->name)}} - {{$inquiry->retailer->zipcode}}<br>
                    Phone: {{$inquiry->retailer->mobile}}<br>
                    Email: {{$inquiry->retailer->email}}<br>
                    GSTIN: {{ ((!empty($inquiry->retailer->gstin))?$inquiry->retailer->gstin:' N/A ') }}
                </address>
            </p>
            <hr>
        </td>
    </tr>
    @endif
    <tr>
        <td>
            <p><strong>Regards,</strong></p>
            <p><strong style="color:#6c1a55">{{config('app.name')}}</strong> <br />
            </p>
        </td>
    </tr>
</table>
@endsection
