<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{config('icon.logo')}}" type="image/x-icon" />
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
<div id="overlay">
  <div class="cv-spinner">
    <span class="spinner"></span>
  </div>
</div>
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
  

    $(".restrictFileUpload").change(function(e) {
        var filePath = $(this).val();
        var allowedExtensions =/(\.jpg|\.jpeg|\.png|\.gif)$/i;
        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid file type, Only allowed .jpg, .jpeg, .png, .gif');
            filePath = $(this).val('');
            return false;
        }else{          
          $(this).parent().parent().parent().find('.alert').hide();
        }
    });
    $(".note-editable").focusout(function(e) {
      $(this).parent().parent().parent().find('.alert').hide();
    });
  $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
    });
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
<script>
  $("document").ready(function(){
        Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 5000
          });
        });
</script>
@yield('js')
<script>
  $("document").ready(function(){  
    $('.required_field').addClass('text-danger');
    $("body").on('focusout','input,select,textarea',function(e){      
      if($(this).val()!=""){        
        // $(this).parent().find(".alert").hide();        
        $(this).parent().find('.alert').hide();
        $(this).parent().parent('.form-group').find('.alert').hide();
      }else{   
        $(this).parent('.form-group').find('.alert').show();
        $(this).parent().parent('.form-group').find('.alert').show();
        // $(this).parent().find(".alert").show();
      }
    });
    $("select").change(function(e){      
      if($(this).val()!=""){        
        $(this).parent().find(".alert").hide();        
      }else{
        $(this).parent().find(".alert").show();
      }
    });
    $("body").on("shown.bs.modal",'.modal', function () {
      $("body").addClass("modal-open");
    }).on("hidden",'.modal', function () {
      $("body").removeClass("modal-open")
    });
    $(".nav-link.active").parent().parent().parent().addClass("menu-is-opening menu-open");
    $('body').on('change','input[type="file"]',function(e){
            e.stopImmediatePropagation();
            $(this).parent('.form-group').find(".alert").hide();
            var fileName = e.target.files[0].name;
            $(this).parent().find("label").html(fileName);
            //$('.custom-file-label').closest('label').html(fileName);
        });
    $('.notifications').on("show.bs.dropdown",function(e){
      if($(".notify-counter").hasClass("blink")){
        $(".notify-counter").removeClass("blink");
        $(".bell-icon").removeClass("bell");
      }
    });
    $('.notifications').on('click','.dropdown-item',function(){
      var notification = $(this).data('notification');
      if(notification != '' || (!isEmpty(notification))){
        getUserNotification(notification).then(function(res){
          markAsRead(notification,1);
        });
      }
    });
    $("input[data-bootstrap-switch]").each(function(){
        // $(this).bootstrapSwitch('state', $(this).prop('checked'));
        $(this).bootstrapSwitch();
    });
    @auth
    setInterval(function(){
      var total_notification = "0";
      if($('.navbar-badge').hasClass('notify-counter')){
        total_notification = $('.notify-counter').text();
      }
      getNotification().then(function(res){
        if(total_notification != $('.notify-counter').text()){
          $('.notify-counter').addClass('blink');
          $('.bell-icon').addClass('bell');
        }
      });
    },60000);
    $('.edit-profile').on('click',function(){
      // $('#profileModal').modal('show');
      var token = $("meta[name='csrf-token']").attr("content");
      $.ajax({
          url: "{{route('profile.edit')}}",
          type: 'POST',
          data:{id:"{{auth()->user()->id}}",'_token':token},
          success: function(res) {
            if(res != '' && res.code == "1"){
              $(`#profileModal .modal-body`).html(res.data.content);
              $('#profileModal .modal-title').text(res.data.title);
              $('#profileModal').modal('show');
              $('#rutika').summernote({
            height: 200,
            toolbar: [
                ["history", ["undo", "redo"]],
                ["style", ["style"]],
                ["font", ["bold", "italic", "underline", "fontname", "strikethrough", "superscript", "subscript", "clear"]],
                ["color", ["forecolor", "backcolor", "color"]],
                ["paragraph", ["ul", "ol", "paragraph", "height"]],
                ["table", ["table"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["codeview"]],
            ],

    });
            }
          }
      });
    });
    @endauth
  });
</script>
<!-- jQuery -->

</body>
</html>