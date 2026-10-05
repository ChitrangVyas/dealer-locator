<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="public/front/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="public/front/css/bootstrap.css" rel="stylesheet">
    <title>{{config('app.name')}}</title>
    <style>
      .error{
        border: 1px solid red !important;
      }
      .hdr-icon{float: right; position: relative; top: 10px; margin:0; padding: 0;}
      .hdr-icon img{max-width: 32px; margin:0px 0 0 15px; padding: 15px 0 0 0;}
      .store-info-window {
        min-width: 100px;
        font-size: 14px;
        padding: 3px;
      }
      .store-info-name {
        font-size: 24px;
        font-weight: bold;
        color: rgba(0, 0, 0, 0.87);
      }
      .store-info-status {
        margin-top: 6px;
        border-bottom: 1px solid #00000029;
        padding-bottom: 6px;
      }
      .store-info-address, .store-info-phone {
        margin-top: 8px;
        display: flex;
        align-items: center;
      }
      ul.location_details li img {
          width: 45px !important;
        }
    </style>
   <!-- Google Tag Manager -->

    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':

    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],

    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=

    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);

    })(window,document,'script','dataLayer','GTM-TWM53PTP');</script>

    <!-- End Google Tag Manager -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD81Jju77TDlsKWhDpykw6r-PTilDCLEI4&v=weekly"></script>
  </head>
  <body>
    <!-- Google Tag Manager (noscript) -->

  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TWM53PTP"

  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

  <!-- End Google Tag Manager (noscript) --> 
    <!-- Header -->
    <!--header>
        <div class="container">
            <div class="row">
                <h1 class="logo"><a href="/"><img src="front/img/logo.png" alt=""></a></h1>
                <div class="c-burger"> <span class="glyphicon glyphicon-menu-hamburger"></span> </div>
                <div class="col-lg-12 justify-content-end text-right hdr-icon">
                  <a href="/"><img src="front/img/home.png" alt=""></a> <a href="#locate"><img src="front/img/location.png" alt=""></a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </header-->
    <!-- Header -->
    <!-- Banner -->
    <section class="banner"> <img src="public/front/img/banner-home.jpg" alt="" class="main-img"> </section>
    <!-- Banner -->
    <!-- Search Area -->
    <section class="main_search" id="locate">
      <form id="locater_form">
        <div class="row">
          <div class="col-xl-3 col-md-6 col-sm-6 col-12 text-center">
              <select  id="category_id" name="category_id" required="required">
                <option value="">Category</option>
                @foreach($categories as $catgory)
                  <option value="{{$catgory->id}}">{{$catgory->name}}</option>
                @endforeach
              </select>
          </div>
          <div class="col-xl-3 col-md-6 col-sm-6 col-12 text-center">
              <select  name="state_id" id="state_id" required="required">
                <option value="">State</option>        
                </select>   
          </div>
          <div class="col-xl-3 col-md-6 col-sm-6 col-12 text-center">
              <select  name="city_id" id="city_id" required="required">
                <option value="">City</option>
              </select>
          </div>
          <div class="col-xl-3 col-md-6 col-sm-6 col-12 text-center">
              <input type="text" id="zip_code" name="zip_code" placeholder="Zip Code">
          </div>
        </div>
        <div class="row">
          <div class="col-xl-3 col-md-6 col-sm-6 col-12"></div>
          <div class="range-slider p-3 col-xl-3 col-md-6 col-sm-6 col-12">
            <div class="d-flex justify-content-center">
              <div class="small_title p-2">Select Radius</div>
              <input type="range" min="5" max="100"  step="5" name="radius" id="radius" value="{{config('app.google_map_radius')}}" onchange="updateTextInput(this.value)" oninput="rangeValue.innerText=this.value+' KM' ">
              <div class="km_txt p-2" id="rangeValue">{{config('app.google_map_radius')}} KM</div>
              <script>
                  function updateTextInput(val) {
                      document.getElementById('textInput').value = val;
                  }
              </script>
            </div>
          </div>
          <div class="srch_btn col-xl-3 col-md-6 col-sm-6 col-12">
            <input type="button" name="" value="Search" id="search">
          </div>
          <div class="col-xl-3 col-md-6 col-sm-6 col-12"></div>
        </div>
      </form>
    </section>  
    <!-- Search Area -->
    <!-- Result Area -->
    <section class="result_area">
      <div class="total_result" style="text-align: center;">Please select Category, State and City and choose the radius then click the search button to locate the nearest available dealers. </div>
      
    </section>
    <!-- Result Area-->
    <!-- Footer Area -->
    <footer>
      <a href="">ABOUT</a>  |  <a href="">PRODUCTS</a>  |  <a href="">INVESTORS</a>  |  <a href="">ESG</a>  |  <a href="">CONTACT</a>  |  <a href="">E-SHOP</a> 
    </footer>

    <!-- Footer Area -->
    <!-- Optional JavaScript -->
    <!-- Sticky Header JS --> 
   
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="public/front/js/bootstrap.min.js"></script>
    <script type="application/javascript">
        $("document").ready(function(){
            var shrinkHeader = $( '.banner' ).outerHeight() / 1;
            var margTop = $( 'header' ).outerHeight();
            $('#locater_form')[0].reset();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });
            $('#category_id').on('change', function() {
                $.ajax({
                    url: "{{route('getState')}}",
                    type: 'POST',
                    data:{ id: $(this).val()},
                    success: function(result){
                        $("#state_id").html(result);
                    }
                });
            });
            $('#state_id').on('change', function() {
                $.ajax({
                    url: "{{route('getValue')}}",
                    type: 'POST',
                    data:{ id: $(this).val(),category_id:$("#category_id").val()},
                    success: function(result){
                        $("#city_id").html(result);
                    }
                });
            });
            $('#category_id').on('change', function() {
             
              $('#zip_code').val('');  
            });
            $('#search').on('click', function() {
              if($("#category_id").val() != '' && ($("#state_id").val() != '' || ($("#state_id").val() != '' && $("#city_id").val() != '') || $("#zip_code").val() != '')){
                $("#state_id").removeClass("error"); 
                $("#city_id").removeClass("error"); 
                $("#category_id").removeClass("error"); 
                let geocoder = new google.maps.Geocoder();
                let category_id = $("#category_id").val();
                let radius = $("#radius").val();
                var address = '';              
                
               
                if($("#state_id").val() != '' && $("#city_id").val() != '' && $("#zip_code").val() != ''){
                  var address = $("#city_id option:selected").text()+' '+$("#state_id option:selected").text()+' '+$("#zip_code").val();
                }else if($("#state_id").val() != '' && $("#city_id").val() != '') {
                  var address = $("#city_id option:selected").text()+','+$("#state_id option:selected").text();
                }else if($("#state_id").val() != '' && $("#zip_code").val() != '') {
                  var address = $("#state_id option:selected").text()+' '+$("#zip_code").val();
                }else if($("#state_id").val() != ''){
                  var address = $("#state_id option:selected").text();
                }else if($("#zip_code").val() != ''){
                  var address = $("#zip_code").val();
                }
                
                console.log(address);
                codeAddress(address,category_id,radius);
                function codeAddress(address,category_id,radius) {                 
                  geocoder.geocode( { 'address': address}, function(results, status) {
                    if (status == 'OK') {
                      
                      console.log(results[0].geometry.location.lat());
                      console.log(results[0].geometry.location.lng());
                    results = results.filter(function(i,key){return i.geometry.bounds});
                    console.log(results);
                      $.ajax({
                          url: "{{route('getDealers')}}",
                          type: 'POST',
                          data: {lat : results[0].geometry.location.lat(),long:results[0].geometry.location.lng(),category_id:category_id,radius:radius},
                          success: function(result){
                              $(".result_area").html(result);     
                              $('html, body').animate({
                                  scrollTop: $(".result_area").offset().top
                              }, 2000);                   
                          }
                      });
                      
                    } else {
                      alert('Geocode was not successful for the following reason: ' + status);
                    }
                  });
                }

              }else{ 
                if($("#state_id").val() == ''){ $("#state_id").addClass("error"); }
                if($("#city_id").val() == ''){ $("#city_id").addClass("error"); }
                if($("#category_id").val() == ''){ $("#category_id").addClass("error"); }
                alert('Please Select Category,State & City Or Category & fill Zip code Input');
              }
            });
        });   
    </script>
  </body>
</html>