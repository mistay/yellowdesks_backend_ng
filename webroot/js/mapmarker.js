var map;
var marker = null;
//var yellowicon = '../img/yellowdot.png';
var yellowicon = 'http://localhost/imgg/yellowdot.png';

// salzburg, default
var position = {lat: 47.80097678080353, lng: 13.044660806655884 };

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: position
    });


    // Create the search box and link it to the UI element.
    var input = document.getElementById('pac-input');
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    // Bias the SearchBox results towards current map's viewport.
    map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
    });

    searchBox.addListener('places_changed', function() {
        console.log("places_changed");
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        // For each place, get the icon, name and location.
        var bounds = new google.maps.LatLngBounds();

        place = places[0];
        var icon = {
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(25, 25)
        };

        setPosition(place.geometry.location.lat, place.geometry.location.lng);
        moveMarker();

        if (place.geometry.viewport) {
            // Only geocodes have viewport.
            bounds.union(place.geometry.viewport);
        } else {
            bounds.extend(place.geometry.location);
        }

        map.fitBounds(bounds);

        setPosition(place.geometry.location.lat, place.geometry.location.lng);
    });

    makeMarker();
}

function makeMarker() {
    marker = new google.maps.Marker({
        position: {lat: this.position.lat, lng: this.position.lng},
        map: map,
        icon: yellowicon,
        draggable:true,
    });

    marker.addListener('dragend', 
        function markerdragged() {
            setPosition( marker.position.lat(), marker.position.lng());
        }
    );
}

function moveMarker() {
    marker.setPosition(new google.maps.LatLng(position.lat, position.lng));
}

function setCenter() {
    map.setCenter({lat: position.lat, lng: position.lng});
}

function setPosition(lat, lng) {
    this.position = { lat: lat, lng: lng };

    var evt = $.Event('positionchanged');
    evt.state = position;
    $(window).trigger(evt);
}

$.getScript( "https://maps.googleapis.com/maps/api/js?key=" + googlemapsapikey + "&libraries=places&callback=initMap");