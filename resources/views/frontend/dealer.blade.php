<style>
  #map {
  height: 100%;
}

</style>
@if(!empty($dealers) && count($dealers) > 0)
<div class="total_result">Total Dealers List({{ $total }})</div>
<div class="row m-0">
  <div class="col-xl-3 col-lg-3 col-sm-12 col-12 p-0">
    <ul class="location_details">
      @php
      $i=1;
      $data = [];
      @endphp

      @foreach($dealers as $dealer)
      @php
      
        $data[] = [$dealer->name,(float)$dealer->latitude,(float)$dealer->longitude,$dealer->address,$dealer->cname,$dealer->mobile];
      @endphp
        <li class="d-flex justify-content-between">       
          <p style="width: 80%;"><strong>{{ $i }}. {{ $dealer->name }}</strong><br>{{ $dealer->address }} <br>Tel : {{ $dealer->mobile }}</p>
          <p style="display: inline-flex;padding: 15px 0;">
            <a href="tel:{{ $dealer->mobile }}" class="d-xl-none mr-2"><img src="front/img/Phone-Icon.png" alt="" width="34"></a>
            <a href="https://www.google.com/maps/dir//{{$dealer->latitude}},{{$dealer->longitude}}/" target="_blank"><img src="front/img/Map-Direction-Icon.png" alt="" width="34"></a>
          </p>
        </li>
        @php
      $i++;
      @endphp
      @endforeach
      
    </ul>
  </div>
  <div class="col-xl-9 col-lg-9 col-sm-12 col-12 p-0">
    <div class="map-responsive" id="mapCanvas">
      
    </div>
  </div>
</div>
<script src="https://maps.googleapis.com/maps/api/js?key={{config('app.google_map_api_key')}}&callback=initMap&v=weekly"></script>
<script>
    var markers_on_map = [];
    var radius_circle = null;
function initMap() {
  const map = new google.maps.Map(document.getElementById("mapCanvas"), {
    zoom: 4.2,
    center: { lat: 19.076, lng: 72.8777 },
    mapTypeId: "roadmap",
  });

  setMarkers(map);
  
}
    
const beaches =@json($data);
function setMarkers(map) {
  let  radius_km = {{ $radius }} ;
  radius_circle = new google.maps.Circle({
  		center: { lat: beaches[0][1], lng: beaches[0][2] },
  		radius: radius_km * 1000,
  		clickable: true,
  		map: map,
      fillColor: '#FF6600',
      fillOpacity: 0.3,
      strokeColor: "#FFF",
      strokeWeight: 0
  	});

  for (let i = 0; i < beaches.length; i++) {
    const beach = beaches[i];
    
        let html = getContentHtml({
            name:  beach[0],
            openStatusText:  beach[4],
            addressLines:  beach[3],
            phoneNumber:  beach[5],
            lat:beach[1],
            long:beach[2]
        });
        var new_marker = new google.maps.Marker({
          position: { lat: beach[1], lng: beach[2] },
          map,
          title: beach[0],
          zoom: 5,
          // icon: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=' + (i + 1) + '|f7685c|000000'
        });
        var infowindow = new google.maps.InfoWindow()
            google.maps.event.addListener(new_marker,'click', (function(new_marker,html,infowindow){              
                return function() {
                  if (typeof(currentInfoWindow) != 'undefined') { 
                      currentInfoWindow.close(); 
                  } 
                  infowindow.setContent(html);
                  infowindow.open(map,new_marker);
                  currentInfoWindow = infowindow; 
                  
                };
            })(new_marker,html,infowindow)); 

  }
  if(radius_circle) map.fitBounds(radius_circle.getBounds());
}

window.initMap = initMap;
function getContentHtml(data) {
    let html = `
        <div class="store-info-window">
            <div class="store-info-name">
                <strong>${data.name}</strong>
            </div>
            <div class="store-info-status">
            <strong> ${data.openStatusText}</strong>
            </div>
            <div class="store-info-address">          
                ${data.addressLines}
                <div class="" style="margin-left:5px">
            <a href="https://www.google.com/maps/dir//${data.lat},${data.long}/" target="_blank" rel="noopener noreferrer" ><img src="public/front/img/Map-Direction-Icon.png" alt="" width="34"></a>
                </div>
                <div class="" style="margin-left:5px">
                  <a href="tel:${data.phoneNumber}" rel="noopener noreferrer" ><img src="public/front/img/Phone-Icon.png" alt="" width="34"></a>
                </div>
            </div>
        </div>`;
    return html;
}

</script>
@else
<div class="total_result" style="text-align: center;">No Result Found. Please Select Nearby City Or Try Increasing Radius To Get The Nearest Store.</div>
@endif