@extends('emails.template')
@section('body')
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td class="col-sm-4 invoice-col" colspan="2">
            <p><strong>Dear {{Str::lower($user->userGroup->name)}},</strong></p>
            <p>Welcome to our site. You can login with below credentials.</p>
        </td>
    </tr>
    @if (!empty($user))
    <tr>
        <td class="col-sm-4 invoice-col" colspan="2">
            <p class="col-sm-4 invoice-col">
                <hr>
                <p><strong>Email:</strong> {{$user->email}}</p>
                <p><strong>Password:</strong> {{$password}}</p>
                <p><a href="{{$link}}" target="_blank">{{$link}}</a></p>
            </p>
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