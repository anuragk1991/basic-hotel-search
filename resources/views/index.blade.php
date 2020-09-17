@extends('layouts.template')

@section('content')
<div>
    <div class="ui-widget">
      <label for="city">City: </label>
      <input id="city" placeholder="Type City Name">
      
    </div>

    <div class="clearfix"></div>

    <div class="row">
        <div class="col-md-6">
            <ul id="hotel-list" class="list-unstyled hotel_listing">
              
          </ul>
        </div>
        <div class="col-md-6" >
            <div id="map"></div>
        </div>
    </div>
</div>

@endSection('content')

@section('scripts')
    <script type="text/javascript">
    var products = []
    var markers = []
    $(function() {
        $( "#city" ).autocomplete({
          source: function( request, response ) {
            axios.get("{{ route('search.product') }}", {params:{q: request.term}}).then((result) => {
                $('#map').show()
                products = result.data.products
                response($.map(result.data.cities , function (item) {
                    return {
                        label: item,
                        value: item
                    };
                }));
            }).catch((error) => {
                console.log(error.response.data.errors.q)
            })
          },
          minLength: 3,
          select: function( event, ui ) {
            var list = []
            list = products.filter((el) => {
                return el.city == ui.item.label
            })

            setMarkers(list)
            listHotels(list)
          },
          open: function() {
            $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
          },
          close: function() {
            $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
          }
        });
    });

    function initMap() {
      map = new google.maps.Map(document.getElementById("map"), {
        center: {
          lat: -34.397,
          lng: 150.644
        },
        zoom: 8
      });

      $('#map').hide()
    }

    function addMarker(location, title, price) {
      const marker = new google.maps.Marker({
        position: location,
        map: map,
        title: title + ' - price: ' + price + ' USD'
      });
      markers.push(marker);
      return marker
    }

    function setMarkersOnMap(map) {
      for (let i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
      }
    }

    function clearMarkers() {
      setMarkersOnMap(null);
    }

    function deleteMarkers() {
      clearMarkers();
      markers = [];
    }

    function setMarkers(data){
        deleteMarkers()

        var bounds = new google.maps.LatLngBounds();
        data.forEach((el) => {
           var position = {
              lat: el.latitude,
              lng: el.longitude,
            }

            var marker = addMarker(position, el.title, el.price)
            bounds.extend(marker.getPosition());
        });

        map.fitBounds(bounds);
    }

    function listHotels(data){
        $('#hotel-list').html('')
        var hotel_data = ''
        data.forEach((el) => {
            hotel_data += ` 
                <li class="media">
                    <img class="mr-3 hotel_image" src="{{ asset('images/default.jpg') }}" alt="Generic placeholder image">
                    <div class="media-body">
                      <h5 class="mt-0 mb-1"><strong>${el.title}</strong></h5>
                      <h6>Price - ${el.price} USD</h6>
                      Basic description about hotel
                      Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    </div>
                  </li>
            `
        })

        $('#hotel-list').html(hotel_data)

    }
</script>
@endSection('scripts')



