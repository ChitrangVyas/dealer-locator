<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{config('app.name', 'Kasyap')}} - @yield('title')</title>

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
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  @include('layouts.partials.header')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  @include('layouts.partials.sidebar')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    
    @include('layouts.partials.breadcrumb')
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      @yield('content')
    </section>
    <!-- /.content -->
  </div>
  @include('layouts.partials.footer')
  <!-- /.content-wrapper -->
  
</div>
<!-- ./wrapper -->
<script src="{{asset('js/app.js') }}"></script>
<script>
    function dataTableRequest(url){
      return {
                "url": url,
                "type": "POST",
                "data": function ( d ) {
                    d._token = $("meta[name='csrf-token']").attr("content");
                }
            }
    }
  </script>
<!-- AdminLTE for demo purposes -->
@if(!config('adminlte.enabled_laravel_mix'))
    <!-- <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script> -->
    {{-- Configured Scripts --}}
    @include('adminlte::plugins', ['type' => 'js'])
    <!-- <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script> -->
@else
    <!-- <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script> -->
@endif

@yield('js')
<script>
  $("document").ready(function(){
    $('.required_field').addClass('text-danger');
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    });
    $(".nav-link.active").parent().parent().parent().addClass("menu-is-opening menu-open");
    $('body').on('change','input[type="file"]',function(e){
            var fileName = e.target.files[0].name;
            $('.custom-file-label').html(fileName);
        });
  });
</script>
<!-- jQuery -->

</body>
</html>