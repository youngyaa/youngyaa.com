/***********************************************
* Loading gif
***********************************************/
$(window).load(function() {
  setTimeout(function () {
    $(".loading").fadeOut("slow");
  }, 1000);
});

/***********************************************
* Slideshow Gallery
***********************************************/
jQuery('#intro-slides').slippry({
  // general elements & wrapper
  slippryWrapper: '<div class="sy-box pictures-slider" />', // wrapper to wrap everything, including pager

  // options
  adaptiveHeight: false, // height of the sliders adapts to current slide
  captions: false, // Position: overlay, below, custom, false

  // pager
  pager: false,
  
  // controls
  controls: false,
  autoHover: false,

  // transitions
  transition: 'kenburns', // fade, horizontal, kenburns, false
  kenZoom: 0,
  speed: 6500 // time the transition takes (ms)
});

/***********************************************
* Google maps
***********************************************/
google.maps.event.addDomListener(window, 'load', init);

function init() {
    // Basic options for a simple Google Map
    // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
    var mapOptions = {
        // How zoomed in you want the map to start at (always required)
        zoom: 16,

        // The latitude and longitude to center the map (always required)
        center: new google.maps.LatLng(-33.8657865,151.20699920000004),
        // New York

        // How you would like to style the map. 
        // This is where you would paste any style found on Snazzy Maps.
        styles: [{"featureType":"water","stylers":[{"visibility":"on"},{"color":"#b5cbe4"}]},{"featureType":"landscape","stylers":[{"color":"#efefef"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#83a5b0"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#bdcdd3"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e3eed3"}]},{"featureType":"administrative","stylers":[{"visibility":"on"},{"lightness":33}]},{"featureType":"road"},{"featureType":"poi.park","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":20}]},{},{"featureType":"road","stylers":[{"lightness":20}]}],

        // Disable scrolling
        scrollwheel: false,
        navigationControl: false,
        mapTypeControl: false,
        scaleControl: false,
    };

    // Get the HTML DOM element that will contain your map 
    // We are using a div with id="map" seen below in the <body>
    var mapElement = document.getElementById('maps');

    // Create the Google Map using our element and options defined above
    var map = new google.maps.Map(mapElement, mapOptions);

    // Let's also add a marker while we're at it
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(-33.8657865,151.20699920000004),
        
        map: map,
        title: ''
        
    });
    
    var infowindow = new google.maps.InfoWindow({content:"<b>YoungYaa</b><br/>301 George Street<br/>2131 Syndey" });
    google.maps.event.addListener(marker, "click", function(){infowindow.open(map,marker);});
    infowindow.open(map,marker);
    
  
}
    //google.maps.event.addDomListener(window, 'load', init_map);

/***********************************************
* Intense - Image zooming
***********************************************/
window.onload = function() {
  var elements = document.querySelectorAll( '.zoom, .portfolio-item' );
  Intense( elements );
}

/***********************************************
* Hamburger menu behaviour
***********************************************/
$(window).scroll(function() {
  if($(document).scrollTop() > 1){
    $('#hamburger').removeClass('dark');
  }
  else {
    $('#hamburger').addClass('dark');
  }
});

// Animate icon on click
$(document).ready(function(){
  $('#hamburger').click(function(){
    $(this).toggleClass('open');
    $('.navbar-abel').toggleClass('open');
  });
});

// Set hamburger icon color depending on background (light or dark)
document.addEventListener('DOMContentLoaded', function () {
  BackgroundCheck.init({
    targets: '.bg-check',
    images: '.bg'
  });
});


/***********************************************
* Smooth scrolling
***********************************************/
$('a').click(function(e){

  // If internal link
  if (/#/.test(this.href)) {
    e.preventDefault();
    
    var target = $( $.attr(this, 'href') );
    $('body,html').animate({'scrollTop': target.offset().top}, 1000, function(){ animating = false; });
  }

});