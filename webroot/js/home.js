var map;

function getinfoboxcontent(host) {

    var imagelist = "";
    if (typeof(host.videourl) !== "undefined") {
        src = baseurl + "/videos/" + host.videourl;
        imagelist += '<li><video autoplay width="200" controls=""><source src="' + src + '" type="video/mp4"></video></li>';
    }

    for (i=0; i< host.pictureids.length; i++) {
        imagelist += '<li><img src="' + baseurl + '/pictures/get/' + host.pictureids[i] + '?resolution=250x" /></li>';
    }

    var str  = '<div class="infobox">'+
        '</div>'+
        '<h1 class="firstHeading">host.nickname</h1>'+
        '<div class="bodyContent">'+
        '<p><b>host.title</b><br />' +
        '<div class="demo"><ul class="lightSlider">' +
        imagelist +
        '</ul></div>' +
        '<p><a href="https://play.google.com/store/apps/details?id=com.yellowdesks.android">Book on Android App</a></p>' +
        '<p><a href="' + baseurl + '/hosts/details/' + host.id + '">Details</a></p>' +
        '<b>Included: </b>host.details<br />'+
        '<b>Extras: </b>host.extras<br />'+
        'open_monday'+
        'open_tuesday'+
        'open_wednesday'+
        'open_thursday'+
        'open_friday'+
        'open_saturday'+
        'open_sunday'+
        'price_1day'+
        'price_10days'+
        'price_1month'+
        'price_6months'+
        '</div>';

    str = str.replace("host.nickname", host.nickname);
    str = str.replace("host.title", host.title);
    str = str.replace("host.details", host.details);
    str = str.replace("host.extras", host.extras);
    str = str.replace("host.picture_id", host.picture_id);
    str = str.replace("host.video_id", host.video_id);
    str = str.replace("host.open_247fixworkers", host.open_247fixworkers);
    str = str.replace("open_monday", host.open_monday_from == null ? "" : '<b>Open Monday</b> ' + host.open_monday_from + '-' + host.open_monday_till + '<br />');
    str = str.replace("open_tuesday", host.open_tuesday_from == null ? "" : '<b>Open Tuesday</b> ' + host.open_tuesday_from + '-' + host.open_tuesday_till + '<br />');
    str = str.replace("open_wednesday", host.open_wednesday_from == null ? "" : '<b>Open Wednesday</b> ' + host.open_wednesday_from + '-' + host.open_wednesday_till + '<br />');
    str = str.replace("open_thursday", host.open_thursday_from == null ? "" : '<b>Open Thursday</b> ' + host.open_thursday_from + '-' + host.open_thursday_till + '<br />');
    str = str.replace("open_friday", host.open_friday_from == null ? "" : '<b>Open Friday</b> ' + host.open_friday_from + '-' + host.open_friday_till + '<br />');
    str = str.replace("open_saturday", host.open_saturday_from == null ? "" : '<b>Open saturday</b> ' + host.open_saturday_from + '-' + host.open_saturday_till + '<br />');
    str = str.replace("open_sunday", host.open_sunday_from == null ? "" : '<b>Open sunday</b> ' + host.open_sunday_from + '-' + host.open_sunday_till + '<br />');

    str = str.replace("price_1day", host.price_1day == null ? "" : '<b>Price 1 Day</b> ' + host.price_1day + 'EUR<br />');
    str = str.replace("price_10days", host.price_10days == null ? "" : '<b>Price 10 Days</b> ' + host.price_10days + 'EUR<br />');
    str = str.replace("price_1month", host.price_1month == null ? "" : '<b>Price 1 Month</b> ' + host.price_1month + 'EUR<br />');
    str = str.replace("price_6months", host.price_6months == null ? "" : '<b>Price 6 Months</b> ' + host.price_6months + 'EUR<br />');

    return str;
}

String.prototype.format = function()
{
    var content = this;
    for (var i=0; i < arguments.length; i++)
    {
        var replacement = '{' + i + '}';
        content = content.replace(replacement, arguments[i]);  
    }
    return content;
};

function markerclick(event) {
    var url = "hosts/details/" + this.host.id;
    $(".iframelightbox iframe").attr("src", url);
    $(".iframelightbox").show();
}

$( document ).ready(function() {
    $( ".iframeclose" ).click(function() {
        $(".iframelightbox").hide();
    });
});

function markerclick2(event) {
    var infowindow = new google.maps.InfoWindow({
        content: getinfoboxcontent(this.host),
    });
    /*gerd infowindow.close();*/
    $("#home-logo").addClass("smalllogo");
    infowindow.open(map, this);

    google.maps.event.addListener(infowindow, 'domready', function(){
        $(".lightSlider").lightSlider({
            gallery: false,
            item: 1,
            auto: true,
            pause: 5000,
            verticalHeight: 100,
            keyPress: true,
            /* loop: true, prevents video from beeing played properly */
        });
    });
}

function initMap() {
    if (baseurl == "/") baseurl="http://localhost";
    var image = baseurl + '/img/yellowdot.png';


    var uluru = {lat: 47.806021, lng: 13.050602000000026};
        map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: uluru,
        gestureHandling: 'greedy',
        mapTypeControl: false,

        });

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

        //setPosition(place.geometry.location.lat, place.geometry.location.lng);
        //moveMarker();

        if (place.geometry.viewport) {
            // Only geocodes have viewport.
            bounds.union(place.geometry.viewport);
        } else {
            bounds.extend(place.geometry.location);
        }

        map.fitBounds(bounds);

        //setPosition(place.geometry.location.lat, place.geometry.location.lng);
    });

    for (i=0; i<hosts.length; i++) {
        marker = new google.maps.Marker({
            position: {lat: hosts[i].lat, lng: hosts[i].lng},
            map: map,
            icon: image,
            host: hosts[i],
            });
        marker.addListener('click', markerclick);
    }
}
$.getScript( "https://maps.googleapis.com/maps/api/js?key=" + googlemapsapikey + "&libraries=places&callback=initMap");
