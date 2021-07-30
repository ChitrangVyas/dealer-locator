<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>App Name - @yield('title')</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
    <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/app.css')}}">
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

<!-- jQuery -->

<!-- AdminLTE for demo purposes -->
<script src="{{asset('js/app.js') }}"></script>
</body>
</html>