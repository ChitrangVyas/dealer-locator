<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{config('icon.logo')}}" type="image/x-icon" />

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
    <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/app.css')}}">

  {{-- Base Stylesheets --}}
    @if(!config('adminlte.enabled_laravel_mix'))
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        {{-- Configured Stylesheets --}}
        @include('adminlte::plugins', ['type' => 'css'])
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif
    
</head>
<body class="hold-transition sidebar-mini" onload="window.print();" onfocus="window.close()">

<div class="hdr" style="padding: 15px 0;">
  <div class="invoice-logo"><img src="{{ asset(config('icon.logo')) }}" alt="Logo" class="brand-image img-circle elevation-3" style="max-height: 300px;background:#000"></div>
</div>

@include('layouts.partials.invoice')
@if($termscondition != '')
  <div class="col-sm-6 col-md-6">
    <label>Terms and Conditions</label>
    <p>{{$termscondition}}</p>
  </div>
@endif
</body>
<script>
    window.onafterprint = function () {
    window.close();
}
</script>
</html>